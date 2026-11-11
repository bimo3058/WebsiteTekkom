@props(['announcement','role'])
@if($announcement->tipe_sistem===null && $announcement->is_published && \Illuminate\Support\Facades\Schema::hasColumn($announcement->getTable(),'pinned_until'))
<div x-data="{pinOpen:false,submitting:false,
    openPin(){this.pinOpen=true;this.$nextTick(()=>window.mpCreatePinExpiryPicker?.(this.$refs.expiry));},
    closePin(){if(!this.submitting){this.pinOpen=false;this.$refs.expiry?._flatpickr?.close();}}
}">
    <button type="button" class="primary" @click="openPin()"><x-eoffice::manajemen-praktikum.ui.icon name="pin" />{{ $announcement->isPinned()?'Ubah Tenggat Sematan':'Sematkan Pengumuman' }}</button>
    @if($announcement->isPinned())
        <form method="POST" action="{{ route('eoffice.manprak.'.$role.'.pengumuman.pin',$announcement->id) }}">
            @csrf @method('PATCH')<input type="hidden" name="pin" value="0">
            <button type="submit"><x-eoffice::manajemen-praktikum.ui.icon name="pin" />Lepas Sematan</button>
        </form>
    @endif
    <template x-teleport="body">
        <div x-show="pinOpen" x-cloak x-transition.opacity @click.self="closePin()" @keydown.escape.window="closePin()" style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45)">
            <section role="dialog" aria-modal="true" aria-label="Sematkan Pengumuman" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:14px;width:760px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px #0003">
                <h2 style="padding:20px 24px;margin:0;border-bottom:1px solid #DFE1E7;color:#111827;font-size:18px;font-weight:700;flex-shrink:0">Sematkan Pengumuman</h2>
                <form method="POST" action="{{ route('eoffice.manprak.'.$role.'.pengumuman.pin',$announcement->id) }}" @submit="submitting=true" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">
                    @csrf @method('PATCH')<input type="hidden" name="pin" value="1">
                    <div style="padding:24px;display:flex;flex-direction:column;gap:20px;overflow-y:auto;min-height:0">
                        <x-eoffice::manajemen-praktikum.ui.announcement-full-preview :announcement="$announcement" />
                        <div><label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:8px">Tenggat Penyematan <span style="color:#DF1C41">*</span>
                            <span style="display:block;position:relative;margin-top:8px">
                                <input type="text" x-ref="expiry" name="pinned_until" data-mp-picker-lazy class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]" placeholder="Pilih tanggal dan waktu..." style="width:100%;padding-right:32px;cursor:pointer;font-weight:400" required value="{{ $announcement->isPinned()?$announcement->pinned_until->format('Y-m-d\TH:i'):'' }}">
                                <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                            </span>
                        </label><p style="font-size:12px;color:#666D80;line-height:1.5;margin:0">Pengumuman akan disematkan di halaman ini dan dashboard mahasiswa sampai waktu yang telah ditentukan</p></div>
                    </div>
                    <div class="mp-modal-footer" style="flex-shrink:0"><button type="button" class="mp-btn secondary md" @click="closePin()" :disabled="submitting">Batal</button><button type="submit" class="mp-btn primary md" :disabled="submitting">Simpan Sematan</button></div>
                </form>
            </section>
        </div>
    </template>
</div>
@endif
