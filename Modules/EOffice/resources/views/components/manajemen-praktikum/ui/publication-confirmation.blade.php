@props(['role'])
<template x-teleport="body">
    <div x-show="showPublicationConfirm" x-cloak x-transition.opacity.duration.200ms
        @keydown.escape.window="if(!publicationSubmitting) showPublicationConfirm=false"
        style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;padding:20px">
        <div role="dialog" aria-modal="true" aria-label="Konfirmasi Publikasi Nilai"
            @click.outside="if(!publicationSubmitting) showPublicationConfirm=false"
            style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);background:white;border-radius:14px;overflow:auto">
            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-weight:700;font-size:16px">Konfirmasi Publikasi Nilai</div>
            <div style="padding:20px 22px;font-size:13px;color:#666D80;line-height:1.6">
                <p style="margin:0 0 12px;color:#0D0D12;font-weight:600">{{ $role==='koor' ? 'Apakah Anda yakin ingin mengirim persetujuan publikasi nilai?' : 'Apakah Anda yakin ingin menyetujui publikasi nilai mahasiswa?' }}</p>
                <p style="margin:0">Persetujuan ini mencakup seluruh nilai modul, tugas akhir, dan nilai keseluruhan. Pastikan nilai dan bobot penilaian sudah benar.</p>
                <p style="margin:12px 0 0">{{ $role==='koor' ? 'Nilai belum dipublikasikan ke mahasiswa sampai mendapat persetujuan dosen.' : 'Setelah persetujuan koordinator dan dosen lengkap, seluruh nilai akan dipublikasikan dan dapat dilihat mahasiswa.' }}</p>
            </div>
            <div style="padding:16px 22px;display:flex;justify-content:flex-end;gap:10px;background:#FAFBFC;border-top:1px solid #DFE1E7">
                <button type="button" class="mp-btn secondary md" :disabled="publicationSubmitting" @click="showPublicationConfirm=false">Batal</button>
                <button type="button" class="mp-btn primary md" :disabled="publicationSubmitting" @click="if(!publicationSubmitting){publicationConfirmed=true;$refs.publicationForm.requestSubmit()}">Ya, Kirim Persetujuan</button>
            </div>
        </div>
    </div>
</template>
