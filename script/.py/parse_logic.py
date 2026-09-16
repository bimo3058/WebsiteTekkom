import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Remove the button "Kelola Kelompok & Shift" from the Search Form
button_pattern = r'\{\{-- Updated Kelola Kelompok Icon --\}\}.*?Kelola Kelompok &amp; Shift\s*</button>'
text = re.sub(button_pattern, '', text, flags=re.DOTALL)

# 2. Extract the Main Modal Content and the Edit Modal Content
# We need to find where the main modal starts and ends.
# The main modal starts at `<div x-show="showMainModal"` 

main_modal_pattern = r'(<div x-show="showMainModal".*?\{\{-- Modal Header --\}\}.*?</div>\s*</div>)(.*?)(<!-- Modul Anak \(Edit Kelompok\) -->)'
match = re.search(r'(<div x-show="showMainModal".*?)\s*(<div x-show="showEditModal")', text, flags=re.DOTALL)

if match:
    # We shouldn't use a simple regex for nested divs. Let's just find things by known anchors.
    pass

