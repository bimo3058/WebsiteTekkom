<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Koordinator">
    @php
        /** @var \Modules\EOffice\Models\Praktikum|null $praktikum */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $daftarAsprak */
        $name = auth()->user()->name;
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
        $pct = ($totalAsprak ?? 0) > 0 ? round(($asprakTerdistribusi ?? 0) / ($totalAsprak ?? 1) * 100) : 0;
    @endphp

    {{-- Page Header --}}
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                    <h1 style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">Dashboard Koordinator</h1>
                    <span class="mp-badge sm" style="background:#E0E7FF;color:#6366F1;"><span class="dot"></span>Koordinator</span>
                </div>
                <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                    Halo, <span style="color:var(--c-fg, #0D0D12); font-weight:600;">{{ $firstName }}</span>
                    <span style="margin-left:4px; color:var(--c-fg-placeholder, #A4ABB8);">·</span>
                    <span style="margin-left:4px; color:var(--c-fg-muted, #666D80);">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}</span>
                </p>
            </div>
            <div>
                @if(isset($praktikum) && $praktikum)
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:13px; font-weight:600; color:#353849;">Informasi mengenai:</span>
                        <form action="{{ route('eoffice.manprak.koor.switch-praktikum') }}" method="POST" style="margin:0;">
                            @csrf
                            @php
                                $praktikumOptions = isset($allPraktikum) && $allPraktikum->count() > 0
                                    ? $allPraktikum->map(function ($p) {
                                        return ['value' => $p->id, 'label' => $p->nama];
                                    })->toArray()
                                    : [['value' => $praktikum->id, 'label' => $praktikum->nama]];
                            @endphp
                            <x-eoffice::manajemen-praktikum.ui.select name="praktikum_id" :options="$praktikumOptions"
                                :selected="$praktikum->id" placeholder="Pilih Praktikum..."
                                onChange="$event.target.form.submit()" minWidth="250px" />
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    @if(!isset($praktikum) || !$praktikum)
        <div class="mp-alert warning flex-shrink-0">
            <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Anda belum ditunjuk sebagai koordinator praktikum
                manapun.</div>
            <div style="font-size:12px;">Hubungi dosen pengampu untuk mendapatkan penugasan.</div>
        </div>
    @else

        {{-- Stat Cards --}}
        <div class="mp-stats-grid cols-4">
            
            <div class="mp-stat" style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon yellow">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Total Praktikan</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalPraktikan ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">Terdaftar</div>
            </div>

            <div class="mp-stat" style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Total Asisten Praktikum</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalAsprak ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">Asisten aktif</div>
            </div>

            <div class="mp-stat" style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Asisten Terdistribusi</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $asprakTerdistribusi ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">Sudah dapat modul</div>
            </div>

            <div class="mp-stat" style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon navy">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Total Modul</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalModul ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">Modul tersedia</div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
        MIDDLE ROW: Pendaftaran Asisten
        ═══════════════════════════════════════════════ --}}
        <div style="display:flex; flex-direction:column; gap:16px; margin-top:24px; flex:1; min-height:0;">

            {{-- Panel Pendaftaran Asisten --}}
            <div
                style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; height:100%;">
                <div
                    style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border, #DFE1E7); flex-shrink:0;">
                    <div style="font-size:15px; font-weight:700; color:var(--c-fg, #0D0D12);">Pendaftaran Asisten Praktikum
                    </div>
                    @if($pendingAsprak > 0)
                        <span class="mp-badge warning sm">{{ $pendingAsprak }} perlu review</span>
                    @endif
                </div>
                <div
                    style="display:grid; grid-template-columns:2fr 100px 150px 100px; gap:16px; padding:12px 20px; border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA; flex-shrink:0; align-items:center;">
                    <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec, #666D80);">Nama Mahasiswa</div>
                    <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec, #666D80); text-align:center;">IPK</div>
                    <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec, #666D80);">Berkas</div>
                    <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec, #666D80); text-align:right;">Status
                    </div>
                </div>
                <div style="flex:1; overflow-y:auto; min-height:0;">
                    @forelse($pendaftarAsprakList ?? [] as $pend)
                        <div
                            style="display:grid; grid-template-columns:2fr 100px 150px 100px; gap:16px; padding:14px 20px; border-bottom:1px solid var(--c-border, #DFE1E7); align-items:center;">
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);">
                                    {{ $pend->user?->name ?? 'Mahasiswa' }}</div>
                                <div style="font-size:12px; color:var(--c-fg-muted, #808897); margin-top: 2px;">
                                    {{ $pend->user?->student_number ?? '-' }}</div>
                            </div>
                            <div style="text-align:center; font-weight:600; color: {{ ($pend->ipk ?? 0) >= 3.0 ? '#40C4AA' : '#DF1C41' }}; font-size:13px;">
                                {{ number_format($pend->ipk ?? 0, 2) }}
                            </div>
                            <div>
                                @if($pend->cv_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pend->cv_path, 'eoffice') }}" target="_blank"
                                        style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:inline-block;" class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:11px;color:#808897;">—</span>
                                @endif
                            </div>
                            <div style="text-align:right;">
                                <span class="mp-badge warning sm">Pending</span>
                            </div>
                        </div>
                    @empty
                        <div
                            style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; height:100%;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                                <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8" />
                            </svg>
                            <span
                                style="font-size:13px; font-weight:500; color:var(--c-fg-muted, #808897); max-width:280px; line-height:1.5;">Tidak
                                ada pendaftaran asisten yang perlu ditinjau.</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>