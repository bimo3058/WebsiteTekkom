{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
<style>
    /* ── SITKOM Design Tokens ── */
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
        --c-success:          #40C4AA;
        --c-success-subtle:   #DDF2EE;
    }

    * { box-sizing: border-box; }

    /* ── Fullscreen wrapper ── */
    .sk-login-root {
        position: fixed;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #fff;
        font-family: 'Inter Tight', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        overflow: hidden;
    }

    /* ── Dot grid background ── */
    .sk-bg-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, #DFE1E7 1px, transparent 1px);
        background-size: 28px 28px;
        opacity: 0.6;
        pointer-events: none;
    }
    .sk-bg-vignette {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.95) 30%, rgba(255,255,255,0.4) 100%);
        pointer-events: none;
    }

    /* ── Top header bar ── */
    .sk-header {
        position: absolute;
        top: 0; left: 0; right: 0;
        padding: 24px 40px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1;
    }
    .sk-logo-mark {
        width: 30px; height: 30px;
        border-radius: 7px;
        background: linear-gradient(135deg, #3C518B, #0B266E);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .sk-brand-name {
        font-weight: 700;
        font-size: 18px;
        color: var(--c-fg);
        letter-spacing: 0.01em;
    }

    /* ── Card ── */
    .sk-card {
        position: relative;
        z-index: 1;
        width: 440px;
        background: #fff;
        border-radius: 16px;
        padding: 36px;
        box-shadow: 0 4px 24px rgba(13,13,18,0.08);
        border: 1px solid var(--c-border);
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ── Card header icon ── */
    .sk-icon-wrap {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(11,38,110,0.12), rgba(11,38,110,0.04));
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #B8D4E9;
        margin: 0 auto;
    }
    .sk-card-title {
        text-align: center;
        font-weight: 700;
        font-size: 22px;
        color: var(--c-fg);
        margin-top: 12px;
        margin-bottom: 4px;
    }
    .sk-card-sub {
        text-align: center;
        font-size: 14px;
        color: var(--c-fg-muted);
    }

    /* ── Alert ── */
    .sk-alert {
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 13.5px;
        line-height: 1.5;
    }
    .sk-alert-error {
        background: var(--c-error-subtle);
        color: var(--c-error);
        border: 1px solid rgba(223,28,65,0.2);
    }
    .sk-alert-warning {
        background: #F9ECCB;
        color: #7A4E10;
        border: 1px solid rgba(211,156,61,0.3);
    }

    /* ── Form fields ── */
    .sk-field-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .sk-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .sk-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg);
        letter-spacing: 0.01em;
    }
    .sk-label span { color: var(--c-error); }
    .sk-input {
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--c-border);
        font-family: inherit;
        font-size: 14px;
        color: var(--c-fg);
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        width: 100%;
        background: #fff;
    }
    .sk-input::placeholder { color: var(--c-fg-placeholder); }
    .sk-input:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px rgba(11,38,110,0.10);
    }

    /* ── SSO Button ── */
    .sk-btn-sso {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        background: var(--c-primary);
        color: #fff;
        border: none;
        font-family: inherit;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        letter-spacing: 0.01em;
        transition: background 0.15s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }
    .sk-btn-sso:hover  { background: var(--c-primary-hover); }
    .sk-btn-sso:active { transform: translateY(1px); }

    /* ── Dev form submit button ── */
    .sk-btn-submit {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        background: var(--c-primary);
        color: #fff;
        border: none;
        font-family: inherit;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        letter-spacing: 0.01em;
        transition: background 0.15s, transform 0.1s;
    }
    .sk-btn-submit:hover  { background: var(--c-primary-hover); }
    .sk-btn-submit:active { transform: translateY(1px); }

    /* ── Divider ── */
    .sk-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--c-fg-placeholder);
        font-size: 12px;
    }
    .sk-divider::before,
    .sk-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--c-border);
    }

    /* ── Footer ── */
    .sk-footer {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        padding: 20px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1;
    }
    .sk-footer span {
        font-size: 12px;
        color: var(--c-fg-placeholder);
    }
    .sk-footer-links {
        display: flex;
        gap: 20px;
    }
    .sk-footer-links a {
        font-size: 12px;
        color: var(--c-fg-placeholder);
        text-decoration: none;
        transition: color 0.12s;
    }
    .sk-footer-links a:hover { color: var(--c-fg-muted); }

    @media (max-width: 520px) {
        .sk-card { width: 100%; margin: 0 16px; padding: 28px 20px; }
        .sk-header { padding: 20px 20px; }
        .sk-footer { padding: 16px 20px; flex-direction: column; gap: 8px; text-align: center; }
        .sk-footer-links { justify-content: center; }
    }
</style>

<div class="sk-login-root">
    {{-- Backgrounds --}}
    <div class="sk-bg-dots"></div>
    <div class="sk-bg-vignette"></div>


    {{-- Card --}}
    <div class="sk-card">

        {{-- Logo + title --}}
        <div style="display:flex;flex-direction:column;align-items:center;gap:2px;">
            <img
                src="{{ asset('images/UNDIPOfficial.png') }}"
                alt="Logo UNDIP"
                style="width:110px;height:110px;object-fit:contain;"
            />
            <div>
                <div class="sk-card-title">SITKOM</div>
                <div class="sk-card-sub">Login untuk mengakses Sistem Informasi Teknik Komputer UNDIP.</div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('status'))
            <div class="sk-alert sk-alert-warning">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="sk-alert sk-alert-error">{{ $errors->first() }}</div>
        @endif

        {{-- SSO Button --}}
        <a href="{{ route('microsoft.redirect') }}" class="sk-btn-sso">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="white">
                <path d="M2 2h8v8H2V2zm10 0h6v6h-6V2zm-10 10h6v6H2v-6zm8 0h6v6h-6v-6z"/>
            </svg>
            Login via SSO UNDIP
        </a>

        @if (app()->isLocal())
            {{-- Divider --}}
            <div class="sk-divider">Development only</div>

            {{-- Dev login form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="sk-field-group">
                    <div class="sk-field">
                        <label class="sk-label" for="email">Email Address <span>*</span></label>
                        <input
                            id="email"
                            class="sk-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@undip.ac.id"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>
                    <div class="sk-field">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label class="sk-label" for="password" style="margin-bottom:0;">Password <span>*</span></label>
                            <a href="{{ route('password.request') }}" style="font-size:12px;font-weight:500;color:var(--c-primary);text-decoration:none;opacity:0.8;transition:opacity 0.15s;"
                               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                                Forgot Password?
                            </a>
                        </div>
                        <input
                            id="password"
                            class="sk-input"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                    </div>
                    <button type="submit" class="sk-btn-submit">Login</button>
                </div>
            </form>
        @endif

    </div>

    {{-- Footer --}}
    <div class="sk-footer">
        <span>© {{ date('Y') }} Departemen Teknik Komunikasi &bull; Universitas Diponegoro</span>
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