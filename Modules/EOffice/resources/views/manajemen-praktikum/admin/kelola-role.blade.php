<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Role — Manajemen Praktikum">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Role Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Assign atau cabut role asprak dan koordinator per praktikum · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select">
                @foreach($praktikumList as $prak)
                <option value="{{ $prak->id }}" {{ $prak->id == $praktikumId ? 'selected' : '' }}>
                    {{ $prak->nama }}
                    @if($prak->dosen) &mdash; {{ $prak->dosen->name }} @endif
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Manajemen Role</span>
    <span class="sec-rule"></span>
    @if($praktikum)
    <span style="font-size:12px;color:#666D80;">{{ $praktikum->nama }}</span>
    @endif
</div>

@if($praktikumList->isEmpty())
<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada praktikum yang tersedia.</div>
    </div>
</div>
@else

<div class="flex gap-4 flex-1 min-h-0">

{{-- Daftar Anggota --}}
    <div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">
                Anggota Terdaftar
                @if($praktikum) &mdash; <span style="font-weight:400;color:var(--c-fg-muted, #666D80);">{{ $praktikum->nama }}</span> @endif
            </h2>
            <span class="mp-badge neutral sm">{{ $anggota->count() }} anggota</span>
        </div>

        @if($praktikum)
        <div style="padding:12px 16px; border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA; display:flex; gap:24px; flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;font-weight:600;color:var(--c-fg-muted, #666D80);">Dosen:</span>
                <div class="mp-av sky" style="width:22px;height:22px;font-size:9px;">{{ strtoupper(substr($praktikum->dosen?->name ?? 'D', 0, 1)) }}</div>
                <span style="font-size:12px;color:var(--c-fg, #0D0D12);">{{ $praktikum->dosen?->name ?? '—' }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;font-weight:600;color:var(--c-fg-muted, #666D80);">Koordinator:</span>
                <span style="font-size:12px;color:var(--c-fg, #0D0D12);">{{ $praktikum->koordinator?->name ?? '—' }}</span>
            </div>
        </div>
        @endif

        <div style="overflow-x:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; min-width:400px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Pengguna</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">Role</th>
                        <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggota as $a)
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                        onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($a->role === 'koor')
                                <div class="mp-av violet">{{ strtoupper(substr($a->user?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($a->user?->name ?? 'U', strpos(($a->user?->name ?? 'U').' ', ' ')+1, 1)) }}</div>
                                @else
                                <div class="mp-av green">{{ strtoupper(substr($a->user?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($a->user?->name ?? 'U', strpos(($a->user?->name ?? 'U').' ', ' ')+1, 1)) }}</div>
                                @endif
                                <div style="min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--c-fg, #0D0D12);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $a->user?->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--c-fg-muted, #666D80);">{{ $a->user?->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px;">
                            @if($a->role === 'koor')
                            <span class="mp-badge navy sm"><span class="dot"></span>Koordinator</span>
                            @else
                            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            <form method="POST"
                                  action="{{ route('eoffice.manprak.admin.kelola-role.revoke', $a->id) }}"
                                  onsubmit="return confirm('Cabut role {{ $a->role }} dari {{ $a->user?->name }}?')"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mp-btn secondary sm" style="color:#EF4444;border-color:#F87171;">Cabut</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            <div style="font-size:13px;font-weight:500;color:var(--c-fg-muted, #666D80);">Belum ada asprak atau koordinator di praktikum ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>

    {{-- Form Assign --}}
    <div class="mp-card flex-shrink-0" style="width:280px;">
        <div class="mp-card-header">
            <span class="mp-card-title">Assign Role</span>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.kelola-role.assign') }}"
              style="display:flex;flex-direction:column;gap:16px;padding:20px;flex:1;">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            {{-- Pilih User --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">User</label>
                <select name="user_id" required class="mp-input mp-select w-full">
                    <option value="">— Pilih user —</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('user_id')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Pilih Role --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Role</label>
                <div style="display:flex;gap:12px;">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                        <input type="radio" name="role" value="asprak" checked class="accent-[#0B266E]">
                        <span style="font-size:13px;color:#0D0D12;">Asprak</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                        <input type="radio" name="role" value="koor" class="accent-[#0B266E]">
                        <span style="font-size:13px;color:#0D0D12;">Koordinator</span>
                    </label>
                </div>
                @error('role')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="mp-btn primary md w-full" style="margin-top:auto;">Assign</button>
        </form>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
