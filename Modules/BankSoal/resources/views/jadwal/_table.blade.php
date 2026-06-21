<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">
    
    {{-- Table Toolbar --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Daftar Sesi Ujian</h2>
        @include('banksoal::jadwal._search_filter')
    </div>

    {{-- Table Content --}}
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:850px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Nama Sesi</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Waktu Mulai/Selesai</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Kuota</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Token</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                    <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $index => $jadwal)
                    @php
                        // Check if paginated or simple collection
                        $isPaginated = $jadwals instanceof \Illuminate\Pagination\LengthAwarePaginator;
                        $rowNo = $isPaginated ? (($jadwals->currentPage() - 1) * $jadwals->perPage() + $index + 1) : ($index + 1);
                        
                        $statusVal = is_object($jadwal->status) ? $jadwal->status->value : $jadwal->status;
                        
                        // Default colors for Aktif/Belum Mulai
                        $badgeColor = '#059669'; 
                        $badgeBg = '#ECFDF5';
                        $badgeLabel = ucfirst($statusVal ?? 'Belum Mulai');
                        
                        if(in_array($statusVal, ['selesai', 'ditutup'])) {
                            $badgeColor = '#475569';
                            $badgeBg = '#F1F5F9';
                        } elseif(in_array($statusVal, ['berjalan', 'aktif'])) {
                            $badgeColor = '#059669';
                            $badgeBg = '#ECFDF5';
                        } elseif(in_array($statusVal, ['pending', 'draft'])) {
                            $badgeColor = '#D97706';
                            $badgeBg = '#FFFBEB';
                        }
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px; font-size:13px; font-weight:400; color:var(--c-fg-muted);">{{ $rowNo }}</td>
                        
                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">{{ $jadwal->nama_sesi }}</p>
                            <p style="font-size:12px; color:var(--c-fg-muted); margin:2px 0 0 0;">{{ $jadwal->ruangan ?? 'Ruangan belum diatur' }}</p>
                        </td>

                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                {{ $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('d F Y') : '-' }}
                            </p>
                        </td>

                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB
                            </p>
                        </td>

                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                {{ $jadwal->kuota }} Mhs
                            </p>
                        </td>

                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; font-family: monospace; letter-spacing: 0.05em;">
                                {{ $jadwal->token ?? '-' }}
                            </p>
                        </td>

                        <td style="padding:12px 16px;">
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:{{ $badgeBg }}; color:{{ $badgeColor }}; margin-bottom: 2px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:{{ $badgeColor }};"></span>
                                {{ $badgeLabel }}
                            </span>
                        </td>

                        <td style="padding:14px 16px; text-align:right;">
                            <div style="position:relative; display:inline-block; text-align:left;" x-data="{ actionOpen: false }">
                                <button type="button" @click="actionOpen = !actionOpen" @click.outside="actionOpen = false"
                                        style="width:28px; height:28px; border-radius:6px; border:1px solid var(--c-border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg-muted); transition:all .15s; margin:0 auto;"
                                        onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
                                        onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                </button>

                                <div x-show="actionOpen"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     style="position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid var(--c-border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.1); min-width:160px; z-index:40; overflow:hidden; display:none;">
                                    <div style="padding:5px;">

                                        {{-- Hapus --}}
                                        <form action="{{ route('banksoal.periode.jadwal.destroy', $jadwal->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.45 8.45 3 9 3H15C15.55 3 16 3.45 16 4V6M19 6L18.12 19.13C18.05 20.18 17.18 21 16.13 21H7.87C6.82 21 5.95 20.18 5.88 19.13L5 6H19Z"/></svg>
                                                Hapus Sesi
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:60px 24px; text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.86 10.83C15.58 9.73 16 8.42 16 7C16 5.58 15.58 4.27 14.86 3.17C15.22 3.06 15.6 3 16 3C18.21 3 20 4.79 20 7C20 9.21 18.21 11 16 11C15.6 11 15.22 10.94 14.86 10.83ZM17.87 21C17.96 20.68 18 20.35 18 20V19C18 17.11 17.34 15.37 16.25 14H17C19.76 14 22 16.24 22 19V20C22 20.55 21.55 21 21 21H17.87Z" fill="currentColor"/>
                                    <path d="M10 14H8C5.24 14 3 16.24 3 19V20C3 20.55 3.45 21 4 21H14C14.55 21 15 20.55 15 20V19C15 16.24 12.76 14 10 14Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M9 11C11.21 11 13 9.21 13 7C13 4.79 11.21 3 9 3C6.79 3 5 4.79 5 7C5 9.21 6.79 11 9 11Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em;">Tidak Ada Jadwal Sesi</p>
                                <p style="font-size:11px; color:var(--c-fg-placeholder);">Belum ada sesi ujian yang dibuat pada periode ini. Silakan klik "Tambah Sesi" di bagian atas untuk memulai pengaturan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Area --}}
    @include('banksoal::jadwal._pagination')
</div>
