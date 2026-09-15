import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# We need to replace the Form Assign section.
# First, let's find the current Form Assign HTML.
current_form_match = re.search(r'<form method="POST" action="\{\{ route\(\'eoffice\.manprak\.koor\.bagi-modul\.store\'\) \}\}".*?</form>', text, re.DOTALL)

new_form_html = """<form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}" 
                      class="flex flex-col gap-3" 
                      x-data="{ asprak_id: '', modul_id: '' }"
                      @change-asprak.window="asprak_id = $event.detail"
                      @change-modul.window="modul_id = $event.detail">
                    @csrf
                    
                    @php
                        $asistenOptions = [];
                        foreach($asistenList ?? [] as $a) {
                            $asistenOptions[] = ['value' => (string)$a->id, 'label' => $a->user?->name];
                        }
                        
                        $modulOpt = [];
                        if (isset($modulList)) {
                            foreach($modulList as $m) {
                                $modulOpt[] = ['value' => (string)$m->id, 'label' => $m->nama];
                            }
                        }
                    @endphp

                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <x-eoffice::manajemen-praktikum.ui.select 
                            name="asprak_id" 
                            :options="$asistenOptions"
                            placeholder="Pilih Asisten Praktikum"
                            onChange="$dispatch('change-asprak', option.value)" />
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <x-eoffice::manajemen-praktikum.ui.select 
                            name="modul_id" 
                            :options="$modulOpt"
                            placeholder="Pilih Modul Praktikum"
                            onChange="$dispatch('change-modul', option.value)" />
                    </div>
                    
                    <button 
                        type="submit" 
                        class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-2"
                        style="height: 40px; font-size: 13px;"
                        :class="(asprak_id && modul_id) ? 'bg-[#0B266E] text-white hover:bg-[#081e59]' : 'bg-[#F4F6F9] text-[#0B266E] pointer-events-none'"
                        :disabled="!(asprak_id && modul_id)">
                        Simpan
                    </button>
                </form>"""

if current_form_match:
    text = text.replace(current_form_match.group(0), new_form_html)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated Form Assign to use custom UI select component.")
