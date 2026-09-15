<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - <?php echo e($mhs->nama); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter Tight', sans-serif;
            color: #0D0D12;
            background: #f5f6fa;
            line-height: 1.6;
        }
        .cv-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
        }

        /* Header */
        .cv-header {
            background: linear-gradient(135deg, #0B266E 0%, #091958 100%);
            color: white;
            padding: 48px 48px 40px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 28px;
        }
        .cv-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0B266E, #5C78B8);
        }
        .cv-photo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.4);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cv-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cv-photo-initial {
            font-size: 36px;
            font-weight: 800;
            color: white;
        }
        .cv-name {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .cv-subtitle {
            font-size: 16px;
            opacity: 0.85;
            font-weight: 500;
        }
        .cv-contact {
            display: flex;
            gap: 20px;
            margin-top: 16px;
            font-size: 13px;
            opacity: 0.9;
        }
        .cv-contact span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Body */
        .cv-body {
            padding: 40px 48px;
        }
        .cv-section {
            margin-bottom: 32px;
        }
        .cv-section-title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #0B266E;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #eef2ff;
        }
        .cv-info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .cv-info-label {
            width: 160px;
            font-weight: 600;
            color: #666D80;
            flex-shrink: 0;
        }
        .cv-info-value {
            font-weight: 500;
            color: #0D0D12;
        }

        /* Tables */
        .cv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .cv-table th {
            background: #FAFAFA;
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
            color: #666D80;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #DFE1E7;
        }
        .cv-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .cv-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        .btn-print {
            background: #0B266E;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(11, 38, 110, 0.3);
            transition: all 0.2s;
            font-family: 'Inter Tight', sans-serif;
        }
        .btn-print:hover {
            background: #091958;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(11, 38, 110, 0.4);
        }
        .btn-back {
            background: #ffffff;
            color: #666D80;
            border: 1.5px solid #DFE1E7;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            font-family: 'Inter Tight', sans-serif;
        }
        .btn-back:hover {
            background: #FAFAFA;
            border-color: #C1C7CF;
            color: #374151;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            .print-controls {
                display: none !important;
            }
            .cv-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            .cv-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                margin: 0;
                size: A4;
            }
        }

        @media screen {
            .cv-container {
                margin: 30px auto;
                box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                border-radius: 12px;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

<!-- Print Controls -->
<div class="print-controls">
    <a href="javascript:history.back()" class="btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>
    <button onclick="window.print()" class="btn-print">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print / Download PDF
    </button>
</div>

<!-- CV Content -->
<div class="cv-container">
    <!-- Header -->
    <div class="cv-header">
        <div class="cv-photo">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->user && ($mhs->user->avatar_url_format || $mhs->user->avatar_url)): ?>
                <img src="<?php echo e($mhs->user->avatar_url_format ?? $mhs->user->avatar_url); ?>" alt="<?php echo e($mhs->nama); ?>">
            <?php else: ?>
                <span class="cv-photo-initial"><?php echo e(strtoupper(substr($mhs->nama, 0, 1))); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div>
            <div class="cv-name"><?php echo e($mhs->nama); ?></div>
            <div class="cv-subtitle">Mahasiswa · <?php echo e($mhs->nim); ?></div>
            <div class="cv-contact">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->user && $mhs->user->email): ?>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <?php echo e($mhs->user->email); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->kontak): ?>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <?php echo e($mhs->kontak); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="cv-body">
        <!-- Informasi Pribadi -->
        <div class="cv-section">
            <div class="cv-section-title">Informasi Pribadi</div>
            <div class="cv-info-row">
                <div class="cv-info-label">Nama Lengkap</div>
                <div class="cv-info-value"><?php echo e($mhs->nama); ?></div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">NIM</div>
                <div class="cv-info-value"><?php echo e($mhs->nim); ?></div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">Angkatan</div>
                <div class="cv-info-value"><?php echo e($mhs->angkatan); ?></div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">Status</div>
                <div class="cv-info-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($mhs->status):
                        case ('aktif'): ?> Aktif <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                        <?php case ('alumni'): ?> Lulus <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                        <?php case ('cuti'): ?> Cuti <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                        <?php case ('drop_out'): ?> Drop Out <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php break; ?>
                        <?php default: ?> <?php echo e(ucfirst($mhs->status)); ?>

                    <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Prestasi -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mhs->prestasi->count() > 0): ?>
        <div class="cv-section">
            <div class="cv-section-title">Prestasi & Penghargaan</div>
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Prestasi</th>
                        <th>Tingkat</th>
                        <th>Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mhs->prestasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td><?php echo e($i + 1); ?></td>
                            <td style="font-weight: 600;"><?php echo e($p->nama_prestasi); ?></td>
                            <td><?php echo e(ucfirst($p->tingkat)); ?></td>
                            <td><?php echo e($p->tanggal ? $p->tanggal->format('Y') : '-'); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Riwayat Kegiatan -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayatKegiatan->count() > 0): ?>
        <div class="cv-section">
            <div class="cv-section-title">Riwayat Kegiatan & Organisasi</div>
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayatKegiatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $rw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
                            $namaManual  = $rw->nama_kegiatan_manual ?? null;
                            $peranManual = $rw->peran_manual ?? null;
                            $peranValue  = $peranManual ?: ucfirst($rw->peran ?? '');
                            $tanggalDisplay = null;
                            if ($hasKegiatan && $rw->kegiatan->tanggal_mulai) {
                                $tanggalDisplay = $rw->kegiatan->tanggal_mulai;
                            } elseif (isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan) {
                                $tanggalDisplay = $rw->tanggal_kegiatan;
                            }
                        ?>
                        <tr>
                            <td><?php echo e($i + 1); ?></td>
                            <td style="font-weight: 600;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasKegiatan): ?>
                                    <?php echo e($rw->kegiatan->judul); ?>

                                <?php elseif($namaManual): ?>
                                    <?php echo e($namaManual); ?>

                                <?php else: ?>
                                    Kegiatan tidak ditemukan
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($peranValue); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggalDisplay): ?>
                                    <?php echo e(\Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y')); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Footer -->
        <div style="margin-top: 48px; padding-top: 20px; border-top: 1px solid #DFE1E7; text-align: center;">
            <p style="font-size: 11px; color: #666D80;">
                Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Kemahasiswaan pada <?php echo e(now()->translatedFormat('d F Y, H:i')); ?> WIB
            </p>
        </div>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\direktori\mahasiswa-cv.blade.php ENDPATH**/ ?>