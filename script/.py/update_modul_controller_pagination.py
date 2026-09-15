import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\ModulController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# I will update 'get()' to 'paginate($perPage)->withQueryString()' but also let's define $perPage
old_lines = """    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();"""

new_lines = """    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();
        $perPage = request('per_page', 10);

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->paginate($perPage)->withQueryString()
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);"""

if old_lines in text:
    text = text.replace(old_lines, new_lines)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated ModulController to use Pagination.")
