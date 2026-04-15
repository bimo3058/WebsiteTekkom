const fs = require('fs');
const file = 'Modules/BankSoal/app/Http/Controllers/BS/BankSoalController.php';
let content = fs.readFileSync(file, 'utf8');
const methodStr = `    public function ekstrak(Request $request)
    {
        $request->validate([
            'mk_id' => 'required',
            'jenis_soal' => 'nullable|array',
            'cpl_id' => 'nullable',
            'cpmk_id' => 'nullable',
            'bobot_total' => 'nullable|numeric'
        ]);

        $query = \\Modules\\BankSoal\\Models\\Pertanyaan::with(['mataKuliah', 'cpl', 'jawaban'])
            ->where('mk_id', $request->mk_id);

        if ($request->filled('jenis_soal')) {
            $query->whereIn('tipe_soal', $request->jenis_soal);
        }

        if ($request->filled('cpl_id')) {
            $query->where('cpl_id', $request->cpl_id);
        }

        // Jika cpmk_id digunakan, bisa join/filter sesuai relasi (asumsi nullable, kita lewati jika tdk relevan)

        $soals = $query->inRandomOrder()->get();

        if($soals->isEmpty()){
            return back()->with('error', 'Tidak ada soal yang sesuai dengan kriteria ekstraksi.');
        }

        $mataKuliah = \\Modules\\BankSoal\\Models\\MataKuliah::find($request->mk_id);
        return view('banksoal::pages.bank-soal.Dosen.ekstrak-result', compact('soals', 'mataKuliah', 'request'));
    }`;

// Remove the badly injected one
content = content.replace(methodStr, '');
// Re-inject before adminDashboard properly
content = content.replace('    public function adminDashboard()', methodStr + '\n\n    public function adminDashboard()');
fs.writeFileSync(file, content);
