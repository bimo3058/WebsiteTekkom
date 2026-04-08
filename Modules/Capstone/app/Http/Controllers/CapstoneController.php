<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Modules\Capstone\Services\BidService;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\PeriodService;
use Modules\Capstone\Services\TitleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CapstoneController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        private PeriodService       $periodService,
        private GroupService        $groupService,
        private TitleService        $titleService,
        private BidService          $bidService,
        private NotificationService $notificationService,
    ) {}

    public function index()
    {
        $this->authorize('capstone.view'); // FIX
        return view('capstone::index');
    }

    /* |--------------------------------------------------------------------------
    | Dashboard Methods
    |-------------------------------------------------------------------------- */

    public function adminDashboard()
    {
        $this->authorize('capstone.view'); // FIX
        $period     = $this->periodService->getActivePeriod();
        $groupStats = $period ? $this->groupService->getStatusStats($period->id) : collect();
        $allPeriods = $this->periodService->getAllPeriods();

        return view('capstone::dashboard.admin', compact('period', 'groupStats', 'allPeriods'));
    }

    public function dosenDashboard()
    {
        $this->authorize('capstone.view'); // FIX
        $user   = auth()->user();
        $period = $this->periodService->getActivePeriod();
        
        $lecturerId = $user->lecturer?->id;
        $titles     = $lecturerId ? $this->titleService->getByLecturer($lecturerId) : collect();
        $unread     = $this->notificationService->getUnreadCount($user->id);

        return view('capstone::dashboard.dosen', compact('period', 'titles', 'unread'));
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('capstone.view'); // FIX
        $user   = auth()->user();
        $period = $this->periodService->getActivePeriod();
        $unread = $this->notificationService->getUnreadCount($user->id);

        $group = null;
        if ($period && $user->student) {
            $group = \Modules\Capstone\Models\CapstoneGroupMember::where('student_id', $user->student->id)
                ->where('period_id', $period->id)
                ->with(['group' => function($q) {
                    $q->select('id', 'status', 'title_id', 'period_id', 'supervisor_1_id');
                }])
                ->first()
                ?->group;
        }

        return view('capstone::dashboard.mahasiswa', compact('period', 'group', 'unread'));
    }

    /* |--------------------------------------------------------------------------
    | CRUD & Action Methods
    |-------------------------------------------------------------------------- */

    public function create()
    {
        $this->authorize('capstone.edit'); // FIX
        return view('capstone::create');
    }

    public function store(Request $request)
    {
        $this->authorize('capstone.edit'); // FIX
        DB::beginTransaction();
        try {
            AuditLogger::log(
                module: 'capstone',
                action: 'CREATE',
                description: "Membuat grup capstone baru",
            );

            DB::commit();
            return redirect()->back()->with('success', 'Grup berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('capstone.view'); // FIX
        return view('capstone::show');
    }

    public function edit($id)
    {
        $this->authorize('capstone.edit'); // FIX
        return view('capstone::edit');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('capstone.edit'); // FIX
        DB::beginTransaction();
        try {
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update gagal.');
        }
    }

    public function destroy($id)
    {
        $this->authorize('capstone.delete'); // FIX
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}