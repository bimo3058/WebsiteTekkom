{{--
    Gaya panel peringatan jadwal (S-81): dipakai form Lokasi (_lokasi-jadwal) dan
    detail Rencana Proker ("Periksa jadwal sebelum mengajukan"). Warna memakai
    color-mix di atas latar halaman supaya tetap terbaca di mode terang & gelap.
--}}
@once
<style>
    .mk-bentrok { --mk-amber: #F79009; --mk-merah: #F04438;
        margin-top: 12px; border: 1px solid color-mix(in srgb, var(--mk-amber) 40%, transparent);
        background: color-mix(in srgb, var(--mk-amber) 8%, transparent); border-radius: 12px; padding: 14px 16px; }
    .mk-bentrok__kepala { display: flex; align-items: center; gap: 10px; }
    .mk-bentrok__ikon { flex: none; width: 28px; height: 28px; border-radius: 8px; display: grid; place-items: center;
        background: color-mix(in srgb, var(--mk-amber) 22%, transparent); color: var(--mk-amber); }
    .mk-bentrok__judul { font-size: 14px; font-weight: 600; color: var(--mk-amber); line-height: 1.3; }
    .mk-bentrok__isi { font-size: 13px; color: var(--c-fg-sec, #475467); margin: 10px 0 0; line-height: 1.5; }
    .mk-bentrok__daftar { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
    .mk-bentrok__item { display: flex; align-items: flex-start; gap: 12px; padding: 10px 12px; border-radius: 10px;
        border: 1px solid var(--c-border, rgba(128,128,128,.25)); background: var(--c-surface, rgba(128,128,128,.06)); }
    .mk-bentrok__item.is-ruang-sama { border-color: color-mix(in srgb, var(--mk-merah) 55%, transparent); }
    .mk-bentrok__bidang { flex: none; min-width: 58px; text-align: center; padding: 3px 8px; border-radius: 999px;
        font-size: 11px; font-weight: 700; letter-spacing: .02em; color: var(--c-fg, inherit); background: rgba(128,128,128,.2); }
    .mk-bentrok__badan { flex: 1; min-width: 0; }
    .mk-bentrok__nama { font-size: 13px; font-weight: 600; color: var(--c-fg, inherit); line-height: 1.4; overflow-wrap: anywhere; }
    .mk-bentrok__meta { display: flex; flex-wrap: wrap; gap: 4px 16px; margin-top: 4px; font-size: 12px; color: var(--c-fg-sec, #475467); }
    .mk-bentrok__meta span { display: inline-flex; align-items: center; gap: 5px; }
    .mk-bentrok__meta svg { flex: none; opacity: .75; }
    .mk-bentrok__sama { flex: none; align-self: center; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;
        color: var(--mk-merah); background: color-mix(in srgb, var(--mk-merah) 14%, transparent); white-space: nowrap; }
    .mk-bentrok__kaki { margin: 12px 0 0; padding-top: 10px; border-top: 1px solid color-mix(in srgb, var(--mk-amber) 25%, transparent);
        font-size: 12px; color: var(--c-fg-muted, #667085); line-height: 1.5; }
    .mk-bentrok__kaki a { color: var(--c-primary-link, inherit); font-weight: 600; text-decoration: underline; }
    @media (max-width: 575px) {
        .mk-bentrok { padding: 12px; }
        .mk-bentrok__item { flex-wrap: wrap; gap: 8px; }
        .mk-bentrok__bidang { min-width: 0; }
        .mk-bentrok__badan { flex-basis: 100%; }
        .mk-bentrok__sama { align-self: flex-start; }
    }
</style>
@endonce
