<?php

namespace Modules\BankSoal\Http\Controllers\BS\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\BankSoal\Models\PenarikanSoal;
use Modules\BankSoal\Services\ArsipSoalService;
use Modules\BankSoal\Models\Shared\MataKuliah;
use Modules\BankSoal\Services\MataKuliahService;
use App\Services\SupabaseStorage;

class ArsipSoalController extends Controller
{
    public function __construct(
        private ArsipSoalService $arsipSoalService,
        private MataKuliahService $mataKuliahService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $filters = [
            'search' => $request->get('search'),
            'tahun_akademik' => $request->get('tahun_akademik'),
            'semester' => $request->get('semester'),
        ];

        $arsipPaginated = $this->arsipSoalService->getArsipPaginated($user->id, $filters);
        $penarikanPending = $this->arsipSoalService->getPenarikanPending($user->id);

        // Data for filters
        $availableYears = \Modules\BankSoal\Models\ArsipSoal::query()
            ->byDosen($user->id)
            ->where('status', 'final')
            ->distinct()
            ->pluck('tahun_akademik')
            ->filter()
            ->sortDesc()
            ->values();

        $stats = [
            'total_arsip' => \Modules\BankSoal\Models\ArsipSoal::byDosen($user->id)->where('status', 'final')->count(),
            'total_penarikan' => $penarikanPending->total(),
            'mata_kuliah' => \Modules\BankSoal\Models\ArsipSoal::byDosen($user->id)->where('status', 'final')->distinct()->count('mk_id'),
        ];

        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);

