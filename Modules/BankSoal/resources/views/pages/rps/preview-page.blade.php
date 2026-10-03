@php
    // Follow the page's route so users with multiple roles keep the correct navigation.
    $previewRole = match (true) {
        request()->routeIs('banksoal.admin.*') => 'admin',
        request()->routeIs('banksoal.rps.gpm.*') => 'gpm',
        request()->routeIs('banksoal.rps.dosen.*') => 'dosen',
        auth()->user()->hasRole('gpm') => 'gpm',
        auth()->user()->hasRole('admin_banksoal') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') => 'admin',
        default => 'dosen',
    };
    $layoutComponent = match ($previewRole) {
        'admin' => 'banksoal::layouts.admin',
        'gpm' => 'banksoal::layouts.gpm-master',
        default => 'banksoal::layouts.dosen-admin',
    };
    $backUrl = route(match ($previewRole) {
        'admin' => 'banksoal.admin.kontrol-banksoal.rps',
        'gpm' => 'banksoal.rps.gpm.validasi-rps',
        default => 'banksoal.rps.dosen.index',
    });
    $status = strtolower($rps->status ?? 'draft');
    [$statusTone, $statusText, $statusMessage] = match ($status) {
        'disetujui' => ['success', 'Disetujui GPM', 'RPS telah disetujui dan siap digunakan sebagai acuan pengajaran.'],
        'revisi' => ['danger', 'Perlu revisi', 'Perbaiki dokumen sesuai catatan penilaian GPM sebelum mengajukan kembali.'],
        'diajukan' => ['pending', 'Menunggu validasi', 'RPS telah diajukan dan menunggu peninjauan oleh GPM.'],
        default => ['neutral', 'Draf', 'RPS belum diajukan untuk proses validasi.'],
    };
    $hasReview = isset($existingReview) && $existingReview->nilai_akhir !== null;
    $scorePercent = $hasReview && ($totalBobot ?? 0) > 0 ? max(0, min(100, $existingReview->nilai_akhir / $totalBobot * 100)) : 0;
@endphp

