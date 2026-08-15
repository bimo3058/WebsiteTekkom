<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Role — Manajemen Praktikum">

{{-- ════════════════════════
     PAGE HEADER
════════════════════════ --}}
<div class="mp-page-header" style="flex-shrink:0;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Role Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Assign atau cabut role asisten praktikum & koordinator per praktikum · {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            @php
                $praktikumOptions = [];
                foreach($praktikumList as $prak) {
                    $label = $prak->nama;
                    $label .= " · {$prak->semester} {$prak->tahun_ajaran}";
                    $praktikumOptions[] = ['value' => (string)$prak->id, 'label' => $label];
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string)$praktikumId"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="240px"
                searchable="true"
                searchPlaceholder="Cari Praktikum..."
            />
        </form>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mp-alert success flex-shrink-0">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mp-alert warning flex-shrink-0">{{ session('error') }}</div>
@endif

@if($praktikumList->isEmpty())
{{-- Empty state --}}
<div class="mp-card flex-shrink-0">
    <div style="padding:56px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada praktikum yang tersedia.</div>
    </div>
</div>

@else

{{-- Info strip praktikum terpilih --}}
@if($praktikum)
<div style="background:#fff;border:1px solid #DFE1E7;border-radius:11px;padding:12px 16px;display:flex;align-items:center;gap:16px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);">
    <div style="flex:1;min-width:0;">
        <div style="font-size:13px;font-weight:700;color:#0D0D12;">
            {{ $praktikum->nama }}
            @if(!$praktikum->is_active)
            <span class="mp-badge warning sm" style="margin-left:8px;"><span class="dot"></span>Non-Aktif</span>
            @endif
        </div>
        <div style="font-size:11px;color:#A4ABB8;margin-top:1px;">{{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</div>
    </div>
    <div style="display:flex;gap:16px;flex-shrink:0;">
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Dosen</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;">{{ $praktikum->dosen?->name ?? '—' }}</div>
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Koordinator</div>
            @if($praktikum->koordinator)
            <div style="font-size:12px;font-weight:600;color:#10B981;">{{ $praktikum->koordinator->name }}</div>
            @else
            <div style="font-size:12px;font-weight:600;color:#EF4444;">Belum ditunjuk</div>
            @endif
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Anggota</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;">{{ $anggota->count() }} orang</div>
        </div>
    </div>
</div>
@endif

@if($praktikum)
{{-- Main layout: tabel kiri + form assign kanan --}}
<div style="display:flex;gap:14px;flex:1;min-height:0;">

    {{-- ══ TABEL ANGGOTA ══════════════════════════════════════════════════ --}}
    <div style="flex:1;min-width:0;display:flex;flex-direction:column;overflow:hidden;">

        {{-- Toolbar --}}
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:12px 16px;margin-bottom:10px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:13px;font-weight:700;color:#0D0D12;">Anggota Terdaftar</div>
                <div style="font-size:11px;color:#A4ABB8;margin-top:1px;">
                    @php
                        $asprakCount = $anggota->where('role','asprak')->count();
                        $koorCount   = $anggota->where('role','koor')->count();
                    @endphp
                    {{ $asprakCount }} asisten praktikum · {{ $koorCount }} koordinator
                </div>
            </div>
            <div style="display:flex;gap:6px;align-items:center;">
                <span class="mp-badge success sm" style="height:fit-content;"><span class="dot"></span>{{ $asprakCount }} Asisten Praktikum</span>
                <span class="mp-badge navy sm" style="height:fit-content;"><span class="dot"></span>{{ $koorCount }} Koordinator</span>
                @if($asprakCount > 0 || $koorCount > 0)
                <div style="width:1px;height:24px;background:#DFE1E7;margin:0 4px;"></div>
                <form method="POST" action="{{ route('eoffice.manprak.admin.kelola-role.revokeAll', $praktikum->id) }}" onsubmit="return confirm('PENTING: Anda akan mencabut SELURUH role Asisten Praktikum dan Koordinator pada praktikum ini.\n\nPraktikum ini akan otomatis dinonaktifkan (mode read-only) setelah proses selesai.\nLanjutkan?')">
                    @csrf
                    <button type="submit" class="mp-btn destructive md" style="height:30px;font-size:12px;padding:0 12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="margin-right:4px;"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        Cabut Semua Role
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Tabel --}}
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;flex-direction:column;flex:1;min-height:0;">
            <div style="overflow-y:auto;flex:1;">
                <table style="width:100%;border-collapse:collapse;min-width:420px;">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr style="border-bottom:1px solid #DFE1E7;background:#FAFAFA;">
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">Pengguna</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:110px;">Role</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Bergabung</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $i => $a)
                        @php
                            $np  = explode(' ', $a->user?->name ?? 'U');
                            $ini = strtoupper(substr($np[0]??'U',0,1).substr($np[1]??$np[0]??'U',0,1));
                            $avc = $a->role === 'koor' ? 'violet' : 'green';
                        @endphp
                        <tr style="border-bottom:1px solid #F8F9FB;transition:background .1s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:13px 16px;font-size:11px;color:#C8CAD4;text-align:center;">{{ $i + 1 }}</td>
                            <td style="padding:13px 16px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="mp-av {{ $avc }}" style="width:30px;height:30px;font-size:11px;flex-shrink:0;">{{ $ini }}</div>
                                    <div style="min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;">{{ $a->user?->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:#A4ABB8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;">{{ $a->user?->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                @if($a->role === 'koor')
                                <span class="mp-badge navy sm"><span class="dot"></span>Koordinator</span>
                                @else
                                <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
                                @endif
                                @if($a->trashed())
                                    <span class="mp-badge error sm" style="margin-top:4px;"><span class="dot"></span>Dicabut</span>
                                @endif
                            </td>
                            <td style="padding:13px 16px;text-align:center;font-size:11px;color:#A4ABB8;white-space:nowrap;">
                                {{ $a->created_at?->locale('id')->isoFormat('D MMM YY') ?? '—' }}
                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                @if($a->trashed())
                                <form method="POST"
                                      action="{{ route('eoffice.manprak.admin.kelola-role.restore', $a->id) }}"
                                      onsubmit="return confirm('Kembalikan role {{ $a->role }} untuk {{ addslashes($a->user?->name ?? '') }}?')"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                            class="mp-btn"
                                            style="font-size:11px;font-weight:600;padding:5px 10px;border-radius:6px;border:1px solid #D1FAE5;background:#ECFDF5;color:#059669;cursor:pointer;transition:all .12s;display:flex;align-items:center;gap:4px;white-space:nowrap;"
                                            onmouseover="this.style.background='#D1FAE5'" onmouseout="this.style.background='#ECFDF5'">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                        Kembalikan Role
                                    </button>
                                </form>
                                @else
                                <form method="POST"
                                      action="{{ route('eoffice.manprak.admin.kelola-role.revoke', $a->id) }}"
                                      onsubmit="return confirm('Cabut role {{ $a->role }} dari {{ addslashes($a->user?->name ?? '') }}?\n\nJika user tidak terdaftar di praktikum lain, role Spatie akan ikut dicabut.')"
                                      style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="mp-btn"
                                            style="font-size:11px;font-weight:600;padding:5px 10px;border-radius:6px;border:1px solid #FADAE1;background:#FFF5F6;color:#DF1C41;cursor:pointer;transition:all .12s;display:flex;align-items:center;gap:4px;white-space:nowrap;"
                                            onmouseover="this.style.background='#FADAE1'" onmouseout="this.style.background='#FFF5F6'">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        Cabut Role
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:56px;text-align:center;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada asisten praktikum atau koordinator di praktikum ini.</div>
                                <div style="font-size:12px;color:#A4ABB8;margin-top:4px;">Asisten dan Koordinator yang diterima pada menu Pendaftaran akan muncul di sini.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- /Tabel --}}
</div>
@endif
@endif

</x-eoffice::manajemen-praktikum.layout>