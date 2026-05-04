{{-- resources/views/superadmin/permission/_modal_confirm.blade.php --}}
<div id="superadminWarningModal"
     style="display:none; position:fixed; inset:0; z-index:100; align-items:center; justify-content:center; padding:16px; background:rgba(13,13,18,0.5); backdrop-filter:blur(2px);">
    <div style="position:fixed; inset:0;" onclick="closeSuperadminWarning()"></div>
    <div style="position:relative; width:100%; max-width:400px; background:#fff; border-radius:16px; border:1px solid var(--c-border); box-shadow:0 20px 40px rgba(13,13,18,0.15); overflow:hidden;">

        {{-- Top stripe --}}
        <div style="height:3px; background:linear-gradient(90deg, var(--c-error) 0%, #ED8296 100%);"></div>

        <div style="padding:28px 28px 24px;">

            {{-- Icon --}}
            <div style="width:44px; height:44px; border-radius:12px; background:var(--c-error-subtle); display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                <svg width="20" height="20" fill="none" stroke="var(--c-error)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                </svg>
            </div>

            <h3 style="font-size:14px; font-weight:700; color:var(--c-fg); margin-bottom:6px; letter-spacing:-0.01em;">Peringatan Keamanan</h3>
            <p style="font-size:12px; color:var(--c-fg-muted); line-height:1.6; margin-bottom:20px;">
                Role <strong style="color:var(--c-fg); font-weight:600;">Superadmin</strong> memberikan otoritas penuh atas seluruh sistem.
                Ketik <strong style="color:var(--c-error); font-weight:700;">KONFIRMASI</strong> untuk melanjutkan.
            </p>

            {{-- Input --}}
            <input type="text" id="confirmSuperadminText"
                   placeholder="Ketik KONFIRMASI..."
                   autocomplete="off"
                   style="width:100%; padding:10px 14px; border:1px solid var(--c-border); border-radius:8px; font-size:13px; font-weight:600; color:var(--c-fg); font-family:inherit; outline:none; transition:border-color .15s, box-shadow .15s; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:16px;"
                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                   onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">

            {{-- Buttons --}}
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeSuperadminWarning()"
                        style="flex:1; padding:9px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-muted); cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    Batalkan
                </button>
                <button type="button" id="btnConfirmSuperadmin" disabled
                        style="flex:1; padding:9px; background:var(--c-border); border:none; border-radius:8px; font-size:12px; font-weight:700; color:var(--c-fg-muted); cursor:not-allowed; font-family:inherit; transition:all .15s; text-transform:uppercase; letter-spacing:0.06em;">
                    Berikan Akses
                </button>
            </div>

        </div>
    </div>
</div>

<style>
#superadminWarningModal.active { display: flex !important; }
</style>