@props(['ta','role','weightsLocked'=>false])
@php
    $headerPalette=[['#EEF2FF','#4338CA'],['#F0FDF4','#15803D'],['#FEFCE8','#A16207'],['#FFF7ED','#C2410C']];
@endphp
<thead>
    <tr>
        @foreach(['NO','NAMA MAHASISWA','NIM','KELOMPOK','SHIFT','KEHADIRAN'] as $heading)
            <th class="mp-th" rowspan="2" style="padding:12px 16px;vertical-align:middle;text-align:{{ in_array($heading,['NAMA MAHASISWA','NIM'])?'left':'center' }};{{ $heading==='NAMA MAHASISWA'?'min-width:250px;':'' }}">{{ $heading }}</th>
        @endforeach
        @foreach($ta->tahap as $headStage)
            @if(count($ta->componentsFor($headStage['id'])))<th class="mp-th" colspan="{{ count($ta->componentsFor($headStage['id'])) }}" style="padding:3px 12px;line-height:16px;text-align:center;vertical-align:middle;font-size:12px;border-bottom:1px solid #DFE1E7">{{ mb_strtoupper($headStage['nama']) }}</th>@endif
        @endforeach
        <th class="mp-th" rowspan="2" style="padding:12px 16px;vertical-align:middle">KETERANGAN</th><th class="mp-th" rowspan="2" style="padding:12px 16px;text-align:center;vertical-align:middle">RATA-RATA</th>
    </tr>
    <tr>
        @foreach(collect($ta->tahap)->flatMap(fn($s)=>$ta->componentsFor($s['id'])) as $headColumn)
            @php
                $headColor=$headerPalette[$loop->index % count($headerPalette)];
            @endphp
            <th class="mp-th" style="padding:4px 16px;line-height:14px;text-align:center;background:{{ $headColor[0] }};color:{{ $headColor[1] }};white-space:nowrap">{{ mb_strtoupper($headColumn['nama']) }}
                <div style="margin-top:2px;line-height:14px;display:flex;justify-content:center;align-items:center;gap:4px;font-size:11px;font-weight:600">
                    @if($role==='koor')<input form="bobotForm" @input="$dispatch('ta-weight-change', {id:@js($headColumn['id']),changed:$el.value!==@js((string)($headColumn['bobot'] ?: ''))})" @readonly($weightsLocked) name="ta_bobot[{{ $headColumn['id'] }}]" type="number" value="{{ $headColumn['bobot'] ?: '' }}" min="1" max="100" step="1" required aria-label="Bobot {{ $headColumn['nama'] }}" style="width:44px;border:1px solid #DFE1E7;border-radius:4px;background:transparent;padding:2px">@else {{ $headColumn['bobot'] }} @endif %
                </div>
            </th>
        @endforeach
    </tr>
</thead>
