import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_js = """                            // Toggle dropdown
                            wrapper.onclick = (e) => {
                                e.stopPropagation();
                                const isOpen = wrapper.classList.contains('is-open');
                                // Close all other custom dropdowns
                                document.querySelectorAll('.custom-month-wrapper').forEach(w => w.classList.remove('is-open'));
                                if(!isOpen) wrapper.classList.add('is-open');
                            };"""

new_js = """                            // Toggle dropdown
                            wrapper.onclick = (e) => {
                                e.stopPropagation();
                                const isOpen = wrapper.classList.contains('is-open');
                                // Close all other custom dropdowns
                                document.querySelectorAll('.custom-month-wrapper').forEach(w => w.classList.remove('is-open'));
                                if(!isOpen) {
                                    wrapper.classList.add('is-open');
                                    // Auto scroll to active month
                                    setTimeout(() => {
                                        const activeItem = dropdown.querySelector('.custom-month-item.active');
                                        if(activeItem) {
                                            dropdown.scrollTop = activeItem.offsetTop - (dropdown.clientHeight / 2) + (activeItem.clientHeight / 2);
                                        }
                                    }, 10);
                                }
                            };"""

text = text.replace(old_js, new_js)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")