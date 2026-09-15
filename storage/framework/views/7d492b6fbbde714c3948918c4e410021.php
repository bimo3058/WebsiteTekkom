<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Pertanyaan Umum</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        :root {
            --primary-50:#eef2ff;--primary-100:#e0e7ff;--primary-500:#4f46e5;
            --grey-50:#f9fafb;--grey-100:#f3f4f6;--grey-200:#e5e7eb;
            --grey-400:#9ca3af;--grey-500:#6b7280;--grey-700:#374151;--grey-800:#1f2937;--grey-900:#030712;
        }
        .sikape-card { background:#fff; border:1px solid var(--grey-200); border-radius:12px; }
    </style>
    <?php if (isset($component)) { $__componentOriginal798d336f107c986f84961f0c7e80911e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal798d336f107c986f84961f0c7e80911e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation-assets','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation-assets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $attributes = $__attributesOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__attributesOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $component = $__componentOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__componentOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
</head>
<body style="background:#f9fafb;" x-data="{ sidebarOpen: false, search: '', openItem: null }">
<div class="flex h-screen w-full overflow-hidden">

    <?php echo $__env->make('eoffice::kp.mahasiswa.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        <?php echo $__env->make('eoffice::kp.mahasiswa.partials.topbar', [
            'breadcrumbs' => ['Informasi', 'Pertanyaan Umum']
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            
            <div class="mb-6">
                <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Pertanyaan Umum</h1>
                <p class="text-sm mt-1" style="color:var(--grey-500);">Temukan jawaban atas pertanyaan yang sering ditanyakan seputar Kerja Praktik.</p>
            </div>

            
            <div class="sikape-card p-4 mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" style="color:var(--grey-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Cari pertanyaan..." class="flex-1 text-sm outline-none bg-transparent" style="color:var(--grey-800);">
                <button x-show="search" @click="search=''" class="text-xs px-2 py-1 rounded" style="color:var(--grey-400);background:var(--grey-100);">Hapus</button>
            </div>

            <?php
                $staticFaqs = [
                    ['cat'=>'Umum','popular'=>true,
                     'q'=>'Apa saja syarat untuk mendaftar Kerja Praktik?',
                     'a'=>'Syarat utama mendaftar KP: (1) Telah menempuh minimal 100 SKS, (2) IPK minimal 2.50, (3) Telah lulus mata kuliah Metodologi Penelitian, (4) Memiliki rencana tempat KP yang jelas. Lengkapi dokumen: KHS semester 4, proposal KP, dan surat pengantar dari departemen.'],
                    ['cat'=>'Umum','popular'=>true,
                     'q'=>'Berapa lama durasi Kerja Praktik yang diperbolehkan?',
                     'a'=>'Durasi KP minimal 1 bulan (30 hari kerja) dan maksimal 6 bulan. Disarankan melaksanakan KP selama 2–3 bulan agar materi yang didapat lebih mendalam dan laporan lebih berbobot.'],
                    ['cat'=>'Administrasi','popular'=>true,
                     'q'=>'Bagaimana cara meminjam ruangan untuk seminar KP?',
                     'a'=>'Langkah peminjaman ruangan seminar: (1) Tentukan jadwal bersama dosen pembimbing, (2) Akses modul Peminjaman di SIKAPE atau langsung ke TU Departemen, (3) Isi formulir peminjaman dengan detail: tanggal, waktu, estimasi peserta, (4) Setelah disetujui, masukkan nama ruangan ke formulir pendaftaran Seminar di SIKAPE. Ruangan tersedia: Ruang Seminar Lt.2, Ruang Rapat Dosen, Lab Komputer (untuk seminar online).'],
                    ['cat'=>'Administrasi','popular'=>false,
                     'q'=>'Bagaimana cara mendapatkan surat pengantar dari departemen?',
                     'a'=>'Untuk mendapatkan surat pengantar: (1) Datang ke TU Departemen pada jam kerja (08.00–15.00), (2) Bawa proposal KP yang sudah disetujui dosen pembimbing, (3) Isi form permintaan surat, (4) Surat selesai dalam 3–5 hari kerja. Biaya: GRATIS.'],
                    ['cat'=>'Dokumen','popular'=>false,
                     'q'=>'Apa itu dokumen A2 dan bagaimana cara mengisinya?',
                     'a'=>'Dokumen A2 adalah borang logbook/presensi harian yang wajib diisi selama KP berlangsung. Berisi: tanggal kehadiran, kegiatan yang dilakukan, dan paraf pembimbing lapangan. Download template A2 di halaman Dokumen, cetak, isi selama KP, kemudian scan dan upload kembali ke sistem.'],
                    ['cat'=>'Dokumen','popular'=>false,
                     'q'=>'Kapan batas upload laporan dan makalah KP?',
                     'a'=>'Laporan dan makalah KP harus sudah diupload dan mendapat persetujuan (ACC) dari dosen pembimbing paling lambat 2 minggu sebelum tanggal seminar yang direncanakan. Koordinator KP akan menentukan deadline final setiap semester melalui pengumuman di halaman Dashboard.'],
                    ['cat'=>'Seminar','popular'=>true,
                     'q'=>'Apa saja syarat yang harus dipenuhi sebelum mendaftar seminar?',
                     'a'=>'Syarat lengkap pendaftaran seminar KP: ✓ Judul dan tempat KP fix sudah diisi, ✓ Bukti diterima di instansi sudah diupload, ✓ Laporan KP sudah disetujui dosen pembimbing, ✓ Makalah IEEE sudah disetujui dosen pembimbing, ✓ Kartu Hijau sudah diupload, ✓ Nilai lapangan dari instansi sudah diupload. Semua syarat dapat dipantau di halaman Seminar KP.'],
                    ['cat'=>'Seminar','popular'=>false,
                     'q'=>'Siapa yang harus hadir pada saat seminar KP?',
                     'a'=>'Peserta wajib seminar KP: (1) Mahasiswa yang bersangkutan, (2) Dosen Pembimbing, (3) Penguji yang ditunjuk Koordinator KP. Peserta opsional: rekan mahasiswa (minimal 3 orang untuk quorum), perwakilan instansi. Form kehadiran peserta (B2) harus dibawa dan ditandatangani seluruh peserta.'],
                ];
            ?>

            <div class="mb-5">

                
                <div class="space-y-3">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $staticFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div x-show="('<?php echo e(addslashes($faq['q'])); ?>'.toLowerCase().includes(search.toLowerCase()) || search === '')"
                         class="sikape-card overflow-hidden">
                        <button @click="openItem = openItem === <?php echo e($i); ?> ? null : <?php echo e($i); ?>"
                                class="flex items-center justify-between w-full p-5 text-left group">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <p class="text-sm font-semibold pr-2" style="color:var(--grey-800);"><?php echo e($faq['q']); ?></p>
                            </div>
                            <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200" :class="openItem === <?php echo e($i); ?> ? 'rotate-180' : ''" style="color:var(--grey-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openItem === <?php echo e($i); ?>" x-collapse>
                            <div class="px-5 pb-5 border-t" style="border-color:var(--grey-100);">
                                <p class="text-sm leading-relaxed pt-4 whitespace-pre-line" style="color:var(--grey-600);"><?php echo e($faq['a']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $faqItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div x-show="('<?php echo e(addslashes($faq->judul)); ?>'.toLowerCase().includes(search.toLowerCase()) || search === '')"
                         class="sikape-card overflow-hidden">
                        <button @click="openItem = openItem === 'db<?php echo e($j); ?>' ? null : 'db<?php echo e($j); ?>'"
                                class="flex items-center justify-between w-full p-5 text-left">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <p class="text-sm font-semibold" style="color:var(--grey-800);"><?php echo e($faq->judul); ?></p>
                            </div>
                            <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200" :class="openItem === 'db<?php echo e($j); ?>' ? 'rotate-180' : ''" style="color:var(--grey-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openItem === 'db<?php echo e($j); ?>'" x-collapse>
                            <div class="px-5 pb-5 border-t" style="border-color:var(--grey-100);">
                                <p class="text-sm leading-relaxed pt-4" style="color:var(--grey-600);"><?php echo e($faq->konten); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    
                    <div x-show="search && search.length > 2" class="hidden last:block">
                        <div class="sikape-card p-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3" style="color:var(--grey-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-semibold" style="color:var(--grey-500);">Pertanyaan tidak ditemukan</p>
                            <p class="text-xs mt-1" style="color:var(--grey-400);">Coba kata kunci lain atau hubungi Koordinator KP.</p>
                        </div>
                    </div>

                </div>
            </div>

            
            <div class="mt-6 rounded-xl p-5 flex flex-col sm:flex-row items-center gap-5" style="background:var(--primary-50);border:1px solid var(--primary-100);">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--primary-100);">
                    <svg class="w-6 h-6" style="color:var(--primary-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color:#3730a3;">Masih ada pertanyaan?</p>
                    <p class="text-xs mt-0.5" style="color:#4338ca;">Hubungi Koordinator KP atau kunjungi Tata Usaha Departemen pada jam kerja (Senin–Jumat, 08.00–15.00 WIB).</p>
                </div>
            </div>

        </main>
    </div>
</div>
    <?php if (isset($component)) { $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $attributes = $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $component = $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\kp\mahasiswa\faq.blade.php ENDPATH**/ ?>