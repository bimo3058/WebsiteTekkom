<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Dashboard Dosen" subtitle="Ringkasan performa bank soal, RPS, dan distribusi soal aktif." />

    {{-- Alert RPS --}}
    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-3 sm:p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Peringatan: Anda belum mengupload RPS Mata Kuliah {CS-201}.</p>
                    <p class="text-xs text-amber-800">Segera upload RPS sebelum tenggat waktu selesai.</p>
                </div>
            </div>
            <a href="{{ route('banksoal.rps.dosen.index') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700 whitespace-nowrap">
                Upload Sekarang
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="mb-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
        <x-banksoal::ui.stat-card label="Total Soal"   :value="128" icon="fa-layer-group"      tone="blue"  />
        <x-banksoal::ui.stat-card label="Approved"     :value="75"  icon="fa-circle-check"     tone="green" />
        <x-banksoal::ui.stat-card label="Perlu Review" :value="28"  icon="fa-clock-rotate-left" tone="amber" />
        <x-banksoal::ui.stat-card label="Ditolak"      :value="10"  icon="fa-circle-xmark"     tone="red"   />
    </div>

    {{-- Row: Analytics / Academic Period / Lecturer Profile --}}
    <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-3">

        {{-- Question Analytics --}}
        <x-banksoal::ui.panel title="Question Analytics" subtitle="Komposisi status soal saat ini" padding="p-3">
            <div class="flex items-center gap-3">
                {{-- Donut --}}
                <div class="relative shrink-0" style="width:80px;height:80px">
                    <svg width="80" height="80" viewBox="0 0 80 80" id="donutChart"></svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <strong class="text-sm font-bold text-slate-900">128</strong>
                        <span class="text-[9px] uppercase tracking-wider text-slate-500">Total</span>
                    </div>
                </div>
                {{-- Legend --}}
                <div class="flex-1 space-y-0.5 text-[11px]">
                    <div class="flex items-center gap-1 text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Approved</span><span>75</span>
                    </div>
                    <div class="flex items-center gap-1 text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Review</span><span>28</span>
                    </div>
                    <div class="flex items-center gap-1 text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Pending</span><span>15</span>
                    </div>
                    <div class="flex items-center gap-1 text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Rejected</span><span>10</span>
                    </div>
                </div>
            </div>
        </x-banksoal::ui.panel>

        {{-- Academic Period --}}
        <x-banksoal::ui.panel title="Academic Period" subtitle="Informasi semester dan MK aktif" padding="p-3">
            <div class="text-center">
                <span class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">RPS: NOT UPLOADED</span>
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Academic Year</p>
                <p class="text-sm font-bold text-slate-900">Genap 2025/2026</p>
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Active Courses</p>
                <div class="mt-1.5 flex justify-center gap-1.5">
                    <span class="rounded-lg bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">CS-201</span>
                    <span class="rounded-lg bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">CS-304</span>
                </div>
            </div>
        </x-banksoal::ui.panel>

        {{-- Lecturer Profile --}}
        <x-banksoal::ui.panel title="Lecturer Profile" subtitle="Ringkasan profil dosen" padding="p-3">
            <div class="text-center">
                <div class="mx-auto mb-1.5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <p class="text-xs font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-600">{{ auth()->user()->lecturer?->employee_number ?? auth()->user()->email }}</p>
                <p class="text-[11px] text-slate-500">{{ auth()->user()->lecturer?->department ?? 'Teknik Komputer' }}</p>
                <button type="button" class="mt-2 inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                    Update Data
                </button>
            </div>
        </x-banksoal::ui.panel>
    </div>

    {{-- Row: Charts --}}
    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
        <x-banksoal::ui.panel title="Question Distribution per CPL" subtitle="Based on Learning Outcomes (CPL)" padding="p-4">
            <x-slot:actions>
                <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
            </x-slot:actions>
            <div class="h-64 flex items-end gap-4" id="cplChart"></div>
        </x-banksoal::ui.panel>

        <x-banksoal::ui.panel title="Question Count per MK" subtitle="Distribution across active courses" padding="p-4">
            <x-slot:actions>
                <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
            </x-slot:actions>
            <div class="h-64 flex items-end gap-4" id="mkChart"></div>
        </x-banksoal::ui.panel>
    </div>

    <script src="{{ asset('modules/banksoal/js/Banksoal/components/DosenDashboard.js') }}"></script>
    @include('banksoal::partials.dosen.layout-scripts')
</x-banksoal::layouts.dosen-admin>