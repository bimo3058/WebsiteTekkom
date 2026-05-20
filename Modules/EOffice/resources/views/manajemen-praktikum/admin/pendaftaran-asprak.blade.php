<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asisten Praktikum">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Asisten Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Review dan proses permohonan calon asisten praktikum</p>
    </div>
</div>

{{-- Filter Status --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter Status</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:14px 16px;">
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
           class="mp-badge neutral sm" style="text-decoration:none;padding:6px 12px;cursor:pointer;border:{{ !request('status') ? '2px solid #0B266E' : '1px solid #DFE1E7' }};">
            Semua
        </a>
        @foreach(['pending'=>['warning','Menunggu'], 'approved'=>['success','Disetujui'], 'rejected'=>['error','Ditolak']] as $st => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $st]) }}"
           class="mp-badge {{ $label[0] }} sm" style="text-decoration:none;padding:6px 12px;cursor:pointer;border:{{ request('status') === $st ? '2px solid #0B266E' : '1px solid transparent' }};">
            {{ $label[1] }}
        </a>
        @endforeach
    </div>
</div>

{{-- Daftar Pendaftar --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Asisten Praktikum</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ count($pendaftaran ?? []) }} pendaftar</span>
</div>

<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div style="overflow-x:auto; flex:1;">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama / NIM</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Praktikum</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">IPK</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Motivasi</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Terdaftar</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran ?? [] as $pend)
                @php
                    $nameParts = explode(' ', $pend->user?->name ?? 'AS');
                    $initials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet'];
                    $avColor = $avColors[crc32($pend->user?->email ?? '') % count($avColors)];
                @endphp
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);">{{ $pend->user?->name ?? '—' }}</div>
                                <div style="font-size:11px; color:var(--c-fg-muted, #666D80);">{{ $pend->user?->student_number ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px; color:var(--c-fg, #0D0D12); font-size:13px;">{{ $pend->praktikum?->nama ?? '—' }}</td>
                    <td style="padding:14px 16px; text-align:center;">
                        <span style="font-size:13px; font-weight:700; color:{{ ($pend->ipk ?? 0) >= 3.0 ? '#10B981' : '#EF4444' }};">{{ number_format($pend->ipk ?? 0, 2) }}</span>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        @if($pend->motivasi)
                        <span class="mp-badge success sm"><span class="dot"></span>Ada</span>
                        @else
                        <span class="mp-badge neutral sm">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px; text-align:center; font-size:12px; color:var(--c-fg-muted, #666D80);">
                        {{ $pend->created_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        @if($pend->status === 'pending')
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        @elseif($pend->status === 'approved')
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                        @else
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        @if($pend->status === 'pending')
                        <div class="flex gap-2 justify-center">
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="mp-btn ghost sm">Terima</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}"
                                  onsubmit="return confirm('Tolak pendaftaran ini?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        @else
                        <span style="font-size:11px;color:var(--c-fg-muted, #666D80);">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
                        </svg>
                        <div style="font-size:13px;color:var(--c-fg-muted, #666D80);">Tidak ada data pendaftaran asisten praktikum.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'links'))
    <div style="padding:12px 16px;border-top:1px solid var(--c-border, #DFE1E7);flex-shrink:0;">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>