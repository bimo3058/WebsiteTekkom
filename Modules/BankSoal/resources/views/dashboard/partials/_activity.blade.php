{{-- Modules/BankSoal/resources/views/dashboard/partials/_activity.blade.php --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px; margin-bottom:20px;">
    
    {{-- Card: Status Periode --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; box-shadow:var(--shadow-card); overflow:hidden; display:flex; flex-direction:column;">
        <div style="padding:14px 20px; border-bottom:1px solid var(--c-border); display:flex; align-items:center; gap:8px;">
            <div style="width:8px; height:8px; border-radius:50%; background:{{ $periodeAktif ? 'var(--c-success)' : 'var(--c-border-strong)' }};"></div>
            <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin:0;">{{ $periodeAktif ? 'Periode Ujian Aktif' : 'Status Periode Ujian' }}</h3>
        </div>
        <div style="padding:20px; flex:1; display:flex; flex-direction:column;">
            @if($periodeAktif)
                <div style="margin-bottom:16px;">
                    <h4 style="font-size:16px; font-weight:700; color:var(--c-fg); margin-bottom:4px;">{{ $periodeAktif->nama_periode }}</h4>
                    <p style="font-size:12px; color:var(--c-fg-muted);">{{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->translatedFormat('d M Y') }}</p>
                </div>
                
                @if($statPendaftar)
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                        <div style="background:var(--c-bg); border-radius:8px; padding:12px; text-align:center;">
                            <p style="font-size:10px; font-weight:700; color:var(--c-fg-muted); letter-spacing:0.05em; margin-bottom:4px;">TOTAL PENDAFTAR</p>
                            <p style="font-size:20px; font-weight:800; color:var(--c-fg);">{{ $statPendaftar->total }}</p>
                        </div>
                        <div style="background:rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.2); border-radius:8px; padding:12px; text-align:center;">
                            <p style="font-size:10px; font-weight:700; color:var(--c-warning); letter-spacing:0.05em; margin-bottom:4px;">MENUNGGU</p>
                            <p style="font-size:20px; font-weight:800; color:var(--c-warning);">{{ $statPendaftar->pending }}</p>
                        </div>
                    </div>
                @endif

                <div style="display:flex; gap:8px; margin-top:auto;">
                    <a href="{{ route('banksoal.pendaftaran.index', ['periode_id' => $periodeAktif->id]) }}" style="flex:1; text-align:center; padding:8px; background:var(--c-primary); color:#fff; font-size:12px; font-weight:600; border-radius:8px; text-decoration:none;">Kelola Pendaftar</a>
                    <a href="{{ route('banksoal.pendaftaran.alokasi-sesi.index') }}" style="flex:1; text-align:center; padding:8px; background:#fff; color:var(--c-fg-sec); border:1px solid var(--c-border); font-size:12px; font-weight:600; border-radius:8px; text-decoration:none;">Jadwal</a>
                </div>
            @else
                <div style="text-align:center; padding:30px 0;">
                    <p style="font-size:13px; color:var(--c-fg-muted); margin-bottom:16px;">Tidak ada periode ujian yang sedang aktif.</p>
                    <a href="{{ route('banksoal.periode.setup') }}" style="display:inline-block; padding:8px 16px; background:var(--c-primary); color:#fff; font-size:12px; font-weight:600; border-radius:8px; text-decoration:none;">+ Buat Periode Baru</a>
                </div>
            @endif
        </div>
    </div>


</div>
