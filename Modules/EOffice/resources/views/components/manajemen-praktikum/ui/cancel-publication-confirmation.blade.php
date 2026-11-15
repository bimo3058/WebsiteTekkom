<template x-teleport="body">
    <div class="mp-confirm-special" x-show="showCancelConfirm" x-cloak x-transition.opacity.duration.200ms @keydown.escape.window="showCancelConfirm=false"
        @click.self="showCancelConfirm=false" style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45)">
        <section class="mp-special-danger" role="dialog" aria-modal="true" aria-label="Batalkan Publikasi Nilai"
            style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);overflow:auto;background:white;border-radius:14px;text-align:left">
            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-weight:700;font-size:16px">Batalkan Publikasi Nilai</div>
            <div style="padding:20px 22px;font-size:13px;color:#666D80;line-height:1.6">
                <p style="margin:0 0 12px;color:#0D0D12;font-weight:600">Apakah Anda yakin ingin membatalkan persetujuan publikasi nilai?</p>
                <p style="margin:0">Nilai tidak lagi ditampilkan kepada mahasiswa dan memerlukan persetujuan kembali sebelum dipublikasikan.</p>
            </div>
            <div style="padding:16px 22px;display:flex;justify-content:flex-end;gap:10px;background:#FAFBFC;border-top:1px solid #DFE1E7">
                <button type="button" class="mp-btn secondary md" @click="showCancelConfirm=false">Batal</button>
                <button type="button" class="mp-btn primary md" :disabled="cancelConfirmed" @click="if(!cancelConfirmed){cancelConfirmed=true;$refs.cancelPublicationForm.requestSubmit()}">Ya, Batalkan Publikasi</button>
            </div>
        </section>
    </div>
</template>
