@php
    $existingRevisionFiles=\Modules\EOffice\Services\ManajemenPraktikum\ModuleRevisionAttachments::files($p->file_revisi_asprak);
    $existingRevisionFiles=array_map(fn($file)=>$file+['url'=>app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($file['path'],'eoffice',$file['original_name'] ?? basename($file['path']))],$existingRevisionFiles);
    $revisionWasSent=count($existingRevisionFiles)>0;
@endphp
<div style="display:flex;align-items:center;gap:8px;" x-data="{open:false,initialFiles:{{ json_encode($existingRevisionFiles) }},kept:{{ json_encode($existingRevisionFiles) }},files:[],error:'',sending:false,
    openModal(){this.kept=this.initialFiles.map(file=>({...file}));this.files=[];this.error='';this.syncFiles();this.open=true},
    addFiles(event){const added=Array.from(event.target.files);this.error='';if(this.kept.length+this.files.length+added.length>3){this.error='Maksimal 3 berkas revisi.';event.target.value='';return}if(added.some(f=>f.size>5*1024*1024)){this.error='Maksimal 5 MB per berkas.';event.target.value='';return}this.files.push(...added);this.syncFiles();event.target.value=''},
    syncFiles(){const data=new DataTransfer();this.files.forEach(f=>data.items.add(f));this.$refs.revisionFiles.files=data.files}
}">
    <button type="button" @click="openModal()" class="mp-btn primary sm" style="min-width:120px;padding:8px 14px;font-size:13px;justify-content:center">{{ $revisionWasSent?'Edit Revisi':'Revisi' }}</button>
    @if($revisionWasSent && $p->status_pengumpulan==='revisi')
    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi.cancel',$p->id) }}" onsubmit="return confirm('Batalkan revisi ini? Berkas revisi tidak lagi ditampilkan kepada mahasiswa.');" style="margin:0;">@csrf<button type="submit" class="mp-btn destructive sm" title="Batalkan revisi" aria-label="Batalkan revisi" style="width:36px;height:36px;padding:0;justify-content:center;"><x-eoffice::manajemen-praktikum.ui.icon name="trash" /></button></form>
    @endif
    <template x-teleport="body">
        <div x-show="open" x-cloak x-transition.opacity @keydown.escape.window="if(!sending)open=false" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
            <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi',$p->id) }}" enctype="multipart/form-data"
                role="dialog" aria-modal="true" aria-label="Lampiran Revisi" @click.outside="if(!sending)open=false"
                onsubmit="return confirm('Simpan perubahan revisi? Jika seluruh lampiran dihapus, revisi akan dibatalkan.');"
                @submit="if(sending){$event.preventDefault();return}if(kept.length+files.length===0 &amp;&amp; !initialFiles.length){$event.preventDefault();error='Lampirkan minimal satu berkas revisi.'}else{sending=true}"
                style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:560px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);overflow:auto;border-radius:14px;background:white">
                @csrf
                <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700;color:#0D0D12">Lampiran Revisi</div>
                <div style="padding:20px 22px;display:grid;gap:12px">
                    <input type="file" multiple name="file_revisi[]" x-ref="revisionFiles" style="display:none">
                    <input type="file" multiple x-ref="addRevisionFiles" @change="addFiles($event)" style="display:none">
                    <label style="font-size:12px;font-weight:600;color:#353849">Berkas Lampiran <span style="color:#DF1C41">*</span></label>
                    <button type="button" @click="$refs.addRevisionFiles.click()" :disabled="sending || kept.length+files.length>=3" style="width:100%;padding:14px;border:1px dashed #DFE1E7;border-radius:8px;background:white;color:#0B266E;display:flex;align-items:center;justify-content:center;gap:10px;font-size:14px;font-weight:500;cursor:pointer"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 4v16M4 12h16"/></svg>Tambah Berkas</button>
                    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:12px;color:#818898"><span>Batas akumulasi maksimal lampiran adalah 3 berkas per tugas.</span><span>Maks. 5 MB per berkas</span></div>
                    <div x-show="kept.length" style="margin-top:6px;font-size:12px;font-weight:600;color:#353849">Berkas Saat Ini:</div>
                    <template x-for="(file,index) in kept" :key="file.path">
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #DFE1E7;border-radius:6px;background:#F9FAFB">
                            <input type="hidden" name="retained_files[]" :value="file.path">
                            <span style="color:#666D80;display:flex;flex-shrink:0"><x-eoffice::manajemen-praktikum.ui.icon name="file" /></span>
                            <a :href="file.url" target="_blank" rel="noopener noreferrer" x-text="file.original_name || file.path.split('/').pop()" style="flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:13px;color:#0B266E"></a>
                            <button type="button" aria-label="Hapus lampiran revisi lama" :disabled="sending" @click="kept.splice(index,1)" style="border:0;background:none;color:#DF1C41;cursor:pointer" class="mp-delete-button" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                        </div>
                    </template>
                    <template x-for="(file,index) in files" :key="index">
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #DFE1E7;border-radius:6px;background:#F9FAFB">
                            <span style="color:#666D80;display:flex;flex-shrink:0"><x-eoffice::manajemen-praktikum.ui.icon name="file" /></span>
                            <span x-text="file.name" style="flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:13px;color:#353849"></span>
                            <button type="button" aria-label="Hapus lampiran revisi baru" :disabled="sending" @click="files.splice(index,1);syncFiles()" style="border:0;background:none;color:#DF1C41;cursor:pointer" class="mp-delete-button" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                        </div>
                    </template>
                    <p x-show="error" x-text="error" role="status" style="margin:0;font-size:12px;color:#DF1C41"></p>
                </div>
                <div style="padding:16px 22px;border-top:1px solid #DFE1E7;background:#FAFBFC;display:flex;justify-content:flex-end;gap:10px">
                    <button type="button" class="mp-btn secondary md" :disabled="sending" @click="open=false">Batal</button>
                    <button type="submit" class="mp-btn primary md" :disabled="sending || (kept.length+files.length===0 &amp;&amp; !initialFiles.length)" x-text="initialFiles.length &amp;&amp; kept.length+files.length===0 ? 'Batalkan Revisi' : (initialFiles.length ? 'Simpan Revisi' : 'Kirim Revisi')">Simpan Revisi</button>
                </div>
            </form>
        </div>
    </template>
</div>
