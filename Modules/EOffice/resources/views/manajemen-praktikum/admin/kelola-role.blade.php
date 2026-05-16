<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Role — Manajemen Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Kelola Role Praktikum</h1>
        <p class="mp-page-sub">Assign atau cabut role asprak dan koordinator per praktikum</p>
    </div>
</div>

{{-- Pilih Praktikum --}}
<div class="flex-shrink-0">
    <form method="GET" class="flex gap-2 items-center">
        <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select">
            @foreach($praktikumList as $prak)
            <option value="{{ $prak->id }}" {{ $prak->id == $praktikumId ? 'selected' : '' }}>
                {{ $prak->nama }}
                @if($prak->dosen) — {{ $prak->dosen->name }} @endif
            </option>
            @endforeach
        </select>
    </form>
</div>

@if($praktikumList->isEmpty())
<div class="flex-1 flex items-center justify-center" style="font-size:14px;color:var(--c-fg-muted);">
    Tidak ada praktikum yang tersedia.
</div>
@else

<div class="flex gap-4 flex-1 min-h-0">

    {{-- Daftar Anggota --}}
    <div class="mp-card flex-1 min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">
                Anggota Terdaftar
                @if($praktikum) — <span style="font-weight:400;">{{ $praktikum->nama }}</span> @endif
            </span>
            <span style="font-size:12px;color:var(--c-fg-muted);">{{ $anggota->count() }} anggota</span>
        </div>

        {{-- Info dosen & koor praktikum --}}
        @if($praktikum)
        <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);background:#FAFBFC;display:flex;gap:24px;flex-shrink:0;">
            <div class="flex items-center gap-2">
                <span style="font-size:11px;font-weight:600;color:var(--c-fg-muted);">Dosen:</span>
                <span style="font-size:12px;color:var(--c-fg);">{{ $praktikum->dosen?->name ?? '—' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span style="font-size:11px;font-weight:600;color:var(--c-fg-muted);">Koor:</span>
                <span style="font-size:12px;color:var(--c-fg);">{{ $praktikum->koordinator?->name ?? '—' }}</span>
            </div>
        </div>
        @endif

        {{-- Rows --}}
        <div class="overflow-y-auto flex-1">
            @forelse($anggota as $a)
            <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
                {{-- Avatar + Nama --}}
                <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                    <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                        {{ strtoupper(substr($a->user?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $a->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);">{{ $a->user?->email ?? '—' }}</div>
                    </div>
                </div>
                {{-- Badge role --}}
                <div style="width:100px;">
                    @if($a->role === 'koor')
                    <span class="mp-badge sky sm">Koordinator</span>
                    @else
                    <span class="mp-badge success sm">Asprak</span>
                    @endif
                </div>
                {{-- Revoke --}}
                <div style="width:80px;text-align:right;">
                    <form method="POST"
                          action="{{ route('eoffice.manprak.admin.kelola-role.revoke', $a->id) }}"
                          onsubmit="return confirm('Cabut role {{ $a->role }} dari {{ $a->user?->name }}?')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mp-btn destructive sm">Cabut</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:48px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">
                Belum ada asprak atau koordinator di praktikum ini.
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
              class="flex flex-col gap-4 p-5 flex-1">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            {{-- Pilih User --}}
            <div>
                <label class="block text-[11px] font-semibold text-[#666D80] uppercase tracking-[.05em] mb-[6px]">User</label>
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
                <label class="block text-[11px] font-semibold text-[#666D80] uppercase tracking-[.05em] mb-[6px]">Role</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="role" value="asprak" checked class="accent-[#0B266E]">
                        <span style="font-size:13px;color:var(--c-fg);">Asprak</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="role" value="koor" class="accent-[#0B266E]">
                        <span style="font-size:13px;color:var(--c-fg);">Koordinator</span>
                    </label>
                </div>
                @error('role')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="mp-btn primary md w-full mt-auto">Assign</button>
        </form>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
