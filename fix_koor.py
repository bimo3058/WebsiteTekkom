import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    orig = f.read()

tunjuk_section = '''
        {{-- SECTION: KOORDINATOR AKTIF --}}
        @if(isset())
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Koordinator Aktif</span>
            <span class="sec-rule"></span>
        </div>
        <div class="mp-card flex-shrink-0" style="margin-bottom: 24px; padding: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap;">
            
            {{-- Kiri: Status Koordinator --}}
            <div style="flex: 1; min-width: 300px;">
                @if()
                    <div style="font-size: 13px; font-weight: 700; color: #10B981; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; display:flex; align-items:center; gap:6px;">
                        <span class="dot" style="background:#10B981;"></span> Koordinator Aktif
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="mp-av navy" style="width: 52px; height: 52px; font-size: 16px;">
                            {{ strtoupper(substr(->name, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size: 16px; font-weight: 700; color: #0D0D12;">{{ ->name }}</div>
                            <div style="font-size: 13px; color: #666D80; margin-top:2px;">{{ ->student?->student_number ?? '—' }} · {{ ->email }}</div>
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
                                <strong>{{ ->nama }}</strong> belum memiliki koordinator. Silahkan buka pendaftaran koordinator di menu Periode, atau tunjuk koordinator via form di samping.
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kanan: Tunjuk Koordinator Langsung --}}
            <div style="width: 380px; background: #FAFAFA; border: 1px solid #DFE1E7; border-radius: 12px; padding: 18px;" 
                 x-data="{
                     query: '',
                     results: [],
                     showDropdown: false,
                     selectedUser: null,
                     isLoading: false,
                     init() {
                         this.\('query', value => {
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
                             const res = await fetch('{{ route('eoffice.manprak.dosen.search-praktikan') }}?praktikum_id={{ ->id }}&q=' + encodeURIComponent(this.query));
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
                <div style="font-size: 13px; font-weight: 700; color: #0D0D12; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>
                    Tunjuk Langsung Mahasiswa
                </div>

                <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ ->id }}">
                    <input type="hidden" name="user_id" :value="selectedUser ? selectedUser.id : ''">

                    <div style="position: relative; margin-bottom: 12px;">
                        <input type="text" x-model="query" @input.debounce.300ms="search()" 
                               placeholder="Cari NIM atau Nama mahasiswa..."
                               class="mp-input text-[13px]" style="width: 100%; border-radius: 8px; padding-right: 36px; background: #fff;"
                               @focus="showDropdown = true" @click.away="showDropdown = false">
                        
                        {{-- Clear Selection Button --}}
                        <button type="button" x-show="query.length > 0" @click="query = ''; selectedUser = null; results = [];" 
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); display:flex; padding:4px; border-radius:4px; color:#A4ABB8; cursor:pointer; border:none; background:transparent;"
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

                    <button type="submit" class="mp-btn primary w-full"
                            style="justify-content: center; font-weight: 600; font-size: 13px; padding: 10px; border-radius: 8px;"
                            x-bind:disabled="!selectedUser"
                            :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                        Tunjuk Sebagai Koordinator
                    </button>
                </form>
            </div>

        </div>
        @endif
'''

if '{{-- SECTION: KOORDINATOR AKTIF --}}' in orig:
    # Already injected previously
    print("Already exists")
    sys.exit(0)

# Inject just before the filter section
target = '{{-- Section: Filter --}}'
if target in orig:
    new_orig = orig.replace(target, tunjuk_section + '\\n        ' + target)
    with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
        f.write(new_orig)
    print("Injected successfully")
else:
    print("Anchor target not found!")
    