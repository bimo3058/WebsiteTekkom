import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

start_marker = "{{-- SECTION: KOORDINATOR AKTIF --}}"
end_marker = "{{-- ORIGINAL SECTION: Filter Pendaftaran --}}"

idx_start = text.find(start_marker)
idx_end = text.find(end_marker)

if idx_start == -1 or idx_end == -1:
    print("Markers not found.")
    sys.exit(1)

new_section = """{{-- SECTION: KOORDINATOR AKTIF & TUNJUK LANGSUNG --}}
        @if(isset($praktikum))
        <div style="display: flex; gap: 24px; margin-bottom: 24px; flex-wrap: wrap; align-items: stretch;">
            
            {{-- Kiri: Status Koordinator --}}
            <div class="mp-card flex-shrink-0" style="flex: 1; min-width: 380px; display: flex; flex-direction: column;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Koordinator Aktif</span>
                </div>
                <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: center;">
                    @if($koordinator)
                        <div style="font-size: 13px; font-weight: 700; color: #10B981; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; display:flex; align-items:center; gap:6px;">
                            <span class="dot" style="background:#10B981;"></span> Koordinator Terpasang
                        </div>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="mp-av navy" style="width: 52px; height: 52px; font-size: 16px;">
                                {{ strtoupper(substr($koordinator->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size: 16px; font-weight: 700; color: #0D0D12;">{{ $koordinator->name }}</div>
                                <div style="font-size: 13px; color: #666D80; margin-top:2px;">{{ $koordinator->student?->student_number ?? '—' }} · {{ $koordinator->email }}</div>
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: #FFF5F5; border: 1px solid #FFEBEB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700; color: #0D0D12; margin-bottom: 4px;">Belum Ada Koordinator</div>
                                <div style="font-size: 13px; color: #666D80; line-height: 1.6; max-width: 400px;">
                                    <strong>{{ $praktikum->nama }}</strong> belum memiliki koordinator. Silahkan buka pendaftaran koordinator di menu Periode, atau tunjuk koordinator via form di samping.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kanan: Tunjuk Koordinator Langsung --}}
            <div class="mp-card flex-shrink-0" style="width: 380px; flex-grow: 1; max-width: 500px; display: flex; flex-direction: column;">
                <div class="mp-card-header">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>
                        <span class="mp-card-title">Tunjuk Langsung Mahasiswa</span>
                    </div>
                </div>
                <div style="padding: 24px; flex: 1; background: #FAFAFA;" 
                     x-data="{
                         query: '',
                         results: [],
                         showDropdown: false,
                         selectedUser: null,
                         isLoading: false,
                         init() {
                             this.$watch('query', value => {
                                 if(this.selectedUser && this.selectedUser.name !== value) {
                                     this.selectedUser = null;
                                 }
                             });
                         },
                         async search() {
                             if(this.query.length < 3) {
                                 this.results = [];
                                 return;
                             }
                             this.isLoading = true;
                             try {
                                 const res = await fetch(`{{ route('eoffice.manprak.dosen.search-praktikan') }}?praktikum_id={{ $praktikum->id }}&q=${encodeURIComponent(this.query)}`);
                                 const data = await res.json();
                                 this.results = data;
                             } catch (e) {
                                 this.results = [];
                             }
                             this.isLoading = false;
                         },
                         selectUser(user) {
                             this.selectedUser = user;
                             this.query = user.name;
                             this.showDropdown = false;
                         }
                     }">

                    <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}" style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                        <input type="hidden" name="user_id" :value="selectedUser ? selectedUser.id : ''">

                        <div style="position: relative; margin-bottom: 16px;">
                            <label style="font-size: 12px; font-weight: 600; color: #666D80; margin-bottom: 8px; display: block;">Cari Mahasiswa</label>
                            <input type="text" x-model="query" @input.debounce.300ms="search()" 
                                   placeholder="Ketik nama atau NIM..."
                                   class="mp-input text-[13px]" style="width: 100%; border-radius: 8px; padding-right: 36px; background: #fff;"
                                   @focus="showDropdown = true" @click.away="showDropdown = false">
                            
                            {{-- Clear Selection Button --}}
                            <button type="button" x-show="query.length > 0" @click="query = ''; selectedUser = null; results = [];" 
                                    style="position: absolute; right: 8px; top: 34px; display:flex; padding:4px; border-radius:4px; color:#A4ABB8; cursor:pointer; border:none; background:transparent;"
                                    title="Hapus pilihan"
                                    onmouseover="this.style.color='#DF1C41'; this.style.background='#FFF5F5'" onmouseout="this.style.color='#A4ABB8'; this.style.background='transparent'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                            
                            {{-- Dropdown --}}
                            <div x-show="showDropdown && query.length >= 3" 
                                 style="position: absolute; z-index: 50; width: 100%; margin-top: 4px; background: white; border: 1px solid #DFE1E7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); max-height: 220px; overflow-y: auto;"
                                 x-transition.opacity>
                                
                                <div x-show="isLoading" style="padding: 12px 16px; text-align: center; color: #666D80; font-size: 12px;">
                                    Mencari data mahasiswa...
                                </div>
                                
                                <template x-if="!isLoading && results.length > 0">
                                    <div>
                                        <template x-for="user in results" :key="user.id">
                                            <div @click="selectUser(user)" 
                                                 style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #F3F4F6; transition: background 0.1s;"
                                                 onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                                <div style="font-size: 13px; font-weight: 700; color: #0D0D12;" x-text="user.name"></div>
                                                <div style="font-size: 11px; color: #666D80; margin-top: 2px;" x-text="(user.student ? user.student.student_number : '') + ' · ' + user.email"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                
                                <div x-show="!isLoading && results.length === 0" style="padding: 12px 16px; text-align: center; color: #DF1C41; font-size: 12px; font-weight:500;">
                                    Mahasiswa tidak ditemukan di Praktikum ini.
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"
                                style="font-weight: 600; font-size: 13px; padding: 10px; border-radius: 8px;"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            Tunjuk Sebagai Koordinator
                        </button>
                    </form>
                </div>
            </div>

        </div>
        @endif

        """

new_text = text[:idx_start] + new_section + text[idx_end:]

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_text)

print("Split cards successfully")