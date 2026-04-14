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
            ->paginate(5, ['*'], 'soal_page');
        
        // Map untuk menyesuaikan dengan field dummy di view.
        $soals->through(function($soal) {
            $soal->kode_soal = 'Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT);
            $soal->nama_topik = $soal->cpl ? $soal->cpl->kode : '-';
            return $soal;
        });

        // 3. Ambil Ekstraksi (Packages)
        $packages = \Modules\BankSoal\Models\MataKuliah::with(['cpl', 'pertanyaan.cpl', 'pertanyaan.cpmk'])
            ->whereIn('id', $mkIds)
            ->has('pertanyaan')
            ->when($request->searchPackages, function($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")->orWhere('kode', 'like', "%{$search}%");
            })
            ->withCount('pertanyaan as jumlah_soal')
            ->paginate(5, ['*'], 'pkg_page');
        
        $packages->through(function($pkg) {
            $mkCpls = $pkg->cpl->pluck('kode')->filter();
            $soalCpls = $pkg->pertanyaan->pluck('cpl.kode')->filter();
            $allCpls = $mkCpls->merge($soalCpls)->unique()->sort()->values();
            
            $pkg->str_cpls = $allCpls->isNotEmpty() ? $allCpls->implode(', ') : '-';
            
            $soalCpmks = $pkg->pertanyaan->pluck('cpmk.kode')->filter();
            $allCpmks = $soalCpmks->unique()->sort()->values();
            
            $pkg->str_cpmks = $allCpmks->isNotEmpty() ? $allCpmks->implode(', ') : '-';
            return $pkg;
        });

        // 4. Pastikan mataKuliahDosen meload CPL dan CPMK yang berkaitan dengan MK maupun Soalnya
        $mataKuliahDosen->load(['cpl', 'pertanyaan.cpl', 'pertanyaan.cpmk']);
        
        $mataKuliahDosen->transform(function ($mk) {
            $mkCpls = collect($mk->cpl);
            $soalCpls = $mk->pertanyaan->pluck('cpl')->filter();
            $mk->all_cpls = $mkCpls->merge($soalCpls)->unique('id')->sortBy('kode')->values();
            
            $soalCpmks = $mk->pertanyaan->pluck('cpmk')->filter();
            $mk->all_cpmks = $soalCpmks->unique('id')->sortBy('kode')->values();
            
            return $mk;
        });
        
        // Pass ke view (untuk digunakan pada Tarik Soal modal)
        return view('banksoal::pages.bank-soal.Dosen.index', compact('soals', 'packages', 'mataKuliahDosen'));
    }

    public function ekstrak(Request $request)
    {
        $request->validate([
            'mk_id' => 'required',
            'jenis_soal' => 'nullable|array',
            'cpl_id' => 'nullable',
            'cpmk_id' => 'nullable',
            'bobot_total' => 'nullable|numeric'
        ]);

        $query = \Modules\BankSoal\Models\Pertanyaan::with(['mataKuliah', 'cpl', 'jawaban'])
            ->where('mk_id', $request->mk_id);

        // TODO: Filter jenis soal dinonaktifkan sementara karena kolom 'tipe_soal' 
        // belum tersedia di skema tabel bs_pertanyaan saat ini.
        // if ($request->filled('jenis_soal')) {
        //     $query->whereIn('tipe_soal', $request->jenis_soal);
        // }

        if ($request->filled('cpl_id')) {
            $query->where('cpl_id', $request->cpl_id);
        }

        $soals = $query->inRandomOrder()->get();

        if($soals->isEmpty()){
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada soal yang sesuai dengan kriteria ekstraksi.']);
            }
            return back()->with('error', 'Tidak ada soal yang sesuai dengan kriteria ekstraksi.');
        }

        $mataKuliah = \Modules\BankSoal\Models\MataKuliah::find($request->mk_id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mataKuliah' => $mataKuliah,
                'soals' => $soals->map(function ($soal) {
                    // Sertakan cpmk_id jika ada relasi CPMK
                    return [
                        'id' => $soal->id,
                        'soal' => strip_tags(\Illuminate\Support\Str::limit($soal->soal, 200)),
                        'cpl' => $soal->cpl ? $soal->cpl->kode : null,
                        'cpmk' => $soal->cpmk ? $soal->cpmk->kode : null,
                    ];
                }),
                'request' => $request->all()
            ]);
        }

        return view('banksoal::pages.bank-soal.Dosen.ekstrak-result', compact('soals', 'mataKuliah', 'request'));
    }

    public function cetakUjian(Request $request)
    {
        $request->validate([
            'soal_ids' => 'required|array',
            'mk_id' => 'required',
            'agenda' => 'nullable',
            'tahun_ajaran' => 'nullable',
            'semester' => 'nullable'
        ]);

        $soals = \Modules\BankSoal\Models\Pertanyaan::with(['cpl', 'cpmk', 'jawaban'])
            ->whereIn('id', $request->soal_ids)
            // Memastikan urutan tetap sesuai yang dikirim request
            ->orderByRaw('ARRAY_POSITION(ARRAY[' . implode(',', $request->soal_ids) . ']::integer[], id)')
            ->get();

        $mataKuliah = \Modules\BankSoal\Models\MataKuliah::find($request->mk_id);

        return view('banksoal::pages.bank-soal.Dosen.print-ujian', compact('soals', 'mataKuliah', 'request'));
    }

    public function getAvailableSoals($mk_id)
    {
        $soals = \Modules\BankSoal\Models\Pertanyaan::with(['cpl', 'cpmk'])
            ->where('mk_id', $mk_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'soals' => $soals->map(function ($soal) {
                return [
                    'id' => $soal->id,
                    'soal' => strip_tags(\Illuminate\Support\Str::limit($soal->soal, 150)),
                    'cpl' => $soal->cpl ? $soal->cpl->kode : null,
                    'cpmk' => $soal->cpmk ? $soal->cpmk->kode : null,
                ];
            })
        ]);
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