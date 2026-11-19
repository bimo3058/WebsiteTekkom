@php
    $scheduleTitle = $scheduleTitle ?? ($activeRole === 'admin' ? 'Dashboard Jadwal' : 'Jadwal Saya');
    $scheduleSubtitle = $scheduleSubtitle ?? ($activeRole === 'admin'
        ? 'Lihat semua jadwal di semua periode. Pilih periode untuk memfilter.'
        : ($activeRole === 'dosen'
            ? 'Lihat semua jadwal Anda: sesi bimbingan, ujian, dan kegiatan.'
            : 'Lihat sesi bimbingan, seminar proposal, expo, dan jadwal sidang TA Anda.'));
@endphp
<div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $scheduleTitle }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $scheduleSubtitle }}</p>
    </div>
    @if($activeRole === 'admin')
    <div x-data="{ open: false }" class="relative shrink-0" @click.outside="open = false" @keydown.escape.window="open = false">
        <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="menu"
            class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#0B266E] px-4 text-sm font-semibold text-white transition-colors hover:bg-[#091958]">
            Buat Jadwal
            <x-capstone::icon name="ChevronDown" class="h-4 w-4" />
        </button>
        <div x-show="open" x-cloak class="absolute right-0 top-full z-30 mt-2 w-60 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg" role="menu">
            @foreach(['sempro' => 'SEMPRO Baru', 'expo' => 'EXPO Baru', 'ta-defense' => 'Sidang TA Baru'] as $path => $label)
            <x-capstone::feature-link href="/admin/{{ $path }}" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100">
                <x-capstone::icon name="CalendarPlus" class="h-4 w-4 text-slate-400" />{{ $label }}
            </x-capstone::feature-link>
            @endforeach
            <div class="my-1.5 border-t border-slate-100"></div>
            <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100" @click="exportCsv(false); open = false">
                <x-capstone::icon name="Download" class="h-4 w-4 text-slate-400" />Ekspor CSV terfilter
            </button>
            <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100" @click="exportCsv(true); open = false">
                <x-capstone::icon name="Download" class="h-4 w-4 text-slate-400" />Ekspor semua CSV
            </button>
        </div>
    </div>
    @elseif($activeRole === 'dosen')
    <div x-data="{ open: false }" class="relative shrink-0" @click.outside="open = false" @keydown.escape.window="open = false">
        <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="menu"
            class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#0B266E] px-4 text-sm font-semibold text-white transition-colors hover:bg-[#091958]">
            Buat Jadwal
            <x-capstone::icon name="ChevronDown" class="h-4 w-4" />
        </button>
        <div x-show="open" x-cloak class="absolute right-0 top-full z-30 mt-2 w-60 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg" role="menu">
            <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100" @click="open = false; edit()">
                <x-capstone::icon name="Plus" class="h-4 w-4 text-slate-400" />BIMBINGAN Baru
            </button>
            <div class="my-1.5 border-t border-slate-100"></div>
            <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100" @click="exportCsv(true); open = false">
                <x-capstone::icon name="Download" class="h-4 w-4 text-slate-400" />Ekspor CSV
            </button>
        </div>
    </div>
    @endif
</div>
