<?php

namespace Modules\BankSoal\Http\Controllers\BS\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\BlindReviewItem;
use Modules\BankSoal\Services\BlindReviewService;

class BlindReviewController extends Controller
{
    public function __construct(
        protected BlindReviewService $blindReviewService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('banksoal.view');

        $user = auth()->user();

        // Items yang harus saya review (sebagai reviewer)
        $items = $this->blindReviewService->getAssignedItemsForReviewer($user->id)
            ->values();

        $pendingCount  = $items->where('status', 'pending')->count();
        $approvedCount = $items->where('status', 'approved')->count();
        $rejectedCount = $items->where('status', 'rejected')->count();

        // Round yang saya buat (sebagai creator) beserta feedback reviewer
        $myRounds = \Modules\BankSoal\Models\BlindReviewRound::query()
            ->with(['mataKuliah', 'items.reviewer', 'items.pertanyaan'])
            ->where('created_by', $user->id)
            ->latest()
            ->get();

        return view('banksoal::pages.bank-soal.Dosen.blind-review', compact(
            'items',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'myRounds'
        ));
    }

    public function submit(Request $request, BlindReviewItem $item)
    {
        $this->authorize('banksoal.edit');

        if ((int) $item->reviewer_id !== (int) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas review ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $item->update([
            'status'      => $validated['status'],
            'catatan'     => $validated['catatan'] ?? null,
            'reviewed_at' => now(),
        ]);

        $this->blindReviewService->updateRoundStatus($item->round, $item);

        return back()->with('success', 'Review berhasil disimpan.');
    }
}
