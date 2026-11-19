@props(['search'=>'','praktikum'=>null,'exportUrl'=>null])
<div class="mp-card-header" style="background:#fff;justify-content:space-between;gap:16px">
    <span class="mp-card-title">Praktikan Terdaftar</span>
    <form method="GET" x-data="{kelompok:@js((string)request('kelompok','')),shift:@js((string)request('shift','')),timer:null}"
        x-init="$watch('kelompok',()=>{clearTimeout(timer);timer=setTimeout(()=>$el.requestSubmit(),600)});$watch('shift',()=>{clearTimeout(timer);timer=setTimeout(()=>$el.requestSubmit(),600)})"
        style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:0">
        @if($praktikum)<input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">@endif
        <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
        <div style="position:relative"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg><input class="mp-input" type="search" name="search" value="{{ $search }}" placeholder="Cari nama / NIM..." aria-label="Cari nama atau NIM praktikan" style="padding-left:32px;width:210px;font-size:13px" @change="$el.form.requestSubmit()"></div>
        <input type="hidden" name="kelompok" x-model="kelompok"><input type="hidden" name="shift" x-model="shift">
        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="kelompok" label="Semua Kelompok" search-placeholder="Cari kelompok..." />
        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="shift" label="Semua Shift" search-placeholder="Cari shift..." />
        @if($exportUrl)<a href="{{ $exportUrl }}" class="mp-btn secondary sm" style="text-decoration:none"><x-eoffice::manajemen-praktikum.ui.icon name="upload" />Ekspor CSV</a>@endif
    </form>
</div>
