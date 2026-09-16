import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Remove the global flatpickr-calendar font-size override
text = text.replace('.flatpickr-calendar { font-family: inherit; font-size: 11px; }', '.flatpickr-calendar { font-family: inherit; }')

# 2. Fix the DOM insertion error
old_js = """                            // Insert before year input
                            const currentYearElement = monthContainer.querySelector('.numInputWrapper');
                            monthContainer.insertBefore(wrapper, currentYearElement);"""

new_js = """                            // Insert before year input
                            const currentMonthContainer = instance.monthNav.querySelector('.flatpickr-current-month');
                            const currentYearElement = currentMonthContainer.querySelector('.numInputWrapper');
                            currentMonthContainer.insertBefore(wrapper, currentYearElement);"""

text = text.replace(old_js, new_js)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")