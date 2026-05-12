{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
<style>
    :root {
        --c-primary:          #0B266E;
        --c-primary-hover:    #091958;
        --c-primary-subtle:   rgba(11,38,110,0.08);
        --c-primary-border:   #3C518B;
        --c-bg:               #F6F8FA;
        --c-card:             #FFFFFF;
        --c-border:           #DFE1E7;
        --c-border-strong:    #C1C7CF;
        --c-fg:               #0D0D12;
        --c-fg-sec:           #353849;
        --c-fg-muted:         #666D80;
        --c-fg-placeholder:   #A4ABB8;
        --c-error:            #DF1C41;
        --c-error-subtle:     #FADAE1;
        --c-warning-subtle:   #F9ECCB;
        --c-warning-text:     #7A4E10;
    }
    * { box-sizing: border-box; }
    .sk-login-root {
        position: fixed; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        background: #fff;
        font-family: 'Inter Tight', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        overflow: hidden;
    }
    .sk-bg-dots {
        position: absolute; inset: 0;
        background-image: radial-gradient(circle, #DFE1E7 1px, transparent 1px);
        background-size: 28px 28px;
        opacity: 0.6; pointer-events: none;
    }
    .sk-bg-vignette {
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.95) 30%, rgba(255,255,255,0.4) 100%);
        pointer-events: none;
    }
    .sk-card {
        position: relative; z-index: 1;
        width: 440px; background: #fff;
        border-radius: 16px; padding: 36px;
        box-shadow: 0 4px 24px rgba(13,13,18,0.08);
        border: 1px solid var(--c-border);
        display: flex; flex-direction: column; gap: 24px;
    }
    .sk-card-title {
        text-align: center; font-weight: 700;
        font-size: 22px; color: var(--c-fg);
        margin-top: 12px; margin-bottom: 4px;
    }
    .sk-card-sub {
        text-align: center; font-size: 14px; color: var(--c-fg-muted);
    }
    .sk-alert {
        padding: 12px 14px; border-radius: 10px;
        font-size: 13.5px; line-height: 1.5;
    }
    .sk-alert-error  { background: var(--c-error-subtle); color: var(--c-error); border: 1px solid rgba(223,28,65,0.2); }
    .sk-alert-warning { background: var(--c-warning-subtle); color: var(--c-warning-text); border: 1px solid rgba(211,156,61,0.3); }
    .sk-btn-sso {
        width: 100%; padding: 14px; border-radius: 12px;
        background: var(--c-primary); color: #fff; border: none;
        font-family: inherit; font-weight: 700; font-size: 15px;
        cursor: pointer; letter-spacing: 0.01em;
        transition: background 0.15s, transform 0.1s;
        display: flex; align-items: center; justify-content: center;
        gap: 10px; text-decoration: none;
    }
    .sk-btn-sso:hover  { background: var(--c-primary-hover); }
    .sk-btn-sso:active { transform: translateY(1px); }
    .sk-footer {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 20px 40px;
        display: flex; align-items: center; justify-content: space-between; z-index: 1;
    }
    .sk-footer span { font-size: 12px; color: var(--c-fg-placeholder); }
    .sk-footer-links { display: flex; gap: 20px; }
    .sk-footer-links a {
        font-size: 12px; color: var(--c-fg-placeholder);
        text-decoration: none; transition: color 0.12s;
    }
    .sk-footer-links a:hover { color: var(--c-fg-muted); }
    @media (max-width: 520px) {
        .sk-card { width: 100%; margin: 0 16px; padding: 28px 20px; }
        .sk-footer { padding: 16px 20px; flex-direction: column; gap: 8px; text-align: center; }
        .sk-footer-links { justify-content: center; }
    }
</style>

<div class="sk-login-root">
    <div class="sk-bg-dots"></div>
    <div class="sk-bg-vignette"></div>

    <div class="sk-card">
        <div style="display:flex;flex-direction:column;align-items:center;gap:2px;">
            <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="Logo UNDIP"
                 style="width:110px;height:110px;object-fit:contain;"/>
            <div>
                <div class="sk-card-title">SITKOM</div>
                <div class="sk-card-sub">Login untuk mengakses Sistem Informasi Teknik Komputer UNDIP.</div>
            </div>
        </div>

        @if (session('status'))
            <div class="sk-alert sk-alert-warning">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="sk-alert sk-alert-error">{{ $errors->first() }}</div>
        @endif

        <a href="{{ route('microsoft.redirect') }}" class="sk-btn-sso">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="white">
                <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
            </svg>
            Login via SSO UNDIP
        </a>
    </div>

    <div class="sk-footer">
        <span>© {{ date('Y') }} Departemen Teknik Komputer &bull; Universitas Diponegoro</span>
        <div class="sk-footer-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Bantuan</a>
        </div>
    </div>
</div>

<script>
    localStorage.removeItem('um_per_page');
    localStorage.removeItem('cat_per_page');
</script>
</x-guest-layout>