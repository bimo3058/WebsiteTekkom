<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\EoAuditLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PracticumGradePolicy;
use Modules\EOffice\Services\ManajemenPraktikum\GradePolicyManager;
use Modules\EOffice\Services\ManajemenPraktikum\StudentCohort;

class GradePolicyController extends Controller
{
    public function index(GradePolicyManager $manager)
    {
        $policy = $manager->active();
        $history = PracticumGradePolicy::orderByDesc('version')->get();
        return view('eoffice::manajemen-praktikum.admin.grade-policies', compact('policy', 'history'));
    }

    public function store(Request $request, GradePolicyManager $manager)
    {
        $data = $request->validate([
            'expected_policy' => 'required|uuid', 'nama' => 'required|string|max:150', 'rules' => 'required|array|min:1|max:20',
            'rules.*.from' => 'required|integer|min:2000|max:2099', 'rules.*.to' => 'nullable|integer|min:2000|max:2099',
            'rules.*.bands' => 'required|array|min:2|max:12',
            'rules.*.bands.*.minimum' => ['required', 'numeric', 'min:0', 'max:100', 'regex:/^\d{1,3}(\.\d{1,2})?$/D'],
            'rules.*.bands.*.letter' => 'required|string|max:4|regex:/^[A-Za-z][A-Za-z+-]{0,3}$/D',
            'rules.*.bands.*.points' => ['required', 'numeric', 'min:0', 'max:4', 'regex:/^\d(\.\d{1,2})?$/D'],
        ]);
        $manager->create($data);

        return back()->with('success', 'Versi skema predikat baru diaktifkan. Nilai yang sudah dipublikasikan tidak berubah.');
    }

    public function verify(Request $request, Student $student, StudentCohort $cohort)
    {
        abort_unless(DaftarPraktikan::where('user_id', $student->user_id)->exists(), 404);
        $data = $request->validate(['nim' => 'required|string', 'stored_year' => 'nullable|string']);
        DB::transaction(function () use ($student, $data, $cohort) {
            $current = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();
            if ((string) $current->student_number !== $data['nim'] || (string) $current->cohort_year !== (string) ($data['stored_year'] ?? '')) {
                throw ValidationException::withMessages(['angkatan' => 'Data mahasiswa telah berubah. Muat ulang halaman.']);
            }
            $year = $cohort->fromNim($current->student_number);
            $before = $current->cohort_year;
            $current->update(['cohort_year' => $year]);
            EoAuditLog::create(['user_id' => auth()->id(), 'action' => 'verify_cohort', 'subject_type' => Student::class, 'subject_id' => (string) $current->id, 'description' => 'Verifikasi angkatan berdasarkan digit ke-7–8 NIM Teknik Komputer.', 'old_data' => ['cohort_year' => $before], 'new_data' => ['cohort_year' => $year], 'created_at' => now()]);
        });

        return back()->with('success','Angkatan mahasiswa berhasil diverifikasi. Nilai historis yang sudah dipublikasikan tetap menggunakan skema sebelumnya.');
    }
}
