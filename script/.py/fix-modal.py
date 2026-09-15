import sys

file_path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen\pendaftaran-koor.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

closing_tag = '</x-eoffice::manajemen-praktikum.layout>'
if closing_tag not in content:
    print('Closing tag not found')
    sys.exit(1)

modals_and_fabs = '''
    <div x-data="{ showTunjukModal: false, showFormModal: false }">
        {{-- FAB Buttons --}}
        <div style="position: fixed; bottom: 32px; right: 32px; display: flex; flex-direction: column; gap: 12px; z-index: 40;">
            <button type="button" @click="if(!{{ $koordinator ? 'true' : 'false' }}) showFormModal = true" class="mp-btn primary sm" style="height:36px; padding:0 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: box-shadow 0.2s; justify-content: flex-start; {{ $koordinator ? 'background: #818898; color: #F6F8FA; border: none; cursor: not-allowed;' : '' }}" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.25)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Buat Form Pendaftaran
            </button>
            <button type="button" @click="if(!{{ $koordinator ? 'true' : 'false' }}) showTunjukModal = true" class="mp-btn primary sm" style="height:36px; padding:0 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: box-shadow 0.2s; justify-content: flex-start; {{ $koordinator ? 'background: #818898; color: #F6F8FA; border: none; cursor: not-allowed;' : '' }}" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.25)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Tunjuk Koordinator
            </button>
        </div>

        {{-- Modal Tunjuk Koordinator --}}
        <div x-show="showTunjukModal" style="display:none;"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
             x-cloak>
            <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]" 
                 @click.away="showTunjukModal = false" x-show="showTunjukModal"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                    <div class="font-bold text-[16px] text-[#0D0D12]">Tunjuk Koordinator Secara Langsung</div>
                </div>
                
                <div class="overflow-y-auto flex-1">
                    <form action="{{ route('eoffice.manprak.dosen.tunjuk-koor.store') }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">
                        
                        <div class="flex flex-col gap-4">
                            <div class="mp-alert info flex-shrink-0 text-[13px]">
                                Mahasiswa yang anda tunjuk otomatis akan menjadi koordinator {{ $praktikum->nama ?? '' }}
                            </div>
        
                            <div x-data="koorSearch()" class="relative">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Cari Mahasiswa (Nama/NIM) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" x-model="query" @input.debounce.100ms="search" @click.away="open = false" @focus="if(query.length >= 2) open = true" class="mp-input w-full" placeholder="Ketik nama atau NIM mahasiswa..." autocomplete="off">
                                    <input type="hidden" name="nim" :value="selectedNim" required>
                                    
                                    <div x-show="open" class="absolute z-50 w-full mt-1 bg-white border border-[#DFE1E7] rounded-[8px] shadow-lg max-h-60 overflow-y-auto" style="display: none;" x-transition>
                                        <div x-show="loading" class="px-4 py-2 text-sm text-[#666D80]">Mencari...</div>
                                        <div x-show="!loading && results.length === 0 && query.length >= 2" class="px-4 py-2 text-sm text-[#666D80]">Mahasiswa tidak ditemukan</div>
                                        
                                        <template x-for="item in results" :key="item.id">
                                            <div @click="select(item)" class="px-4 py-2 text-[13px] cursor-pointer hover:bg-[#F6F8FA] border-b border-[#F6F8FA] last:border-0 transition-colors">
                                                <div x-text="item.text" class="text-[#0D0D12] font-medium"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                            <button type="button" @click="showTunjukModal = false" class="mp-btn secondary sm" style="height:36px; padding:0 16px;">Batal</button>
                            <button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">Tunjuk Sebagai Koordinator</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Buat Form Pendaftaran --}}
        <div x-show="showFormModal" style="display:none;" @open-form-modal.window="showFormModal = true"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
             x-cloak>
            <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" 
                 @click.away="showFormModal = false" x-show="showFormModal"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                    <div class="font-bold text-[16px] text-[#0D0D12]">{{ isset($periode) && $periode ? 'Update Form Pendaftaran Koordinator' : 'Buat Form Pendaftaran Koordinator' }}</div>
                </div>
                
                <div class="overflow-y-auto flex-1">
                    <form action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.store') }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">
                        
                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Judul</label>
                                <input type="text" name="judul" class="mp-input w-full" placeholder="Contoh: Seleksi Koordinator Genap 2025" value="{{ $periode->judul ?? '' }}">
                            </div>
        
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                                <textarea name="deskripsi" class="mp-input w-full" rows="3" maxlength="500" placeholder="Tuliskan deskripsi atau link soal tes..." style="resize: vertical; min-height: 80px; max-height: 200px;">{{ $periode->deskripsi ?? '' }}</textarea>
                                <p class="text-[11px] text-[#666D80] mt-1">Maksimal 500 karakter.</p>
                            </div>

                            <hr class="border-t border-dashed border-[#DFE1E7] my-2">

                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dibuka Pada</label>
                                <div class="relative">
                                    <input type="text" name="dibuka_pada" class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]" placeholder="Pilih tanggal dan waktu..." value="{{ isset($periode) && $periode && $periode->dibuka_pada ? \Carbon\Carbon::parse($periode->dibuka_pada)->format('Y-m-d H:i') : '' }}" style="padding-right: 32px; cursor: pointer;">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <p class="text-[11px] text-[#666D80] mt-1">Kosongkan untuk langsung dibuka saat ini.</p>
                            </div>
        
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Ditutup Pada <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="ditutup_pada" class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]" placeholder="Pilih tanggal dan waktu..." value="{{ isset($periode) && $periode && $periode->ditutup_pada ? \Carbon\Carbon::parse($periode->ditutup_pada)->format('Y-m-d H:i') : '' }}" required style="padding-right: 32px; cursor: pointer;">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <p class="text-[11px] text-[#666D80] mt-1">Wajib diisi sebagai batas penutupan.</p>
                            </div>

                            <hr class="border-t border-dashed border-[#DFE1E7] my-2">
                            
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Biodata & Persyaratan (Wajib diisi mahasiswa)</label>
                                
                                <div class="flex gap-4 mb-3">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-[#666D80] mb-1">Nama Mahasiswa</label>
                                        <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled style="background: #F9FAFB; color: #808897;">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-[#666D80] mb-1">NIM</label>
                                        <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled style="background: #F9FAFB; color: #808897;">
                                    </div>
                                </div>
                                
                                <div class="flex gap-4 mb-3">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-[#666D80] mb-1">IPK *</label>
                                        <input type="text" class="mp-input w-full text-[12px]" value="Diisi oleh mahasiswa" disabled style="background: #F9FAFB; color: #808897;">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-[#666D80] mb-1">Transkrip Nilai *</label>
                                        <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)" disabled style="background: #F9FAFB; color: #808897; border-style: dashed;">
                                    </div>
                                </div>
                                
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-[11px] font-semibold text-[#666D80] mb-1">Keanggotaan CERC</label>
                                        <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)" disabled style="background: #F9FAFB; color: #808897; border-style: dashed;">
                                    </div>
                                    <div class="flex-1"></div>
                                </div>
                            </div>

                            <div x-data="{ addBerkas: {{ isset($periode) && $periode && $periode->nama_berkas_tambahan ? 'true' : 'false' }} }">
                                <div x-show="addBerkas" style="display: none;" x-transition class="flex items-start gap-2 p-4 border border-[#DFE1E7] rounded-[8px] bg-[#FAFAFA]">
                                    <div class="flex-1">
                                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Berkas Tambahan <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_berkas_tambahan" class="mp-input w-full text-[13px]" placeholder="Contoh: Jawaban Tes (PDF)" :required="addBerkas" :disabled="!addBerkas" value="{{ $periode->nama_berkas_tambahan ?? '' }}">
                                        <p class="text-[11px] text-[#666D80] mt-1">Mahasiswa wajib mengunggah file (khusus PDF) untuk field ini.</p>
                                    </div>
                                    <button type="button" @click="addBerkas = false; $el.previousElementSibling.querySelector('input').value = ''" class="flex-shrink-0 w-[38px] h-[38px] mt-[22px] flex items-center justify-center rounded-[8px] border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] transition-colors" title="Hapus Berkas Tambahan">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    </button>
                                </div>

                                <button type="button" x-show="!addBerkas" @click="addBerkas = true" class="w-full flex items-center justify-center gap-1.5 h-[38px] mt-1 border border-dashed border-[#DFE1E7] rounded-[8px] text-[12px] font-semibold text-[#0B266E] bg-[#FAFAFA] hover:bg-[#F6F8FA] transition-colors">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Tambah Pengumpulan Berkas
                                </button>
                            </div>
                        </div>
        
                        <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                            <button type="button" @click="showFormModal = false" class="mp-btn secondary sm" style="height:36px; padding:0 16px;">Batal</button>
                            <button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">{{ isset($periode) && $periode ? 'Update Pendaftaran' : 'Buka Periode Pendaftaran' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
<script>
function koorSearch() {
    return {
        query: '',
        selectedNim: '',
        results: [],
        open: false,
        loading: false,
        
        abortController: null,
        
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            
            this.loading = true;
            this.open = true;
            
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            try {
                const response = await fetch(`{{ route('eoffice.manprak.dosen.search-praktikan') }}?q=${encodeURIComponent(this.query)}&praktikum_id={{ $praktikum->id ?? '' }}`, {
                    signal: this.abortController.signal
                });
                this.results = await response.json();
                this.loading = false;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error(error);
                    this.results = [];
                    this.loading = false;
                }
            }
        },
        
        select(item) {
            this.selectedNim = item.id;
            this.query = item.text;
            this.open = false;
        }
    }
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('.flatpickr-input', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true
        });
    });
</script>
'''

new_content = content.replace(closing_tag, modals_and_fabs + '\n' + closing_tag)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)
