<x-eoffice::manajemen-praktikum.layout :pageTitle="'Detail Pendaftaran '.$label">
    @php
        $statusLabel = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        $statusTone = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'error'];
        $canOverride = $type === 'koor' && $registration->status_dosen !== 'disetujui'
            && ($registration->status === 'pending' || ($registration->status === 'rejected' && $registration->status_dosen === 'ditolak'));
        $canApprove = $registration->status === 'pending'
            && (($type === 'koor' && $registration->status_dosen === 'disetujui') || ($type === 'asprak' && $registration->status_koor === 'disetujui'));
    @endphp
    <style>
        .mp-review-grid { display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:18px;align-items:start; }
        .mp-review-stack { display:grid;gap:18px;min-width:0; }
        .mp-review-pad { padding:20px;display:grid;gap:16px;min-width:0; }
        .mp-review-facts { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }
        .mp-review-facts dt,.mp-review-label { display:block;font-size:12px;color:var(--c-fg-sec);margin-bottom:5px; }
        .mp-review-facts dd { font-size:13px;font-weight:600;overflow-wrap:anywhere; }
        .mp-review-copy { font-size:13px;line-height:1.7;white-space:pre-line;overflow-wrap:anywhere; }
        .mp-review-doc { display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px;border:1px solid var(--c-border);border-radius:10px; }
        @media(max-width:1000px) { .mp-review-grid { grid-template-columns:minmax(0,1fr); } }
        @media(max-width:480px) { .mp-review-facts { grid-template-columns:minmax(0,1fr); } .mp-review-pad { padding:16px; } }
    </style>
    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pendaftaran {{ $label }}</h1>
            <p class="mp-page-sub">Periksa data, berkas, dan hasil seleksi mahasiswa.</p>
        </div>
        <a class="mp-btn secondary md" href="{{ route('eoffice.manprak.admin.pendaftaran-'.$type.'.index') }}">
            <x-eoffice::manajemen-praktikum.ui.icon name="arrow-left" /> Kembali ke daftar
        </a>
    </div>
    @include('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => $type])
    <div class="mp-review-grid">
        <div class="mp-review-stack">
            <section class="mp-card">
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
                    @if($type !== 'praktikan')
                        <div><h3 class="mp-review-label">Motivasi</h3><p class="mp-review-copy">{{ $registration->motivasi ?: 'Tidak ada motivasi yang dicantumkan.' }}</p></div>
                    @endif
                    @if($type === 'asprak' && !empty($registration->jadwal))
                        <div><h3 class="mp-review-label">Jadwal yang diajukan</h3>
                            @foreach((array) $registration->jadwal as $jadwal)
                                <p class="mp-review-copy">{{ is_array($jadwal) ? implode(' · ', array_filter($jadwal, 'is_scalar')) : $jadwal }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
            <section class="mp-card">
                <div class="mp-card-header"><h2 class="mp-card-title">Dokumen pendaftaran</h2><x-eoffice::manajemen-praktikum.ui.icon name="file" /></div>
                <div class="mp-review-pad">
                    <p class="mp-page-sub">PDF dan gambar dibuka di tab baru. Format lainnya akan diunduh.</p>
                    @foreach($documents as $field => $documentLabel)
                        <div class="mp-review-doc">
                            <span style="display:flex;gap:10px;align-items:center;font-size:13px;font-weight:600;">
                                <x-eoffice::manajemen-praktikum.ui.icon name="file" /> {{ $documentLabel }}
                            </span>
                            @if($registration->getAttribute($field))
                                <a class="mp-btn secondary sm" target="_blank" rel="noopener" aria-label="Buka {{ $documentLabel }} di tab baru"
                                   href="{{ route('eoffice.manprak.admin.pendaftaran.document', ['type' => $type, 'id' => $registration->id, 'document' => $field]) }}">
                                    Lihat dokumen <x-eoffice::manajemen-praktikum.ui.icon name="arrow-right" />
                                </a>
                            @else
                                <span class="mp-badge sm">Tidak diunggah</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="mp-review-stack">
            <section class="mp-card">
                <div class="mp-card-header"><h2 class="mp-card-title">Hasil review {{ $type === 'koor' ? 'dosen' : 'koordinator' }}</h2></div>
                <div class="mp-review-pad">
                    @php($reviewStatus = $type === 'koor' ? $registration->status_dosen : ($type === 'asprak' ? $registration->status_koor : $registration->status))
                    <span class="mp-badge {{ in_array($reviewStatus, ['approved', 'disetujui']) ? 'success' : (in_array($reviewStatus, ['rejected', 'ditolak']) ? 'error' : 'warning') }} sm" style="justify-self:start;">
                        {{ ['disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'menunggu' => 'Menunggu review'][$reviewStatus] ?? ($statusLabel[$reviewStatus] ?? 'Menunggu review') }}
                    </span>
                    <dl class="mp-review-facts">
                        <div><dt>Direview oleh</dt><dd>{{ $registration->direviewOleh?->name ?? 'Belum ada reviewer' }}</dd></div>
                        <div><dt>Waktu review</dt><dd>{{ $registration->direview_pada?->format('d M Y, H:i') ?? 'Belum direview' }}</dd></div>
                    </dl>
                    <div><h3 class="mp-review-label">Catatan reviewer</h3><p class="mp-review-copy">{{ ($type === 'koor' ? $registration->catatan_dosen : $registration->catatan_koor) ?: 'Tidak ada catatan.' }}</p></div>
                    @if($registration->alasan_penolakan)
                        <div><h3 class="mp-review-label">Riwayat alasan penolakan</h3><p class="mp-review-copy">{{ $registration->alasan_penolakan }}</p></div>
                    @endif
                    @if($overrideAudit)
                        <div class="mp-alert info">
                            <strong>Persetujuan melalui override admin</strong>
                            <p class="mp-review-copy">{{ $overrideAudit->new_data['override_reason'] ?? $overrideAudit->description }}</p>
                            <p style="margin-top:8px;font-size:12px;">{{ $overrideAudit->user?->name ?? 'Admin' }} · {{ $overrideAudit->created_at?->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            </section>
            @if($canOverride || $canApprove || ($type !== 'praktikan' && $registration->status === 'pending'))
                <section class="mp-card">
                    <div class="mp-card-header"><h2 class="mp-card-title">Keputusan admin</h2></div>
                    <div class="mp-review-pad">
                        @if($canOverride)
                            <div class="mp-alert warning">Admin dapat menyetujui pendaftaran meskipun dosen belum menyetujui atau sudah menolak. Keputusan dosen tetap tercatat.</div>
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.approve', $registration->id) }}" style="display:grid;gap:12px;">
                                @csrf
                                <input type="hidden" name="override_dosen" value="1">
                                <div>
                                    <label class="mp-review-label" for="override_reason">Alasan override <span aria-hidden="true">*</span></label>
                                    <textarea class="mp-input" id="override_reason" name="override_reason" rows="4" required minlength="10" maxlength="1000" aria-describedby="override-help">{{ old('override_reason') }}</textarea>
                                    <p id="override-help" class="mp-page-sub">Minimal 10 karakter. Alasan dan akun admin dicatat di riwayat audit.</p>
                                </div>
                                <button class="mp-btn primary md" type="submit"><x-eoffice::manajemen-praktikum.ui.icon name="check" /> Override &amp; Setujui</button>
                            </form>
                        @elseif($canApprove)
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-'.$type.'.approve', $registration->id) }}">
                                @csrf
                                <button class="mp-btn primary md" type="submit"><x-eoffice::manajemen-praktikum.ui.icon name="check" /> Setujui pendaftaran</button>
                            </form>
                        @else
                            <p class="mp-page-sub">Menunggu persetujuan koordinator sebelum persetujuan akhir admin.</p>
                        @endif
                        @if($registration->status === 'pending')
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-'.$type.'.reject', $registration->id) }}" style="display:grid;gap:12px;border-top:1px solid var(--c-border);padding-top:16px;">
                                @csrf
                                <div><label class="mp-review-label" for="alasan_penolakan">Alasan penolakan</label><textarea class="mp-input" id="alasan_penolakan" name="alasan_penolakan" required maxlength="1000" rows="2">{{ old('alasan_penolakan') }}</textarea></div>
                                <button class="mp-btn secondary md" type="submit"><x-eoffice::manajemen-praktikum.ui.icon name="close" /> Tolak pendaftaran</button>
                            </form>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-eoffice::manajemen-praktikum.layout>
