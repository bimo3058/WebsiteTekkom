import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Change title
text = text.replace('<span class="mp-card-title">Pendaftaran Asisten</span>', 
                    '<span class="mp-card-title">Form Pendaftaran Asisten</span>')

# 2. Add Jadwal options after the Berkas div
jadwal_html = """
                        <div class="mt-5">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-2">Jadwal Praktikum <span class="text-[#808897] font-normal">(Pilihan yang akan tampil)</span></label>
                            <div class="flex flex-col gap-2">
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled checked class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Senin</span>
                                    </label>
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Selasa</span>
                                    </label>
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Rabu</span>
                                    </label>
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Kamis</span>
                                    </label>
                                </div>
                                <div class="flex gap-6 mt-1">
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Jumat</span>
                                    </label>
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Sabtu</span>
                                    </label>
                                    <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                        <input type="radio" disabled class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                        <span class="text-[12px] text-[#666D80]">Minggu</span>
                                    </label>
                                </div>
                            </div>
                        </div>
"""

# Insert just before <div class="mt-5 border border-[#DFE1E7] rounded-[10px] overflow-hidden" x-data="quizBuilder()">
insert_target = '<div class="mt-5 border border-[#DFE1E7] rounded-[10px] overflow-hidden" x-data="quizBuilder()">'
if insert_target in text:
    text = text.replace(insert_target, jadwal_html + '                        ' + insert_target)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Added Jadwal form fields successfully!")
