{{-- resources/views/auth/dev-login.blade.php --}}
{{-- Accessible only via direct URL: /dev/login (not linked anywhere in UI) --}}
<x-guest-layout>
<style>
    :root {
        --c-primary:          #0B266E;
        --c-primary-hover:    #091958;
        --c-primary-subtle:   rgba(11,38,110,0.08);
        --c-bg:               #F6F8FA;
        --c-border:           #DFE1E7;
        --c-border-strong:    #C1C7CF;
        --c-fg:               #0D0D12;
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
    .sk-dev-badge {
        display: inline-flex; align-items: center; gap: 6px;
        margin: 0 auto;
        background: #FFF3CD; color: #7A4E10;
        border: 1px solid rgba(211,156,61,0.35);
        font-size: 11px; font-weight: 600;
        padding: 4px 10px; border-radius: 9999px;
        letter-spacing: .04em; text-transform: uppercase;
    }
    .sk-alert {
        padding: 12px 14px; border-radius: 10px;
        font-size: 13.5px; line-height: 1.5;
    }
    .sk-alert-error   { background: var(--c-error-subtle); color: var(--c-error); border: 1px solid rgba(223,28,65,0.2); }
    .sk-alert-warning { background: var(--c-warning-subtle); color: var(--c-warning-text); border: 1px solid rgba(211,156,61,0.3); }
    .sk-field-group { display: flex; flex-direction: column; gap: 16px; }
    .sk-field { display: flex; flex-direction: column; gap: 6px; }
    .sk-label { font-size: 13px; font-weight: 500; color: var(--c-fg); letter-spacing: 0.01em; }
    .sk-label span { color: var(--c-error); }
    .sk-input {
        padding: 12px 14px; border-radius: 10px;
        border: 1px solid var(--c-border);
        font-family: inherit; font-size: 14px; color: var(--c-fg);
        outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        width: 100%; background: #fff;
    }
    .sk-input::placeholder { color: var(--c-fg-placeholder); }
    .sk-input:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px rgba(11,38,110,0.10);
    }
    .sk-btn-submit {
        width: 100%; padding: 14px; border-radius: 12px;
        background: var(--c-primary); color: #fff; border: none;
        font-family: inherit; font-weight: 700; font-size: 15px;
        cursor: pointer; letter-spacing: 0.01em;
        transition: background 0.15s, transform 0.1s;
    }
    .sk-btn-submit:hover  { background: var(--c-primary-hover); }
    .sk-btn-submit:active { transform: translateY(1px); }
    .sk-back-link {
        text-align: center; font-size: 13px; color: var(--c-fg-muted);
    }
    .sk-back-link a {
        color: var(--c-primary); text-decoration: none;
        font-weight: 500; opacity: .8; transition: opacity .15s;
    }
    .sk-back-link a:hover { opacity: 1; }
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
        <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
            <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="Logo UNDIP"
                 style="width:110px;height:110px;object-fit:contain;"/>
            <div>
                <div class="sk-card-title">Developer Login</div>
                <div class="sk-card-sub">Akses khusus SUPERADMIN. Jangan disebarkan.</div>
            </div>
            <span class="sk-dev-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                SUPERADMIN Only
            </span>
        </div>

        @if (session('status'))
            <div class="sk-alert sk-alert-warning">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="sk-alert sk-alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="sk-field-group">
                <div class="sk-field">
                    <label class="sk-label" for="email">Email Address <span>*</span></label>
                    <input id="email" class="sk-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="nama@undip.ac.id"
                           required autofocus autocomplete="username"/>
                </div>
                <div class="sk-field">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <label class="sk-label" for="password" style="margin-bottom:0;">Password <span>*</span></label>
                        <a href="{{ route('password.request') }}"
                           style="font-size:12px;font-weight:500;color:var(--c-primary);text-decoration:none;opacity:0.8;transition:opacity 0.15s;"
                           onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                            Forgot Password?
                        </a>
                    </div>
                    <input id="password" class="sk-input" type="password" name="password"
                           placeholder="••••••••" required autocomplete="current-password"/>
                </div>
                <button type="submit" class="sk-btn-submit">Login</button>
            </div>
        </form>

        <div class="sk-back-link">
            Kembali ke <a href="{{ route('login') }}">halaman login utama</a>
        </div>
    </div>

    <div class="sk-footer">
        <span>© {{ date('Y') }} Departemen Teknik Komunikasi &bull; Universitas Diponegoro</span>
        <div class="sk-footer-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Bantuan</a>
        </div>
    </div>
</div>
</x-guest-layout>