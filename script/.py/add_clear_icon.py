with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re

target = r'(@focus="showDropdown = true" @click\.away="showDropdown = false">)\s*\{\{-- Dropdown --\}\}'

repl = r'''\1

                {{-- X Delete Icon --}}
                <div x-show="selectedUser" style="display: none; position: absolute; right: 10px; top: 12px; cursor: pointer;" 
                     @click="selectedUser = null; query = '';">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-red-500 hover:text-red-600 transition-colors" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>

                {{-- Dropdown --}}'''

text = re.sub(target, repl, text)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done adding clear icon")