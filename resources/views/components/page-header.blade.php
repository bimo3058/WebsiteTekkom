@props(['title', 'badge' => null, 'subtitle' => null])
{{--
    Header baku halaman "global" (di luar Modules/), meniru spesifikasi visual
    <x-manajemenmahasiswa::ui.page-header> (judul 22px/700/-0.02em, badge pill,
    subtitle 12px) TANPA memanggilnya — dibuat mandiri di sini karena CV Builder
    (dan halaman profil global lain di masa depan) tidak boleh bergantung ke
    Modules/ManajemenMahasiswa.

    Tidak pakai @push('styles') karena resources/views/layouts/app.blade.php
    (layout global) tidak punya @stack('styles') — beda dari layout MM. Semua
    gaya ditulis lewat class Tailwind arbitrary-value (pola text-[Npx] yang
    sudah dipakai luas di codebase ini) supaya tidak bergantung pada stack apa pun.
--}}
<div {{ $attributes->class(['flex items-center justify-between gap-4 flex-wrap']) }}>
    <div class="flex items-center gap-3 min-w-0">
        @isset($leading)
            <div class="flex items-center shrink-0">{{ $leading }}</div>
        @endisset
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-[3px]">
                <h1 class="text-[22px] max-md:text-[18px] font-bold leading-[1.2] tracking-[-0.02em] m-0" style="color: var(--c-fg, #0D0D12);">{{ $title }}</h1>
                @if($badge)
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full tracking-[0.03em] whitespace-nowrap" style="color: var(--c-primary, #0B266E); background: rgba(11,38,110,0.09); border: 1px solid rgba(11,38,110,0.18);">{{ $badge }}</span>
                @endif
            </div>
            @if($subtitle)
                <p class="text-[12px] leading-[1.5] m-0" style="color: var(--c-fg-muted, #666D80);">{{ $subtitle }}</p>
            @elseif($slot->isNotEmpty())
                <p class="text-[12px] leading-[1.5] m-0" style="color: var(--c-fg-muted, #666D80);">{{ $slot }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2 flex-wrap shrink-0">{{ $actions }}</div>
    @endisset
</div>
