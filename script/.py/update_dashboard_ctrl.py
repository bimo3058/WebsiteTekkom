import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace("$query->paginate(20)->withQueryString()", "$query->paginate($request->input('per_page', 10))->withQueryString()")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
print("Updated DashboardController.")
