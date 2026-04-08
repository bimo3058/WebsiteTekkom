<x-banksoal::layouts.admin>
    <div class="w-full">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pendaftar</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data mahasiswa yang mendaftar ujian pada periode aktif.</p>
        </div>

        <!-- Toolbar: Period Select, Search, Filter, Add Button -->
        <form method="GET" action="{{ route('banksoal.pendaftaran.index') }}" id="filter-form">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4 mb-6">
                <!-- Left: Period Dropdown -->
                <div class="w-full lg:w-72 relative">
                    <select
                        name="periode_id"
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium cursor-pointer shadow-sm transition-all"
                    >
                        <option value="">Pilih Periode Ujian...</option>
                        @foreach ($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <!-- Search -->
                    <div class="relative w-full sm:w-56">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari pendaftar..."
                            {{ !request('periode_id') ? 'disabled' : '' }}
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-700 placeholder-slate-400 transition-all {{ !request('periode_id') ? 'bg-slate-50 cursor-not-allowed text-slate-400' : 'bg-white' }}"
                        >
                    </div>

                    <!-- Filter Status -->
                    <div class="relative w-full sm:w-40">
                        <select
                            name="status"
                            onchange="document.getElementById('filter-form').submit()"
                            {{ !request('periode_id') ? 'disabled' : '' }}
                            class="w-full appearance-none pl-4 pr-9 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all {{ !request('periode_id') ? 'bg-slate-50 cursor-not-allowed text-slate-400' : 'bg-white text-slate-700 cursor-pointer' }}"
                        >
                            <option value="">Semua Status</option>
                            <option value="pending"  {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved'  ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Add Button (opens modal via Alpine) -->
                    <button
                        type="button"
                        onclick="document.getElementById('modal-tambah-manual').classList.remove('hidden')"
                        {{ !request('periode_id') ? 'disabled title=Pilih periode terlebih dahulu' : '' }}
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition-all focus:ring-2 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Manual
                    </button>

                    {{-- Search submit (hidden button for pressing Enter) --}}
                    @if(request('periode_id'))
                        <button type="submit" class="hidden">Cari</button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Info Banner (If no period selected) -->
        @if (!request('periode_id'))
            <div class="mb-6 bg-blue-50/50 border border-blue-100 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-800">
                    Data pendaftar akan tampil dan tabel akan terisi setelah Anda memilih Periode Ujian pada dropdown di atas.
                </p>
            </div>
        @endif

        <!-- Include Table Component -->
        @include('banksoal::pendaftaran.partials.table')

        <!-- Include Modal Components -->
        @include('banksoal::pendaftaran.partials.modal-manual')
        @include('banksoal::pendaftaran.partials.modal-detail')
    </div>
</x-banksoal::layouts.admin>
