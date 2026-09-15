import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Update `resetPlot` logic
new_reset_logic = """        DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->update([
                'kelompok' => null,
                'shift'    => null,
            ]);

        $praktikum->update([
            'jumlah_kelompok' => 0,
            'jumlah_shift'    => 0,
        ]);"""

text = re.sub(r'DaftarPraktikan::where\(\'praktikum_id\', \$praktikum->id\)\s*->update\(\[\s*\'kelompok\' => null,\s*\'shift\'    => null,\s*\]\);', new_reset_logic, text)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
print("Updated Controller")
