<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" :pageTitle="'Detail Pendaftaran '.$label">
    @php
        $statusLabel = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        $statusTone = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'error'];
        $canOverride = $type === 'koor' && $registration->status_dosen !== 'disetujui'
            && ($registration->status === 'pending' || ($registration->status === 'rejected' && $registration->status_dosen === 'ditolak'));
        $canApprove = $registration->status === 'pending'
            && (($type === 'koor' && $registration->status_dosen === 'disetujui') || ($type === 'asprak' && $registration->status_koor === 'disetujui'));
    @endphp
    <style>
        .mp-review-grid { display:grid;grid-template-columns:minmax(0,1.35fr) minmax(360px,1fr);gap:18px;align-items:start; }
        .mp-review-data { grid-column:1;grid-row:1; }
        .mp-review-result { grid-column:2;grid-row:1; }
        .mp-review-documents { grid-column:1 / -1;grid-row:2; }
        .mp-review-pad { padding:20px;display:grid;gap:16px;min-width:0; }
        .mp-review-facts { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }
        .mp-review-facts dt,.mp-review-label { display:block;font-size:12px;color:var(--c-fg-sec);margin-bottom:5px; }
        .mp-review-facts dd { font-size:13px;font-weight:600;overflow-wrap:anywhere; }
        .mp-review-copy { font-size:13px;line-height:1.7;white-space:pre-line;overflow-wrap:anywhere; }
        .mp-review-doc { display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px;border:1px solid var(--c-border);border-radius:10px; }
        @media(max-width:1000px) { .mp-review-grid { grid-template-columns:minmax(0,1fr); } .mp-review-data,.mp-review-result,.mp-review-documents,.mp-review-decision { grid-column:1;grid-row:auto; } }
        @media(max-width:480px) { .mp-review-facts { grid-template-columns:minmax(0,1fr); } .mp-review-pad { padding:16px; } }
    </style>
    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pendaftaran {{ $label }}</h1>
            <p class="mp-page-sub">Periksa data, berkas, dan hasil seleksi mahasiswa.</p>
        </div>
        <a class="mp-btn secondary md" href="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran-'.$type.'.index') }}">
            <x-eoffice::manajemen-praktikum.ui.icon name="arrow-left" /> Kembali ke daftar
        </a>
    </div>
    <div class="mp-review-grid">
            <section class="mp-card mp-review-data">
                <div class="mp-card-header">
                    <h2 class="mp-card-title">Data mahasiswa</h2>
                    <span class="mp-badge {{ $statusTone[$registration->status] ?? 'warning' }} sm">{{ $statusLabel[$registration->status] ?? $registration->status }}</span>
                </div>
                <div class="mp-review-pad">
                    <dl class="mp-review-facts">
                        <div><dt>Nama</dt><dd>{{ $registration->user?->name ?? 'Akun tidak tersedia' }}</dd></div>
                        <div><dt>NIM</dt><dd>{{ $registration->user?->student?->student_number ?? $registration->user?->external_id ?? 'Belum tercatat' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $registration->user?->email ?? 'Belum tercatat' }}</dd></div>
                        <div><dt>Praktikum</dt><dd>{{ $registration->praktikum?->nama ?? 'Praktikum tidak tersedia' }}</dd></div>
                        <div><dt>Tanggal daftar</dt><dd>{{ $registration->created_at?->format('d M Y, H:i') ?? 'Belum tercatat' }}</dd></div>
                        @if($type !== 'praktikan')
                            <div><dt>IPK</dt><dd>{{ $registration->ipk !== null ? number_format($registration->ipk, 2) : 'Belum tercatat' }}</dd></div>
                        @endif
                    </dl>
                </div>
            </section>
            <section class="mp-card mp-review-documents">
                <div class="mp-card-header"><h2 class="mp-card-title">Dokumen pendaftaran</h2><x-eoffice::manajemen-praktikum.ui.icon name="file" /></div>
                <div class="mp-review-pad">
                    @foreach($documents as $field => $documentLabel)
                        <div class="mp-review-doc">
                            <span style="display:flex;gap:10px;align-items:center;font-size:13px;font-weight:600;">
                                <x-eoffice::manajemen-praktikum.ui.icon name="file" /> {{ $documentLabel }}
                            </span>
                            @if($registration->getAttributes()[$field] ?? null)
                                <a class="mp-btn secondary sm" target="_blank" rel="noopener" aria-label="Buka {{ $documentLabel }} di tab baru"
                                   href="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran.document', ['type' => $type, 'id' => $registration->id, 'document' => $field]) }}">
                                    Lihat dokumen <x-eoffice::manajemen-praktikum.ui.icon name="arrow-right" />
                                </a>
                            @elseif($field === 'berkas_tambahan_path' && !($additionalDocumentRequired ?? false))
                                <span class="mp-badge sm">Tidak disyaratkan</span>
                            @elseif($field === 'berkas_tambahan_path')
                                <span class="mp-badge sm">Tidak mengunggah</span>
                            @else
                                <span class="mp-badge sm">Tidak diunggah</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
            <section class="mp-card mp-review-result">
                <div class="mp-card-header"><h2 class="mp-card-title">Hasil review {{ $type === 'koor' ? 'dosen' : 'koordinator' }}</h2></div>
                <div class="mp-review-pad">
                    @php
                        $reviewStatus = $type === 'koor'
                            ? $registration->status_dosen
                            : ($type === 'asprak' ? $registration->status_koor : $registration->status);
                    @endphp
                    <span class="mp-badge {{ in_array($reviewStatus, ['approved', 'disetujui']) ? 'success' : (in_array($reviewStatus, ['rejected', 'ditolak']) ? 'error' : 'warning') }} sm" style="justify-self:start;">
                        {{ ['disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'menunggu' => 'Menunggu review'][$reviewStatus] ?? ($statusLabel[$reviewStatus] ?? 'Menunggu review') }}
                    </span>
                    <dl class="mp-review-facts">
                        <div><dt>Direview oleh</dt><dd>{{ $registration->direviewOleh?->name ?? 'Belum ada reviewer' }}</dd></div>
                        <div><dt>Waktu review</dt><dd>{{ $registration->direview_pada?->format('d M Y, H:i') ?? 'Belum direview' }}</dd></div>
                    </dl>
                    @if($overrideAudit)
                        <div class="mp-alert info">
                            <strong>Persetujuan melalui override admin</strong>
                            <p class="mp-review-copy">{{ $overrideAudit->new_data['override_reason'] ?? $overrideAudit->description }}</p>
                            <p style="margin-top:8px;font-size:12px;">{{ $overrideAudit->user?->name ?? 'Admin' }} · {{ $overrideAudit->created_at?->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            @if(auth()->user()->hasAnyRole(['superadmin','admin_praktikum']) && $reviewStatus !== 'menunggu' && ($canOverride || $canApprove || ($type !== 'praktikan' && $registration->status === 'pending')))
                <x-eoffice::manajemen-praktikum.ui.registration-decision :registration="$registration" :type="$type" :can-override="$canOverride" :can-approve="$canApprove" />
            @endif
            </section>
    </div>
</x-eoffice::manajemen-praktikum.layout>
