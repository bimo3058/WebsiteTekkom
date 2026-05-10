{{-- resources/views/superadmin/modules/_modal_manage.blade.php --}}
<div id="modal-{{ $module->slug }}"
     style="display:none; position:fixed; inset:0; z-index:100; align-items:center; justify-content:center; padding:16px; background:rgba(13,13,18,0.5); backdrop-filter:blur(2px);">
    <div style="position:fixed; inset:0;" onclick="closeModal('modal-{{ $module->slug }}')"></div>

    <div style="position:relative; width:100%; max-width:480px; background:#fff; border-radius:16px; border:1px solid var(--c-border); box-shadow:0 20px 40px rgba(13,13,18,0.15); overflow:hidden;">

        {{-- Top stripe --}}
        <div style="height:3px; background:linear-gradient(90deg, var(--c-primary) 0%, var(--c-primary-border) 100%);"></div>

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:38px; height:38px; border-radius:10px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="var(--c-primary)" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6.78883 3.18702L9.45314 2.08342C10.0104 1.85259 10.6493 2.11723 10.8801 2.6745C11.0637 3.11762 11.5233 3.38148 12.0029 3.38184C12.4828 3.38219 12.9361 3.11793 13.1197 2.67459C13.3506 2.11727 13.9895 1.85261 14.5469 2.08346L17.211 3.187C17.7683 3.41784 18.033 4.05676 17.8021 4.61407C17.6185 5.0574 17.7523 5.56484 18.0918 5.90389C18.4312 6.24278 18.9429 6.38129 19.386 6.19774C19.9433 5.96691 20.5822 6.23155 20.813 6.78882L21.9166 9.45314C22.1474 10.0104 21.8828 10.6493 21.3255 10.8801C20.8824 11.0637 20.6185 11.5234 20.6182 12.003C20.6178 12.4828 20.8821 12.9362 21.3254 13.1198C21.8827 13.3507 22.1473 13.9896 21.9165 14.5469L20.813 17.2111C20.5821 17.7684 19.9432 18.033 19.3859 17.8022C18.9426 17.6186 18.4351 17.7523 18.0961 18.0918C17.7572 18.4312 17.6187 18.9429 17.8023 19.386C18.0331 19.9433 17.7685 20.5821 17.2112 20.813L14.5469 21.9166C13.9896 22.1474 13.3507 21.8828 13.1199 21.3255C12.9363 20.8824 12.4766 20.6185 11.997 20.6182C11.5171 20.6178 11.0637 20.8821 10.8801 21.3254C10.6492 21.8827 10.0103 22.1474 9.45297 21.9165L6.78887 20.813C6.23152 20.5822 5.96686 19.9432 6.19772 19.3859C6.38136 18.9425 6.24769 18.4351 5.90812 18.096C5.56872 17.7571 5.05713 17.6187 4.61402 17.8022C4.05674 18.0331 3.41786 17.7684 3.18703 17.2112L2.08343 14.5469C1.8526 13.9896 2.11723 13.3507 2.67451 13.1199C3.11762 12.9363 3.38149 12.4766 3.38185 11.997C3.3822 11.5171 3.11794 11.0638 2.67458 10.8801C2.11724 10.6493 1.85257 10.0103 2.08343 9.45299L3.18693 6.78891C3.41779 6.23157 4.05675 5.9669 4.61409 6.19776C5.05745 6.38141 5.56487 6.24771 5.90395 5.90813C6.24284 5.56874 6.38129 5.05713 6.19775 4.61401C5.96692 4.05674 6.23155 3.41785 6.78883 3.18702Z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:14px; font-weight:700; color:var(--c-fg); line-height:1.2;">Module Settings</h3>
                    <p style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--c-fg-muted); margin-top:2px;">{{ $module->name }}</p>
                </div>
            </div>
            <button onclick="closeModal('modal-{{ $module->slug }}')"
                    style="width:28px; height:28px; border-radius:7px; border:1px solid var(--c-border); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-fg-muted); transition:background .15s;"
                    onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('superadmin.modules.update-config', $module->slug) }}" method="POST">
            @csrf
            <div style="padding:20px 22px; display:flex; flex-direction:column; gap:18px;">

                {{-- Identity --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted); margin-bottom:6px;">Nama Modul</label>
                        <input type="text" name="name" value="{{ $module->name }}"
                               style="width:100%; padding:8px 12px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg); font-family:inherit; outline:none; transition:border-color .15s, box-shadow .15s;"
                               onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                               onblur="this.style.borderColor='#D0D5DD'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted); margin-bottom:6px;">System Slug</label>
                        <input type="text" value="{{ $module->slug }}" disabled
                               style="width:100%; padding:8px 12px; background:var(--c-bg); border:1px solid var(--c-border); border-radius:8px; font-size:12px; color:var(--c-fg-muted); font-family:inherit; cursor:not-allowed; font-style:italic;">
                    </div>
                </div>

                {{-- Resource Limits --}}
                <div>
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:10px;">
                        <svg width="13" height="13" fill="none" stroke="var(--c-primary)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-primary);">Resource Limits</span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--c-fg-muted); margin-bottom:6px;">Max Upload (MB)</label>
                            <input type="number" name="settings[max_upload]" value="{{ $module->setting('max_upload', 10) }}" min="1" max="100"
                                   style="width:100%; padding:8px 12px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; color:var(--c-fg); font-family:inherit; outline:none; transition:border-color .15s, box-shadow .15s;"
                                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                                   onblur="this.style.borderColor='#D0D5DD'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label style="display:block; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--c-fg-muted); margin-bottom:6px;">Storage Quota (GB)</label>
                            <input type="number" name="settings[quota]" value="{{ $module->setting('quota', 5) }}" min="1" max="500"
                                   style="width:100%; padding:8px 12px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; color:var(--c-fg); font-family:inherit; outline:none; transition:border-color .15s, box-shadow .15s;"
                                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                                   onblur="this.style.borderColor='#D0D5DD'; this.style.boxShadow='none'">
                        </div>
                    </div>
                </div>

                {{-- Advanced Config --}}
                <div>
                    <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted); margin-bottom:10px;">Advanced Config</p>

                    {{-- Debug mode checkbox --}}
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--c-bg); border:1px solid var(--c-border); border-radius:10px; cursor:pointer; margin-bottom:8px; transition:border-color .15s;"
                           onmouseover="this.style.borderColor='var(--c-primary-border)'" onmouseout="this.style.borderColor='var(--c-border)'">
                        <input type="checkbox" name="settings[debug_mode]" value="1"
                               {{ $module->setting('debug_mode', false) ? 'checked' : '' }}
                               style="width:14px; height:14px; accent-color:var(--c-primary); cursor:pointer; flex-shrink:0;">
                        <div>
                            <span style="display:block; font-size:12px; font-weight:600; color:var(--c-fg); line-height:1.2;">Debug Mode</span>
                            <span style="font-size:10px; color:var(--c-fg-muted);">Log exceptions & slow queries untuk modul ini.</span>
                        </div>
                    </label>

                    {{-- Status info (read-only) --}}
                    <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--c-bg); border:1px solid var(--c-border); border-radius:10px;">
                        <svg width="16" height="16" fill="none" stroke="{{ $module->is_active ? 'var(--c-success)' : 'var(--c-warning)' }}" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            @if($module->is_active)
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            @else
                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            @endif
                        </svg>
                        <div style="flex:1;">
                            <span style="display:block; font-size:12px; font-weight:600; color:var(--c-fg); line-height:1.2;">Status Modul</span>
                            <span style="font-size:10px; color:var(--c-fg-muted);">Gunakan toggle di card untuk mengubah status.</span>
                        </div>
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:3px 8px; border-radius:9999px; background:{{ $module->is_active ? 'var(--c-success-subtle)' : 'var(--c-warning-subtle)' }}; color:{{ $module->is_active ? '#287F6E' : 'var(--c-warning)' }};">
                            {{ $module->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 22px; border-top:1px solid var(--c-border); background:var(--c-bg);">
                {{-- Purge cache --}}
                <button type="button"
                        onclick="if(confirm('Bersihkan cache modul {{ $module->name }}?')) document.getElementById('purge-{{ $module->slug }}').submit();"
                        title="Purge Cache"
                        style="display:flex; align-items:center; gap:6px; padding:7px 12px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:11px; font-weight:600; color:var(--c-fg-muted); cursor:pointer; font-family:inherit; transition:background .15s, color .15s;"
                        onmouseover="this.style.background='var(--c-warning-subtle)'; this.style.color='var(--c-warning)'; this.style.borderColor='#FBD982'"
                        onmouseout="this.style.background='#fff'; this.style.color='var(--c-fg-muted)'; this.style.borderColor='var(--c-border)'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Purge Cache
                </button>

                {{-- Save --}}
                <button type="submit"
                        style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z M17 21v-8H7v8 M7 3v5h8"/>
                    </svg>
                    Save Config
                </button>
            </div>
        </form>

        <form id="purge-{{ $module->slug }}" action="{{ route('superadmin.modules.purge-cache', $module->slug) }}" method="POST" style="display:none;">@csrf</form>
    </div>
</div>