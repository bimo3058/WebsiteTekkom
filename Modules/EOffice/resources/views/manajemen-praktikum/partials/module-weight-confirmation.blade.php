<template x-teleport="body">
    <div class="mp-confirm-special" x-show="showWeightConfirm" x-cloak x-transition.opacity.duration.200ms @keydown.escape.window="showWeightConfirm=false" style="display:none;">
        <div class="mp-special-warning" role="dialog" aria-modal="true" aria-label="Ubah Bobot Penilaian" @click.outside="showWeightConfirm=false" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);overflow-y:auto;background:white;border-radius:14px;overflow:hidden">
            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-weight:700;font-size:16px">Simpan Perubahan Penilaian</div>
            <p style="padding:20px 22px;margin:0;font-size:13px;color:#666D80;line-height:1.6">Perubahan akan menghitung ulang nilai terkait. Apakah Anda yakin ingin menyimpan perubahan?</p>
            <div style="padding:16px 22px;display:flex;justify-content:flex-end;gap:10px;background:#FAFBFC;border-top:1px solid #DFE1E7"><button type="button" class="mp-btn secondary md" @click="showWeightConfirm=false">Batal</button><button type="button" class="mp-btn primary md" @click="weightConfirmed=true;showWeightConfirm=false;$refs.moduleWeightForm.requestSubmit()">Simpan</button></div>
        </div>
    </div>
</template>
