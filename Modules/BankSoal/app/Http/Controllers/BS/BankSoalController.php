<?php

namespace Modules\BankSoal\Http\Controllers\BS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Services\MataKuliahService;
use Modules\BankSoal\Services\PertanyaanService;
use Modules\BankSoal\Services\KompreService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BankSoalController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected MataKuliahService $mataKuliahService,
        protected PertanyaanService $pertanyaanService,
        protected KompreService $kompreService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('banksoal.view');
        
        $user = auth()->user();
        
        // 1. Ambil ID MK yang diampu dosen
        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);
        $mkIds = $mataKuliahDosen->pluck('id')->toArray();

        // 2. Ambil Soal
        $soals = \Modules\BankSoal\Models\Pertanyaan::with(['mataKuliah', 'cpl'])
            ->whereIn('mk_id', $mkIds)
            ->when($request->searchSoal, function($q, $search) {
                return $q->where('soal', 'like', "%{$search}%");
            })
            ->when($request->mk_id, function($q, $mk_id) {
                return $q->where('bs_pertanyaan.mk_id', $mk_id);
            })
            ->when($request->kesulitan, function($q, $kesulitan) {
                return $q->where('bs_pertanyaan.kesulitan', $kesulitan);
            })
            // Tambahkan orderBy mataKuliah.nama agar soal dalam satu MK terkumpul
            ->join('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            ->select('bs_pertanyaan.*')
            ->orderBy('bs_mata_kuliah.nama')
            ->orderByDesc('bs_pertanyaan.created_at')
            ->paginate(10, ['*'], 'soal_page');
        
        // Map untuk menyesuaikan dengan field dummy di view.
        $soals->through(function($soal) {
            $soal->kode_soal = 'Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT);
            $soal->nama_topik = $soal->cpl ? $soal->cpl->kode : '-';
            return $soal;
        });

        // 3. Ambil Ekstraksi (Packages)
        $packages = \Modules\BankSoal\Models\MataKuliah::with('cpl')
            ->whereIn('id', $mkIds)
            ->when($request->searchPackages, function($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")->orWhere('kode', 'like', "%{$search}%");
            })
            ->withCount('pertanyaan as jumlah_soal')
            ->paginate(10, ['*'], 'pkg_page');
        
        $packages->through(function($pkg) {
            $pkg->str_cpls = $pkg->cpl->pluck('kode')->implode(', ') ?: '-';
            // Misal: asumsikan CPMK belum ada di model MK, kita beri string kosong atau dummy
            $pkg->str_cpmks = '-';
            return $pkg;
        });

        return view('banksoal::pages.bank-soal.Dosen.index', compact('soals', 'packages', 'mataKuliahDosen'));
    }

    /* |--------------------------------------------------------------------------
    | Dashboard Methods
    |
    | FIX: Tambahkan $this->authorize('banksoal.view') di SETIAP dashboard method.
    | Sebelumnya method-method ini TIDAK ada pengecekan permission,
    | sehingga siapa saja yang punya role bisa langsung akses.
    |-------------------------------------------------------------------------- */

    public function adminDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $mataKuliah = $this->mataKuliahService->listAll();
        return view('banksoal::dashboard.admin', compact('mataKuliah'));
    }

    public function gpmDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $mataKuliah = $this->mataKuliahService->listAll();
        $statsSoal = $mataKuliah->mapWithKeys(function ($mk) {
            return [$mk->id => $this->pertanyaanService->list(['mk_id' => $mk->id])->total()];
        });
        return view('banksoal::dashboard.gpm', compact('mataKuliah', 'statsSoal'));
    }

    public function dosenDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $user = auth()->user();
        $mataKuliah = $this->mataKuliahService->getMkByDosen($user->id);
        return view('banksoal::dashboard.dosen', compact('mataKuliah'));
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('banksoal.view'); // FIX: tambahkan
        $user = auth()->user();
        $history = $this->kompreService->getRiwayat($user->id);
        $activeSession = $history->where('status', 'ongoing')->first();
        return view('banksoal::dashboard.mahasiswa', compact('activeSession', 'history'));
    }

    /* |--------------------------------------------------------------------------
    | CRUD Methods — sudah benar, tidak perlu diubah
    |-------------------------------------------------------------------------- */

    public function create()
    {
        $this->authorize('banksoal.edit');
        
        $user = auth()->user();
        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);

        return view('banksoal::pages.bank-soal.Dosen.create', compact('mataKuliahDosen'));
    }

    public function store(Request $request)
    {
        $this->authorize('banksoal.edit');
        
        $request->validate([
            'mk_id' => 'required|exists:bs_mata_kuliah,id',
            'cpl_id' => 'required|exists:bs_cpl,id',
            'soal' => 'required|string',
            'kesulitan' => 'required|in:easy,intermediate,advanced',
            'jawaban' => 'required|array|min:2',
            'jawaban.*.teks' => 'required|string',
            'jawaban_benar' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $bobot = match($request->kesulitan) {
                'easy' => 1,
                'intermediate' => 2,
                'advanced' => 3,
                default => 1,
            };

            $dataSoal = [
                'mk_id' => $request->mk_id,
                'cpl_id' => $request->cpl_id,
                'soal' => $request->soal,
                'kesulitan' => $request->kesulitan,
                'bobot' => $bobot,
                // gambar? bisa ditambah kalau ada
            ];

            // Mapping form jawaban ke expected params: [{ opsi, deskripsi, is_benar }, ...]
            $jawabanData = [];
            $abjad = range('A', 'Z');
            foreach ($request->jawaban as $idx => $jawab) {
                $jawabanData[] = [
                    'opsi' => $abjad[$idx] ?? 'A',
                    'deskripsi' => $jawab['teks'],
                    'is_benar' => ((string)$request->jawaban_benar === (string)$idx) ? true : false,
                ];
            }

            $this->pertanyaanService->create($dataSoal, $jawabanData);

            DB::commit();
            return redirect()->route('banksoal.soal.dosen.index')->with('success', 'Pertanyaan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $this->authorize('banksoal.view');
        $soal = $this->pertanyaanService->findById($id);
        return view('banksoal::pages.bank-soal.Dosen.show', compact('soal'));
    }

    public function edit($id)
    {
        $this->authorize('banksoal.edit');
        
        $user = auth()->user();
        $mataKuliahDosen = $this->mataKuliahService->getMkByDosen($user->id);
        $soal = $this->pertanyaanService->findById($id);

        return view('banksoal::pages.bank-soal.Dosen.edit', compact('soal', 'mataKuliahDosen'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('banksoal.edit');

        $request->validate([
            'mk_id' => 'required|exists:bs_mata_kuliah,id',
            'cpl_id' => 'required|exists:bs_cpl,id',
            'soal' => 'required|string',
            'kesulitan' => 'required|in:easy,intermediate,advanced',
            'jawaban' => 'required|array|min:2',
            'jawaban.*.teks' => 'required|string',
            'jawaban_benar' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $bobot = match($request->kesulitan) {
                'easy' => 1,
                'intermediate' => 2,
                'advanced' => 3,
                default => 1,
            };

            $dataSoal = [
                'mk_id' => $request->mk_id,
                'cpl_id' => $request->cpl_id,
                'soal' => $request->soal,
                'kesulitan' => $request->kesulitan,
                'bobot' => $bobot,
            ];

            $jawabanData = [];
            $abjad = range('A', 'Z');
            foreach ($request->jawaban as $idx => $jawab) {
                $jawabanData[] = [
                    'opsi' => $abjad[$idx] ?? 'A',
                    'deskripsi' => $jawab['teks'],
                    'is_benar' => ((string)$request->jawaban_benar === (string)$idx) ? true : false,
                ];
            }

            $this->pertanyaanService->update($id, $dataSoal, $jawabanData);

            DB::commit();
            return redirect()->route('banksoal.soal.dosen.index')->with('success', 'Pertanyaan berhasil diubah/diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $this->authorize('banksoal.delete');
        DB::beginTransaction();
        try {
            $this->pertanyaanService->delete($id);
            DB::commit();
            return back()->with('success', 'Soal berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }
}