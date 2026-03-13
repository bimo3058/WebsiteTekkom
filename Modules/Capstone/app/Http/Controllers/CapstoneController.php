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

class CapstoneController extends Controller
{
    public function __construct(
        private PeriodService       $periodService,
        private GroupService        $groupService,
        private TitleService        $titleService,
        private BidService          $bidService,
        private NotificationService $notificationService,
    ) {}

    public function index()
    {
        return view('capstone::index');
    }

    public function dashboard()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name');

        if ($roles->intersect(['superadmin', 'admin'])->isNotEmpty()) {
            return $this->adminDashboard();
        }

        if ($roles->contains('dosen')) {
            return $this->dosenDashboard();
        }

        return $this->mahasiswaDashboard();
    }

    private function adminDashboard()
    {
        $period     = $this->periodService->getActivePeriod();
        $groupStats = $period ? $this->groupService->getStatusStats($period->id) : collect();
        $allPeriods = $this->periodService->getAllPeriods();
        return view('capstone::dashboard.admin', compact('period', 'groupStats', 'allPeriods'));
    }

    private function dosenDashboard()
    {
        $user   = auth()->user();
        $period = $this->periodService->getActivePeriod();
        $titles = $this->titleService->getByLecturer($user->lecturer->id);
        $unread = $this->notificationService->getUnreadCount($user->id);
        return view('capstone::dashboard.dosen', compact('period', 'titles', 'unread'));
    }

    private function mahasiswaDashboard()
    {
        $user   = auth()->user();
        $period = $this->periodService->getActivePeriod();
        $unread = $this->notificationService->getUnreadCount($user->id);

        $group = null;
        if ($period) {
            $group = \App\Models\CapstoneGroupMember::where('student_id', $user->student->id)
                ->where('period_id', $period->id)
                ->with('group:id,status,title_id,period_id')
                ->select('id', 'group_id', 'student_id', 'period_id', 'is_leader')
                ->first()
                ?->group;
        }
        return view('capstone::dashboard.mahasiswa', compact('period', 'group', 'unread'));
    }

    public function create()
    {
        return view('capstone::create');
    }

    public function store(Request $request)
    {
        // TODO: implementasi
        // AuditLogger::create('capstone', "Membuat group capstone: {$group->name}", $group, $group->toArray());
    }

    public function show($id)
    {
        // TODO: implementasi
        // AuditLogger::view('capstone', "Melihat capstone ID {$id}");
        return view('capstone::show');
    }

    public function edit($id)
    {
        return view('capstone::edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: implementasi
        // AuditLogger::update('capstone', "Mengubah capstone ID {$id}", $model, $oldData, $newData);
    }

    public function destroy($id)
    {
        // TODO: implementasi
        // AuditLogger::delete('capstone', "Menghapus capstone ID {$id}", $model, $oldData);
    }
}