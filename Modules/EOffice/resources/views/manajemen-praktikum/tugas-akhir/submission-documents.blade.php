@if($entry)
    @if(($entry['kategori'] ?? null) === 'terlambat')<span class="mp-badge warning sm ta-collection-tag" style="margin-bottom:4px"><span class="dot"></span>Terlambat</span>@endif
    @foreach($entry['files'] ?? [] as $file)
        <a class="ta-collection-doc" target="_blank" rel="noopener noreferrer" title="{{ $file['nama'] }}" href="{{ route($base.'download', ['praktikum'=>$praktikum->id, 'path'=>$file['path']]) }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.7" style="flex-shrink:0"><path d="M14 2H5v20h14V7zM14 2v5h5"/></svg><span>{{ $file['nama'] }}</span></a>
    @endforeach
    @if(!empty($entry['link']))<a class="ta-collection-doc" title="{{ $entry['link'] }}" href="{{ $entry['link'] }}" target="_blank" rel="noopener noreferrer">↗ {{ $entry['link'] }}</a>@endif
@else
    <span style="color:#9CA3AF">{{ $emptyLabel ?? ($original ? '-' : 'Belum mengumpulkan') }}</span>
@endif
