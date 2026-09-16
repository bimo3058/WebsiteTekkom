import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Add CSS for overflow visible and font scaling
css_rules = """
                /* Fix dropdown clipping and font size */
                .flatpickr-months, .flatpickr-month, .flatpickr-current-month {
                    overflow: visible !important;
                }
                .flatpickr-current-month {
                    font-size: 110% !important; /* Decrease by ~2px from default 135% */
                }
                .custom-month-wrapper {
                    font-size: inherit;
                }
                .flatpickr-current-month input.cur-year {
                    font-size: inherit !important;
                    font-weight: 500 !important;
                }
"""

text = text.replace('/* Custom Month Dropdown Styles */', css_rules + '\n                /* Custom Month Dropdown Styles */')

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")