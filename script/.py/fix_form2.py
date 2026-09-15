import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Margin top 4px
text = text.replace('style="margin-top: 9px; flex-shrink: 0;"', 'style="margin-top: 4px; flex-shrink: 0;"')

# 2. Extract and Replace Flatpickr Section completely
pattern = r'<!-- Flatpickr Setup -->[\s\S]*?{{-- ORIGINAL SECTION: Filter Pendaftaran --}}'

flatpickr_setup = """<!-- Flatpickr Setup -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <style>
                .flatpickr-calendar { font-family: inherit; font-size: 11px; }
                .flatpickr-day.selected { background: #0B266E; border-color: #0B266E; }
                .flatpickr-time .flatpickr-hour { font-weight: normal !important; }
                
                /* Hide native month select */
                .flatpickr-monthDropdown-months { display: none !important; }

                /* Custom Month Dropdown Styles */
                .custom-month-wrapper {
                    position: relative;
                    display: inline-flex;
                    align-items: center;
                    cursor: pointer;
                    font-weight: 600;
                    color: inherit;
                    padding: 0 4px;
                }
                .custom-month-text { margin-right: 4px; }
                .custom-month-chevron {
                    transition: transform 0.3s ease;
                    width: 12px; height: 12px;
                }
                .custom-month-wrapper.is-open .custom-month-chevron {
                    transform: rotate(180deg);
                }
                
                .custom-month-dropdown {
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    width: 120px;
                    max-height: 200px;
                    overflow-y: auto;
                    z-index: 1000;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.2s, visibility 0.2s;
                    padding: 8px;
                    display: flex;
                    flex-direction: column;
                    gap: 2px;
                }
                .custom-month-wrapper.is-open .custom-month-dropdown {
                    opacity: 1;
                    visibility: visible;
                }
                
                .custom-month-item {
                    padding: 8px;
                    border-radius: 6px;
                    text-align: center;
                    transition: all 0.2s;
                    color: #0B266E;
                }
                .custom-month-item:hover { background: #F3F4F6; }
                .custom-month-item.active { background: #0B266E; color: white; font-weight: 600; }
                
                /* Custom Scrollbar for dropdown */
                .custom-month-dropdown::-webkit-scrollbar { width: 4px; }
                .custom-month-dropdown::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    
                    flatpickr(".date-picker", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                        onReady: function(selectedDates, dateStr, instance) {
                            // Find month container
                            const monthContainer = instance.monthNav.querySelector('.flatpickr-month');
                            const nativeSelect = monthContainer.querySelector('.flatpickr-monthDropdown-months');
                            
                            // Build custom wrapper
                            const wrapper = document.createElement('div');
                            wrapper.className = 'custom-month-wrapper';
                            
                            const textSpan = document.createElement('span');
                            textSpan.className = 'custom-month-text';
                            textSpan.innerText = months[instance.currentMonth];
                            
                            const chevron = document.createElement('div');
                            chevron.className = 'custom-month-chevron';
                            chevron.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;
                            
                            wrapper.appendChild(textSpan);
                            wrapper.appendChild(chevron);
                            
                            // Build dropdown list
                            const dropdown = document.createElement('div');
                            dropdown.className = 'custom-month-dropdown';
                            
                            months.forEach((m, idx) => {
                                const item = document.createElement('div');
                                item.className = 'custom-month-item' + (idx === instance.currentMonth ? ' active' : '');
                                item.innerText = m;
                                item.onclick = (e) => {
                                    e.stopPropagation();
                                    instance.changeMonth(idx);
                                    // Update active class
                                    dropdown.querySelectorAll('.custom-month-item').forEach(el => el.classList.remove('active'));
                                    item.classList.add('active');
                                    wrapper.classList.remove('is-open');
                                };
                                dropdown.appendChild(item);
                            });
                            
                            wrapper.appendChild(dropdown);
                            
                            // Toggle dropdown
                            wrapper.onclick = (e) => {
                                e.stopPropagation();
                                const isOpen = wrapper.classList.contains('is-open');
                                // Close all other custom dropdowns
                                document.querySelectorAll('.custom-month-wrapper').forEach(w => w.classList.remove('is-open'));
                                if(!isOpen) wrapper.classList.add('is-open');
                            };
                            
                            // Close when clicking outside
                            document.addEventListener('click', () => {
                                wrapper.classList.remove('is-open');
                            });
                            
                            // Insert before year input
                            const currentYearElement = monthContainer.querySelector('.numInputWrapper');
                            monthContainer.insertBefore(wrapper, currentYearElement);
                        },
                        onMonthChange: function(selectedDates, dateStr, instance) {
                            const wrapper = instance.monthNav.querySelector('.custom-month-wrapper');
                            if(wrapper) {
                                wrapper.querySelector('.custom-month-text').innerText = months[instance.currentMonth];
                                const items = wrapper.querySelectorAll('.custom-month-item');
                                items.forEach(el => el.classList.remove('active'));
                                if(items[instance.currentMonth]) items[instance.currentMonth].classList.add('active');
                            }
                        }
                    });
                });
            </script>
            {{-- ORIGINAL SECTION: Filter Pendaftaran --}}"""

text = re.sub(pattern, flatpickr_setup, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")