<div style="display:flex;gap:16px;flex-wrap:wrap">
    @foreach($files as $file)
        <a rel="noopener noreferrer" target="_blank" href="{{ route('eoffice.manprak.'.$role.'.tugas-akhir.download', ['praktikum'=>$praktikum->id, 'path'=>$file['path']]) }}" style="display:flex;flex-direction:column;width:140px;height:140px;border:1px solid #DFE1E7;border-radius:8px;overflow:hidden;text-decoration:none" title="{{ $file['nama'] }}">
            <span style="flex:1;background:#FAFAFA;display:flex;align-items:center;justify-content:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></span>
            <span style="background:#0B266E;padding:12px 14px;font-size:12px;color:#fff;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $file['nama'] }}</span>
        </a>
    @endforeach
</div>
