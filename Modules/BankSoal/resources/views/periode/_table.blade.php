<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">
    
    {{-- Table Toolbar --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Daftar Periode</h2>
        @include('banksoal::periode._search_filter')
    </div>

    {{-- Table Content --}}
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Nama Periode</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Timeline Pendaftaran</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Rentang Ujian</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                    <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $index => $periode)
                    @php
                        $rowNo = ($periodes->currentPage() - 1) * $periodes->perPage() + $index + 1;
                        $now = now();
                        $tglMulai = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
                        $tglSelesai = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
                        $hasPendaftar = \Modules\BankSoal\Models\Komprehensif\PendaftarUjian::where('periode_ujian_id', $periode->id)->exists();
                        $periodeData = [
                            'id' => $periode->id,
                            'nama_periode' => $periode->nama_periode,
                            'tanggal_mulai' => $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d') : null,
                            'tanggal_selesai' => $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d') : null,
                            'tanggal_mulai_ujian' => $periode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('Y-m-d') : null,
                            'tanggal_selesai_ujian' => $periode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('Y-m-d') : null,
                            'status' => $periode->status,
                            'deskripsi' => $periode->deskripsi,
                            'kuota_peserta' => $periode->kuota_peserta,
                            'target_wisuda_options' => $periode->target_wisuda_options ?? [],
                        ];

                        $tglSelesaiUjian = $periode->tanggal_selesai_ujian
                            ? \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->endOfDay()
                            : \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();

                        $badgeColor = '#059669'; // default Aktif
                        $badgeBg = '#ECFDF5';
                        $badgeLabel = 'Aktif';

                        if ($now->lt($tglMulai)) {
                            // Belum mulai
                            $badgeColor = '#475569';
                            $badgeBg = '#F1F5F9';
                            $badgeLabel = 'Draft';
                        } elseif ($now->gt($tglSelesaiUjian)) {
                            // Melewati hari terakhir ujian
                            $badgeColor = '#475569';
                            $badgeBg = '#F1F5F9';
                            $badgeLabel = 'Selesai';
                        }
                        // else: aktif (dari pendaftaran s.d. ujian terakhir)

                        $daftarDitutup = $periode->pendaftaran_ditutup_paksa && $periode->status === 'aktif';
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px; font-size:13px; font-weight:400; color:var(--c-fg-muted);">{{ $rowNo }}</td>
                        
                        <td style="padding:14px 16px; min-width:200px;">
                            <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">{{ $periode->nama_periode }}</p>
                        </td>

                        <td style="padding:14px 16px;">
                            <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y') }}
                            </p>
                        </td>

                        <td style="padding:14px 16px;">
                            @if($periode->tanggal_mulai_ujian && $periode->tanggal_selesai_ujian)
                            <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->translatedFormat('d F Y') }}
                            </p>
                            @else
                            <span style="font-size:13px; color:var(--c-fg-muted); font-style:italic;">Belum diatur</span>
                            @endif
                        </td>

                        <td style="padding:12px 16px;">
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:{{ $badgeBg }}; color:{{ $badgeColor }}; margin-bottom: 2px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:{{ $badgeColor }};"></span>
                                {{ $badgeLabel }}
                            </span>
                            @if($daftarDitutup)
                                <br/>
                                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:#FFFBEB; color:#D97706; margin-top: 4px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#D97706;"></span>
                                    Daftar Ditutup
                                </span>
                            @endif
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

                                        {{-- Edit --}}
                                        @if($periode->status === 'selesai')
                                            <button type="button" disabled style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:var(--c-fg-placeholder); cursor:not-allowed; font-family:inherit; text-align:left;" title="Tidak dapat diedit — periode sudah selesai">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"/></svg>
                                                Edit Periode
                                            </button>
                                        @else
                                            <button type="button" @click="openEdit({{ \Illuminate\Support\Js::from($periodeData) }}); actionOpen = false" style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:var(--c-fg-sec); cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='none'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"/></svg>
                                                Edit Periode
                                            </button>
                                        @endif

                                        {{-- Tutup Pendaftaran --}}
                                        @if($periode->pendaftaran_terbuka)
                                            <div style="height:1px; background:var(--c-border); margin:4px 6px;"></div>
                                            <button type="button" @click="openConfirm('{{ route('banksoal.periode.close-pendaftaran', $periode->id) }}', 'PATCH', 'Tutup Pendaftaran?', 'Mahasiswa tidak dapat mendaftar lagi meskipun tanggal belum berakhir.', 'bg-yellow-500 hover:bg-yellow-600 text-white', 'text-yellow-500', 'bg-yellow-50'); actionOpen = false" style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#D97706; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background='none'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Tutup Pendaftaran
                                            </button>
                                        @endif

                                        {{-- Buka Kembali Pendaftaran --}}
                                        @if($periode->pendaftaran_ditutup_paksa && $now->lte(\Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay()))
                                            <div style="height:1px; background:var(--c-border); margin:4px 6px;"></div>
                                            <button type="button" @click="openConfirm('{{ route('banksoal.periode.open-pendaftaran', $periode->id) }}', 'PATCH', 'Buka Pendaftaran?', 'Mahasiswa yang memenuhi syarat akan bisa mendaftar lagi.', 'bg-green-600 hover:bg-green-700 text-white', 'text-green-600', 'bg-green-50'); actionOpen = false" style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#059669; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="this.style.background='#ECFDF5'" onmouseout="this.style.background='none'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                Buka Pendaftaran
                                            </button>
                                        @endif

                                        <div style="height:1px; background:var(--c-border); margin:4px 6px;"></div>

                                        {{-- Hapus --}}
                                        @if(!$hasPendaftar)
                                            <button type="button" @click="openConfirm('{{ route('banksoal.periode.destroy', $periode->id) }}', 'DELETE', 'Hapus Periode?', 'Apakah Anda yakin ingin menghapus periode ini? Aksi ini tidak dapat dibatalkan.', 'bg-red-600 hover:bg-red-700 text-white', 'text-red-600', 'bg-red-50'); actionOpen = false" style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.45 8.45 3 9 3H15C15.55 3 16 3.45 16 4V6M19 6L18.12 19.13C18.05 20.18 17.18 21 16.13 21H7.87C6.82 21 5.95 20.18 5.88 19.13L5 6H19Z"/></svg>
                                                Hapus Periode
                                            </button>
                                        @else
                                            <button type="button" disabled style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:var(--c-fg-placeholder); cursor:not-allowed; font-family:inherit; text-align:left;" title="Periode memiliki data pendaftar dan tidak dapat dihapus">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.45 8.45 3 9 3H15C15.55 3 16 3.45 16 4V6M19 6L18.12 19.13C18.05 20.18 17.18 21 16.13 21H7.87C6.82 21 5.95 20.18 5.88 19.13L5 6H19Z"/></svg>
                                                Hapus Periode
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:60px 24px; text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.86 10.83C15.58 9.73 16 8.42 16 7C16 5.58 15.58 4.27 14.86 3.17C15.22 3.06 15.6 3 16 3C18.21 3 20 4.79 20 7C20 9.21 18.21 11 16 11C15.6 11 15.22 10.94 14.86 10.83ZM17.87 21C17.96 20.68 18 20.35 18 20V19C18 17.11 17.34 15.37 16.25 14H17C19.76 14 22 16.24 22 19V20C22 20.55 21.55 21 21 21H17.87Z" fill="currentColor"/>
                                    <path d="M10 14H8C5.24 14 3 16.24 3 19V20C3 20.55 3.45 21 4 21H14C14.55 21 15 20.55 15 20V19C15 16.24 12.76 14 10 14Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M9 11C11.21 11 13 9.21 13 7C13 4.79 11.21 3 9 3C6.79 3 5 4.79 5 7C5 9.21 6.79 11 9 11Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em;">Tidak Ada Periode</p>
                                <p style="font-size:11px; color:var(--c-fg-placeholder);">Belum ada periode ujian yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Area --}}
    @include('banksoal::periode._pagination')
</div>
