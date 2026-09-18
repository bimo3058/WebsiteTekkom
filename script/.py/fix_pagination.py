import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_pagination_pattern = r'\{\{-- Pagination Custom Fungsional --\}\}.*?(?=\n\s*(?:</div>|{{--))'

# We replace the pagination block. But we have to make sure we don't accidentally match too much. Let's trace it down manually.