@php
    $grid=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumCalendarPresentation::class)->build($calendar);
    $canChooseSlot=isset($package)?(($editable??false)&&$package->kind==='new'):!($reviewer??true);
    $guardChanges=$editable??false;
    $calendarRoomOptions=array_merge([['value'=>'','label'=>'Semua Ruangan']],$calendar['rooms']->map(fn($room)=>['value'=>(string)$room->id,'label'=>$room->nama])->all());
    $pickerSlots=[];
    foreach($grid['days'] as $day)foreach($grid['rooms'] as $room)foreach($grid['hours'] as $hour){
        $cell=$grid['cells'][$day->toDateString()][$room->id][$hour];
        if($cell['selectable'] && $cell['state']!=='draft')$pickerSlots[]=['date'=>$day->toDateString(),'weekday'=>$day->dayOfWeekIso,'roomId'=>(string)$room->id,'roomName'=>$room->nama,'start'=>$cell['start'],'end'=>$cell['end']];
    }
    $pickerConfig=['slots'=>$pickerSlots,'initial'=>$calendarSeed??null,'redirectUrl'=>$canChooseSlot&&!isset($package)?route('eoffice.manprak.cerc.scheduling.create'):null];
@endphp
<x-eoffice::manajemen-praktikum.ui.schedule-calendar-script />
<section id="kalender-ruang" class="mp-card mp-settings-card mp-room-calendar" x-data="practicumCalendarPicker(@js($pickerConfig))" @pointerup.window="finishCalendarRange()" @pointercancel.window="cancelCalendarRange()" @calendar-selection-cancelled.window="cancelCalendarRange()">
    <div class="mp-card-header">
        <div><h2 class="mp-card-title">Kalender Ruangan</h2><p class="mp-settings-description">{{ $canChooseSlot?'Klik atau tarik rentang jam kosong untuk membuat rancangan jadwal mingguan shift.':'Periksa ketersediaan ruang sebelum mengajukan jadwal praktikum.' }} Jam operasional {{ $calendar['settings']['open'] }}–{{ $calendar['settings']['close'] }} WIB.</p></div>
        <form method="GET" action="{{ url()->current() }}#kalender-ruang" class="mp-calendar-filter">
            @foreach(request()->except(['week','calendar_room','page','schedule_page']) as $queryKey=>$queryValue)
                @if(is_scalar($queryValue))<input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">@endif
            @endforeach
            <input type="hidden" name="week" value="{{ $calendar['start']->toDateString() }}">
            <fieldset @if($guardChanges) :disabled="dirty" @endif>
                <legend class="sr-only">Filter ruangan kalender</legend>
                <x-eoffice::manajemen-praktikum.ui.select name="calendar_room" :options="$calendarRoomOptions" :selected="$calendar['selected_room']" onChange="$event.target.form.requestSubmit()" />
                <button class="mp-btn secondary sm" type="submit">Tampilkan</button>
            </fieldset>
        </form>
    </div>
    <div class="mp-calendar-body">
        <div class="mp-calendar-toolbar">
            <a class="mp-btn secondary sm" href="{{ request()->fullUrlWithQuery(['week'=>$calendar['start']->copy()->subWeek()->toDateString()]) }}#kalender-ruang" @if($guardChanges) :aria-disabled="dirty" @click="if(dirty)$event.preventDefault()" @endif><x-eoffice::manajemen-praktikum.ui.icon name="chevron-left" /> Minggu Lalu</a>
            <form method="GET" action="{{ url()->current() }}#kalender-ruang" class="mp-calendar-date" @if($guardChanges) @submit="if(dirty)$event.preventDefault()" @endif>
                @foreach(request()->except(['week','page','schedule_page']) as $queryKey=>$queryValue)
                    @if(is_scalar($queryValue))<input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">@endif
                @endforeach
                <span>{{ $calendar['start']->locale('id')->translatedFormat('d M Y') }} – {{ $calendar['end']->locale('id')->translatedFormat('d M Y') }}</span>
                <label class="sr-only" for="calendar-week">Pindah ke tanggal</label>
                <input id="calendar-week" type="date" name="week" value="{{ $calendar['start']->toDateString() }}" class="mp-input" @if($guardChanges) :disabled="dirty" @endif>
                <button class="mp-btn secondary sm" @if($guardChanges) :disabled="dirty" @endif>Pergi</button>
            </form>
            <div class="mp-schedule-actions"><a class="mp-btn secondary sm" href="{{ request()->fullUrlWithQuery(['week'=>today()->startOfWeek()->toDateString()]) }}#kalender-ruang" @if($guardChanges) :aria-disabled="dirty" @click="if(dirty)$event.preventDefault()" @endif>Hari Ini</a><a class="mp-btn secondary sm" href="{{ request()->fullUrlWithQuery(['week'=>$calendar['start']->copy()->addWeek()->toDateString()]) }}#kalender-ruang" @if($guardChanges) :aria-disabled="dirty" @click="if(dirty)$event.preventDefault()" @endif>Minggu Depan <x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" /></a></div>
        </div>
        <div class="mp-calendar-legend" aria-label="Keterangan kalender">
            @if($canChooseSlot)<span><i data-state="selected"></i>Pilihan Anda</span>@endif
            @foreach(['available'=>'Tersedia','pending'=>'Menunggu Persetujuan','occupied'=>'Terpakai','internal'=>'Jadwal Internal / Kuliah','draft'=>'Rancangan','closed'=>'Libur / Tutup','conflict'=>'Rancangan Bentrok'] as $state=>$label)<span><i data-state="{{ $state }}"></i>{{ $label }}</span>@endforeach
        </div>
        @if($guardChanges)<p class="mp-settings-description" x-show="dirty" x-cloak role="status">Simpan rancangan sebelum mengganti minggu atau filter ruangan.</p>@endif
        <p class="mp-schedule-field-error" x-show="selectionError" x-text="selectionError" x-cloak role="alert"></p>
        @if($grid['rooms']->isEmpty())
            <div class="mp-schedule-empty"><x-eoffice::manajemen-praktikum.ui.icon name="calendar" /><h3>Belum Ada Ruangan</h3><p>Ruangan aktif akan tampil setelah ditambahkan oleh admin ruang.</p></div>
        @else
            <div class="mp-calendar-scroll" tabindex="0" aria-label="Kalender ketersediaan ruang mingguan" @pointerleave="hoveredColumn=''">
                <table class="mp-calendar-table" style="min-width:{{ max(900,72+7*$grid['rooms']->count()*115) }}px">
                    <thead><tr><th rowspan="2" class="mp-calendar-time" scope="col">JAM</th>
                        @foreach($grid['days'] as $day)<th scope="colgroup" colspan="{{ $grid['rooms']->count() }}" @if($day->isToday()) aria-current="date" data-calendar-today @endif><strong>{{ $day->locale('id')->translatedFormat('l') }}</strong><span>{{ $day->format('d/m') }}</span></th>@endforeach
                    </tr><tr>@foreach($grid['days'] as $day)@foreach($grid['rooms'] as $room)<th scope="col" title="{{ $room->nama }} · Kapasitas {{ $room->kapasitas }} orang">{{ $room->nama }}</th>@endforeach @endforeach</tr></thead>
                    <tbody>@foreach($grid['hours'] as $hour)<tr><th scope="row" class="mp-calendar-time">{{ sprintf('%02d:00',$hour) }}</th>
                        @foreach($grid['days'] as $day)@foreach($grid['rooms'] as $room)
                            @php $cell=$grid['cells'][$day->toDateString()][$room->id][$hour]; @endphp
                            @if(!$cell['skip'])
                                @php $slotData=['date'=>$day->toDateString(),'weekday'=>$day->dayOfWeekIso,'roomId'=>(string)$room->id,'roomName'=>$room->nama,'start'=>$cell['start'],'end'=>$cell['end']]; @endphp
                                <td rowspan="{{ $cell['rowspan'] }}" data-state="{{ $cell['state'] }}" data-pickable="{{ $canChooseSlot && $cell['selectable'] && $cell['state']!=='draft' ? 'true' : 'false' }}" :class="calendarRangeClasses(@js($slotData))" @pointerenter="hoveredColumn=@js($day->toDateString().'|'.$room->id);@if($canChooseSlot)extendCalendarRange(@js($slotData))@endif">
                                    @if($canChooseSlot && $cell['selectable'] && $cell['state']!=='draft')<button type="button" class="mp-calendar-slot" @pointerdown="startCalendarRange(@js($slotData),$event)" @click.prevent="if($event.detail===0)pickCalendarRange(@js($slotData))" @if(isset($package)) :disabled="visiblePlans.length===0" @endif aria-label="Pilih {{ $room->nama }}, {{ $day->format('d/m/Y') }}, {{ $cell['start'] }} sampai {{ $cell['end'] }}">
                                    @else<div class="mp-calendar-slot">
                                    @endif
                                        @foreach($cell['items'] as $item)
                                            @if($canChooseSlot && ($item['status']??'')==='Rancangan' && isset($item['meeting_id']))<button type="button" class="mp-calendar-booking mp-calendar-draft-edit" title="Ubah atau hapus {{ $item['title'] }}" @pointerdown.stop @click.stop.prevent="editCalendarMeeting(@js($item['meeting_id']),slots)" :disabled="submitting" aria-label="Ubah atau hapus {{ $item['title'] }}">
                                            @else<div class="mp-calendar-booking" title="{{ $item['title'] }}">@endif<strong>{{ $item['status']==='Disetujui'?'Terpakai':($item['status']==='Internal'?'Jadwal Internal':$item['status']) }}</strong><span>{{ $item['start'] }}–{{ $item['end'] }}</span>@if(isset($item['shift']))<strong>Shift {{ $item['shift'] }}</strong>@endif<span>{{ $item['title'] }}</span>@if($canChooseSlot && ($item['status']??'')==='Rancangan' && isset($item['meeting_id']))</button>@else</div>@endif @endforeach
                                        @if($cell['closed'])<span class="mp-calendar-caption">{{ $cell['closed'] }}</span>@endif
                                    @if($canChooseSlot && $cell['selectable'] && $cell['state']!=='draft')</button>@else</div>@endif
                                </td>
                            @endif
                        @endforeach @endforeach
                    </tr>@endforeach</tbody>
                </table>
            </div>
        @endif
        <p class="mp-settings-description">Pengajuan menunggu belum memesan ruang. Ketersediaan seluruh pertemuan diperiksa kembali saat paket dikirim dan disetujui.</p>
    </div>
</section>
