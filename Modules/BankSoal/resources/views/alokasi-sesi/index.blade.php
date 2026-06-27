<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Jadwal & Sesi</span>
    @endsection

    @section('hide_global_errors', true)
        <div id="alokasiSesiRoot" x-data="alokasiSesiApp()" class="w-full relative"
            data-open-modal="{{ $errors->any() ? '1' : '0' }}"
            data-jadwals='@json($jadwals ?? [])'
            data-pendaftars='@json($pendaftars ?? [])'>
        
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Jadwal & Sesi</h1>
                    <p class="text-sm text-slate-500 mt-1">Mengatur sesi ujian dan membagi jadwal peserta ujian komprehensif.</p>
                </div>

                <!-- ============================================================= -->
                <!-- CONTEXT SWITCHER: Searchable Period Popover (scalable)         -->
                <!-- ============================================================= -->
                @if($periodes->isEmpty())
                    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Belum ada periode. <a href="{{ route('banksoal.periode.setup') }}" class="underline font-bold">Buat Periode Pertama</a>
                    </div>
                @else
                @php
                    $periodesForJs = $periodes->map(fn($p) => [
                        'id'   => $p->id,
                        'nama' => $p->nama_periode,
                        'url'  => route('banksoal.pendaftaran.alokasi-sesi.index', ['periode_id' => $p->id]),
                    ])->values();
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
                     }"
                     class="relative flex-shrink-0"
                     @keydown.escape.window="close()">

                    <!-- Trigger Button -->
                    <button type="button" @click="toggle()"
                            class="group inline-flex items-center gap-2.5 pl-4 pr-3 py-2 rounded-lg text-[13px] font-semibold border transition-all duration-200 shadow-sm
                                   {{ $selectedPeriode
                                       ? 'bg-white text-slate-700 border-slate-300 hover:border-primary/40 hover:shadow-md'
                                       : 'bg-primary text-white border-primary hover:bg-primary/90' }}">
                        @if($selectedPeriode)
                            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                            <span>{{ $selectedPeriode->nama_periode }}</span>
                        @else
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Pilih Periode Ujian</span>
                        @endif
                        <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $selectedPeriode ? 'text-slate-400' : 'text-white/70' }}"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Popover Panel -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         @click.outside="close()"
                         class="absolute top-full right-0 mt-2 z-50 bg-white border border-slate-200 rounded-2xl shadow-2xl w-72 overflow-hidden"
                         style="display:none">

                        <!-- Search Header -->
                        <div class="p-3 border-b border-slate-100">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                                <input x-ref="searchInput"
                                       x-model="search"
                                       type="text"
                                       placeholder="Cari nama periode..."
                                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2 pl-1">
                                <span x-text="filtered.length"></span> dari {{ $periodes->count() }} periode
                            </p>
                        </div>

                        <!-- Period List -->
                        <div class="overflow-y-auto max-h-64 py-1.5">
                            <template x-for="p in filtered" :key="p.id">
                                <a :href="p.url"
                                   class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-slate-50 group/item"
                                   :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary/10 text-primary' : 'text-slate-700'">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                          :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary' : 'bg-slate-300'"></span>
                                    <span x-text="p.nama" class="flex-1"></span>
                                    <svg x-show="p.id == {{ $selectedPeriodeId ?? 'null' }}" class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </a>
                            </template>

                            <!-- Empty state -->
                            <div x-show="filtered.length === 0" class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                                <p class="text-[13px] text-slate-400 font-medium">Tidak ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>



        <!-- Rentang tanggal periode terpilih -->
        @if($selectedPeriode)
        <div class="flex items-center gap-2 mb-6">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[13px] text-slate-500 font-medium">Rentang Ujian:</span>
            @if($selectedPeriode->tanggal_mulai_ujian && $selectedPeriode->tanggal_selesai_ujian)
                <span class="px-3 py-1 bg-primary/10 text-primary text-[13px] font-semibold border border-primary/20 rounded-lg">
                    {{ \Carbon\Carbon::parse($selectedPeriode->tanggal_mulai_ujian)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($selectedPeriode->tanggal_selesai_ujian)->translatedFormat('d F Y') }}
                </span>
            @else
                <span class="text-[13px] text-slate-400 italic">Tanggal Ujian belum diatur di Setup Periode.</span>
            @endif
        </div>
        @else
        <!-- Empty State: no periode selected -->
        <div class="mb-6 bg-primary/5 border border-primary/10 rounded-xl p-4 flex gap-3">
            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-primary/80">
                Data sesi ujian akan tampil setelah Anda memilih <strong>Periode Ujian</strong> di atas.
            </p>
        </div>
        @endif

        <!-- Table Jadwal Container -->
        <div class="mb-8">
            @include('banksoal::alokasi-sesi._table')
        </div>

        @if($activePeriode)
            @php
                $allocatedGroups = collect($pendaftars ?? [])->whereNotNull('jadwal_ujian_id')->groupBy('jadwal_ujian_id')->sortBy(function($group) {
                    return optional($group->first()->jadwal)->waktu_mulai;
                });
            @endphp
            
            @if($allocatedGroups->count() > 0)
            <!-- Table Peserta Terjadwal -->
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 flex items-center justify-between border-b border-slate-200 bg-slate-50">
                    <h2 class="font-bold text-slate-800">Daftar Peserta {{ $activePeriode->nama_periode }}</h2>
                </div>
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    @foreach($allocatedGroups as $jadwalId => $pesertas)
                        @php
                            $jadwal = $pesertas->first()->jadwal;
                            $tanggal = $jadwal && $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('l, d F Y') : 'Tgl Belum Diatur';
                            $mulai = $jadwal && $jadwal->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '...';
                            $selesai = $jadwal && $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '...';
                        @endphp
                        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col">
                            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                                <h3 class="font-bold text-[15px] text-primary mb-0 m-0">
                                    {{ is_numeric($jadwal->nama_sesi) ? 'Sesi ' . $jadwal->nama_sesi : ($jadwal->nama_sesi ?? 'Nama Sesi Kosong') }}: {{ $tanggal }} pukul {{ $mulai }}-{{ $selesai }} WIB
                                </h3>
                            </div>
                            <div class="overflow-x-auto w-full">
                                <table class="w-full text-left text-[13px] border-collapse min-w-[380px]">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-[#FAFAFA]">
                                            <th scope="col" class="px-4 py-[11px] w-12 text-left font-semibold text-[11px] text-slate-500">No</th>
                                            <th scope="col" class="px-4 py-[11px] w-32 text-left font-semibold text-[11px] text-slate-500">NIM</th>
                                            <th scope="col" class="px-4 py-[11px] text-left font-semibold text-[11px] text-slate-500">Nama Mahasiswa</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        @foreach($pesertas as $index => $peserta)
                                        <tr class="border-b border-[#F3F4F6] hover:bg-[#FAFAFA] transition-colors duration-150">
                                            <td class="px-4 py-3.5 text-slate-500 font-medium">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-4 py-3.5 font-semibold text-slate-800">
                                                {{ $peserta->nim }}
                                            </td>
                                            <td class="px-4 py-3.5 font-medium text-slate-700">
                                                {{ $peserta->nama_lengkap }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        <!-- Modal Kelola Peserta -->
        <div x-show="openDrawer" tabindex="-1" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" style="display: none;" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
                <!-- Dimmed background -->
                <div x-show="openDrawer" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                     @click="closeDrawer()" aria-hidden="true"></div>

                <div x-show="openDrawer" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95" 
                     class="relative w-full max-w-2xl max-h-[85vh] flex flex-col bg-white rounded-2xl shadow-xl overflow-hidden">
                            <!-- Drawer Header -->
                            <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 rounded-t-2xl flex-shrink-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelola Peserta</p>
                                        <h2 class="text-lg font-bold text-slate-800 leading-snug" id="slide-over-title"><span x-text="!isNaN(selectedJadwal?.nama_sesi) ? 'Sesi ' + selectedJadwal?.nama_sesi : selectedJadwal?.nama_sesi"></span></h2>
                                        <p class="text-[13px] text-slate-500 mt-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="formatTime(selectedJadwal?.waktu_mulai)"></span> – <span x-text="formatTime(selectedJadwal?.waktu_selesai)"></span> WIB
                                        </p>
                                    </div>
                                    <button type="button" class="rounded-lg p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition-colors focus:outline-none" @click="closeDrawer()">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <!-- Quota Bar — compact inline -->
                                <div class="mt-4 flex items-center gap-3">
                                    <div class="flex-1">
                                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full transition-all duration-500" :class="(selectedJadwal?.terisi >= selectedJadwal?.kuota) ? 'bg-red-500' : 'bg-primary-500'" :style="`width: ${Math.min(((selectedJadwal?.terisi || 0) / (selectedJadwal?.kuota || 1)) * 100, 100)}%`"></div>
                                        </div>
                                    </div>
                                    <span class="text-[12px] font-bold tabular-nums" :class="(selectedJadwal?.terisi >= selectedJadwal?.kuota) ? 'text-red-600' : 'text-slate-600'">
                                        <span x-text="selectedJadwal?.terisi"></span>/<span x-text="selectedJadwal?.kuota"></span>
                                    </span>
                                    <span x-show="(selectedJadwal?.terisi >= selectedJadwal?.kuota)" class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">PENUH</span>
                                </div>
                            </div>

                            <!-- Drawer Body -->
                            <div class="relative flex-1 overflow-y-auto bg-white p-6">
                                
                                <!-- TAB PANEL -->
                                <div class="flex bg-slate-100 p-1 rounded-lg mb-5 w-full">
                                    <button type="button" @click="drawerTab = 'unassigned'" :class="{'bg-white shadow-sm text-primary-600': drawerTab === 'unassigned', 'text-slate-500 hover:text-slate-700': drawerTab !== 'unassigned'}" class="flex-1 py-2 rounded-md text-[13px] font-bold transition-all flex justify-center items-center gap-1.5">
                                        Belum Dialokasi
                                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold" :class="{'bg-primary-50 text-primary-600': drawerTab === 'unassigned', 'bg-slate-200/80 text-slate-500': drawerTab !== 'unassigned'}" x-text="unassignedStudents.length"></span>
                                    </button>
                                    <button type="button" @click="drawerTab = 'assigned'" :class="{'bg-white shadow-sm text-primary-600': drawerTab === 'assigned', 'text-slate-500 hover:text-slate-700': drawerTab !== 'assigned'}" class="flex-1 py-2 rounded-md text-[13px] font-bold transition-all flex justify-center items-center gap-1.5">
                                        Dalam Sesi Ini
                                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold" :class="{'bg-primary-50 text-primary-600': drawerTab === 'assigned', 'bg-slate-200/80 text-slate-500': drawerTab !== 'assigned'}" x-text="assignedStudents.length"></span>
                                    </button>
                                </div>

                                <!-- Tab 1: Unassigned -->
                                <div x-show="drawerTab === 'unassigned'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                    <form action="{{ route('banksoal.pendaftaran.alokasi-sesi.store') }}" method="POST" id="formAssign">
                                        @csrf
                                        <input type="hidden" name="jadwal_id" :value="selectedJadwal?.id">
                                        
                                        <div class="flex justify-between items-center mb-3">
                                            <h3 class="font-bold text-slate-700 text-[13px]">Tambahkan ke sesi ini:</h3>
                                            <button type="button" @click="document.getElementById('formAssign').submit()" class="bg-primary hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors flex items-center gap-2" :disabled="selectedUnassignedIds.length === 0 || (selectedJadwal?.terisi + selectedUnassignedIds.length) > selectedJadwal?.kuota">
                                                Tambahkan (<span x-text="selectedUnassignedIds.length"></span>)
                                            </button>
                                        </div>

                                        <!-- Search filter -->
                                        <div class="relative mb-3">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            <input x-model="drawerSearch" type="text" placeholder="Cari nama atau NIM..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        </div>
                                        
                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                                            <table class="w-full text-left text-sm">
                                                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                                    <tr>
                                                        <th class="px-5 py-3.5 w-12 text-center border-r border-slate-100">
                                                            <input type="checkbox" x-model="checkAllUnassigned" @change="toggleAllUnassigned()" class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4">
                                                        </th>
                                                        <th class="px-5 py-3.5">Nama & NIM Mahasiswa</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    <template x-for="p in filteredUnassignedStudents" :key="p.id">
                                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                                            <td class="px-5 py-3 text-center border-r border-slate-100">
                                                                <input type="checkbox" name="pendaftar_ids[]" :value="p.id" x-model="selectedUnassignedIds" class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4">
                                                            </td>
                                                            <td class="px-5 py-3">
                                                                <div class="font-bold text-slate-800" x-text="p.mahasiswa?.nama || p.nama_lengkap"></div>
                                                                <div class="text-[12px] text-slate-500 font-medium mt-0.5" x-text="p.mahasiswa?.nim || p.nim"></div>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template x-if="filteredUnassignedStudents.length === 0">
                                                        <tr>
                                                            <td colspan="2" class="px-5 py-10 text-center">
                                                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                                <p class="text-slate-500 text-[13px] font-medium" x-text="drawerSearch ? 'Tidak ditemukan.' : 'Semua pendaftar sudah dialokasikan.'"></p>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>

                                <!-- Tab 2: Assigned -->
                                <div x-show="drawerTab === 'assigned'" style="display: none;" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                    <form action="{{ route('banksoal.pendaftaran.alokasi-sesi.remove') }}" method="POST" id="formRemove">
                                        @csrf
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="font-bold text-slate-700 text-[13px]">Daftar peserta di sesi ini:</h3>
                                            <button type="button" @click="document.getElementById('formRemove').submit()" class="bg-white text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed border border-red-200 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors flex items-center gap-2" :disabled="selectedAssignedIds.length === 0">
                                                Keluarkan (<span x-text="selectedAssignedIds.length"></span>)
                                            </button>
                                        </div>

                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                                            <table class="w-full text-left text-sm">
                                                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                                    <tr>
                                                        <th class="px-5 py-3.5 w-12 text-center border-r border-slate-100">
                                                            <input type="checkbox" x-model="checkAllAssigned" @change="toggleAllAssigned()" class="rounded border-red-300 text-red-600 focus:ring-red-500 w-4 h-4">
                                                        </th>
                                                        <th class="px-5 py-3.5">Nama & NIM Mahasiswa</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    <template x-for="p in assignedStudents" :key="p.id">
                                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                                            <td class="px-5 py-3 text-center border-r border-slate-100">
                                                                <input type="checkbox" name="pendaftar_ids[]" :value="p.id" x-model="selectedAssignedIds" class="rounded border-red-300 text-red-600 focus:ring-red-500 w-4 h-4">
                                                            </td>
                                                            <td class="px-5 py-3">
                                                                <div class="font-bold text-slate-800" x-text="p.mahasiswa?.nama || p.nama_lengkap"></div>
                                                                <div class="text-[12px] text-slate-500 font-medium mt-0.5" x-text="p.mahasiswa?.nim || p.nim"></div>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template x-if="assignedStudents.length === 0">
                                                        <tr>
                                                            <td colspan="2" class="px-5 py-10 text-center">
                                                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                                <p class="text-slate-400 text-[13px] font-medium">Belum ada peserta di sesi ini.</p>
                                                                <p class="text-slate-400 text-[11px] mt-1">Buka tab "Belum Dialokasi" untuk menambahkan.</p>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>

                            </div>
                </div>
        </div>

        <!-- Modal Popup: Tambah/Edit Sesi Baru -->
        <div x-show="openModal" tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
            <!-- Dimmed Backdrop -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                 @click="openModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative w-full max-w-md bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 flex items-center justify-between border-b border-transparent">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Tambah Sesi Baru</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 pb-6 overflow-y-auto w-full">
                    
                    <form action="{{ route('banksoal.periode.jadwal.store') }}" method="POST" id="formTambahSesi" class="space-y-4">
                        @csrf
                        <input type="hidden" name="periode_ujian_id" value="{{ $selectedPeriodeId }}">
                        
                        @if(session('error'))
                            <div class="p-3 mb-4 rounded-xl bg-red-50 border border-red-200 text-[13px] font-medium text-red-600 flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- Box 1: Nama Sesi -->
                        <div>
                            <label class="block text-[13px] text-slate-700 mb-1.5 font-bold">Sesi Ke-</label>
                            <input type="number" name="nama_sesi" value="{{ old('nama_sesi') }}" placeholder="1" min="1" required class="w-full px-4 py-2.5 bg-white rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 placeholder-slate-400 transition-shadow {{ $errors->has('nama_sesi') ? 'border-red-500 ring-red-500/20' : 'border border-slate-300' }}">
                            @error('nama_sesi')
                                <p class="mt-1.5 text-[12px] text-red-500 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Dropdown Tanggal Ujian -->
                        <div>
                            <label class="block text-[13px] text-slate-700 mb-1.5 font-bold">Tanggal Ujian (Berdasarkan Rentang Periode)</label>
                            <div class="relative">
                                <select name="tanggal_ujian" required class="w-full appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow cursor-pointer">
                                    <option value="">Pilih Tanggal Ujian...</option>
                                    @if($selectedPeriode && $selectedPeriode->tanggal_mulai_ujian && $selectedPeriode->tanggal_selesai_ujian)
                                        @php
                                            $startDate = \Carbon\Carbon::parse($selectedPeriode->tanggal_mulai_ujian);
                                            $endDate = \Carbon\Carbon::parse($selectedPeriode->tanggal_selesai_ujian);
                                            for($d = $startDate; $d->lte($endDate); $d->addDay()) {
                                                echo '<option value="' . $d->format('Y-m-d') . '"' . (old('tanggal_ujian') == $d->format('Y-m-d') ? ' selected' : '') . '>' . $d->translatedFormat('d F Y') . '</option>';
                                            }
                                        @endphp
                                    @endif
                                </select>
                            </div>
                            @error('tanggal_ujian')
                                <p class="mt-1.5 text-[12px] text-red-500 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Box 2 & 3: Waktu Mulai & Selesai -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] text-slate-700 mb-1.5 font-bold">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-[13px] text-slate-700 mb-1.5 font-bold">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai') }}" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow">
                            </div>
                        </div>

                        <!-- Box 4: Kapasitas -->
                        <div>
                            <label class="block text-[13px] text-slate-700 mb-1.5 font-bold">Kapasitas Maksimal</label>
                            <input type="number" name="kuota" value="{{ old('kuota') }}" placeholder="50" min="1" step="1" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 placeholder-slate-400 transition-shadow">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex flex-col sm:flex-row justify-end gap-2">
                    <button type="button" @click="openModal = false" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-800 shadow-sm rounded-xl focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formTambahSesi').submit()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 shadow-sm rounded-xl focus:outline-none transition-colors">
                        Simpan Sesi
                    </button>
                </div>
            </div>

        </div>

        <!-- Modal Popup: Konfirmasi Hapus Sesi -->
        <div x-show="deleteConfirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;">
            <div x-show="deleteConfirmModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="closeDeleteConfirm()">
            </div>

            <div x-show="deleteConfirmModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden max-h-full">
                
                <div class="px-6 pt-6 pb-4 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-[17px] font-extrabold text-slate-800 tracking-tight mb-2">Hapus Sesi Ujian?</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed">
                        Apakah Anda yakin ingin menghapus sesi ini? Semua alokasi mahasiswa di dalamnya akan dicabut.
                    </p>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center gap-3">
                    <button type="button" @click="closeDeleteConfirm()" class="flex-1 px-4 py-2.5 text-[13px] font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm rounded-xl focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="submitDelete()" class="flex-1 px-4 py-2.5 text-[13px] font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm rounded-xl focus:outline-none transition-colors flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Sesi
                    </button>
                </div>
            </div>
        </div>

    </div> <!-- end x-data -->

    <script>
        document.querySelectorAll('[data-progress]').forEach((bar) => {
            bar.style.width = `${Number(bar.getAttribute('data-progress') || 0)}%`;
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('alokasiSesiApp', () => ({
                openModal: false,
                openDrawer: false,
                deleteConfirmModal: false,
                jadwalToDeleteId: null,
                drawerTab: 'unassigned', // unassigned | assigned
                selectedJadwal: null,
                jadwals: [],
                pendaftars: [],
                
                checkAllUnassigned: false,
                checkAllAssigned: false,
                selectedUnassignedIds: [],
                selectedAssignedIds: [],
                drawerSearch: '',
                
                init() {
                    const root = document.getElementById('alokasiSesiRoot');
                    this.openModal = root?.dataset.openModal === '1';
                    this.jadwals = JSON.parse(root?.dataset.jadwals || '[]');
                    this.pendaftars = JSON.parse(root?.dataset.pendaftars || '[]');
                },

                get unassignedStudents() {
                    return this.pendaftars.filter(p => !p.jadwal_ujian_id);
                },

                get filteredUnassignedStudents() {
                    const q = this.drawerSearch.trim().toLowerCase();
                    const unassigned = this.pendaftars.filter(p => !p.jadwal_ujian_id);
                    if (!q) return unassigned;
                    return unassigned.filter(p => {
                        const nama = (p.mahasiswa?.nama || p.nama_lengkap || '').toLowerCase();
                        const nim = (p.mahasiswa?.nim || p.nim || '').toLowerCase();
                        return nama.includes(q) || nim.includes(q);
                    });
                },
                
                get assignedStudents() {
                    if(!this.selectedJadwal) return [];
                    return this.pendaftars.filter(p => parseInt(p.jadwal_ujian_id) === parseInt(this.selectedJadwal.id));
                },

                openJadwalDrawer(jadwalId) {
                    this.selectedJadwal = this.jadwals.find(j => parseInt(j.id) === parseInt(jadwalId));
                    this.drawerTab = 'unassigned';
                    this.selectedUnassignedIds = [];
                    this.selectedAssignedIds = [];
                    this.checkAllUnassigned = false;
                    this.checkAllAssigned = false;
                    this.drawerSearch = '';
                    this.openDrawer = true;
                    document.body.style.overflow = 'hidden';
                },

                closeDrawer() {
                    this.openDrawer = false;
                    this.drawerSearch = '';
                    setTimeout(() => {
                        this.selectedJadwal = null;
                        document.body.style.overflow = '';
                    }, 300);
                },

                openDeleteConfirm(id) {
                    this.jadwalToDeleteId = id;
                    this.deleteConfirmModal = true;
                },

                closeDeleteConfirm() {
                    this.deleteConfirmModal = false;
                    setTimeout(() => {
                        this.jadwalToDeleteId = null;
                    }, 300);
                },

                submitDelete() {
                    if (this.jadwalToDeleteId) {
                        const form = document.getElementById('form-delete-' + this.jadwalToDeleteId);
                        if(form) form.submit();
                    }
                },

                toggleAllUnassigned() {
                    if (this.checkAllUnassigned) {
                        this.selectedUnassignedIds = this.unassignedStudents.map(p => p.id.toString());
                    } else {
                        this.selectedUnassignedIds = [];
                    }
                },

                toggleAllAssigned() {
                    if (this.checkAllAssigned) {
                        this.selectedAssignedIds = this.assignedStudents.map(p => p.id.toString());
                    } else {
                        this.selectedAssignedIds = [];
                    }
                },

                formatTime(timeStr) {
                    if(!timeStr) return '';
                    // timeStr is usually "HH:MM:SS" or "HH:MM"
                    return timeStr.substring(0, 5);
                }
            }));
        });
    </script>

    <script>
    // Validasi client-side: waktu_selesai minimal 100 menit setelah waktu_mulai
    document.addEventListener('DOMContentLoaded', () => {
        const mulaiEl   = document.getElementById('as_waktu_mulai');
        const selesaiEl = document.getElementById('as_waktu_selesai');
        if (!mulaiEl || !selesaiEl) return;

        function updateMinSelesai() {
            if (!mulaiEl.value) return;
            const [h, m] = mulaiEl.value.split(':').map(Number);
            const total  = h * 60 + m + 100;
            const minH   = String(Math.floor(total / 60) % 24).padStart(2, '0');
            const minM   = String(total % 60).padStart(2, '0');
            selesaiEl.min = minH + ':' + minM;
            if (selesaiEl.value && selesaiEl.value < selesaiEl.min) {
                selesaiEl.value = selesaiEl.min;
            }
        }

        mulaiEl.addEventListener('change', updateMinSelesai);
        mulaiEl.addEventListener('input',  updateMinSelesai);
    });
    </script>

</x-banksoal::layouts.admin>