@foreach($praktikum->komponen_modul ?? [] as $extra)
    <th class="mp-th" style="padding:12px 16px;text-align:center;white-space:nowrap;background:{{ $loop->first ? '#F5F3FF' : '#F0FDFA' }};color:{{ $loop->first ? '#7C3AED' : '#0F766E' }}">{{ mb_strtoupper($extra['nama']) }}
        <div style="margin-top:4px;display:flex;justify-content:center;align-items:center;gap:4px">
            @if(($extraMode ?? '')==='koor')
                <input type="number" x-model="extraWeights.{{ $extra['id'] }}" :disabled="moduleWeightsLocked" min="1" max="100" step="1" required aria-label="Bobot {{ $extra['nama'] }}" style="width:44px;text-align:center;border:1px solid #CBD5E1;border-radius:4px;background:transparent"> %
            @else {{ $extra['bobot'] }}% @endif
        </div>
    </th>
@endforeach