        return view('banksoal::pages.arsip.Dosen.index', compact(
            'arsipPaginated', 
            'penarikanPending', 
            'stats', 
            'availableYears',
            'filters',
            'mataKuliahDosen'
        ));
    }

    public function create(Request $request)
    {
        return redirect()->route('banksoal.arsip.dosen.index');
    }

    public function createPdf(Request $request)
    {
        $user = $request->user();
        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);

        return view('banksoal::pages.arsip.Dosen.create-pdf', compact('mataKuliahDosen'));
    }

    public function createCsv(Request $request)
    {
        $user = $request->user();
        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);

        return view('banksoal::pages.arsip.Dosen.create-csv', compact('mataKuliahDosen'));
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $arsip = $this->arsipSoalService->getArsipById($id, $user->id);

        $pdfUrl = null;
        if (!empty($arsip->pdf_file_path)) {
            $supabaseStorage = new SupabaseStorage();
            $pdfUrl = $supabaseStorage->getPublicUrl($arsip->pdf_file_path);
        }

        return view('banksoal::pages.arsip.Dosen.show', [
            'record' => $arsip,
            'mode' => 'arsip',
            'soalList' => $arsip->getSoalArray(),
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function showPenarikan(Request $request, int $id)
    {
        $user = $request->user();
        $penarikan = $this->arsipSoalService->getPenarikanById($id, $user->id);

        $pdfUrl = null;
        if (!empty($penarikan->pdf_file_path)) {
            $supabaseStorage = new SupabaseStorage();
            $pdfUrl = $supabaseStorage->getPublicUrl($penarikan->pdf_file_path);
        }

        return view('banksoal::pages.arsip.Dosen.show', [
            'record' => $penarikan,
            'mode' => 'penarikan',
            'soalList' => $penarikan->getSoalArray(),
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function editPenarikan(Request $request, int $id)
    {
        $user = $request->user();
        $penarikan = $this->arsipSoalService->getPenarikanById($id, $user->id);

        return view('banksoal::pages.arsip.Dosen.convert', compact('penarikan'));
    }

    public function convertPenarikan(Request $request, int $id)
    {
        $data = $request->validate([
            'nama_arsip' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'catatan_internal' => 'nullable|string',
            'catatan_konversi' => 'nullable|string',
        ]);

        $user = $request->user();
        $arsip = $this->arsipSoalService->convertPenarikanToArsip($id, $user->id, $data);

        return redirect()->route('banksoal.arsip.dosen.show', $arsip->id)->with('success', 'Penarikan berhasil dikonversi menjadi arsip.');
    }

    public function storeFromEkstraksi(Request $request)
    {
        $validated = $request->validate([
            'mk_id' => 'required|exists:bs_mata_kuliah,id',
            'agenda' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap,Antara',
            'metode_ujian' => 'nullable|in:online,offline',
            'soal_json' => 'required|string',
            'direct_archive' => 'nullable|boolean',
            'penarikan_id' => 'nullable|integer',
        ]);

        $soalList = json_decode($validated['soal_json'], true);

        if (! is_array($soalList) || empty($soalList)) {
            return response()->json([
                'success' => false,
                'message' => 'Data soal tidak valid.',
            ], 422);
        }

        $mk = MataKuliah::findOrFail($validated['mk_id']);
        $payload = [
            'nama_ekstraksi' => $mk->nama . ' - ' . $validated['agenda'],
            'nama_arsip' => $mk->nama . ' - ' . $validated['agenda'],
            'tipe_ujian' => $this->mapAgendaToTipeUjian($validated['agenda']),
            'metode_ujian' => $validated['metode_ujian'] ?? 'online',
            'status_cetak' => ($validated['metode_ujian'] ?? 'online') === 'offline' ? 'pending' : null,
            'tahun_akademik' => $validated['tahun_ajaran'],
            'semester' => $validated['semester'],
            'soal_list' => $soalList,
            'deskripsi' => $request->input('deskripsi'),
            'catatan_internal' => $request->input('catatan_internal'),
        ];

        $user = $request->user();
        $directArchive = (bool) ($validated['direct_archive'] ?? false);

        if ($directArchive && !empty($validated['penarikan_id'])) {
            $arsip = $this->arsipSoalService->convertPenarikanToArsip(
                (int) $validated['penarikan_id'],
                $user->id,
                [
                    'nama_arsip' => $payload['nama_arsip'] ?? null,
                    'deskripsi' => $payload['deskripsi'] ?? null,
                    'catatan_internal' => $payload['catatan_internal'] ?? null,
                    'catatan_konversi' => $payload['catatan_konversi'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'type' => 'archived',
                'message' => 'Soal berhasil diarsipkan.',
                'data' => $arsip,
            ]);
        }

        $result = $this->arsipSoalService->createFromEkstraksi(
            $user->id,
            (int) $validated['mk_id'],
            $payload,
            $directArchive
        );

        return response()->json([
            'success' => true,
            'type' => $directArchive ? 'archived' : 'pending',
            'message' => $directArchive
                ? 'Soal berhasil diarsipkan.'
                : 'Soal berhasil disimpan ke riwayat penarikan.',
            'data' => $result,
        ]);
    }

    public function destroyPenarikan(Request $request, int $id)
    {
        $user = $request->user();
        $this->arsipSoalService->discardPenarikan($id, $user->id);

        return back()->with('success', 'Riwayat penarikan berhasil dipindahkan ke status discarded.');
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $arsip = $this->arsipSoalService->getArsipById($id, $user->id);
        $arsip->delete();

        return back()->with('success', 'Arsip soal berhasil dihapus.');
    }

    public function uploadPdf(Request $request)
    {
        $validated = $request->validate([
            'mk_id' => 'required|exists:bs_mata_kuliah,id',
            'tipe_ujian' => 'required|in:kuis,uts,uas,tugas,lainnya',
            'nama_arsip' => 'required|string|max:255',
            'tahun_akademik' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap,Antara',
            'metode_ujian' => 'nullable|in:online,offline',
            'tanggal_ujian' => 'nullable|date',
            'pdf_file' => 'required|file|mimes:pdf|max:5120',
        ]);

        $userId = $request->user()->id;
        $file = $validated['pdf_file'];
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($baseName) ?: 'arsip-soal-pdf';
        $supabaseStorage = new SupabaseStorage();
        $storedPath = $supabaseStorage->upload($file, "banksoal/arsip-soal/pdf/{$userId}", null, $safeBaseName . '-' . now()->format('YmdHis'));

        if (! $storedPath) {
            return back()->with('error', 'Gagal menyimpan file PDF arsip ke Cloud Storage.');
        }

        $metodeUjian = $validated['metode_ujian'] ?? 'online';
        $penarikan = $this->arsipSoalService->savePenarikanSoal($userId, $validated['mk_id'], [
            'nama_ekstraksi' => $validated['nama_arsip'],
            'tipe_ujian' => $validated['tipe_ujian'],
            'metode_ujian' => $metodeUjian,
            'status_cetak' => $metodeUjian === 'offline' ? 'pending' : null,
            'tahun_akademik' => $validated['tahun_akademik'],
            'semester' => $validated['semester'],
            'tanggal_ujian' => $validated['tanggal_ujian'] ?? null,
            'soal_list' => [],
            'pdf_file_path' => $storedPath,
            'status' => 'pending'
        ]);

        return redirect()->route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id)
            ->with('success', 'File PDF berhasil diunggah. Silakan review data sebelum dimasukkan ke arsip final.');
    }

    public function uploadCsv(Request $request)
    {
        $validated = $request->validate([
            'mk_id' => 'required|exists:bs_mata_kuliah,id',
            'tipe_ujian' => 'required|in:kuis,uts,uas,tugas,lainnya',
            'nama_arsip' => 'required|string|max:255',
            'tahun_akademik' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap,Antara',
            'metode_ujian' => 'nullable|in:online,offline',
            'tanggal_ujian' => 'nullable|date',
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:1024',
        ]);

        $userId = $request->user()->id;
        $file = $validated['csv_file'];
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($baseName) ?: 'arsip-soal-csv';
        $extension = strtolower($file->getClientOriginalExtension() ?: 'csv');
        $fileName = $safeBaseName . '-' . now()->format('YmdHis') . '.' . $extension;
        $storedPath = $file->storeAs("banksoal/arsip-soal/csv/{$userId}", $fileName, 'local');

        if (! $storedPath) {
            return back()->with('error', 'Gagal menyimpan file CSV arsip.');
        }

        $metodeUjian = $validated['metode_ujian'] ?? 'online';
        $penarikan = $this->arsipSoalService->savePenarikanSoal($userId, $validated['mk_id'], [
            'nama_ekstraksi' => $validated['nama_arsip'],
            'tipe_ujian' => $validated['tipe_ujian'],
            'metode_ujian' => $metodeUjian,
            'status_cetak' => $metodeUjian === 'offline' ? 'pending' : null,
            'tahun_akademik' => $validated['tahun_akademik'],
            'semester' => $validated['semester'],
            'tanggal_ujian' => $validated['tanggal_ujian'] ?? null,
            'soal_list' => [],
            'status' => 'pending'
        ]);

        return redirect()->route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id)
            ->with('success', 'File CSV/Excel berhasil diunggah. Silakan review data sebelum dimasukkan ke arsip final.');
    }

    private function mapAgendaToTipeUjian(string $agenda): string
    {
        $agenda = strtolower($agenda);

        return match (true) {
            str_contains($agenda, 'uts') => 'uts',
            str_contains($agenda, 'uas') => 'uas',
            str_contains($agenda, 'kuis') => 'kuis',
            str_contains($agenda, 'pratek') || str_contains($agenda, 'praktek') => 'praktek',
            default => 'lainnya',
        };
    }
}