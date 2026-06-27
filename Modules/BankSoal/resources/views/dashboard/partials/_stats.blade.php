{{-- Modules/BankSoal/resources/views/dashboard/partials/_stats.blade.php --}}
@php
    $statsUjian = [
        ['label' => 'Total Pendaftar', 'value' => $statPendaftar ? $statPendaftar->total : 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Jumlah Sesi Ujian', 'value' => $jumlahSesi ?? 0, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    ];
@endphp
{{-- Kategori 1: Manajemen Bank Soal --}}
<div style="margin-bottom:24px;">
    <h3 style="font-size:16px; font-weight:700; color:var(--c-fg); margin-bottom:12px;">Bank Soal</h3>
    
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:20px;box-shadow:var(--shadow-card);">
        {{-- Total Soal Disetujui --}}
        <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--c-border);padding-bottom:16px;">
            <div>
                <p style="font-size:13px;font-weight:600;color:var(--c-fg);">Total Soal Disetujui</p>
                <p style="font-size:11px;color:var(--c-fg-muted);">Keseluruhan soal yang tervalidasi</p>
            </div>
            <div style="font-size:28px;font-weight:800;color:var(--c-primary);letter-spacing:-0.03em;">
                {{ number_format($soalDisetujui) }}
            </div>
        </div>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:32px;">
            {{-- Distribusi Kesulitan --}}
            <div style="display:flex; flex-direction:column;">
                <p style="font-size:11px;font-weight:700;color:var(--c-fg);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.05em;">Berdasarkan Kesulitan</p>
                <div id="chartKesulitan" style="flex:1; display:flex; align-items:center; justify-content:center; min-height:160px; margin-top:-10px;"></div>
            </div>
            
            {{-- CPL --}}
            <div>
                <p style="font-size:11px;font-weight:700;color:var(--c-fg);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.05em;">Berdasarkan CPL</p>
                <div style="display:flex;flex-direction:column;gap:12px;max-height:180px;overflow-y:auto;padding-right:8px;">
                    @php $totalCpl = $distCpl->sum('total'); @endphp
                    @forelse($distCpl as $cpl)
                    @php $pct = $totalCpl > 0 ? round(($cpl->total/$totalCpl)*100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                            <span style="font-weight:500;color:var(--c-fg-muted);">{{ $cpl->label }}</span>
                            <span style="font-weight:600;color:var(--c-fg);">{{ $cpl->total }}</span>
                        </div>
                        <div style="width:100%;background:var(--c-bg-subtle);height:4px;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;background:var(--c-primary);border-radius:999px;width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:12px;color:var(--c-fg-muted);">Belum ada data distribusi CPL.</p>
                    @endforelse
                </div>
            </div>

            {{-- MK --}}
            <div>
                <p style="font-size:11px;font-weight:700;color:var(--c-fg);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.05em;">Berdasarkan Mata Kuliah</p>
                <div style="display:flex;flex-direction:column;gap:12px;max-height:180px;overflow-y:auto;padding-right:8px;">
                    @php $totalMkDist = $distMk->sum('total'); @endphp
                    @forelse($distMk as $mk)
                    @php $pct = $totalMkDist > 0 ? round(($mk->total/$totalMkDist)*100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                            <span style="font-weight:500;color:var(--c-fg-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;" title="{{ $mk->label }}">{{ $mk->label }}</span>
                            <span style="font-weight:600;color:var(--c-fg);">{{ $mk->total }}</span>
                        </div>
                        <div style="width:100%;background:var(--c-bg-subtle);height:4px;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;background:var(--c-primary);border-radius:999px;width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:12px;color:var(--c-fg-muted);">Belum ada data distribusi MK.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Kategori 2: Ujian Komprehensif --}}
<div style="margin-bottom:24px;">
    <h3 style="font-size:16px; font-weight:700; color:var(--c-fg); margin-bottom:12px;">Ujian Komprehensif</h3>
    
    {{-- Status Periode Ujian ditaruh di sini --}}
    @include('banksoal::dashboard.partials._activity')

    <div class="dash-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
        {{-- Card 1: Total Peserta --}}
        <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
             onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
             onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Total Mahasiswa</p>
            </div>
            <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">{{ $cbTotalPeserta }}</p>
        </div>

        {{-- Card 2: Lulus --}}
        <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
             onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
             onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Lulus</p>
            </div>
            <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">{{ $cbtLulus }}</p>
        </div>

        {{-- Card 3: Tidak Lulus --}}
        <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
             onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
             onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Tidak Lulus</p>
            </div>
            <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">{{ $cbtTidakLulus }}</p>
        </div>
    </div>
</div>

<style>
    @media (max-width: 1024px) {
        .dash-stats {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 640px) {
        .dash-stats {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kesData = {!! json_encode(array_values($distKesulitan)) !!};
        const kesLabels = {!! json_encode(array_keys($distKesulitan)) !!};
        
        const total = kesData.reduce((a, b) => a + b, 0);
        
        if (total > 0) {
            new ApexCharts(document.querySelector("#chartKesulitan"), {
                series: kesData,
                chart: { type: 'pie', height: 180, sparkline: { enabled: false } },
                labels: kesLabels,
                colors: ['#10B981', '#F59E0B', '#EF4444'], // Easy, Intermediate, Advanced
                plotOptions: {
                    pie: {
                        expandOnClick: false
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 2, colors: ['#fff'] },
                legend: { show: true, position: 'right', fontSize: '11px', fontFamily: 'Inter Tight, sans-serif', fontWeight: 500, markers: { width: 8, height: 8, radius: 2 } },
                tooltip: {
                    y: { formatter: function(val) { return val + " soal" } }
                }
            }).render();
        } else {
            document.querySelector("#chartKesulitan").innerHTML = '<span style="font-size:12px; color:var(--c-fg-muted);">Belum ada data</span>';
        }
    });
</script>
@endpush