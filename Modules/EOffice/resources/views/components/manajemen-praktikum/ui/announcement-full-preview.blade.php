@props(['announcement'])
<article style="border:1px solid #DFE1E7;border-radius:12px;background:#fff;overflow:hidden;min-width:0;flex-shrink:0">
    <header style="padding:16px 20px;border-bottom:1px solid #DFE1E7;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <x-eoffice::manajemen-praktikum.ui.announcement-author :announcement="$announcement" />
    </header>
    <div style="padding:16px 20px;display:grid;gap:12px;min-width:0;overflow-wrap:anywhere;max-height:min(40dvh,360px);overflow-y:auto;box-sizing:border-box">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap"><h3 style="font-size:15px;line-height:1.5;font-weight:700;color:#353849;margin:0">{{ $announcement->judul }}</h3><x-eoffice::manajemen-praktikum.ui.announcement-pin-badge :announcement="$announcement" /></div>
        <p style="font-size:13px;line-height:1.6;color:#374151;white-space:pre-line;margin:0">{{ $announcement->konten }}</p>
        @if(!empty($announcement->lampiran))
            <div style="display:flex;flex-wrap:wrap;gap:12px">
                @foreach($announcement->lampiran as $file)
                    @php
                        $path=is_array($file)?($file['path']??''):$file;
                        $name=is_array($file)?($file['name']??basename($path)):basename($path);
                    @endphp
                    @if($path)
                        <a href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($path,'eoffice',$name) }}" target="_blank" rel="noopener noreferrer" title="{{ $name }}" style="width:140px;height:140px;border:1px solid #DFE1E7;border-radius:8px;overflow:hidden;display:flex;flex-direction:column;text-decoration:none;background:#F9FAFB">
                            <div style="display:flex;align-items:center;justify-content:center;flex:1;color:#C1C7D0"><x-eoffice::manajemen-praktikum.ui.icon name="file" size="36" /></div>
                            <div style="background:#0B266E;color:white;padding:10px 12px;font-size:12px;font-weight:600;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;text-align:center">{{ $name }}</div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</article>
