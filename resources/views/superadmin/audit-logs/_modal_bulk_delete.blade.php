{{-- resources/views/superadmin/audit-logs/_modal_bulk_delete.blade.php --}}
<div id="modalBulkDeleteAudit"
     style="display:none; position:fixed; inset:0; z-index:100; align-items:center; justify-content:center; padding:16px; background:rgba(13,13,18,0.5); backdrop-filter:blur(2px);">
    <div style="position:fixed; inset:0;" onclick="closeModal('modalBulkDeleteAudit')"></div>
    <div style="position:relative; width:100%; max-width:420px; background:#fff; border-radius:16px; border:1px solid var(--c-border); box-shadow:0 20px 40px rgba(13,13,18,0.15); overflow:hidden;">

        {{-- Top stripe --}}
        <div style="height:3px; background:linear-gradient(90deg, var(--c-error) 0%, #ED8296 100%);"></div>

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px 0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:38px; height:38px; border-radius:10px; background:var(--c-error-subtle); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="var(--c-error)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:14px; font-weight:700; color:var(--c-fg); line-height:1.2;">Hapus Log Aktivitas</h3>
                    <p style="font-size:11px; color:var(--c-fg-muted); margin-top:2px;">Pilih kriteria log yang akan dihapus</p>
                </div>
            </div>
            <button onclick="closeModal('modalBulkDeleteAudit')"
                    style="width:28px; height:28px; border-radius:7px; border:1px solid var(--c-border); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-fg-muted); transition:background .15s;"
                    onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="formBulkDeleteAudit" action="{{ route('superadmin.audit-logs.bulk-destroy') }}" method="POST" style="padding:20px 24px 24px;">
            @csrf
            @method('DELETE')

            <div id="selectedIdsContainer"></div>

            {{-- Options --}}
            <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">

                @foreach([
                    ['6hours',  'Log lebih dari 6 jam',  'Semua log yang berumur > 6 jam'],
                    ['12hours', 'Log lebih dari 12 jam', 'Semua log yang berumur > 12 jam'],
                    ['24hours', 'Log lebih dari 24 jam', 'Semua log yang berumur > 24 jam'],
                ] as [$val, $label, $desc])
                <label style="display:flex; align-items:center; gap:12px; padding:10px 14px; border:1px solid var(--c-border); border-radius:10px; cursor:pointer; transition:border-color .15s, background .15s;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <input type="radio" name="delete_type" value="{{ $val }}"
                           style="width:14px; height:14px; accent-color:var(--c-error); cursor:pointer; flex-shrink:0;">
                    <div>
                        <span style="font-size:13px; font-weight:600; color:var(--c-fg); display:block;">{{ $label }}</span>
                        <span style="font-size:10px; color:var(--c-fg-muted);">{{ $desc }}</span>
                    </div>
                </label>
                @endforeach

                {{-- Divider --}}
                <div style="position:relative; margin:4px 0;">
                    <div style="position:absolute; inset:0; display:flex; align-items:center;">
                        <div style="width:100%; height:1px; background:var(--c-border);"></div>
                    </div>
                    <div style="position:relative; display:flex; justify-content:center;">
                        <span style="background:#fff; padding:0 10px; font-size:10px; font-weight:700; color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:0.06em;">Atau</span>
                    </div>
                </div>

                {{-- Selected --}}
                <label style="display:flex; align-items:center; gap:12px; padding:10px 14px; border:1px solid var(--c-border); border-radius:10px; cursor:pointer; transition:border-color .15s, background .15s;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <input type="radio" name="delete_type" value="selected" id="deleteSelectedRadio"
                           style="width:14px; height:14px; accent-color:var(--c-error); cursor:pointer; flex-shrink:0;">
                    <div>
                        <span style="font-size:13px; font-weight:600; color:var(--c-fg); display:block;">Hanya log yang dipilih</span>
                        <span style="font-size:10px; color:var(--c-fg-muted);">
                            Menghapus <span id="selectedCountText" style="font-weight:700; color:var(--c-error);">0</span> log yang dicentang
                        </span>
                    </div>
                </label>
            </div>

            {{-- Warning --}}
            <div style="display:flex; align-items:flex-start; gap:10px; padding:12px 14px; background:var(--c-warning-subtle); border:1px solid #FBD982; border-radius:10px; margin-bottom:20px;">
                <svg width="16" height="16" fill="none" stroke="var(--c-warning)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink:0; margin-top:1px;">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                </svg>
                <p style="font-size:11px; color:#5B3D1E; line-height:1.6;">
                    <strong>Peringatan:</strong> Data log yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            {{-- Buttons --}}
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeModal('modalBulkDeleteAudit')"
                        style="flex:1; padding:10px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1; padding:10px; background:var(--c-error); border:none; border-radius:8px; font-size:12px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:6px; transition:background .15s;"
                        onmouseover="this.style.background='#95122B'" onmouseout="this.style.background='var(--c-error)'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Log
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#modalBulkDeleteAudit.active { display: flex !important; }
</style>