<x-dynamic-component :component="$layoutComponent" :rps-preview="true">
    @section('breadcrumbs')
        <a href="{{ $backUrl }}" class="text-slate-500 hover:text-primary">RPS</a>
        <span class="mx-2 text-slate-300">/</span><span class="text-slate-800">Preview RPS</span>
    @endsection

    <div class="dosen-page-wrap">
        <div class="dosen-page-box">
            <header class="dosen-page-header">
                <div class="bs-heading-row">
                    <div><div class="bs-heading-label"><h1>Detail &amp; Preview RPS</h1><span class="bs-role-badge">{{ $previewRole === 'gpm' ? 'GPM' : ucfirst($previewRole) }}</span></div><p>Tinjau dokumen, capaian pembelajaran, dan hasil penilaian GPM.</p></div>
                    <div class="bs-heading-actions">
                        <a href="{{ $backUrl }}" class="dosen-management-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 5-7 7 7 7M5 12h14" /></svg>Kembali ke RPS</a>
                        @if(!empty($rps) && !empty($downloadUrl))
                            <a href="{{ $downloadUrl }}" class="dosen-management-btn dosen-management-btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v12m-4-4 4 4 4-4M4 16v5h16v-5" /></svg>Unduh RPS</a>
                        @endif
                    </div>
                </div>
            </header>
            <div class="dosen-page-body">
                <x-banksoal::notification.alerts />
                @if(!empty($rps))
                    <section class="bs-section rp-summary" aria-label="Informasi RPS">
                        <div class="rp-course">
                            <span class="rp-eyebrow">Mata kuliah</span>
                            <h2>{{ $rps->mk_nama }}</h2>
                            <dl class="rp-course-meta">
                                <div><dt>Kode</dt><dd>{{ $rps->kode ?: '—' }}</dd></div>
                                <div><dt>Semester</dt><dd>{{ $rps->semester ?: '—' }}</dd></div>
                                <div><dt>Tahun ajaran</dt><dd>{{ $rps->tahun_ajaran ?: '—' }}</dd></div>
                            </dl>
                        </div>
                        <div class="rp-lecturers"><h3 class="rp-eyebrow">Dosen pengampu</h3><div class="rp-chips">
                            @forelse($dosenPengampu as $dosen)<span class="rp-chip">{{ $dosen->name }}</span>
                            @empty<p class="rp-muted">Belum ada dosen pengampu terdata.</p>@endforelse
                        </div></div>
                        <div class="rp-status"><span class="rp-badge rp-badge-{{ $statusTone }}">{{ $statusText }}</span><p>{{ $statusMessage }}</p></div>
                    </section>

                    <div class="rp-workspace">
                        <section class="bs-section rp-document" aria-labelledby="rp-document-title">
                            <div class="bs-section-heading">
                                <div class="rp-file-heading">
                                    <span class="rp-file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M14 2H6v20h12V6zM14 2v5h5M9 12h6M9 16h4" /></svg></span>
                                    <div><h2 id="rp-document-title">Dokumen RPS</h2><p>{{ !empty($rps->dokumen) ? basename($rps->dokumen) : 'Dokumen belum tersedia' }}</p></div>
                                </div>
                                @if(!empty($fileUrl))<a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="dosen-management-btn" aria-label="Buka dokumen RPS di tab baru">Buka tab baru<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 3h7v7m0-7L10 14M10 3H3v18h18v-7" /></svg></a>@endif
                            </div>
                            @if(!empty($fileUrl))
                                <div class="rp-pdf-surface"><iframe id="pdfFrame" src="{{ $fileUrl }}" loading="eager" title="Preview PDF RPS {{ $rps->mk_nama }}"></iframe></div>
                                <p class="rp-pdf-help">Preview tidak muncul? <a href="{{ $fileUrl }}" target="_blank" rel="noopener">Buka dokumen di tab baru</a> untuk membacanya.</p>
                            @else
                                <div class="rp-empty rp-file-missing" role="status">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M14 2H6v20h12V6zM14 2v5h5M12 11v4m0 3h.01" /></svg>
                                    <h3>Berkas PDF belum tersedia</h3><p>{{ $errorMessage ?? 'Dokumen RPS belum diunggah atau berkas tidak ditemukan.' }}</p>
                                </div>
                            @endif
                        </section>

                        <div class="rp-review-column">
                            <section class="bs-section" aria-labelledby="rp-review-title">
                                <div class="bs-section-heading"><div><h2 id="rp-review-title">Penilaian GPM</h2><p>Ringkasan evaluasi dan kesesuaian RPS.</p></div></div>
                                <div class="rp-panel-body">
                                    <div class="rp-score">
                                        <span class="rp-eyebrow">Skor akhir evaluasi</span>
                                        <div class="rp-score-value">{{ $hasReview ? $existingReview->nilai_akhir : '—' }}<span>/ {{ $totalBobot ?? 0 }}</span></div>
                                        @if($hasReview && ($totalBobot ?? 0) > 0)
                                            <meter min="0" max="100" value="{{ $scorePercent }}" aria-label="Persentase skor evaluasi">{{ round($scorePercent) }}%</meter>
                                        @else<p class="rp-muted">{{ $hasReview ? 'Bobot penilaian belum tersedia.' : 'Belum ada hasil penilaian GPM.' }}</p>@endif
                                    </div>
                                    @if(!empty($existingReview->catatan))<div class="rp-notes"><h3 class="rp-eyebrow">Catatan GPM</h3><p>{{ $existingReview->catatan }}</p></div>@endif
                                    <h3 class="rp-eyebrow rp-checklist-title">Parameter kesesuaian</h3>
                                    <ul class="rp-checklist">
                                        @forelse($parameters as $param)
                                            @php
                                                $score = $reviewChecklist[$param->id] ?? null;
                                                $checked = $score !== null && in_array($score, [1, '1', true], true);
                                                $unchecked = $score !== null && in_array($score, [0, '0', false], true);
                                            @endphp
                                            <li><div><strong>{{ $param->aspek }}</strong><span class="rp-muted">Bobot {{ $param->bobot }}</span></div><span class="rp-check {{ $checked ? 'rp-check-yes' : ($unchecked ? 'rp-check-no' : '') }}" aria-label="{{ $checked ? 'Sesuai' : ($unchecked ? 'Belum sesuai' : 'Belum dinilai') }}" title="{{ $checked ? 'Sesuai' : ($unchecked ? 'Belum sesuai' : 'Belum dinilai') }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="{{ $checked ? 'm5 12 4 4L19 6' : ($unchecked ? 'm6 6 12 12M18 6 6 18' : 'M6 12h12') }}" /></svg>
                                            </span></li>
                                        @empty<li class="rp-muted">Belum ada parameter penilaian.</li>@endforelse
                                    </ul>
                                </div>
                            </section>
                            <section class="bs-section" aria-labelledby="rp-history-title">
                                <div class="bs-section-heading"><h2 id="rp-history-title">Riwayat aktivitas</h2></div>
                                <div class="rp-panel-body rp-history-body"><ol class="rp-history">
                                    @forelse($history as $item)
                                        <li><div class="rp-history-heading"><strong>{{ ucfirst($item->action) }}</strong><time datetime="{{ \Carbon\Carbon::parse($item->created_at)->toIso8601String() }}">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y · H:i') }}</time></div>@if($item->description)<p>{{ $item->description }}</p>@endif</li>
                                    @empty<li class="rp-muted">Belum ada riwayat aktivitas untuk RPS ini.</li>@endforelse
                                </ol></div>
                            </section>
                        </div>
                    </div>

                    <section class="bs-section rp-mapping" x-data="{ expanded: false }">
                        <div class="bs-section-heading"><div><h2>Capaian pembelajaran</h2><p>Pemetaan CPL dan CPMK yang terhubung dengan mata kuliah.</p></div>
                            <button type="button" class="dosen-management-btn" @click="expanded = !expanded" :aria-expanded="expanded" aria-controls="rp-mapping-content"><span x-text="expanded ? 'Tutup pemetaan' : 'Lihat pemetaan'">Lihat pemetaan</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" :style="expanded ? 'transform:rotate(180deg)' : ''" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg></button>
                        </div>
                        <div id="rp-mapping-content" class="rp-mapping-grid" x-show="expanded" x-cloak>
                            @forelse($cplCpmkMappings as $rows)
                                <article><h3>{{ $rows->first()->cpl_kode }}</h3><ul>@foreach($rows as $row)<li><span class="rp-chip">{{ $row->cpmk_kode }}</span><p>{{ $row->cpmk_deskripsi }}</p></li>@endforeach</ul></article>
                            @empty<p class="rp-muted">Belum ada pemetaan CPL/CPMK untuk mata kuliah ini.</p>@endforelse
                        </div>
                    </section>
                @else
                    <section class="bs-section rp-empty rp-not-found">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M14 2H6v20h12V6zM14 2v5h5M12 11v4m0 3h.01" /></svg>
                        <h2>RPS tidak ditemukan</h2><p>Detail RPS tidak tersedia atau belum dapat dimuat. Kembali ke daftar RPS untuk memilih dokumen lain.</p>
                        <a href="{{ $backUrl }}" class="dosen-management-btn dosen-management-btn-primary">Kembali ke daftar RPS</a>
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
