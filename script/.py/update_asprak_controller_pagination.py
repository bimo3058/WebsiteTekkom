import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\PendaftaranAsprakController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

old_paginate = "$pendaftaran = $query->paginate(15)->withQueryString();"
new_paginate = "$perPage = request('per_page', 10);\n        $pendaftaran = $query->paginate($perPage)->withQueryString();"

if old_paginate in text:
    text = text.replace(old_paginate, new_paginate)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated PendaftaranAsprakController to use per_page.")
