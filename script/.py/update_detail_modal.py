import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace a tag with button and alpine wrapper
old_btn = """<a href="{{ route('eoffice.manprak.koor.modul.show', $m->id) }}" class="mp-btn primary sm" style="text-decoration:none; font-size:11px; padding-top:6px; padding-bottom:6px;">Detail</a>"""
new_btn = """<div x-data="{ showDetail: false }" class="inline-block">
                                    <button type="button" @click="showDetail = true" class="mp-btn primary sm" style="text-decoration:none; font-size:11px; padding-top:6px; padding-bottom:6px;">Detail</button>

                                    <template x-teleport="body">
                                        <div x-show="showDetail" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50" style="display: none;" x-transition.opacity>
                                            <div @click.away="showDetail = false" class="bg-white rounded-[12px] shadow-xl w-full max-w-2xl flex flex-col max-h-[90vh]">
                                                <!-- Header -->
                                                <div class="px-5 py-4 border-b border-[#DFE1E7] flex justify-between items-center">
                                                    <h3 class="text-[16px] font-bold text-[#0D0D12]">Detail Modul: {{ $m->nama }}</h3>
                                                    <button @click="showDetail = false" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-gray-100">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                
                                                <!-- Body -->
                                                <div class="p-5 overflow-y-auto flex flex-col gap-6 bg-[#FAFAFA]" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                                    
                                                    <!-- Edit form -->
                                                    <div class="bg-white p-4 rounded-[10px] border border-[#DFE1E7] shadow-sm">
                                                        <h4 class="text-[13px] font-bold text-[#0B266E] mb-3 border-b border-gray-100 pb-2">✏️ Edit Modul</h4>
                                                        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.update', $m->id) }}" class="flex flex-col gap-3">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-[#353849] mb-1">Nama Modul <span class="text-red-500">*</span></label>
                                                                    <input type="text" name="nama" value="{{ $m->nama }}" class="mp-input w-full text-[12px]" required>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[11px] font-bold text-[#353849] mb-1">Urutan <span class="text-red-500">*</span></label>
                                                                    <input type="number" name="urutan" value="{{ $m->urutan }}" class="mp-input w-full text-[12px]" required>
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-end mt-1">
                                                                <button type="submit" class="bg-[#0B266E] text-white font-medium text-[12px] px-4 py-2 rounded-[6px] hover:bg-[#081e59] transition-colors">
                                                                    Simpan Perubahan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-5">
                                                        <!-- Materi Modul -->
                                                        <div>
                                                            <h4 class="text-[12px] font-bold text-[#0D0D12] mb-2 flex justify-between items-center">
                                                                <span>📚 Materi Modul</span>
                                                                <span class="bg-[#F0F4FF] text-[#0B266E] px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $m->materi->count() }}</span>
                                                            </h4>
                                                            @if($m->materi->count() > 0)
                                                                <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm">
                                                                    @foreach($m->materi as $mat)
                                                                        <div class="px-3 py-2 border-b border-[#DFE1E7] last:border-0 text-[11px] font-medium text-[#353849] truncate hover:bg-gray-50 transition-colors">
                                                                            {{ $mat->judul }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="text-[11px] text-gray-500 italic p-3 bg-white rounded-[8px] border border-dashed border-[#DFE1E7] text-center">Belum ada materi.</div>
                                                            @endif
                                                        </div>

                                                        <!-- Tugas Modul -->
                                                        <div>
                                                            <h4 class="text-[12px] font-bold text-[#0D0D12] mb-2 flex justify-between items-center">
                                                                <span>📝 Tugas Modul</span>
                                                                <span class="bg-[#FFF0F4] text-[#DF1C41] px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $m->tugas->count() }}</span>
                                                            </h4>
                                                            @if($m->tugas->count() > 0)
                                                                <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm">
                                                                    @foreach($m->tugas as $tug)
                                                                        <div class="px-3 py-2 border-b border-[#DFE1E7] last:border-0 text-[11px] font-medium text-[#353849] truncate hover:bg-gray-50 transition-colors">
                                                                            {{ $tug->judul }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="text-[11px] text-gray-500 italic p-3 bg-white rounded-[8px] border border-dashed border-[#DFE1E7] text-center">Belum ada tugas.</div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Asisten Modul -->
                                                    <div class="mb-4">
                                                        <h4 class="text-[12px] font-bold text-[#0D0D12] mb-2 flex justify-between items-center">
                                                            <span>🧑‍🏫 Asisten Penanggung Jawab</span>
                                                            <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ ($m->modulAsprak ?? collect())->count() }}</span>
                                                        </h4>
                                                        @if(($m->modulAsprak ?? collect())->count() > 0)
                                                            <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm">
                                                                @forelse($m->modulAsprak as $ma)
                                                                    <div class="px-4 py-2 border-b border-[#DFE1E7] last:border-0 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                                                                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-[11px] font-bold flex-shrink-0">
                                                                            {{ strtoupper(substr($ma->asprak->user->name ?? 'A', 0, 1)) }}
                                                                        </div>
                                                                        <div class="min-w-0 flex-1">
                                                                            <div class="text-[12px] font-bold text-[#0D0D12] truncate">{{ $ma->asprak->user->name }}</div>
                                                                            <div class="text-[10px] text-gray-500 truncate">{{ $ma->asprak->user->email }}</div>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                @endforelse
                                                            </div>
                                                        @else
                                                            <div class="text-[11px] text-gray-500 italic p-4 bg-white rounded-[8px] border border-dashed border-[#DFE1E7] text-center">Belum ada asisten yang ditugaskan mengatur modul ini.</div>
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>"""

text = text.replace(old_btn, new_btn)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated Detail button to Modal popup!")
