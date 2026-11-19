<x-dynamic-component :component="$reviewer?'eoffice::manajemen-ruangan.layout':'eoffice::manajemen-praktikum.layout'" pageTitle="Peminjaman Ruang Praktikum">
    <x-eoffice::manajemen-praktikum.ui.schedule-styles />
    @php
        $base=$reviewer?'eoffice.peminjaman.admin.praktikum.':'eoffice.manprak.cerc.scheduling.';
        $statuses=['pending'=>'Menunggu Persetujuan','approved'=>'Disetujui','rejected'=>'Ditolak','partial'=>'Sebagian Diputuskan','cancelled'=>'Dibatalkan'];
    @endphp
    <div class="mp-page-header">
        <div><h1 class="mp-page-title">Peminjaman Ruang Praktikum</h1><p class="mp-page-sub">{{ $reviewer?'Tinjau pengajuan jadwal dan pantau pemakaian ruang praktikum.':'Cek ketersediaan ruang, susun jadwal tiap shift, dan pantau status pengajuan praktikum.' }}</p></div>
        <div class="mp-schedule-actions"><a class="mp-btn secondary md" href="#daftar-pengajuan">{{ $tab==='schedules'?'Lihat Jadwal Disetujui':'Lihat Pengajuan' }}</a>@unless($reviewer)<a class="mp-btn primary md" href="{{ route($base.'create') }}"><x-eoffice::manajemen-praktikum.ui.icon name="calendar" /> Buat Rancangan</a>@endunless</div>
    </div>
    @include('eoffice::manajemen-praktikum.scheduling.messages')
    @include('eoffice::manajemen-praktikum.scheduling.calendar')
    <section id="daftar-pengajuan" class="mp-card mp-settings-card">
        <nav class="mp-schedule-tabs" aria-label="Peminjaman praktikum">
            @foreach(($reviewer?['requests'=>'Pengajuan','schedules'=>'Jadwal Disetujui']:['drafts'=>'Rancangan Jadwal','requests'=>'Pengajuan','schedules'=>'Jadwal Disetujui']) as $key=>$label)
                <a href="{{ route($base.'index',['tab'=>$key,'week'=>$calendar['start']->toDateString(),'calendar_room'=>$calendar['selected_room']]) }}#daftar-pengajuan" @if($tab===$key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="mp-card-header">
            <div><h2 class="mp-card-title">{{ $tab==='schedules'?'Jadwal Praktikum Disetujui':($tab==='drafts'?'Daftar Rancangan Jadwal':'Daftar Pengajuan Praktikum') }}</h2><p class="mp-settings-description">{{ $tab==='schedules'?'Pembatalan langsung melepas ruang. Reschedule sementara belum tersedia.':'Satu paket dapat memuat beberapa praktikum. Admin ruang memutuskan setiap booking pertemuan secara terpisah.' }}</p></div>
            @if($tab!=='schedules')
                <form method="GET" class="mp-schedule-list-tools">
                    <input type="hidden" name="tab" value="{{ $tab }}"><input type="hidden" name="week" value="{{ $calendar['start']->toDateString() }}"><input type="hidden" name="calendar_room" value="{{ $calendar['selected_room'] }}">
                    <label class="mp-schedule-search"><span class="sr-only">Cari nama praktikum</span><input type="search" class="mp-input" name="q" value="{{ request('q') }}" placeholder="Cari nama praktikum..." maxlength="100"></label>
                    @if($tab==='requests')<x-eoffice::manajemen-praktikum.ui.select name="status" :selected="request('status',$reviewer?'pending':'')" :options="array_merge([['value'=>'','label'=>'Semua Status']],collect($statuses)->map(fn($label,$value)=>['value'=>$value,'label'=>$label])->values()->all())" onChange="$event.target.form.requestSubmit()" />@endif
                    <button class="mp-btn secondary sm" type="submit">Cari</button>
                </form>
            @endif
        </div>
        @if($tab!=='schedules')
            @if($packages->isEmpty())
                <div class="mp-schedule-empty"><x-eoffice::manajemen-praktikum.ui.icon name="calendar" /><h3>{{ request()->filled('q') || request()->filled('status')?'Data tidak ditemukan.':($tab==='drafts'?'Belum Ada Rancangan':'Belum Ada Pengajuan') }}</h3><p>{{ $reviewer?'Pengajuan dari Koordinator CERC akan muncul di sini untuk ditinjau.':'Mulai dengan membuat rancangan jadwal, lalu pilih ruang dan waktu untuk setiap shift praktikum.' }}</p>@unless($reviewer)<a class="mp-btn primary md" href="{{ route($base.'create') }}">Buat Rancangan</a>@endunless</div>
            @else
                <div x-data="{listOpen:false,practicumItems:[],listOpener:null,closeList(){this.listOpen=false;this.$nextTick(()=>this.listOpener?.focus())}}" @keydown.escape.window="if(listOpen)closeList()">
                    <div class="mp-data-scroll"><table class="mp-table mp-settings-table mp-schedule-packages"><thead><tr>
                        <th class="mp-th mp-settings-no">NO</th><th class="mp-th">PRAKTIKUM</th><th class="mp-th">PERIODE / RENTANG JADWAL</th><th class="mp-th">RINGKASAN JADWAL</th>@if($tab==='requests')<th class="mp-th">STATUS</th>@endif<th class="mp-th">AKSI</th>
                    </tr></thead><tbody>
                        @foreach($packages as $p)
                            @php $summary=$p->schedule_summary; @endphp
                            <tr class="mp-tr">
                                <td class="mp-settings-no">{{ $packages->firstItem()+$loop->index }}</td>
                                <td>
                                    <div class="mp-schedule-practicums">
                                        @forelse(array_slice($summary['practicums'],0,2) as $practicum)<div class="mp-settings-name">{{ $practicum['name'] }}</div>
                                        @empty<div class="mp-settings-email">Belum ada praktikum dipilih.</div>@endforelse
                                        @if($summary['count']>2)<button type="button" class="mp-schedule-more" aria-haspopup="dialog" @click="practicumItems=@js($summary['practicums']);listOpener=$event.currentTarget;listOpen=true;$nextTick(()=>$refs.practicumListClose.focus())">+{{ $summary['count']-2 }} lainnya</button>@endif
                                    </div>
                                    <div class="mp-settings-email">Dibuat {{ $p->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td>
                                    @forelse($summary['periods'] as $period)<div class="mp-settings-name">{{ $period }}</div>
                                    @empty<div class="mp-settings-name">{{ $p->period->semester }} {{ $p->period->tahun_ajaran }}</div>@endforelse
                                    <div class="mp-settings-email">{{ $p->meetings_min_date?\Illuminate\Support\Carbon::parse($p->meetings_min_date)->format('d M Y').' – '.\Illuminate\Support\Carbon::parse($p->meetings_max_date)->format('d M Y'):'Jadwal belum disusun' }}</div>
                                </td>
                                <td><div class="mp-schedule-counts"><span class="mp-badge neutral sm">{{ $summary['count'] }} praktikum</span><span class="mp-badge neutral sm">{{ $summary['shifts'] }} shift</span><span class="mp-badge neutral sm">{{ $summary['sessions'] }} sesi</span></div></td>
                                @if($tab==='requests')<td><x-eoffice::manajemen-praktikum.ui.schedule-status :status="$p->status" /></td>@endif
                                <td><a class="mp-btn secondary sm" href="{{ route($base.'show',$p->id) }}">Lihat Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody></table></div>
                    @include('eoffice::manajemen-praktikum.scheduling.practicum-list-dialog')
                </div>
                <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$packages" :options="[10]" fragment="daftar-pengajuan" />
            @endif
        @else
            @unless($reviewer)
                <form method="POST" action="{{ route($base.'amendment') }}" x-data="{confirmOpen:false,confirmed:false,submitting:false,selected:[]}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
                    @csrf
            @endunless
            <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:950px"><thead><tr>@unless($reviewer)<th class="mp-th mp-settings-no">PILIH</th>@endunless<th class="mp-th">PRAKTIKUM / MODUL</th><th class="mp-th">SHIFT</th><th class="mp-th">TANGGAL PEMAKAIAN</th><th class="mp-th">WAKTU</th><th class="mp-th">RUANG</th></tr></thead><tbody>
                @forelse($schedules as $m)<tr class="mp-tr">
                    @unless($reviewer)<td class="mp-settings-no"><input type="checkbox" name="meetings[]" x-model="selected" value="{{ $m->id }}" aria-label="Pilih {{ $m->module_name }} shift {{ $m->shift }}" @disabled(!$m->date->copy()->setTimeFromTimeString($m->starts_at)->isFuture())></td>@endunless
                    <td><div class="mp-settings-name">{{ $m->praktikum?->nama }}</div><div class="mp-settings-email">{{ $m->module_name }} · {{ $m->request->code }}</div></td><td style="text-align:center">{{ $m->shift }}</td><td>{{ $m->date->locale('id')->translatedFormat('D, d M Y') }}</td><td style="white-space:nowrap">{{ substr($m->starts_at,0,5) }}–{{ substr($m->ends_at,0,5) }} WIB</td><td>{{ $m->room?->nama }}</td>
                </tr>@empty<tr><td colspan="{{ $reviewer?5:6 }}" class="mp-settings-empty">Belum ada jadwal disetujui.</td></tr>@endforelse
            </tbody></table></div>
            @unless($reviewer)
                <div class="mp-settings-body"><div class="mp-schedule-controls"><input type="hidden" name="kind" value="cancel"><div><label class="mp-settings-label" for="amend-reason">Alasan</label><input id="amend-reason" class="mp-input" name="reason" required maxlength="2000" placeholder="Alasan pembatalan"></div></div></div>
                <div class="mp-settings-footer"><p class="mp-settings-description"><span x-text="selected.length"></span> pertemuan dipilih. Pembatalan langsung melepas ruang.</p><button class="mp-btn primary md" :disabled="submitting||selected.length===0">Batalkan Booking</button></div>
                <x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Batalkan Booking" message="Batalkan seluruh pertemuan terpilih? Ruang langsung dilepas tanpa menunggu persetujuan admin." buttonLabel="Ya, Batalkan" />
                </form>
            @endunless
            <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$schedules" :options="[20]" fragment="daftar-pengajuan" />
        @endif
    </section>
</x-dynamic-component>
