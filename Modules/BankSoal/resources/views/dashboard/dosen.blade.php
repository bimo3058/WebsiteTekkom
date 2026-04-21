<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Dashboard Dosen" subtitle="Ringkasan performa bank soal, RPS, dan distribusi soal aktif." />

    {{-- Alert RPS --}}
    @if(count($mkTanpaRps) > 0)
    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-3 sm:p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Peringatan: Anda belum mengupload RPS Mata Kuliah {{ implode(', ', $mkTanpaRps) }}.</p>
                    <p class="text-xs text-amber-800">Segera upload RPS sebelum Anda bisa mengelola bank soal untuk mata kuliah tersebut.</p>
                </div>
            </div>
            <a href="{{ route('banksoal.rps.dosen.index') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700 whitespace-nowrap">
                Upload Sekarang
            </a>
        </div>
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="mb-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
        <x-banksoal::ui.stat-card label="Total Soal"   :value="$totalSoal" icon="fa-layer-group"      tone="blue"  />
        <x-banksoal::ui.stat-card label="Approved"     :value="$approved"  icon="fa-circle-check"     tone="green" />
        <x-banksoal::ui.stat-card label="Dalam Pengajuan" :value="$perluReview"  icon="fa-clock-rotate-left" tone="blue" />
        <x-banksoal::ui.stat-card label="Revisi / Ditolak" :value="$revisi + $ditolak" icon="fa-circle-xmark" tone="red" />
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
                        <strong class="text-sm font-bold text-slate-900">{{ $totalSoal }}</strong>
                        <span class="text-[9px] uppercase tracking-wider text-slate-500">Total</span>
                    </div>
                </div>
                {{-- Legend --}}
                <div class="flex-1 space-y-0.5 text-[11px]">
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Approved</span><span>{{ $approved }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Pending (Diajukan)</span><span>{{ $perluReview }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Perlu Revisi</span><span>{{ $revisi }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Rejected</span><span>{{ $ditolak }}</span>
                    </div>
                </div>
            </div>
        </x-banksoal::ui.panel>

        {{-- Academic Period --}}
        <x-banksoal::ui.panel title="Academic Period" subtitle="Informasi semester dan MK aktif" padding="p-3">
            <div class="text-center">
                @if(count($mkTanpaRps) > 0)
                    <span class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">RPS: NOT UPLOADED</span>
                @else
                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">RPS: UPLOADED</span>
                @endif
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Academic Year</p>
                <p class="text-sm font-bold text-slate-900">Semester Berjalan</p>
                <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-500">Active Courses</p>
                <div class="mt-1.5 flex justify-center gap-1.5 flex-wrap">
                    @foreach($mataKuliah as $mk)
                        <span class="rounded-lg bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">{{ $mk->kode }}</span>
                    @endforeach
                    @if($mataKuliah->isEmpty())
                        <span class="text-xs text-slate-400">Belum ada mata kuliah</span>
                    @endif
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
                <a href="/profile" class="mt-2 inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                    Lihat Profil
                </a>
            </div>
        </x-banksoal::ui.panel>
    </div>

    {{-- Row: Charts --}}
    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
        <x-banksoal::ui.panel title="Question Distribution per CPL" subtitle="Berdasarkan Capaian Pembelajaran (CPL)" padding="p-4">
            <x-slot:actions>
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
            </x-slot:actions>
            <div class="h-64 flex items-end gap-4" id="cplChart"></div>
        </x-banksoal::ui.panel>

        <x-banksoal::ui.panel title="Question Count per MK" subtitle="Distribusi seluruh bank soal dosen" padding="p-4">
            <x-slot:actions>
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Details <i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
            </x-slot:actions>
            <div class="h-64 flex items-end gap-4" id="mkChart"></div>
        </x-banksoal::ui.panel>
    </div>

    <script src="{{ asset('modules/banksoal/js/Banksoal/components/DosenDashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Data dinamis dari server
            const baseDonutData = @json($donutData);
            const baseCplData = @json($cplDist);
            const baseMkData = @json($mkDist);

            // Update menggunakan method pada DosenDashboardComponent (Global Instance)
            setTimeout(() => {
                if(typeof DosenDashboard !== 'undefined') {
                    DosenDashboard.updateDonutChart('donutChart', baseDonutData);
                    DosenDashboard.updateCplBarChart('cplChart', baseCplData);
                    DosenDashboard.updateMkBarChart('mkChart', baseMkData);
                }
            }, 100);
        });
    </script>
    @include('banksoal::partials.dosen.layout-scripts')
</x-banksoal::layouts.dosen-admin>
