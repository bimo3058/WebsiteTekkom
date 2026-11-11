@props(['lecturers'])
<span style="display:block;margin:10px 0;min-width:0;">
    <span style="display:block;font-size:12px;font-weight:600;color:#666D80;margin-bottom:4px;">Dosen Pengampu</span>
    <span style="display:block;white-space:normal;overflow-wrap:anywhere;font-size:13px;line-height:20px;color:#353849;">
        @if($lecturers->isNotEmpty())
            {{ $lecturers->unique('id')->pluck('name')->implode('; ') }}
        @else
            <span class="mp-badge error sm">Belum ditunjuk</span>
        @endif
    </span>
</span>
