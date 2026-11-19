<template x-teleport="body">
    <div class="mp-confirm-special" x-show="confirmOpen" x-cloak x-transition.opacity.duration.200ms @keydown.escape.window="if(!submitting)confirmOpen=false" @click.self="if(!submitting)confirmOpen=false" style="display:none;">
        <section x-show="confirmOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95" role="dialog" aria-modal="true" :aria-label="editData.status === 'nonaktif' ? 'Nonaktifkan Praktikum' : 'Aktifkan Praktikum'" style="width:480px;max-width:100%;max-height:calc(100dvh - 40px);overflow:auto;border-radius:14px;background:#fff;box-shadow:0 20px 60px #0003">
            <div class="mp-confirm-symbol" :class="editData.status === 'nonaktif' ? 'mp-status-warning' : ''"><x-eoffice::manajemen-praktikum.ui.icon name="alert-circle" :size="26" /></div>
            <h2 style="padding:20px 22px;margin:0;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700;color:#353849" x-text="editData.status === 'nonaktif' ? 'Nonaktifkan Praktikum' : 'Aktifkan Praktikum'"></h2>
            <p style="padding:20px 22px;margin:0;font-size:13px;color:#666D80;line-height:1.6" x-text="editData.status === 'nonaktif' ? 'Mahasiswa hanya dapat membaca arsip. Penugasan dosen, koordinator, dan asisten pada praktikum ini akan dicabut. Data kegiatan dan berkas tetap disimpan.' : 'Praktikum dapat digunakan kembali. Penugasan dosen, koordinator, dan asisten perlu ditetapkan ulang.'"></p>
            <div class="mp-modal-footer">
                <button type="button" class="mp-btn secondary md" :disabled="submitting" @click="confirmOpen=false">Batal</button>
                <button type="button" class="mp-btn primary md" :disabled="submitting" @click="confirmed=true;confirmOpen=false;$nextTick(()=>$refs.statusForm.requestSubmit())" x-text="editData.status === 'nonaktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'"></button>
            </div>
        </section>
    </div>
</template>
