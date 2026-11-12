<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pelaksanaan Praktikum">
    <x-eoffice::manajemen-praktikum.ui.schedule-styles />
    <div class="mp-page-header"><div><h1 class="mp-page-title">Periode Pelaksanaan</h1><p class="mp-page-sub">Batas pelaksanaan seluruh praktikum per semester, termasuk pergeseran minggu akibat hari libur.</p></div></div>
    @include('eoffice::manajemen-praktikum.scheduling.messages')
    @foreach($periods->push(null) as $period)
        <section class="mp-card mp-settings-card"><div class="mp-card-header"><div><h2 class="mp-card-title">{{ $period?$period->semester.' '.$period->tahun_ajaran:'Tambah Periode' }}</h2><p class="mp-settings-description">Perubahan batas periode tidak menggeser booking resmi otomatis. Pengajuan yang menunggu perlu ditinjau ulang.</p></div></div>
            <form method="POST" action="{{ $period?route('eoffice.manprak.admin.schedule-periods.update',$period->id):route('eoffice.manprak.admin.schedule-periods.store') }}" x-data="{submitting:false}" @submit="if(submitting){$event.preventDefault()}else{submitting=true}">@csrf @if($period)@method('PUT')@endif<input type="hidden" name="version" value="{{ $period?->version??0 }}">
                <div class="mp-settings-body"><div class="mp-schedule-controls"><div><label class="mp-settings-label">Tahun Ajaran</label><input class="mp-input" name="tahun_ajaran" value="{{ $period?->tahun_ajaran }}" placeholder="2026/2027" pattern="[0-9]{4}/[0-9]{4}" required></div><div><label class="mp-settings-label">Semester</label><x-eoffice::manajemen-praktikum.ui.select name="semester" :options="['Ganjil'=>'Ganjil','Genap'=>'Genap']" :selected="$period?->semester??'Ganjil'" /></div><div><label class="mp-settings-label">Tanggal Mulai</label><input class="mp-input" name="starts_on" type="date" value="{{ $period?->starts_on?->toDateString() }}" required></div><div><label class="mp-settings-label">Tanggal Akhir</label><input class="mp-input" name="ends_on" type="date" value="{{ $period?->ends_on?->toDateString() }}" required></div></div></div>
                <div class="mp-settings-footer"><p class="mp-settings-description">Tanggal akhir harus setelah tanggal mulai. Periode maksimal 366 hari.</p><button class="mp-btn primary md" :disabled="submitting">Simpan Periode</button></div>
            </form>
        </section>
    @endforeach
</x-eoffice::manajemen-praktikum.layout>
