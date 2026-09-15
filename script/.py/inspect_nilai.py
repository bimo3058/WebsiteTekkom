with open(r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php", "r", encoding="utf-8") as f:
    text = f.read()
import re
print("Page Actions Export CSV found at:", text.find('<div class="mp-page-actions"'))
print("Global Filter found at:", text.find('<div x-data="{ globalSearch: \'\', globalKelompok: \'\', globalShift: \'\' }">'))
