@props(['title'=>'Konfirmasi Pengajuan','message'=>'Lanjutkan pengajuan ini?','buttonLabel'=>'Ya, Lanjutkan'])
<template x-teleport="body"><div class="mp-confirm-special" x-show="confirmOpen" x-cloak x-transition.opacity.duration.200ms @click.self="if(!submitting)confirmOpen=false" @keydown.escape.window="if(!submitting)confirmOpen=false" style="display:none;">
    <section class="{{ preg_match('/Cabut|Hapus|Batalkan/i', $title) ? 'mp-special-danger' : (preg_match('/Tutup|Tarik|Ubah/i', $title) ? 'mp-special-warning' : 'mp-special-primary') }}" role="dialog" aria-modal="true" aria-label="{{ $title }}" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 40px);max-height:calc(100dvh - 40px);overflow:auto;background:white;border-radius:14px">
        <div class="mp-confirm-symbol"><x-eoffice::manajemen-praktikum.ui.icon name="alert-circle" :size="26" /></div>
        <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700;color:#0D0D12">{{ $title }}</div>
        <p style="padding:20px 22px;margin:0;font-size:13px;color:#666D80;line-height:1.6">{{ $message }}</p>
        <div class="mp-modal-footer"><button type="button" class="mp-btn secondary md" :disabled="submitting" @click="confirmOpen=false">Batal</button><button type="button" class="mp-btn primary md" :disabled="submitting" @click="confirmed=true;$nextTick(()=>$refs.actionForm.requestSubmit())">{{ $buttonLabel }}</button></div>
    </section>
</div></template>
