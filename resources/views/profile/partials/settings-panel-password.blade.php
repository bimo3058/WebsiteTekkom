{{-- profile/partials/settings-panel-password.blade.php --}}
<p class="panel-title">Password</p>
<p class="panel-sub">Perbarui atau ubah password Anda</p>

<form method="POST" action="{{ route('password.update') }}" id="form-password">
    @csrf @method('PUT')

    <div style="display:flex;flex-direction:column;gap:18px;max-width:480px;">

        <div>
            <label class="field-label">Password Saat Ini</label>
            <div style="position:relative;">
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                    class="field-input" style="padding-right:40px;">
                <button type="button" onclick="togglePwd('update_password_current_password',this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                    <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="flex:1;height:1px;background:#f1f5f9;"></div>
            <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:#94a3b8;">Sandi Baru</span>
            <div style="flex:1;height:1px;background:#f1f5f9;"></div>
        </div>

        <div>
            <label class="field-label">Password Baru</label>
            <div style="position:relative;">
                <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                    oninput="checkStrength(this.value)"
                    class="field-input" style="padding-right:40px;">
                <button type="button" onclick="togglePwd('update_password_password',this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                    <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
                </button>
            </div>
            {{-- Strength --}}
            <div style="margin-top:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <p id="strength-label" style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;min-height:14px;"></p>
                    <p id="strength-percent" style="font-size:9px;font-weight:700;text-transform:uppercase;color:#94a3b8;"></p>
                </div>
                <div style="display:flex;gap:4px;">
                    <div class="str-bar" id="bar1"></div>
                    <div class="str-bar" id="bar2"></div>
                    <div class="str-bar" id="bar3"></div>
                    <div class="str-bar" id="bar4"></div>
                </div>
                <ul style="margin-top:10px;list-style:none;padding:0;display:flex;flex-direction:column;gap:4px;">
                    <li id="req-length" style="font-size:9px;font-weight:700;color:#94a3b8;display:flex;align-items:center;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:12px;">circle</span> Minimal 8 karakter
                    </li>
                    <li id="req-caps-num" style="font-size:9px;font-weight:700;color:#94a3b8;display:flex;align-items:center;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:12px;">circle</span> Kombinasi Huruf Besar & Angka
                    </li>
                </ul>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label class="field-label">Konfirmasi Password Baru</label>
            <div style="position:relative;">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                    class="field-input" style="padding-right:40px;">
                <button type="button" onclick="togglePwd('update_password_password_confirmation',this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                    <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        @if(session('status') === 'password-updated')
        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:13px;color:#15803d;">
            <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
            Password berhasil diperbarui.
        </div>
        @endif

    </div>
</form>

<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('span');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.textContent = input.type === 'password' ? 'visibility' : 'visibility_off';
}

function checkStrength(val) {
    let score = 0;
    const hasLength  = val.length >= 8;
    const hasUpper   = /[A-Z]/.test(val);
    const hasNumber  = /[0-9]/.test(val);
    const hasSpecial = /[^A-Za-z0-9]/.test(val);
    if (val.length >= 4) score++;
    if (hasLength) score++;
    if (hasUpper || hasNumber) score++;
    if (hasUpper && hasNumber) score++;
    if (val.length >= 10 && hasSpecial) score++;
    score = Math.min(score, 4);

    const reqLen = document.getElementById('req-length');
    const reqCap = document.getElementById('req-caps-num');
    reqLen.style.color = hasLength ? '#10b981' : '#94a3b8';
    reqLen.querySelector('span').textContent = hasLength ? 'check_circle' : 'circle';
    const isCombined = hasUpper && hasNumber;
    reqCap.style.color = isCombined ? '#10b981' : '#94a3b8';
    reqCap.querySelector('span').textContent = isCombined ? 'check_circle' : 'circle';

    const colors   = ['','#f87171','#fb923c','#60a5fa','#34d399'];
    const labels   = ['','Sangat Lemah','Cukup Aman','Kuat','Sangat Aman'];
    const txtColors= ['','#ef4444','#f59e0b','#3b82f6','#10b981'];
    const percents = ['','25%','50%','75%','100%'];

    for (let i = 1; i <= 4; i++) {
        const b = document.getElementById('bar'+i);
        b.style.background = val.length === 0 ? '#e2e8f0' : (i <= score ? colors[score] : '#e2e8f0');
    }
    const lbl = document.getElementById('strength-label');
    const pct = document.getElementById('strength-percent');
    lbl.textContent = val.length ? labels[score] : '';
    lbl.style.color = val.length ? txtColors[score] : '';
    pct.textContent = val.length ? percents[score] : '';
}
</script>