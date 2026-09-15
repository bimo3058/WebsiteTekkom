<section class="module-access" style="--access-color:{{ $tag['color'] }};--access-bg:{{ $tag['bg'] }};"
         aria-label="Aktivitas pengguna {{ $module['name'] }} selama 14 hari terakhir">
    <div class="module-access-heading">
        <span>Aktivitas pengguna</span>
        <span>14 hari</span>
    </div>
    <div class="module-access-summary">
        <div><strong>{{ number_format($access['total_users'], 0, ',', '.') }}</strong><span>pengguna unik</span></div>
        <div class="module-access-today"><strong>{{ number_format($access['today_users'], 0, ',', '.') }}</strong><span>hari ini</span></div>
    </div>
    @php
        $wavePoints = [];
        foreach ($access['days'] as $index => $day) {
            $wavePoints[] = [
                'x' => 10 + $index * 20,
                'y' => round(76 - $day['users'] / max(1, $access['peak_users']) * 68, 2),
            ];
        }
        $wavePath = 'M '.$wavePoints[0]['x'].' '.$wavePoints[0]['y'];
        foreach (array_slice($wavePoints, 1) as $index => $point) {
            $previous = $wavePoints[$index];
            $midX = ($previous['x'] + $point['x']) / 2;
            // Horizontal tangents keep the wave smooth without exceeding the recorded values.
            $wavePath .= ' C '.$midX.' '.$previous['y'].', '.$midX.' '.$point['y'].', '.$point['x'].' '.$point['y'];
        }
        $waveFill = $wavePath.' L 270 76 L 10 76 Z';
        $gradientId = 'module-access-gradient-'.$moduleKey;
    @endphp
    <div class="module-access-plot" role="group" aria-label="Grafik pengguna unik per hari">
        <svg class="module-access-wave" viewBox="0 0 280 80" preserveAspectRatio="none" aria-hidden="true">
            <defs>
                <linearGradient id="{{ $gradientId }}" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="var(--access-color)" stop-opacity=".22"/>
                    <stop offset="100%" stop-color="var(--access-color)" stop-opacity=".015"/>
                </linearGradient>
            </defs>
            <path d="{{ $waveFill }}" fill="url(#{{ $gradientId }})"/>
            <path d="{{ $wavePath }}" fill="none" stroke="var(--access-color)" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
        </svg>
        <div class="module-access-points">
            @foreach($access['days'] as $index => $day)
                <div class="module-access-column" tabindex="0" role="img"
                     aria-label="{{ $day['label'] }}: {{ $day['users'] }} pengguna, {{ $day['events'] }} aktivitas">
                    <span class="module-access-dot" style="top:{{ $wavePoints[$index]['y'] / 80 * 100 }}%;"></span>
                    <span class="module-access-tooltip" aria-hidden="true">{{ $day['label'] }}<br><b>{{ $day['users'] }} pengguna</b><br>{{ $day['events'] }} aktivitas</span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="module-access-axis" aria-hidden="true">
        <span>{{ $access['days'][0]['label'] }}</span><span>{{ $access['days'][13]['label'] }}</span>
    </div>
    <p class="module-access-caption">
        @if($access['total_events'] > 0)
            {{ number_format($access['total_events'], 0, ',', '.') }} aktivitas tercatat di audit log
        @else
            Belum ada aktivitas tercatat dalam 14 hari ini.
        @endif
    </p>
</section>

@once
<style>
    .module-access { border-top:1px solid var(--c-border); padding-top:12px; margin-top:2px; min-width:0; }
    .module-access-heading, .module-access-summary, .module-access-axis { display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .module-access-heading { font-size:10px; color:var(--c-fg-muted); }
    .module-access-heading > :first-child { font-weight:600; color:var(--c-fg-sec); }
    .module-access-heading > :last-child { background:var(--access-bg); color:var(--access-color); padding:2px 6px; border-radius:4px; white-space:nowrap; }
    .module-access-summary { margin:8px 0 14px; align-items:flex-end; }
    .module-access-summary strong { display:block; font-size:23px; line-height:1.2; letter-spacing:-.04em; color:var(--c-fg); font-variant-numeric:tabular-nums; }
    .module-access-summary span { display:block; font-size:10px; color:var(--c-fg-muted); margin-top:2px; }
    .module-access-today { text-align:right; }
    .module-access-today strong { font-size:17px; color:var(--access-color); }
    .module-access-plot { height:80px; position:relative; border-bottom:1px solid var(--c-border); background:repeating-linear-gradient(to top,transparent 0,transparent 38px,var(--c-border) 39px,transparent 40px); }
    .module-access-wave { display:block; width:100%; height:100%; overflow:visible; }
    .module-access-points { position:absolute; inset:0; display:grid; grid-template-columns:repeat(14,minmax(0,1fr)); }
    .module-access-column { height:100%; position:relative; min-width:0; border-radius:3px; outline-offset:3px; }
    .module-access-column:focus-visible { outline:2px solid var(--access-color); }
    .module-access-dot { position:absolute; left:50%; transform:translate(-50%,-50%); width:7px; height:7px; border:2px solid var(--access-color); border-radius:50%; background:#fff; opacity:0; pointer-events:none; }
    .module-access-column:hover .module-access-dot, .module-access-column:focus .module-access-dot { opacity:1; }
    .module-access-tooltip { display:none; position:absolute; bottom:calc(100% + 6px); left:50%; transform:translateX(-50%); padding:7px 9px; border-radius:7px; color:white; background:#202638; font-size:10px; line-height:1.5; white-space:nowrap; z-index:5; pointer-events:none; box-shadow:0 3px 10px #0002; }
    .module-access-column:nth-child(-n+4) .module-access-tooltip { left:0; transform:none; }
    .module-access-column:nth-last-child(-n+4) .module-access-tooltip { left:auto; right:0; transform:none; }
    .module-access-column:hover .module-access-tooltip, .module-access-column:focus .module-access-tooltip { display:block; }
    .module-access-axis { font-size:9px; color:var(--c-fg-muted); margin-top:6px; }
    .module-access-caption { font-size:10px; color:var(--c-fg-muted); line-height:1.5; margin:9px 0 0; min-height:30px; }
</style>
@endonce
