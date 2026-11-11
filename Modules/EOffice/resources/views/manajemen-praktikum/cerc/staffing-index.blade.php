<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Koordinator & Asisten">
    <x-eoffice::manajemen-praktikum.ui.staffing-styles />
    <x-eoffice::manajemen-praktikum.ui.practicum-list-frame />
    <div class="mp-practicum-list-frame">
    <div class="mp-page-header">
        <div><h1 class="mp-page-title">Kelola Koordinator & Asisten</h1><p class="mp-page-sub">Pilih praktikum aktif untuk mengatur koordinator dan asisten praktikum.</p></div>
    </div>
    <section class="mp-card mp-settings-card mp-staffing-list">
        <div class="mp-card-header">
            <h2 class="mp-card-title">Daftar Praktikum Aktif</h2>
            <form method="GET" role="search" class="mp-staffing-list-search"><x-eoffice::manajemen-praktikum.ui.icon name="search" /><label class="sr-only" for="staffing-search">Cari praktikum</label><input id="staffing-search" class="mp-input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari praktikum..." maxlength="100"><button type="submit" class="sr-only">Cari</button></form>
        </div>
        <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:850px;table-layout:fixed">
            <thead><tr><th class="mp-th">NAMA PRAKTIKUM</th><th class="mp-th">DOSEN PENGAMPU</th><th class="mp-th">KOORDINATOR PRAKTIKUM</th><th class="mp-th" style="text-align:center">AKSI</th></tr></thead>
            <tbody>@forelse($praktikums as $prak)<tr class="mp-tr">
                <td style="white-space:normal"><div class="text-[13px] font-semibold text-[#353849]" style="white-space:normal;overflow-wrap:anywhere;line-height:1.45">{{ $prak->nama }}</div><div class="text-[11px] text-[#666D80] mt-1">{{ $prak->tahun_ajaran }} / Semester {{ $prak->semester }}</div></td>
                <td><div class="flex flex-col gap-1.5 w-full items-start">@forelse($prak->dosens as $dosen)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$dosen" :wrap="true" />@empty<span class="mp-badge error sm">Belum ditunjuk</span>@endforelse</div></td>
                <td>@if($prak->koordinator)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$prak->koordinator" :wrap="true" />@else<span class="mp-badge error sm">Belum ditunjuk</span>@endif</td>
                <td style="text-align:center"><a class="mp-btn secondary sm" href="{{ route('eoffice.manprak.cerc.kelola-role.index',['praktikum_id'=>$prak->id]) }}">Lihat Detail</a></td>
            </tr>@empty<tr><td colspan="4" class="mp-settings-empty">{{ request()->filled('q')?'Data tidak ditemukan.':'Belum ada praktikum aktif.' }}</td></tr>@endforelse</tbody>
        </table></div>
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$praktikums" :options="[10,20,50]" />
    </section>
    </div>
</x-eoffice::manajemen-praktikum.layout>
