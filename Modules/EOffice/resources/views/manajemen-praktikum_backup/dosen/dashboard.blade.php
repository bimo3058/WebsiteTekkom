<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Dosen — Manajemen Praktikum">
    @php
        $name = auth()->user()->name;
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
    @endphp

    {{-- Page Header --}}
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Dashboard Dosen</h1>
                <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
            </div>
            <p class="mp-page-sub">Selamat datang, {{ $firstName }} ·
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index') }}" class="mp-btn secondary md"
                style="text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                </svg>
                Seleksi Koordinator
            </a>
        </div>
    </div>

    {{-- Section: Ringkasan --}}
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Ringkasan</span>
        <span class="sec-rule"></span>
    </div>

    {{-- Stat Cards --}}
    <div class="mp-stats-grid cols-4">
        <div class="mp-stat">
            <div class="mp-stat-icon navy">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>
            <div class="mp-stat-label">Praktikum Aktif</div>
            <div class="mp-stat-value">{{ $totalPraktikumAktif ?? 0 }}</div>
            <div class="mp-stat-sub">diampu semester ini</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                </svg>
            </div>
            <div class="mp-stat-label">Total Mahasiswa</div>
            <div class="mp-stat-value">{{ $totalMahasiswa ?? 0 }}</div>
            <div class="mp-stat-sub">semua praktikum</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon sky">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                </svg>
            </div>
            <div class="mp-stat-label">Total Modul</div>
            <div class="mp-stat-value">{{ $totalModul ?? 0 }}</div>
            <div class="mp-stat-sub">modul tersedia</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M9 15l2 2 4-4" />
                </svg>
            </div>
            <div class="mp-stat-label">Nilai Pending</div>
            <div class="mp-stat-value">{{ $nilaiPending ?? 0 }}</div>
            <div class="mp-stat-sub">perlu diinput</div>
        </div>
    </div>

    {{-- Alert: Praktikum Tanpa Koordinator --}}
    @if(isset($praktikumTanpaKoor) && $praktikumTanpaKoor->isNotEmpty())
        <div class="mp-alert error flex-shrink-0">
            <div class="flex items-center gap-2 mb-3">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span style="font-size:13px;font-weight:700;">Praktikum Belum Punya Koordinator</span>
            </div>
            <div class="flex flex-col gap-2">
                @foreach($praktikumTanpaKoor as $p)
                    <div class="flex items-center justify-between bg-white rounded-[8px] px-3 py-2 gap-4">
                        <span style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</span>
                        <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}"
                            class="flex items-center gap-2 flex-shrink-0">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                            <div x-data="koorSearch('{{ $p->id }}')" class="relative">
                                <input type="hidden" name="nim" x-model="selectedNim">
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchSuggestions" @focus="showDropdown = true" @click.away="showDropdown = false" placeholder="Cari Nama/NIM..." class="mp-input" style="width:200px;font-size:12px;" autocomplete="off">
                                <div x-show="showDropdown && searchQuery.length >= 2" class="absolute z-50 w-full mt-1 bg-white border border-[#DFE1E7] rounded-md shadow-lg max-h-48 overflow-y-auto" style="display:none;">
                                    <template x-for="item in suggestions" :key="item.id">
                                        <div @click="selectItem(item)" class="px-3 py-2 text-[12px] cursor-pointer hover:bg-[#F6F8FA]" x-text="item.text"></div>
                                    </template>
                                    <div x-show="loading" class="px-3 py-2 text-[12px] text-gray-500">Mencari...</div>
                                    <div x-show="!loading && suggestions.length === 0" class="px-3 py-2 text-[12px] text-red-500">Hasil tidak ditemukan</div>
                                </div>
                            </div>
                            <button type="submit" class="mp-btn primary sm">Tunjuk</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section: Praktikum yang Diampu --}}
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Praktikum yang Diampu</span>
        <span class="sec-rule"></span>
        @if(isset($praktikums) && !$praktikums->isEmpty())
            <span class="mp-badge navy sm">{{ $praktikums->count() }} praktikum</span>
        @endif
    </div>

    <div class="mp-card flex-shrink-0">
        @if(!isset($praktikums) || $praktikums->isEmpty())
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikum yang diampu.</div>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;padding:18px;">
                @foreach($praktikums as $p)
                    <div style="border:1px solid #DFE1E7;border-radius:14px;padding:18px;transition:border-color .15s,box-shadow .15s;"
                        onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
                        onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">

                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-3 gap-3">
                            <div class="min-w-0">
                                <div style="font-size:14px;font-weight:700;color:#0D0D12;margin-bottom:3px;" class="truncate">
                                    {{ $p->nama }}</div>

                            </div>
                            @if($p->status === 'aktif')
                                <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Aktif</span>
                            @else
                                <span class="mp-badge neutral sm flex-shrink-0"><span class="dot"></span>Nonaktif</span>
                            @endif
                        </div>

                        {{-- Mini stats --}}
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    {{ $p->daftar_praktikan_count ?? 0 }}</div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Mahasiswa</div>
                            </div>
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    {{ $p->modul_count ?? 0 }}</div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Modul</div>
                            </div>
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    {{ $p->asisten_praktikum_count ?? 0 }}</div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Asprak</div>
                            </div>
                        </div>

                        {{-- Koordinator --}}
                        <div style="font-size:11px;color:#666D80;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <path
                                    d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                            </svg>
                            @if($p->koordinator)
                                <span style="font-weight:600;color:#0D0D12;">{{ $p->koordinator->name }}</span>
                            @else
                                <span style="font-weight:600;color:#DF1C41;">Belum ditunjuk</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
                                class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Lihat Modul</a>
                            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
                                class="mp-btn primary sm flex-1 text-center" style="text-decoration:none;">Lihat Nilai</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('koorSearch', (praktikumId) => ({
            praktikumId: praktikumId,
            searchQuery: '',
            selectedNim: '',
            suggestions: [],
            showDropdown: false,
            loading: false,

            async fetchSuggestions() {
                if (this.searchQuery.length < 2) {
                    this.suggestions = [];
                    return;
                }
                this.loading = true;
                this.showDropdown = true;
                try {
                    const response = await fetch(`{{ route('eoffice.manprak.dosen.search-praktikan') }}?q=${encodeURIComponent(this.searchQuery)}&praktikum_id=${this.praktikumId}`);
                    if (response.ok) {
                        this.suggestions = await response.json();
                    } else {
                        this.suggestions = [];
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    this.suggestions = [];
                }
                this.loading = false;
            },

            selectItem(item) {
                this.selectedNim = item.id;
                this.searchQuery = item.text;
                this.showDropdown = false;
            }
        }));
    });
    </script>
</x-eoffice::manajemen-praktikum.layout>