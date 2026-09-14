import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\ModulController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Make sure AsistenPraktikum and ModulAsprak are imported
imports_to_add = """use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\ModulAsprak;"""

if 'use Modules\EOffice\Models\AsistenPraktikum;' not in text:
    text = text.replace('use Modules\EOffice\Models\AsprakPraktikum;', imports_to_add)

old_index = """    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.modul', compact('praktikum', 'moduls'));
    }"""

new_index = """    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        $asistenList = $praktikum
            ? AsistenPraktikum::where('praktikum_id', $praktikum->id)
                ->where('role', 'asprak')
                ->with(['user', 'modulAsprak.modul'])
                ->get()
            : collect();

        $distribusiList = $praktikum
            ? ModulAsprak::whereHas('modul', fn ($q) => $q->where('praktikum_id', $praktikum->id))
                ->with(['modul', 'asprak.user'])
                ->get()
            : collect();

        // Also define $modulList for the template
        $modulList = $moduls;

        return view('eoffice::manajemen-praktikum.koordinator.modul', compact('praktikum', 'moduls', 'modulList', 'asistenList', 'distribusiList'));
    }"""

text = text.replace(old_index, new_index)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated ModulController to pass asisten/distribusi.")
