{{-- resources/views/auth/forgot-password.blade.php --}}
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
        --c-success:          #287F6E;
        --c-success-subtle:   #DDF2EE;
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
        text-align: center; font-size: 14px; color: var(--c-fg-muted); line-height: 1.6;
    }
    .sk-icon-wrap {
        width: 52px; height: 52px; border-radius: 50%;
        background: rgba(11,38,110,0.07);
        border: 1px solid rgba(11,38,110,0.15);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto;
    }
    .sk-alert {
        padding: 12px 14px; border-radius: 10px;
        font-size: 13.5px; line-height: 1.5;
    }
    .sk-alert-error   { background: var(--c-error-subtle); color: var(--c-error); border: 1px solid rgba(223,28,65,0.2); }
    .sk-alert-success { background: var(--c-success-subtle); color: var(--c-success); border: 1px solid rgba(40,127,110,0.2); }
    .sk-alert-warning { background: var(--c-warning-subtle); color: var(--c-warning-text); border: 1px solid rgba(211,156,61,0.3); }
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

        {{-- Icon + heading --}}
        <div style="display:flex;flex-direction:column;align-items:center;gap:2px;">
            <div class="sk-icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="#0B266E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <div>
                <div class="sk-card-title">Lupa Password?</div>
                <div class="sk-card-sub">Masukkan email kamu dan kami akan mengirimkan link untuk reset password.</div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('status'))
            <div class="sk-alert sk-alert-success">
                <strong style="display:block;margin-bottom:2px;">Email terkirim!</strong>
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="sk-alert sk-alert-error">{{ $errors->first() }}</div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="sk-field">
                    <label class="sk-label" for="email">Email Address <span>*</span></label>
                    <input id="email" class="sk-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="nama@undip.ac.id"
                           required autofocus autocomplete="username"/>
                </div>
                <button type="submit" class="sk-btn-submit">Kirim Link Reset Password</button>
            </div>
        </form>

        <div class="sk-back-link">
            Ingat password kamu? <a href="{{ route('login') }}">Kembali ke login</a>
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