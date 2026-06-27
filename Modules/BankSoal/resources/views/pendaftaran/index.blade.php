<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Sistem Ujian</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-slate-800 font-semibold">Daftar Peserta</span>
    @endsection

    <div class="w-full">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Manajemen Peserta</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Kelola data mahasiswa yang mendaftar ujian pada periode
                    aktif.</p>
            </div>

            <!-- ============================================================= -->
            <!-- CONTEXT SWITCHER: Searchable Period Popover (scalable)         -->
            <!-- ============================================================= -->
            @if($periodes->isEmpty())
                <div
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    Belum ada periode. <a href="{{ route('banksoal.periode.setup') }}" class="underline font-bold">Buat
                        Periode Pertama</a>
                </div>
            @else
                    @php
                        $periodesForJs = $periodes->map(fn($p) => [
                            'id' => $p->id,
                            'nama' => $p->nama_periode,
                            'url' => route('banksoal.pendaftaran.index', ['periode_id' => $p->id]),
                        ])->values();
                        $selectedPeriodeId = request('periode_id');
                        $selectedPeriode = $periodes->firstWhere('id', $selectedPeriodeId);
                    @endphp
                    <div x-data="{
                            open: false,
                            search: '',
                            periodeList: {{ $periodesForJs->toJson() }},
                            get filtered() {
                                const q = this.search.trim().toLowerCase();
                                if (!q) return this.periodeList;
                                return this.periodeList.filter(p => p.nama.toLowerCase().includes(q));
                            },
                            toggle() {
                                this.open = !this.open;
                                if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus());
                            },
                            close() { this.open = false; this.search = ''; }
                         }" class="relative flex-shrink-0" @keydown.escape.window="close()">

                        <!-- Trigger Button -->
                        <button type="button" @click="toggle()" class="group inline-flex items-center gap-2.5 pl-4 pr-3 py-2 rounded-lg text-[13px] font-semibold border transition-all duration-200 shadow-sm
                                       {{ $selectedPeriode
                ? 'bg-white text-gray-900 border-gray-300 hover:border-primary/40 hover:shadow-md'
                : 'bg-primary text-white border-primary hover:bg-primary/90' }}">
                            @if($selectedPeriode)
                                <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                <span class="">{{ $selectedPeriode->nama_periode }}</span>
                            @else
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>Pilih Periode Ujian</span>
                            @endif
                            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $selectedPeriode ? 'text-gray-400' : 'text-white/70' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Popover Panel -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1" @click.outside="close()"
                            class="absolute top-full right-0 mt-2 z-50 bg-white border border-gray-200 rounded-xl shadow-xl w-72 overflow-hidden"
                            style="display:none">

                            <!-- Search Header -->
                            <div class="p-3 border-b border-gray-100">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                                    </svg>
                                    <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari nama periode..."
                                        class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-2 pl-1">
                                    <span x-text="filtered.length"></span> dari {{ $periodes->count() }} periode
                                </p>
                            </div>

                            <!-- Period List -->
                            <div class="overflow-y-auto max-h-64 py-1.5">
                                <template x-for="p in filtered" :key="p.id">
                                    <a :href="p.url"
                                        class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-gray-50"
                                        :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary/10 text-primary' : 'text-gray-900'">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                            :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary' : 'bg-slate-300'"></span>
                                        <span x-text="p.nama" class="flex-1 whitespace-normal break-words text-left"></span>
                                        <svg x-show="p.id == {{ $selectedPeriodeId ?? 'null' }}"
                                            class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </a>
                                </template>
                                <div x-show="filtered.length === 0" class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                                    </svg>
                                    <p class="text-[13px] text-gray-400 font-medium">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>
                    </div>
            @endif
        </div>

        @if (!request('periode_id'))
            <div class="mb-6 bg-primary/5 border border-primary/20 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-[13px] text-primary font-medium mt-0.5">
                    Data peserta ujian akan tampil setelah Anda memilih <strong class="font-bold">Periode Ujian</strong> di atas.
                </p>
            </div>
        @endif

        {{-- Hidden Form for GET filters --}}
        <form method="GET" action="{{ route('banksoal.pendaftaran.index') }}" id="filter-form" class="hidden">
            @if(request('periode_id'))
                <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
            @endif
            <input type="hidden" name="search" id="hidden-search" value="{{ request('search') }}">
            <input type="hidden" name="status" id="hidden-status" value="{{ request('status') }}">
            <input type="hidden" name="per_page" id="hidden-per-page" value="{{ request('per_page', 5) }}">
        </form>

        {{-- Table Container Wrapper (No longer a form) --}}
        <div id="filter-container">
            <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom: 2rem;">
                
                {{-- Table Toolbar --}}
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
                    <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Tabel Daftar Peserta</h2>

                    <!-- Filters & Search (Kanan) -->
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
                        <!-- Search -->
                        <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
                            <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" value="{{ request('search') }}"
                                placeholder="Cari NIM atau nama..." {{ !request('periode_id') ? 'disabled' : '' }}
                                oninput="document.getElementById('hidden-search').value = this.value;"
                                onkeydown="if(event.key === 'Enter') { document.getElementById('hidden-search').value = this.value; document.getElementById('filter-form').submit(); }"
                                style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:{{ !request('periode_id') ? '#f8fafc' : '#fff' }}; {{ !request('periode_id') ? 'cursor:not-allowed; opacity:0.6;' : '' }}"
                                onfocus="if({{ request('periode_id') ? 'true' : 'false' }}){ this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)' }"
                                onblur="if({{ request('periode_id') ? 'true' : 'false' }}){ this.style.borderColor='var(--c-border)'; this.style.boxShadow='none' }">
                        </div>

                        <!-- Filter Status -->
                        <div class="relative inline-block" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold text-[var(--c-fg-sec)] whitespace-nowrap transition-all box-border disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                    style="border-color:var(--c-border); font-family:inherit;"
                                    :style="open ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''"
                                    {{ !request('periode_id') ? 'disabled' : '' }}>
                                <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                                </svg>
                                <span class="leading-none tracking-normal">Filter</span>
                                @if(request('status'))
                                    <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                                @endif
                            </button>

                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 sm:right-auto sm:left-0 top-[calc(100%+6px)] bg-white border rounded-xl shadow-lg min-w-[160px] z-50 overflow-hidden"
                                style="border-color:var(--c-border); display:none;">
                                <div class="p-1.5">
                                    @php 
                                        $statuses = [
                                            '' => 'Semua Status',
                                            'pending' => 'Pending',
                                            'approved' => 'Disetujui'
                                        ]; 
                                        $statusFilter = request('status', '');
                                    @endphp
                                    @foreach($statuses as $val => $label)
                                    <button type="button"
                                        onclick="document.getElementById('hidden-status').value = '{{ $val }}'; document.getElementById('filter-form').submit();"
                                        class="block w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] transition-colors"
                                        style="font-weight:{{ $statusFilter === $val ? '700' : '500' }}; color:{{ $statusFilter === $val ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; text-decoration:none; background:{{ $statusFilter === $val ? 'rgba(94,83,244,0.06)' : 'transparent' }};"
                                        onmouseover="if('{{ $statusFilter }}' !== '{{ $val }}') this.style.background='var(--c-bg)'" onmouseout="if('{{ $statusFilter }}' !== '{{ $val }}') this.style.background='transparent'">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button type="button"
                            onclick="document.getElementById('modal-tambah-manual').classList.remove('hidden')"
                            {{ !request('periode_id') ? 'disabled' : '' }}
                            class="flex items-center justify-center gap-1.5 h-[34px] px-3.5 border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border bg-primary text-white hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed border-transparent shadow-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Peserta
                        </button>
                    </div>
                </div>

                <!-- Include Table Component -->
                @include('banksoal::pendaftaran.partials.table')
            </div>
        </div>

                <!-- Include Modal Components -->
                @include('banksoal::pendaftaran.partials.modal-manual')
                @include('banksoal::pendaftaran.partials.modal-detail')
            </div>

            {{-- Auto-reopen modal jika ada validation error di bag 'pendaftar' --}}
            @if($errors->hasBag('pendaftar') && $errors->getBag('pendaftar')->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.getElementById('modal-tambah-manual').classList.remove('hidden');
                    });
                </script>
            @endif
</x-banksoal::layouts.admin>
