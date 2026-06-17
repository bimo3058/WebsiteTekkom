{{-- Daftar read-only dokumen aturan reward (SK FT 774). Param: $items (Collection RewardAturan) --}}
@php($items = $items ?? collect())
@if($items->count())
    <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($items as $a)
            <a href="{{ $a->public_url }}" target="_blank" rel="noopener"
               style="display:flex; align-items:center; gap:10px; text-decoration:none; background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:9px 12px;">
                <span style="width:34px; height:34px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:10px; font-weight:800; letter-spacing:.03em; color:#fff; background:{{ $a->isImage() ? '#0ea5e9' : '#dc2626' }};">
                    {{ $a->isImage() ? 'IMG' : 'PDF' }}
                </span>
                <span style="flex:1; min-width:0;">
                    <span style="display:block; font-size:13px; font-weight:700; color:#1f2937; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $a->judul }}</span>
                    <span style="display:block; font-size:11px; color:#9ca3af; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $a->nama_file }}</span>
                </span>
                <span style="flex-shrink:0; font-size:12px; font-weight:600; color:#4f46e5;">Lihat&nbsp;↗</span>
            </a>
        @endforeach
    </div>
@else
    <div style="font-size:12px; color:#9ca3af;">Belum ada dokumen aturan yang diunggah admin.</div>
@endif
