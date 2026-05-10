<x-manajemenmahasiswa::layouts.mahasiswa>
<style>
.btn-back{width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:18px;transition:all 0.2s;flex-shrink:0}
.btn-back:hover{background:#f3f4f6}
.detail-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);margin-bottom:20px}
.detail-card-title{font-weight:700;font-size:16px;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.meta-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-top:16px}
.meta-item{background:#f9fafb;border-radius:10px;padding:14px 16px;border:1px solid #f3f4f6}
.meta-item-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:4px}
.meta-item-value{font-size:14px;font-weight:600;color:#1f2937}
.badge-bidang{font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;background:#eef2ff;color:#4f46e5}
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700}
.status-draft{background:#f3f4f6;color:#6b7280}
.status-menunggu_ttd_ketua,.status-diajukan{background:#fef3c7;color:#92400e}
.status-menunggu_ttd_dpm{background:#dbeafe;color:#1e40af}
.status-menunggu_ttd_dept{background:#ede9fe;color:#5b21b6}
.status-disetujui{background:#dcfce7;color:#166534}
.status-ditolak{background:#fee2e2;color:#dc2626}
/* Stepper */
.workflow-stepper{display:flex;align-items:flex-start;gap:0;margin-bottom:24px;overflow-x:auto;padding-bottom:8px}
.stepper-step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative;min-width:100px}
.stepper-step:not(:last-child)::after{content:'';position:absolute;top:18px;left:50%;width:100%;height:2px;background:#e5e7eb;z-index:0}
.stepper-step.done:not(:last-child)::after{background:#4f46e5}
.stepper-step.partial:not(:last-child)::after{background:linear-gradient(to right,#4f46e5 50%,#e5e7eb 50%)}
.step-dot{width:36px;height:36px;border-radius:50%;background:#f3f4f6;border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-size:13px;z-index:1;position:relative;transition:all 0.3s}
.stepper-step.done .step-dot{background:#4f46e5;border-color:#4f46e5;color:#fff}
.stepper-step.current .step-dot{background:#fff;border-color:#4f46e5;color:#4f46e5;box-shadow:0 0 0 4px #e0e7ff}
.stepper-step.partial .step-dot{background:#fef3c7;border-color:#f59e0b;color:#92400e}
.step-label{font-size:10px;font-weight:600;color:#9ca3af;margin-top:8px;text-align:center;max-width:90px;line-height:1.3}
.stepper-step.done .step-label,.stepper-step.current .step-label{color:#4f46e5}
.stepper-step.partial .step-label{color:#92400e}
/* TTD Panel */
.ttd-panel{background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:20px;margin-bottom:12px}
.ttd-panel.signed{background:#f0fdf4;border-color:#bbf7d0}
.ttd-panel.waiting{background:#f0f9ff;border-color:#bae6fd}
.ttd-role-label{font-weight:700;font-size:14px;color:#1f2937;display:flex;align-items:center;gap:8px;margin-bottom:12px}
.ttd-signed-info{font-size:12px;color:#166534;background:#dcfce7;padding:6px 12px;border-radius:6px;margin-top:8px}
/* PDF Viewer */
.pdf-viewer-container{position:relative;background:#374151;border-radius:12px;overflow:hidden;margin-top:16px}
.pdf-canvas-wrapper{position:relative;display:inline-block;cursor:crosshair}
#pdfCanvas{display:block;max-width:100%}
.sig-overlay{position:absolute;cursor:move;border:2px dashed #4f46e5;background:rgba(255,255,255,0.85);border-radius:4px;user-select:none;display:flex;align-items:center;justify-content:center;min-width:80px;min-height:40px}
.sig-overlay img{max-width:100%;max-height:100%;object-fit:contain;pointer-events:none}
.sig-overlay .sig-remove{position:absolute;top:-8px;right:-8px;width:18px;height:18px;background:#dc2626;color:#fff;border-radius:50%;font-size:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none}
.sig-overlay .sig-resize{position:absolute;bottom:-5px;right:-5px;width:12px;height:12px;background:#4f46e5;border-radius:2px;cursor:se-resize}
/* Buttons */
.btn-ajukan{background:#4f46e5;color:#fff;font-weight:600;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;font-size:14px;transition:all 0.2s}
.btn-ajukan:hover{background:#4338ca;transform:translateY(-1px)}
.btn-tolak{background:#fee2e2;color:#dc2626;font-weight:600;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;font-size:14px}
.btn-pasang-ttd{background:#16a34a;color:#fff;font-weight:600;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;font-size:13px;transition:all 0.2s}
.btn-pasang-ttd:hover{background:#15803d}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
.modal-box{background:#fff;border-radius:16px;padding:32px;max-width:440px;width:90%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.15)}
/* PDF page nav */
.pdf-nav-bar{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:#1f2937;color:#fff}
.pdf-nav-bar button{background:#374151;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:13px;cursor:pointer;font-weight:600}
.pdf-nav-bar button:hover{background:#4b5563}
.sig-upload-area{border:2px dashed #d1d5db;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all 0.2s;background:#fafafa}
.sig-upload-area:hover{border-color:#818cf8;background:#f5f3ff}
</style>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;border:none;background:#fee2e2;color:#dc2626;font-weight:500;font-size:14px;">
    {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-back">&larr;</a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">Detail Rencana Proker</h3>
            <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">{{ $proker->judul }}</p>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($isAdmin && in_array($proker->status, ['menunggu_ttd_ketua','menunggu_ttd_dpm','menunggu_ttd_dept','diajukan']))
            <button class="btn-tolak" onclick="document.getElementById('tolakModal').style.display='flex'">Tolak</button>
        @endif
        @if(($isCreator || $isAdmin) && in_array($proker->status, ['draft','ditolak']))
            <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
               class="btn" style="background:#f3f4f6;color:#374151;font-weight:600;font-size:13px;padding:8px 18px;border-radius:10px;">
                &#9998; Edit
            </a>
        @endif
        @if($isAdmin && in_array($proker->status, ['draft','ditolak']))
            <button class="btn btn-danger" style="font-size:13px;padding:8px 18px;border-radius:10px;"
                    onclick="document.getElementById('deleteModal').style.display='flex'">Hapus</button>
        @endif
    </div>
</div>

{{-- 5-Step Workflow Stepper --}}
@php
    $statusOrder = [
        'draft'              => 0,
        'ditolak'            => 0,
        'diajukan'           => 1,
        'menunggu_ttd_ketua' => 1,
        'menunggu_ttd_dpm'   => 2,
        'menunggu_ttd_dept'  => 3,
        'disetujui'          => 4,
        'akan_datang'        => 4,
        'berlangsung'        => 4,
        'selesai'            => 4,
    ];
    $cur = $statusOrder[$proker->status] ?? 0;

    // Cek TTD step 1 (perlu keduanya)
    $ttdKetuaDone = $ttdData->has('ketua_himpunan');
    $ttdBendaDone = $ttdData->has('bendahara');
    $step1Partial  = ($ttdKetuaDone || $ttdBendaDone) && !($ttdKetuaDone && $ttdBendaDone);

    $steps = [
        ['label' => 'Draft', 'sub' => 'Dibuat pengurus'],
        ['label' => 'TTD Ketua & Bendahara', 'sub' => 'Tanda tangan Step 2'],
        ['label' => 'TTD DPM', 'sub' => 'Tanda tangan Step 3'],
        ['label' => 'TTD Ketua Dept.', 'sub' => 'Tanda tangan Step 4'],
        ['label' => 'Selesai', 'sub' => 'Proker disetujui'],
    ];
@endphp

<div class="detail-card" style="padding:20px 24px;">
    <div class="workflow-stepper">
        @foreach($steps as $i => $step)
            @php
                $isDone    = $cur > $i;
                $isCurrent = $cur === $i;
                $isPartial = ($i === 1 && $step1Partial && $cur === 1);
                $cls = $isDone ? 'done' : ($isPartial ? 'partial' : ($isCurrent ? 'current' : ''));
            @endphp
            <div class="stepper-step {{ $cls }}">
                <div class="step-dot">
                    @if($isDone) ✓
                    @elseif($isPartial) ½
                    @else {{ $i+1 }}
                    @endif
                </div>
                <div class="step-label">{{ $step['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>
        @if($proker->status === 'draft' && ($isCreator || $isPengurus))
            <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-ajukan">
                    @if(!$proker->surat_proker)
                        <span title="Upload surat PDF dulu">🔒 Ajukan</span>
                    @else
                        &#128394; Ajukan ke Ketua Himpunan
                    @endif
                </button>
            </form>
            @if(!$proker->surat_proker)
                <small class="text-danger fw-semibold">⚠ Upload surat proker (PDF) di Edit sebelum mengajukan.</small>
            @else
                <small class="text-muted">Setelah diajukan, menunggu tanda tangan Ketua Himpunan & Bendahara.</small>
            @endif
        @elseif($proker->status === 'ditolak')
            <small style="color:#dc2626;font-weight:500;">&#9888; Proker ditolak. Silakan edit dan ajukan ulang.</small>
        @elseif($proker->status === 'menunggu_ttd_ketua')
            <small class="text-muted">Menunggu tanda tangan Ketua Himpunan dan paraf Bendahara.</small>
        @elseif($proker->status === 'menunggu_ttd_dpm')
            <small class="text-muted">Menunggu tanda tangan DPM.</small>
        @elseif($proker->status === 'menunggu_ttd_dept')
            <small class="text-muted">Menunggu tanda tangan Ketua Departemen.</small>
        @elseif($proker->status === 'disetujui')
            <small style="color:#16a34a;font-weight:500;">&#10003; Semua tanda tangan selesai. Proker disetujui!</small>
        @endif
    </div>
</div>

{{-- Catatan Penolakan --}}
@if($proker->status === 'ditolak' && $proker->catatan_penolakan)
<div style="background:#fff5f5;border:1.5px solid #fecaca;border-radius:12px;padding:20px;margin-bottom:20px;">
    <div style="font-weight:700;color:#dc2626;margin-bottom:8px;">&#9888; Proker Ditolak</div>
    <p style="color:#374151;font-size:14px;margin:0;">{{ $proker->catatan_penolakan }}</p>
    @if($isCreator || $isPengurus)
        <div style="margin-top:12px;">
            <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
               class="btn" style="background:#4f46e5;color:#fff;font-size:13px;padding:8px 18px;border-radius:10px;font-weight:600;">
                &#9998; Revisi &amp; Ajukan Ulang
            </a>
        </div>
    @endif
</div>
@endif

{{-- PANEL TTD — hanya tampil saat status relevan --}}
@php
    $showTtdPanel = in_array($proker->status, ['menunggu_ttd_ketua','menunggu_ttd_dpm','menunggu_ttd_dept','disetujui']);
    $canSignKetua = $isKetuaHimpunan && $proker->status === 'menunggu_ttd_ketua';
    $canSignBenda = $isBendahara && $proker->status === 'menunggu_ttd_ketua';
    $canSignDpm   = $isDpm && $proker->status === 'menunggu_ttd_dpm';
    $canSignDept  = $isKetuaDept && $proker->status === 'menunggu_ttd_dept';
    $anySigner    = $canSignKetua || $canSignBenda || $canSignDpm || $canSignDept;
@endphp

@if($showTtdPanel)
<div class="detail-card">
    <div class="detail-card-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Tanda Tangan Digital
    </div>

    @php
        $rolePanels = [
            ['role'=>'ketua_himpunan','label'=>'Ketua Himpunan','step'=>1,'status_req'=>'menunggu_ttd_ketua'],
            ['role'=>'bendahara','label'=>'Bendahara (Paraf)','step'=>1,'status_req'=>'menunggu_ttd_ketua'],
            ['role'=>'dpm','label'=>'DPM','step'=>2,'status_req'=>'menunggu_ttd_dpm'],
            ['role'=>'ketua_departemen','label'=>'Ketua Departemen','step'=>3,'status_req'=>'menunggu_ttd_dept'],
        ];
    @endphp

    <div class="row g-3">
    @foreach($rolePanels as $rp)
        @php
            $signed   = $ttdData->has($rp['role']);
            $ttdItem  = $ttdData->get($rp['role']);
            $panelCls = $signed ? 'signed' : (($proker->status === $rp['status_req']) ? 'waiting' : '');
        @endphp
        <div class="col-md-6">
            <div class="ttd-panel {{ $panelCls }}">
                <div class="ttd-role-label">
                    @if($signed) <span style="color:#16a34a;">&#10003;</span>
                    @elseif($proker->status === $rp['status_req']) <span style="color:#f59e0b;">&#9998;</span>
                    @else <span style="color:#9ca3af;">&#9711;</span>
                    @endif
                    {{ $rp['label'] }}
                </div>
                @if($signed)
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="{{ $ttdItem->signature_url }}" alt="TTD" style="max-height:60px;max-width:150px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;padding:4px;">
                        <div>
                            <div class="ttd-signed-info">&#10003; Ditandatangani oleh {{ $ttdItem->signedBy?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $ttdItem->signed_at?->translatedFormat('d M Y H:i') }}</div>
                            <div style="font-size:11px;color:#9ca3af;">Hal. {{ $ttdItem->page_number }} &bull; Posisi ({{ round($ttdItem->pos_x_percent) }}%, {{ round($ttdItem->pos_y_percent) }}%)</div>
                        </div>
                    </div>
                @elseif($proker->status === $rp['status_req'])
                    <div style="font-size:13px;color:#6b7280;">Menunggu tanda tangan...</div>
                @else
                    <div style="font-size:13px;color:#d1d5db;">Belum giliran</div>
                @endif
            </div>
        </div>
    @endforeach
    </div>

    {{-- PDF Viewer + TTD Drag & Drop (tampil jika user bisa tanda tangan DAN ada PDF) --}}
    @if($anySigner && $proker->surat_proker)
    @php
        $myRole = $canSignKetua ? 'ketua_himpunan' : ($canSignBenda ? 'bendahara' : ($canSignDpm ? 'dpm' : 'ketua_departemen'));
        $alreadySigned = $ttdData->has($myRole);
    @endphp
    <div style="margin-top:20px;border-top:1px solid #f3f4f6;padding-top:20px;">
        <div style="font-weight:700;font-size:15px;color:#1f2937;margin-bottom:12px;">
            &#128393; Pasang Tanda Tangan Anda di Surat Proker
        </div>

        @if($alreadySigned)
        <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <span style="color:#166534;font-weight:500;font-size:14px;">&#10003; Anda sudah menandatangani surat ini.</span>
            <button type="button"
                onclick="batalkanTtd('{{ $myRole }}', {{ $proker->id }}, '{{ route('manajemenmahasiswa.proker.batal_ttd', $proker->id) }}')"
                style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;border-radius:8px;padding:6px 14px;font-size:13px;font-weight:600;cursor:pointer;">
                &#8635; Batalkan &amp; Ulang TTD
            </button>
        </div>
        @endif

        {{-- Upload gambar TTD --}}
        <div style="margin-top:14px;">
            <label style="font-weight:600;font-size:13px;color:#374151;margin-bottom:8px;display:block;">
                1. Upload Gambar Tanda Tangan Anda (PNG/JPG transparan)
            </label>
            <div class="sig-upload-area" id="sigUploadArea" onclick="document.getElementById('sigImageInput').click()">
                <div style="font-size:28px;margin-bottom:6px;">✍️</div>
                <p style="margin:0;font-size:13px;">Klik untuk upload gambar tanda tangan</p>
                <small style="color:#9ca3af;">PNG transparan direkomendasikan • Maks 2MB</small>
            </div>
            <input type="file" id="sigImageInput" accept="image/png,image/jpeg,image/webp" style="display:none;" onchange="loadSignaturePreview(this)">
            <div id="sigPreviewBox" style="display:none;margin-top:10px;padding:12px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                <img id="sigPreviewImg" style="max-height:80px;max-width:200px;border:1px solid #e5e7eb;border-radius:4px;background:#fff;padding:4px;" alt="Preview TTD">
                <button type="button" onclick="clearSig()" style="margin-left:10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:4px 10px;font-size:12px;cursor:pointer;">✕ Hapus</button>
            </div>
        </div>

        {{-- PDF Viewer --}}
        <div style="margin-top:16px;">
            <label style="font-weight:600;font-size:13px;color:#374151;margin-bottom:8px;display:block;">
                2. Drag tanda tangan ke posisi yang diinginkan di PDF
            </label>
            <div class="pdf-viewer-container">
                <div class="pdf-nav-bar">
                    <button onclick="prevPage()">&#8249; Prev</button>
                    <span id="pageInfo" style="font-size:13px;">Halaman <span id="pageNum">1</span> / <span id="pageCount">?</span></span>
                    <button onclick="nextPage()">Next &#8250;</button>
                </div>
                <div style="overflow:auto;max-height:600px;background:#525659;display:flex;justify-content:center;padding:16px;">
                    <div class="pdf-canvas-wrapper" id="pdfCanvasWrapper">
                        <canvas id="pdfCanvas"></canvas>
                        <div id="sigOverlay" class="sig-overlay" style="display:none;left:20%;top:60%;width:15%;height:8%;">
                            <img id="sigOverlayImg" src="" alt="TTD">
                            <button class="sig-remove" onclick="removeSigOverlay()">&#10005;</button>
                            <div class="sig-resize" id="sigResize"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;margin-top:8px;">
                <p style="font-size:12px;color:#9ca3af;margin:0;">Drag tanda tangan untuk memindahkan posisi. Drag sudut kanan bawah untuk mengubah ukuran.</p>
                <button type="button" onclick="resetOverlayPosition()" style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:4px 12px;font-size:12px;color:#374151;cursor:pointer;white-space:nowrap;">
                    &#8635; Reset Posisi
                </button>
            </div>
        </div>

        {{-- Tombol Pasang TTD --}}
        <div style="margin-top:16px;">
            <button class="btn-pasang-ttd" id="btnPasangTtd" onclick="submitTtd('{{ $myRole }}', {{ $proker->id }}, '{{ route('manajemenmahasiswa.proker.pasang_ttd', $proker->id) }}')" disabled>
                &#10003; Pasang Tanda Tangan Saya
            </button>
            <span id="ttdStatusMsg" style="margin-left:12px;font-size:13px;color:#9ca3af;">Upload TTD dan posisikan di PDF terlebih dahulu.</span>
        </div>
    </div>
    @elseif($anySigner && !$proker->surat_proker)
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px;color:#92400e;font-size:14px;margin-top:16px;">
            &#9888; Surat proker (PDF) belum diupload. Pengurus perlu mengedit proker dan mengupload PDF surat terlebih dahulu.
        </div>
    @endif
</div>
@endif

{{-- Surat Proker Link --}}
@if($proker->surat_proker)
<div class="detail-card">
    <div class="detail-card-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        Surat Proker
    </div>
    <a href="{{ $proker->surat_proker_url }}" target="_blank"
       style="display:inline-flex;align-items:center;gap:10px;padding:12px 18px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;text-decoration:none;color:#0369a1;font-weight:600;font-size:14px;">
        &#128196; Lihat / Download Surat Proker (PDF)
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    </a>
</div>
@endif

{{-- Info Utama --}}
<div class="detail-card">
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if($proker->bidangs && $proker->bidangs->count() > 0)
            @foreach($proker->bidangs as $b)
                <span class="badge-bidang">{{ $b->nama_bidang }}</span>
            @endforeach
        @else
            <span class="badge-bidang" style="background:#f3e8ff;color:#7c3aed;">Prodi</span>
        @endif
        @foreach(($proker->kategoris ?? collect()) as $kat)
            <span class="badge-bidang" style="background:#fef3c7;color:#92400e;">{{ $kat->nama_kategori }}</span>
        @endforeach
    </div>
    <h4 class="fw-bold text-dark mb-3">{{ $proker->judul }}</h4>
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-item-label">Rencana Tanggal</div>
            <div class="meta-item-value">{{ $proker->tanggal_mulai->translatedFormat('d M Y') }}
                @if($proker->tanggal_selesai) &mdash; {{ $proker->tanggal_selesai->translatedFormat('d M Y') }} @endif
            </div>
        </div>
        @if($proker->lokasi)<div class="meta-item"><div class="meta-item-label">Lokasi</div><div class="meta-item-value">{{ $proker->lokasi }}</div></div>@endif
        @if($proker->target_peserta)<div class="meta-item"><div class="meta-item-label">Target Peserta</div><div class="meta-item-value">{{ number_format($proker->target_peserta) }} orang</div></div>@endif
        @if($proker->anggaran)<div class="meta-item"><div class="meta-item-label">Anggaran</div><div class="meta-item-value">Rp {{ number_format($proker->anggaran,0,',','.') }}</div></div>@endif
        @if($proker->tahun)<div class="meta-item"><div class="meta-item-label">Tahun</div><div class="meta-item-value">{{ $proker->tahun }}</div></div>@endif
        @if($proker->ketuaPelaksana)<div class="meta-item"><div class="meta-item-label">Ketua Pelaksana</div><div class="meta-item-value">{{ $proker->ketuaPelaksana->user->name ?? '-' }}</div></div>@endif
        <div class="meta-item"><div class="meta-item-label">Dibuat Oleh</div><div class="meta-item-value">{{ $proker->creator?->name ?? '-' }}</div></div>
    </div>
    @if($proker->panitia && $proker->panitia->count() > 0)
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
        <div class="meta-item-label" style="margin-bottom:10px;">RENCANA PANITIA ({{ $proker->panitia->count() }} orang)</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($proker->panitia as $p)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#eef2ff;color:#4338ca;border-radius:20px;font-size:12px;font-weight:600;border:1px solid #c7d2fe;">
                    {{ $p->user->name ?? '-' }}
                    @if($p->pivot->peran)<span style="color:#3730a3;"> - {{ $p->pivot->peran }}</span>@endif
                </span>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Deskripsi --}}
<div class="detail-card">
    <div class="detail-card-title">Deskripsi &amp; Latar Belakang</div>
    <div style="font-size:14px;color:#374151;line-height:1.75;white-space:pre-line;">{{ $proker->deskripsi }}</div>
</div>

{{-- Link ke Pelaksanaan --}}
@if(in_array($proker->status, ['disetujui','akan_datang','berlangsung','selesai']))
<div class="detail-card" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);border:1.5px solid #c7d2fe;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#4338ca;margin-bottom:4px;">&#128640; Proker Disetujui!</div>
            <div style="font-size:14px;color:#4f46e5;font-weight:500;">Input data realisasi di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="btn" style="background:#4f46e5;color:#fff;font-weight:600;padding:10px 22px;border-radius:10px;font-size:14px;white-space:nowrap;">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

{{-- Modals --}}
@if($isAdmin && in_array($proker->status, ['menunggu_ttd_ketua','menunggu_ttd_dpm','menunggu_ttd_dept','diajukan']))
<div id="tolakModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">&#9747;</div>
        <h5 class="fw-bold mb-2">Tolak Proker</h5>
        <form action="{{ route('manajemenmahasiswa.proker.tolak', $proker->id) }}" method="POST">
            @csrf @method('PATCH')
            <textarea name="catatan_penolakan" class="form-control mb-3" rows="3" style="border-radius:10px;" placeholder="Alasan penolakan..." required></textarea>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn" style="background:#f3f4f6;color:#374151;font-weight:600;border-radius:10px;" onclick="document.getElementById('tolakModal').style.display='none'">Batal</button>
                <button type="submit" class="btn-tolak">Tolak Proker</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($isAdmin && in_array($proker->status, ['draft','ditolak']))
<div id="deleteModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">&#128465;</div>
        <h5 class="fw-bold mb-2">Hapus Proker?</h5>
        <p style="color:#6b7280;font-size:14px;">Data proker "<strong>{{ $proker->judul }}</strong>" akan dihapus permanen.</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <button class="btn" style="background:#f3f4f6;color:#374151;font-weight:600;border-radius:10px;" onclick="document.getElementById('deleteModal').style.display='none'">Batal</button>
            <form action="{{ route('manajemenmahasiswa.proker.destroy', $proker->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="border-radius:10px;font-weight:600;">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endif

@php
    // Siapkan data TTD yang sudah tersimpan untuk dikirim ke JavaScript
    // (tidak bisa langsung di @json() karena Blade parser tidak bisa menangani fn() => [...] di dalamnya)
    $existingTtdsForJs = $ttdData->map(fn($t) => [
        'role'          => $t->role,
        'signature_url' => $t->signature_url,
        'page_number'   => $t->page_number,
        'pos_x_percent' => $t->pos_x_percent,
        'pos_y_percent' => $t->pos_y_percent,
        'width_percent' => $t->width_percent,
        'height_percent'=> $t->height_percent,
    ])->values();
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" crossorigin="anonymous"></script>
<script>
// ── PDF.js Setup ──────────────────────────────────────────────────────────────
const PDFURL          = @json($proker->surat_proker_url ?? null);
// PDF ASLI sebelum ada TTD tertanam — untuk regenerasi saat cancel/ulang
const PDFURL_ORIGINAL = @json($proker->surat_proker_original_url ?? null) ?? PDFURL;
// Data TTD yang sudah tersimpan (untuk re-embed saat regenerasi)
const EXISTING_TTDS   = @json($existingTtdsForJs);

let pdfDoc = null, currentPage = 1, totalPages = 0;
let sigImageDataUrl = null;
let pdfRendered = false; // flag: PDF sudah selesai dirender
const canvas   = document.getElementById('pdfCanvas');
const ctx      = canvas ? canvas.getContext('2d') : null;
const wrapper  = document.getElementById('pdfCanvasWrapper');
const overlay  = document.getElementById('sigOverlay');
const overlayImg = document.getElementById('sigOverlayImg');

if (PDFURL && canvas) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    pdfjsLib.getDocument({ url: PDFURL, withCredentials: false }).promise.then(pdf => {
        pdfDoc = pdf; totalPages = pdf.numPages;
        document.getElementById('pageCount').textContent = totalPages;
        renderPage(1);
    }).catch(e => {
        console.warn('PDF load error:', e);
    });
}

function renderPage(num) {
    if (!pdfDoc) return;
    pdfRendered = false;
    pdfDoc.getPage(num).then(page => {
        const viewport = page.getViewport({ scale: 1.4 });
        canvas.width  = viewport.width;
        canvas.height = viewport.height;
        page.render({ canvasContext: ctx, viewport }).promise.then(() => {
            document.getElementById('pageNum').textContent = num;
            pdfRendered = true;
            // Jika gambar TTD sudah diupload, tampilkan overlay di posisi default
            if (sigImageDataUrl && overlay) {
                placeOverlayDefault();
            }
            checkCanSubmit();
        });
    });
}

/**
 * Tempatkan overlay di posisi default: horizontal tengah, 60% dari atas canvas
 */
function placeOverlayDefault() {
    if (!overlay || !canvas) return;
    const cw = canvas.offsetWidth  || canvas.width  || 595;
    const ch = canvas.offsetHeight || canvas.height || 842;
    const ow = Math.round(cw * 0.20);
    const oh = Math.round(ch * 0.08);
    overlay.style.width  = ow + 'px';
    overlay.style.height = oh + 'px';
    overlay.style.left   = Math.round((cw - ow) / 2) + 'px';
    overlay.style.top    = Math.round(ch * 0.60) + 'px';
    overlay.style.display = 'flex';
}

function prevPage() { if (currentPage > 1) { currentPage--; renderPage(currentPage); } }
function nextPage() { if (pdfDoc && currentPage < totalPages) { currentPage++; renderPage(currentPage); } }

/**
 * Reset posisi overlay ke tengah-bawah canvas
 */
function resetOverlayPosition() {
    if (!overlay || overlay.style.display === 'none') {
        const msg = document.getElementById('ttdStatusMsg');
        if (msg) msg.textContent = 'Upload gambar TTD terlebih dahulu.';
        return;
    }
    placeOverlayDefault();
    const msg = document.getElementById('ttdStatusMsg');
    if (msg) msg.textContent = '\u2713 Posisi direset ke tengah.';
}

// ── Signature Image ───────────────────────────────────────────────────────────
function loadSignaturePreview(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        sigImageDataUrl = e.target.result;
        document.getElementById('sigPreviewImg').src = sigImageDataUrl;
        document.getElementById('sigPreviewBox').style.display = 'block';
        document.getElementById('sigUploadArea').style.display = 'none';
        if (overlay && overlayImg) {
            overlayImg.src = sigImageDataUrl;
            // Jika PDF sudah dirender, posisikan overlay; jika belum, render callback akan melakukannya
            if (pdfRendered) {
                placeOverlayDefault();
            } else {
                overlay.style.display = 'flex'; // tampilkan dulu, posisi diupdate saat PDF selesai
            }
        }
        checkCanSubmit();
    };
    reader.readAsDataURL(input.files[0]);
}
function clearSig() {
    sigImageDataUrl = null;
    document.getElementById('sigImageInput').value = '';
    document.getElementById('sigPreviewBox').style.display = 'none';
    document.getElementById('sigUploadArea').style.display = 'block';
    if (overlay) overlay.style.display = 'none';
    checkCanSubmit();
}
function removeSigOverlay() { if (overlay) overlay.style.display = 'none'; checkCanSubmit(); }

function checkCanSubmit() {
    const btn = document.getElementById('btnPasangTtd');
    const msg = document.getElementById('ttdStatusMsg');
    if (!btn) return;
    const hasImage = !!sigImageDataUrl;
    const hasPos   = overlay && overlay.style.display !== 'none';
    const hasPdf   = pdfRendered;
    btn.disabled = !(hasImage && hasPos && hasPdf);
    if (!hasImage) msg.textContent = 'Upload gambar tanda tangan terlebih dahulu.';
    else if (!hasPdf) msg.textContent = 'Menunggu PDF selesai dimuat...';
    else if (!hasPos) msg.textContent = 'Posisikan tanda tangan di dokumen PDF.';
    else msg.textContent = '✓ Siap dipasang! Atur posisi lalu klik tombol.';
}

// ── Drag & Resize Overlay ─────────────────────────────────────────────────────
if (overlay && wrapper) {
    let dragging = false, resizing = false, startX, startY, startL, startT, startW, startH;

    overlay.addEventListener('mousedown', e => {
        if (e.target.id === 'sigResize') return;
        dragging = true; startX = e.clientX; startY = e.clientY;
        const r = overlay.getBoundingClientRect();
        startL = overlay.offsetLeft; startT = overlay.offsetTop;
        e.preventDefault();
    });

    document.getElementById('sigResize')?.addEventListener('mousedown', e => {
        resizing = true; startX = e.clientX; startY = e.clientY;
        startW = overlay.offsetWidth; startH = overlay.offsetHeight;
        e.preventDefault(); e.stopPropagation();
    });

    document.addEventListener('mousemove', e => {
        if (dragging) {
            const dx = e.clientX - startX, dy = e.clientY - startY;
            // Gunakan canvas sebagai batas drag agar koordinat konsisten dengan submit
            const canvasW = canvas ? (canvas.offsetWidth  || canvas.width)  : wrapper.offsetWidth;
            const canvasH = canvas ? (canvas.offsetHeight || canvas.height) : wrapper.offsetHeight;
            const maxL = Math.max(0, canvasW - overlay.offsetWidth);
            const maxT = Math.max(0, canvasH - overlay.offsetHeight);
            overlay.style.left = Math.max(0, Math.min(startL + dx, maxL)) + 'px';
            overlay.style.top  = Math.max(0, Math.min(startT + dy, maxT)) + 'px';
            checkCanSubmit();
        }
        if (resizing) {
            const nw = Math.max(60, startW + (e.clientX - startX));
            const nh = Math.max(30, startH + (e.clientY - startY));
            overlay.style.width  = nw + 'px';
            overlay.style.height = nh + 'px';
            checkCanSubmit();
        }
    });
    document.addEventListener('mouseup', () => { dragging = false; resizing = false; });
}

// ── Submit TTD ────────────────────────────────────────────────────────────────
async function submitTtd(role, prokerId, url) {
    if (!sigImageDataUrl || !overlay || overlay.style.display === 'none') {
        alert('Harap upload gambar tanda tangan dan posisikan di PDF terlebih dahulu.');
        return;
    }
    const cw = canvas ? (canvas.offsetWidth  || canvas.width)  : 0;
    const ch = canvas ? (canvas.offsetHeight || canvas.height) : 0;
    if (!cw || !ch) {
        alert('PDF belum selesai dimuat. Tunggu sebentar lalu coba lagi.');
        return;
    }

    const btn = document.getElementById('btnPasangTtd');
    const msg = document.getElementById('ttdStatusMsg');
    btn.disabled = true;

    // Hitung posisi sebagai persentase relatif terhadap canvas
    const xPct = Math.min(100, Math.max(0, (overlay.offsetLeft / cw * 100)));
    const yPct = Math.min(100, Math.max(0, (overlay.offsetTop  / ch * 100)));
    const wPct = Math.min(100, Math.max(1, (overlay.offsetWidth  / cw * 100)));
    const hPct = Math.min(100, Math.max(1, (overlay.offsetHeight / ch * 100)));

    console.log('TTD submit:', { role, page: currentPage, xPct, yPct, wPct, hPct, cw, ch });

    try {
        // ── Step 1: Load PDF ASLI (original, sebelum ada TTD tertanam) ──────────────
        // Selalu load dari original agar cancel/ulang tidak duplikasi signature
        msg.textContent = '⏳ Memuat PDF asli...';
        const baseUrl = PDFURL_ORIGINAL || PDFURL;
        const pdfResponse = await fetch(baseUrl);
        if (!pdfResponse.ok) throw new Error('Gagal memuat PDF dari server: ' + pdfResponse.status);
        const pdfBytes = await pdfResponse.arrayBuffer();

        msg.textContent = '⏳ Menyematkan tanda tangan ke PDF...';
        const pdfLibDoc = await PDFLib.PDFDocument.load(pdfBytes, { ignoreEncryption: true });

        // ── Step 2: Re-embed semua TTD yang sudah ada (kecuali role yang sedang sign) ──
        // Ini memastikan TTD lain yang valid tetap ada di PDF hasil
        if (EXISTING_TTDS && EXISTING_TTDS.length > 0) {
            msg.textContent = '⏳ Menyatukan tanda tangan sebelumnya...';
            for (const existingTtd of EXISTING_TTDS) {
                if (existingTtd.role === role) continue; // Skip role yang sedang re-sign
                if (!existingTtd.signature_url) continue;
                try {
                    const exSigResp   = await fetch(existingTtd.signature_url);
                    const exSigBlob   = await exSigResp.blob();
                    const exSigBuffer = await exSigBlob.arrayBuffer();
                    const exPage = pdfLibDoc.getPage((existingTtd.page_number || 1) - 1);
                    const { width: exW, height: exH } = exPage.getSize();
                    const exIsPng = exSigBlob.type === 'image/png';
                    const exImg = exIsPng
                        ? await pdfLibDoc.embedPng(exSigBuffer)
                        : await pdfLibDoc.embedJpg(exSigBuffer);
                    exPage.drawImage(exImg, {
                        x: exW * (existingTtd.pos_x_percent / 100),
                        y: exH * (1 - (existingTtd.pos_y_percent + existingTtd.height_percent) / 100),
                        width:  exW * (existingTtd.width_percent  / 100),
                        height: exH * (existingTtd.height_percent / 100),
                        opacity: 1,
                    });
                } catch (exErr) {
                    console.warn('Gagal embed existing TTD role', existingTtd.role, exErr);
                }
            }
        }

        // Muat gambar tanda tangan
        const sigFetch  = await fetch(sigImageDataUrl);
        const sigBlob   = await sigFetch.blob();
        const sigBuffer = await sigBlob.arrayBuffer();

        // Embed sebagai PNG atau JPEG
        const isPng = sigBlob.type === 'image/png' || sigImageDataUrl.startsWith('data:image/png');
        const embeddedImg = isPng
            ? await pdfLibDoc.embedPng(sigBuffer)
            : await pdfLibDoc.embedJpg(sigBuffer);

        // Ambil halaman PDF yang dituju (pdf-lib menggunakan index 0-based)
        const pdfPage = pdfLibDoc.getPage(currentPage - 1);
        const { width: pageW, height: pageH } = pdfPage.getSize();

        // Konversi persentase canvas ke koordinat PDF (poin)
        // CATATAN: sistem koordinat PDF berasal dari pojok kiri-BAWAH (y meningkat ke atas)
        //          sedangkan canvas berasal dari pojok kiri-ATAS (y meningkat ke bawah)
        const xPt = pageW * (xPct / 100);
        const hPt = pageH * (hPct / 100);
        const wPt = pageW * (wPct / 100);
        // yPct dari atas canvas -> konversi ke y dari bawah PDF
        const yPt = pageH * (1 - (yPct + hPct) / 100);

        pdfPage.drawImage(embeddedImg, {
            x: xPt, y: yPt,
            width: wPt, height: hPt,
            opacity: 1,
        });

        // Serialisasi PDF yang sudah di-embed TTD
        msg.textContent = '⏳ Mengupload PDF bertanda tangan...';
        const signedPdfBytes = await pdfLibDoc.save();
        const signedPdfFile  = new File([signedPdfBytes], 'surat_proker_signed.pdf', { type: 'application/pdf' });

        // ── Step 2: Siapkan FormData ──────────────────────────────────────────
        const sigFile = new File([sigBlob], 'signature.png', { type: sigBlob.type });

        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        fd.append('role', role);
        fd.append('signature_image', sigFile);    // Gambar TTD mentah (untuk panel TTD)
        fd.append('signed_pdf', signedPdfFile);   // PDF dengan TTD terbenam (untuk download)
        fd.append('page_number', currentPage);
        fd.append('pos_x_percent', xPct.toFixed(2));
        fd.append('pos_y_percent', yPct.toFixed(2));
        fd.append('width_percent', wPct.toFixed(2));
        fd.append('height_percent', hPct.toFixed(2));

        // ── Step 3: Upload ke server ──────────────────────────────────────────
        const response = await fetch(url, { method: 'POST', body: fd });
        const rawText  = await response.text();
        let data;
        try {
            data = JSON.parse(rawText);
        } catch {
            console.error('Server non-JSON response (HTTP ' + response.status + '):', rawText.substring(0, 600));
            msg.textContent = '❌ Server error (HTTP ' + response.status + '). Lihat console browser.';
            msg.style.color = '#dc2626';
            btn.disabled = false;
            btn.textContent = '✓ Pasang Tanda Tangan Saya';
            return;
        }

        if (data.success) {
            msg.textContent = '✓ ' + (data.message || 'Tanda tangan berhasil dipasang!');
            msg.style.color = '#16a34a';
            btn.textContent = '✓ Berhasil!';
            setTimeout(() => location.reload(), 1500);
        } else {
            msg.textContent = '❌ Gagal: ' + (data.message || 'Terjadi kesalahan.');
            msg.style.color = '#dc2626';
            btn.disabled = false;
            btn.textContent = '✓ Pasang Tanda Tangan Saya';
        }
    } catch(err) {
        console.error('Error saat pasang TTD:', err);
        msg.textContent = '❌ Error: ' + err.message;
        msg.style.color = '#dc2626';
        btn.disabled = false;
        btn.textContent = '✓ Pasang Tanda Tangan Saya';
    }
}

// ── Batalkan TTD ──────────────────────────────────────────────────────────────
async function batalkanTtd(role, prokerId, url) {
    if (!confirm('⚠️ Batalkan tanda tangan Anda? Anda dapat menandatangani ulang setelah ini.')) return;

    try {
        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        fd.append('_method', 'DELETE');
        fd.append('role', role);

        const response = await fetch(url, { method: 'POST', body: fd });
        const rawText  = await response.text();
        let data;
        try { data = JSON.parse(rawText); } catch {
            alert('❌ Server error saat membatalkan TTD (HTTP ' + response.status + ').');
            return;
        }

        if (data.success) {
            // Notifikasi singkat lalu reload
            const alertEl = document.createElement('div');
            alertEl.textContent = '✓ ' + data.message;
            alertEl.style.cssText = 'position:fixed;top:20px;right:20px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:10px;padding:14px 20px;font-weight:600;font-size:14px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.1);';
            document.body.appendChild(alertEl);
            setTimeout(() => location.reload(), 1200);
        } else {
            alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan.'));
        }
    } catch(err) {
        console.error('Error batalkan TTD:', err);
        alert('❌ Error jaringan: ' + err.message);
    }
}

// Close modals on backdrop click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
</script>

</x-manajemenmahasiswa::layouts.mahasiswa>

