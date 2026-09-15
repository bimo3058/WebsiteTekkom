with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Reduce gap between Koordinator Aktif section and Form Pendaftaran by 5px (24 -> 19)
text = text.replace(
    '<div style="margin-bottom: 24px; display: flex; flex-direction: row;',
    '<div style="margin-bottom: 19px; display: flex; flex-direction: row;'
)

# 2. Shrink "September 2026" font: cur-month and cur-year from 14px -> 12px
text = text.replace(
    '.flatpickr-current-month {\n          font-size: 14px !important;',
    '.flatpickr-current-month {\n          font-size: 12px !important;'
)
text = text.replace(
    '.flatpickr-current-month .cur-month {\n          font-size: 14px !important;',
    '.flatpickr-current-month .cur-month {\n          font-size: 12px !important;'
)
text = text.replace(
    '.flatpickr-current-month input.cur-year {\n          font-size: 14px !important;',
    '.flatpickr-current-month input.cur-year {\n          font-size: 12px !important;'
)

# 3. Add CSS for the month dropdown arrow animation and custom chevron
arrow_css = """
    /* Month dropdown arrow animation */
    .flatpickr-months .flatpickr-month {
        position: relative;
    }
    .custom-month-chevron {
        display: inline-block;
        margin-left: 4px;
        transition: transform 0.25s ease;
        vertical-align: middle;
        font-size: 10px;
        color: #666D80;
    }
    .custom-month-chevron.open {
        transform: rotate(180deg);
    }
    .custom-month-dropdown {
        animation: fadeInDown 0.15s ease forwards;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
"""

text = text.replace(
    '.custom-month-dropdown {',
    arrow_css + '\n    .custom-month-dropdown {'
)

# 4. Update JS to add chevron SVG next to month name and animate it on dropdown open/close
# Find the onReady function block and add the chevron logic
old_on_ready_end = """                  const monthDropdown = document.createElement('div');
                  monthDropdown.className = 'custom-month-dropdown';"""
new_on_ready_end = """                  // Add animated chevron to month name
                  const curMonthEl = instance.calendarContainer.querySelector('.cur-month');
                  if (curMonthEl && !curMonthEl.querySelector('.custom-month-chevron')) {
                      const chevron = document.createElement('span');
                      chevron.className = 'custom-month-chevron';
                      chevron.innerHTML = '&#9660;';
                      curMonthEl.appendChild(chevron);
                  }

                  const monthDropdown = document.createElement('div');
                  monthDropdown.className = 'custom-month-dropdown';"""
text = text.replace(old_on_ready_end, new_on_ready_end)

# 5. Find where dropdown show/hide is triggered and add chevron toggle animation
# Look for "dropdown.style.display = 'none'" pattern and "dropdown.style.display = 'block'"
text = text.replace(
    "monthDropdown.style.display = 'none'",
    "monthDropdown.style.display = 'none'; const ch1 = instance.calendarContainer.querySelector('.custom-month-chevron'); if(ch1) ch1.classList.remove('open');"
)
text = text.replace(
    'monthDropdown.style.display = \'block\';',
    "monthDropdown.style.display = 'block'; const ch2 = instance.calendarContainer.querySelector('.custom-month-chevron'); if(ch2) ch2.classList.add('open');"
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("calendar fixed")