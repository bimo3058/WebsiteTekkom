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
    <div class="mp-card flex-1 min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">
                Anggota Terdaftar
                @if($praktikum) &mdash; <span style="font-weight:400;color:#666D80;">{{ $praktikum->nama }}</span> @endif
            </span>
            <span class="mp-badge neutral sm">{{ $anggota->count() }} anggota</span>
        </div>

        {{-- Info dosen & koor praktikum --}}
        @if($praktikum)
        <div style="padding:12px 20px;border-bottom:1px solid #DFE1E7;background:#F9FAFB;display:flex;gap:24px;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;font-weight:600;color:#666D80;">Dosen:</span>
                <div class="mp-av sky" style="width:22px;height:22px;font-size:9px;">{{ strtoupper(substr($praktikum->dosen?->name ?? 'D', 0, 1)) }}</div>
                <span style="font-size:12px;color:#0D0D12;">{{ $praktikum->dosen?->name ?? '—' }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;font-weight:600;color:#666D80;">Koordinator:</span>
                <span style="font-size:12px;color:#0D0D12;">{{ $praktikum->koordinator?->name ?? '—' }}</span>
            </div>
        </div>
        @endif

        {{-- Table header --}}
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
            <div class="mp-th flex-1">Pengguna</div>
            <div class="mp-th" style="width:110px;">Role</div>
            <div class="mp-th" style="width:90px;text-align:right;">Aksi</div>
        </div>

        {{-- Rows --}}
        <div style="overflow-y:auto;flex:1;">
            @forelse($anggota as $a)
            <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;"
                 onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                {{-- Avatar + Nama --}}
                <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0;padding-right:12px;">
                    @if($a->role === 'koor')
                    <div class="mp-av violet">{{ strtoupper(substr($a->user?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($a->user?->name ?? 'U', strpos(($a->user?->name ?? 'U').' ', ' ')+1, 1)) }}</div>
                    @else
                    <div class="mp-av green">{{ strtoupper(substr($a->user?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($a->user?->name ?? 'U', strpos(($a->user?->name ?? 'U').' ', ' ')+1, 1)) }}</div>
                    @endif
                    <div style="min-width:0;">
                        <div style="font-size:13px;font-weight:500;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $a->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#666D80;">{{ $a->user?->email ?? '—' }}</div>
                    </div>
                </div>
                {{-- Badge role --}}
                <div style="width:110px;">
                    @if($a->role === 'koor')
                    <span class="mp-badge navy sm"><span class="dot"></span>Koordinator</span>
                    @else
                    <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
                    @endif
                </div>
                {{-- Revoke --}}
                <div style="width:90px;text-align:right;">
                    <form method="POST"
                          action="{{ route('eoffice.manprak.admin.kelola-role.revoke', $a->id) }}"
                          onsubmit="return confirm('Cabut role {{ $a->role }} dari {{ $a->user?->name }}?')"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Cabut</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asprak atau koordinator di praktikum ini.</div>
            </div>
            @endforelse
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
