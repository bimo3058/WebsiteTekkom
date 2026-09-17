<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Role, Student, Lecturer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Capstone\Support\CapstoneActor;

/** Account management uses users.id; academic selectors keep their existing IDs. */
class BladeUserController extends Controller
{
    private const ROLE_NAMES = ['admin_capstone', 'dosen', 'mahasiswa'];

    public function index(Request $request)
    {
        $data = $request->validate([
            'role'=>'nullable|in:admin,dosen,mahasiswa', 'search'=>'nullable|string|max:255',
            'status'=>'nullable|in:active,suspended', 'sort_by'=>'nullable|in:name,email,created_at',
            'sort_order'=>'nullable|in:asc,desc', 'per_page'=>'nullable|integer|min:1|max:100',
        ]);
        $query = User::query()->with(['roles', 'student'=>fn($q)=>$q->without('user'), 'lecturer'=>fn($q)=>$q->without('user')]);
        if (!empty($data['role'])) {
            $names = $data['role']==='admin' ? ['admin_capstone','superadmin'] : [$data['role']];
            $query->whereHas('roles', fn($q)=>$q->whereIn('name',$names));
        }
        if (!empty($data['search'])) {
            $term='%'.mb_strtolower($data['search']).'%';
            $query->where(fn($q)=>$q->whereRaw('LOWER(name) LIKE ?',[$term])->orWhereRaw('LOWER(email) LIKE ?',[$term])
                ->orWhereHas('student',fn($s)=>$s->where('student_number','like',$term))
                ->orWhereHas('lecturer',fn($l)=>$l->where('employee_number','like',$term)));
        }
        if (($data['status']??null)==='active') $query->whereNull('suspended_at');
        if (($data['status']??null)==='suspended') $query->whereNotNull('suspended_at');
        return $query->orderBy($data['sort_by']??'name',$data['sort_order']??'asc')->orderBy('id')
            ->paginate($data['per_page']??10)->through(fn($user)=>$this->payload($user,$request));
    }

    public function show(Request $request, User $user)
    {
        return response()->json($this->payload($user,$request));
    }

    public function store(Request $request)
    {
        $data=$this->validated($request);
        $user=DB::transaction(function () use ($data) {
            $user=User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']), 'external_id'=>'capstone-local-'.Str::uuid()]);
            $this->saveRolesAndProfiles($user,$data);
            return $user;
        });
        $user->clearUserCache();
        return response()->json($this->payload($user,$request),201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($this->canManage($user),403,'Akun dengan hak administrasi di luar Capstone dikelola melalui administrator sistem.');
        $data=$this->validated($request,$user);
        abort_if($request->user()->id===$user->id && !in_array('admin',$data['roles'],true),422,'Anda tidak dapat menghapus role admin sendiri.');
        DB::transaction(function () use ($user,$data) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $fields=['name'=>$data['name'],'email'=>$data['email']];
            if (!empty($data['password'])) $fields['password']=Hash::make($data['password']);
            $user->update($fields);
            $this->saveRolesAndProfiles($user,$data);
            if (!empty($data['password'])) $user->forceLogout();
        });
        $user->clearUserCache();
        return response()->json($this->payload($user->fresh(),$request));
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($this->canManage($user),403,'Akun ini dikelola melalui administrator sistem.');
        abort_if($request->user()->id===$user->id,422,'Anda tidak dapat menghapus akun sendiri.');
        DB::transaction(function () use ($user) {
            $user->forceLogout();
            $user->tokens()->delete();
            $user->delete();
        });
        $user->clearUserCache();
        return response()->json(['message'=>'User deleted.']);
    }

    private function validated(Request $request, ?User $user=null): array
    {
        $user?->loadMissing(['student','lecturer']);
        $roles=$request->input('roles',[]);
        $isStudent=is_array($roles)&&in_array('mahasiswa',$roles,true);
        $isLecturer=is_array($roles)&&in_array('dosen',$roles,true);
        $data=$request->validate([
            'name'=>'required|string|min:2|max:100',
            'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user?->id)],
            'password'=>[$user?'nullable':'required','string','min:8','max:255'],
            'roles'=>'required|array|min:1|max:3', 'roles.*'=>'required|distinct|in:admin,dosen,mahasiswa',
            'nim'=>[$isStudent?'required':'nullable','string','min:8','max:100',Rule::unique('students','student_number')->ignore($user?->student?->id)],
            'nip'=>[$isLecturer?'required':'nullable','string','max:100',Rule::unique('lecturers','employee_number')->ignore($user?->lecturer?->id)],
            'cohort_year'=>[$isStudent?'required':'nullable','integer','min:1900','max:'.(now()->year+1)],
        ]);
        if ($isStudent && count($data['roles'])>1) {
            throw \Illuminate\Validation\ValidationException::withMessages(['roles'=>'Role mahasiswa harus berdiri sendiri.']);
        }
        return $data;
    }

    private function saveRolesAndProfiles(User $user,array $data): void
    {
        // Preserve unrelated roles and permissions; only change Capstone/academic roles.
        $user->roles()->detach(Role::whereIn('name',self::ROLE_NAMES)->pluck('id'));
        foreach ($data['roles'] as $slug) {
            $name=$slug==='admin'?'admin_capstone':$slug;
            $role=Role::where('name',$name)->where('guard_name','web')->orderByRaw("CASE WHEN module = ? THEN 0 ELSE 1 END",[$slug==='admin'?'capstone':'global'])->firstOrFail();
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
        if (in_array('mahasiswa',$data['roles'],true)) Student::updateOrCreate(['user_id'=>$user->id],['student_number'=>$data['nim'],'cohort_year'=>$data['cohort_year']]);
        if (in_array('dosen',$data['roles'],true)) Lecturer::updateOrCreate(['user_id'=>$user->id],['employee_number'=>$data['nip']]);
        $user->unsetRelation('roles')->unsetRelation('student')->unsetRelation('lecturer');
    }

    private function canManage(User $user): bool
    {
        return !$user->loadMissing('roles')->roles->contains(fn($role)=>$role->name==='superadmin'||(str_starts_with($role->name,'admin_')&&$role->name!=='admin_capstone'));
    }

    private function payload(User $user,Request $request): array
    {
        CapstoneActor::loadProfiles($user);
        $roles=CapstoneActor::roles($user);
        return ['id'=>$user->id,'user_id'=>$user->id,'name'=>$user->name,'email'=>$user->email,
            'role'=>$roles[0]??null,'roles'=>$roles,'other_roles'=>$user->roles->pluck('name')->reject(fn($r)=>in_array($r,[...self::ROLE_NAMES,'superadmin']))->values(),
            'nim'=>$user->student?->student_number,'nip'=>$user->lecturer?->employee_number,'cohort_year'=>$user->student?->cohort_year,
            'lecturer_id'=>$user->lecturer?->id,'student_id'=>$user->student?->id,'created_at'=>$user->created_at,'last_login'=>$user->last_login,
            'status'=>$user->suspended_at?'suspended':'active','is_sso'=>!empty($user->sso_data),
            'can_edit'=>$this->canManage($user),'can_delete'=>$this->canManage($user)&&$request->user()->id!==$user->id];
    }
}
