import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<template x-teleport="body">.*?</template>'
new_modal = """<template x-teleport="body">
                                        <div x-show="showDetail" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300" style="display: none;" x-cloak>
                                            <div @click.away="showDetail = false" x-show="showDetail"
                                                 x-transition:enter="transition ease-out duration-300" 
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="transition ease-in duration-200" 
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]">
                                                
                                                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                                                    <div class="font-bold text-[16px] text-[#0D0D12]">Edit Modul</div>
                                                </div>
                                                
                                                <div class="overflow-y-auto flex-1">
                                                    <form method="POST" action="{{ route('eoffice.manprak.koor.modul.update', $m->id) }}" class="p-6">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <div class="flex flex-col gap-4">
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul <span class="text-red-500">*</span></label>
                                                                <input type="text" name="nama" value="{{ $m->nama }}" class="mp-input w-full" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan <span class="text-red-500">*</span></label>
                                                                <input type="number" name="urutan" value="{{ $m->urutan }}" class="mp-input w-full" required>
                                                            </div>

                                                            <!-- Materi Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Materi Modul</label>
                                                                @if($m->materi->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->materi as $mat)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $mat->judul }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada materi." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>

                                                            <!-- Tugas Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tugas Modul</label>
                                                                @if($m->tugas->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->tugas as $tug)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $tug->judul }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada tugas." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>

                                                            <!-- Asisten Praktikum Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Asisten Praktikum Modul</label>
                                                                @if(($m->modulAsprak ?? collect())->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->modulAsprak as $ma)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $ma->asprak->user->name }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada asisten." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                                                            <button type="button" @click="showDetail = false" class="mp-btn secondary md px-5" style="border-radius: 8px;">Batal</button>
                                                            <button type="submit" class="mp-btn primary md px-5" style="border-radius: 8px; background-color: #0B266E; border-color: #0B266E; color: white;">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>"""

text = re.sub(pattern, new_modal, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated Detail popup strictly to referential admin modal style!")
