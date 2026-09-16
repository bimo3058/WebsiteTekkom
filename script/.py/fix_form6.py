import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Z-index fix for parent containers
old_css_z = """                /* Fix dropdown clipping and font size */
                .flatpickr-months, .flatpickr-month, .flatpickr-current-month {
                    overflow: visible !important;
                }"""
new_css_z = """                /* Fix dropdown clipping and font size */
                .flatpickr-months, .flatpickr-month, .flatpickr-current-month {
                    overflow: visible !important;
                    z-index: 999999 !important;
                }"""
text = text.replace(old_css_z, new_css_z)

# 2. Add wheel event listener to stop propagation
old_js = """                            wrapper.appendChild(dropdown);
                            
                            // Toggle dropdown"""
new_js = """                            wrapper.appendChild(dropdown);
                            
                            // Stop flatpickr from hijacking scroll
                            dropdown.addEventListener('wheel', (e) => {
                                e.stopPropagation();
                            });
                            
                            // Toggle dropdown"""
text = text.replace(old_js, new_js)

# 3. Margin top decrement by 5px (4px -> -1px)
text = text.replace('style="margin-top: 4px; flex-shrink: 0;"', 'style="margin-top: -1px; flex-shrink: 0;"')

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")