<?php if (isset($component)) { $__componentOriginal28fe751225ca81f38990401ea46d9b64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal28fe751225ca81f38990401ea46d9b64 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.forum-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.forum-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php $__env->startPush('styles'); ?>
        <style>
            /* ── Page Title ──────────────────────────────────────────────────── */
            .page-title { margin-bottom: 22px; display: flex; align-items: center; gap: 16px; }
            .page-title .back-btn { background: #fff; border: 1px solid #e5e7eb; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #4b5563; text-decoration: none; transition: background 0.2s; }
            .page-title .back-btn:hover { background: #f3f4f6; }
            .page-title h1 { font-size: 26px; font-weight: 700; color: #111827; margin: 0 0 2px; letter-spacing: -0.02em; }
            .page-title p { font-size: 14px; color: #6b7280; margin: 0; }

            .create-post-card {
                background: transparent;
                border-radius: 12px;
                padding: 30px 0;
                border: none;
                margin-bottom: 20px;
            }

            .form-label {
                font-weight: 600;
                color: #1f2937;
                font-size: 14px;
                margin-bottom: 8px;
            }

            .custom-input,
            .custom-select,
            .custom-textarea {
                background-color: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 14px;
                width: 100%;
                transition: all 0.2s;
            }

            .custom-input:focus,
            .custom-select:focus,
            .custom-textarea:focus {
                background-color: #ffffff;
                border-color: #6F7DA4;
                box-shadow: 0 0 0 3px rgba(41, 60, 121, 0.1);
                outline: none;
            }

            .char-count {
                font-size: 12px;
                color: #9ca3af;
                position: absolute;
                bottom: 12px;
                right: 16px;
            }

            .input-wrapper {
                position: relative;
            }

            .btn-action {
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
                font-size: 14px;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-post { background-color: #293C79; color: white; }
            .btn-post:hover { background-color: #415086; }
            .btn-cancel { background-color: #ef4444; color: white; }
            .btn-cancel:hover { background-color: #dc2626; }

            .section-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                transition: all 0.2s;
                width: 100%;
                text-align: left;
            }

            .section-toggle:hover { background: #E7E8F0; border-color: #293C79; color: #415086; }
            .section-toggle.active { background: #E7E8F0; border-color: #293C79; color: #415086; }

            .section-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease, padding 0.3s ease;
            }

            .section-content.open {
                max-height: 600px;
                padding-top: 16px;
            }

            .media-dropzone {
                border: 2px dashed #d1d5db;
                border-radius: 12px;
                padding: 32px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #f9fafb;
                position: relative;
            }

            .media-dropzone:hover, .media-dropzone.dragover {
                border-color: #293C79;
                background: #E7E8F0;
            }

            .media-dropzone .dropzone-text { font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px; }
            .media-dropzone .dropzone-hint { font-size: 13px; color: #9ca3af; }
            .media-dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

            .media-preview-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
                margin-top: 16px;
            }

            .media-preview-item {
                position: relative;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                background: #000;
                display: flex;
                flex-direction: column;
            }

            .media-preview-item .media-thumb {
                position: relative;
                width: 100%;
                aspect-ratio: 1;
                overflow: hidden;
                flex-shrink: 0;
            }

            .media-preview-item img, .media-preview-item video {
                width: 100%; height: 100%; object-fit: cover; display: block;
            }

            .media-preview-item .remove-media {
                position: absolute; top: 6px; right: 6px;
                width: 26px; height: 26px; border-radius: 50%;
                background: #ef4444; color: white;
                border: 2px solid #fff; font-size: 13px;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer; transition: transform 0.15s; z-index: 2;
                box-shadow: 0 1px 4px rgba(0,0,0,0.25);
            }

            .media-preview-item .remove-media:hover { transform: scale(1.12); }

            .media-preview-item .file-info {
                padding: 8px 10px;
                background: #fff;
                border-top: 1px solid #f3f4f6;
            }

            .media-preview-item .file-name {
                color: #111827;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.4;
            }

            .media-preview-item .file-size {
                color: #9ca3af;
                font-size: 11px;
                font-weight: 400;
                margin-top: 1px;
            }

            .existing-badge {
                position: absolute; top: 6px; left: 6px;
                background: rgba(79, 70, 229, 0.85); color: white;
                font-size: 10px; font-weight: 700;
                padding: 2px 6px; border-radius: 4px; z-index: 2;
            }

            .media-counter {
                font-size: 13px; font-weight: 600;
                padding: 4px 12px; border-radius: 20px;
                display: inline-block; margin-top: 8px;
            }
            .media-counter.ok { background: #dcfce7; color: #16a34a; }
            .media-counter.warn { background: #fef3c7; color: #d97706; }
            .media-counter.full { background: #fee2e2; color: #dc2626; }

        </style>
    <?php $__env->stopPush(); ?>

    <div class="page-title">
        <a href="<?php echo e(route('manajemenmahasiswa.forum.show', $thread->id)); ?>" class="back-btn">
            <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'arrow-narrow-left','size' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-narrow-left','size' => '20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
        </a>
        <div>
            <h1>Edit Thread</h1>
            <p>Perbarui konten postingan Anda</p>
        </div>
    </div>

    <div class="create-post-card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger" style="border-radius: 10px; border: none; font-size: 14px;">
                <ul class="mb-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php
            $existingMedia = $thread->extractMediaUrls();
            $existingText = $thread->getTextContent();
            // Extract link from konten
            $existingLink = '';
            if (preg_match('/href=["\']([^"\']+)["\']/', $thread->konten ?? '', $m)) {
                $existingLink = $m[1];
            }
        ?>

        <form action="<?php echo e(route('manajemenmahasiswa.forum.update', $thread->id)); ?>" method="POST" id="editPostForm"
            enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div class="mb-4">
                <label class="form-label">Judul Postingan <span class="text-danger">*</span></label>
                <div class="input-wrapper">
                    <input type="text" name="judul" id="inputJudul"
                        class="custom-input <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="Tuliskan judul yang menarik dan deskriptif..." maxlength="200" required
                        value="<?php echo e(old('judul', $thread->judul)); ?>">
                    <span class="char-count"><span id="judulCount">0</span>/200</span>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="form-label">Kategori Postingan <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-3 mt-2 <?php $__errorArgs = ['kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php
                        $selectedKategori = old('kategori', is_array($thread->kategori) ? $thread->kategori : [$thread->kategori]);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input shadow-none" style="cursor: pointer;" type="checkbox" name="kategori[]" id="kategori_<?php echo e($key); ?>" value="<?php echo e($key); ?>" <?php echo e(in_array($key, $selectedKategori) ? 'checked' : ''); ?>>
                            <label class="form-check-label text-dark" style="cursor: pointer; font-size: 14px;" for="kategori_<?php echo e($key); ?>"><?php echo e($label); ?></label>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="form-label">Isi Postingan</label>
                <textarea name="konten" id="inputKonten" class="custom-textarea" rows="6"
                    placeholder="Bagikan apa yang ada di pikiranmu..."><?php echo e(old('konten', $existingText)); ?></textarea>
            </div>

            
            <div class="mb-4">
                <button type="button" class="section-toggle <?php echo e(count($existingMedia) > 0 ? 'active' : ''); ?>" id="toggleMedia" onclick="toggleSection('media')">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'image-02','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'image-02','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?> Gambar / Video
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($existingMedia) > 0): ?>
                        <span style="background: #6F7DA4; color: white; font-size: 11px; padding: 1px 8px; border-radius: 10px; margin-left: 4px;"><?php echo e(count($existingMedia)); ?> file</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content <?php echo e(count($existingMedia) > 0 ? 'open' : ''); ?>" id="sectionMedia">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($existingMedia) > 0): ?>
                        <label class="form-label mb-2" style="font-size: 13px; color: #6b7280;">Media yang sudah ada (klik ✕ untuk menghapus):</label>
                        <div class="media-preview-grid mb-3" id="existingMediaGrid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $existingMedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="media-preview-item" id="existing-media-<?php echo e($i); ?>">
                                    <div class="media-thumb">
                                        <span class="existing-badge">Existing</span>
                                        <button type="button" class="remove-media" onclick="removeExistingMedia(<?php echo e($i); ?>, '<?php echo e($media['url']); ?>')">✕</button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($media['type'] === 'image'): ?>
                                            <img src="<?php echo e($media['url']); ?>" alt="Media">
                                        <?php else: ?>
                                            <video src="<?php echo e($media['url']); ?>" muted></video>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name" title="<?php echo e(basename($media['url'])); ?>"><?php echo e(basename($media['url'])); ?></div>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="media-dropzone" id="mediaDropzone">
                        <input type="file" name="media_files[]" id="mediaFileInput" multiple
                            accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                        <div class="dropzone-text">Tambah gambar / video baru</div>
                        <div class="dropzone-hint">JPG, PNG, GIF, WEBP, MP4, WEBM • Maks 10MB per file</div>
                    </div>
                    <div id="mediaCounter"></div>
                    <div class="media-preview-grid" id="mediaPreviewGrid"></div>
                </div>
            </div>

            
            <div id="removeMediaInputs"></div>

            
            <div class="mb-3">
                <button type="button" class="section-toggle <?php echo e($existingLink ? 'active' : ''); ?>" id="toggleLink" onclick="toggleSection('link')">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'link-01','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'link-01','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?> Link
                    <span style="margin-left: auto; font-size: 12px; opacity: 0.6;">▼</span>
                </button>
                <div class="section-content <?php echo e($existingLink ? 'open' : ''); ?>" id="sectionLink">
                    <input type="url" name="link_url" id="inputLinkUrl" class="custom-input"
                        placeholder="https://contoh.com/artikel-menarik" value="<?php echo e(old('link_url', $existingLink)); ?>">
                </div>
            </div>

            
            <?php $poll = $thread->poll; ?>
            <div class="mb-5">
                <button type="button" class="section-toggle <?php echo e($poll ? 'active' : ''); ?>" id="togglePoll" onclick="togglePollSection()">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'bar-chart-11','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bar-chart-11','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                    Poll
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poll): ?>
                        <span style="background:#6F7DA4;color:#fff;font-size:11px;padding:1px 8px;border-radius:10px;margin-left:4px;">
                            <?php echo e($poll->options->count()); ?> opsi
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span id="pollToggleChevron" style="margin-left:auto;font-size:12px;opacity:0.6;"><?php echo e($poll ? '▲' : '▼'); ?></span>
                </button>

                <div class="section-content <?php echo e($poll ? 'open' : ''); ?>" id="sectionPoll">
                    <style>
                        .poll-edit-row { display:flex;align-items:center;gap:8px;margin-bottom:8px; }
                        .poll-edit-input { flex:1;padding:9px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:500;outline:none;transition:border-color 0.2s; }
                        .poll-edit-input:focus { border-color:#293C79;box-shadow:0 0 0 3px rgba(41,60,121,0.1); }
                        .poll-edit-remove { width:30px;height:30px;border-radius:50%;border:none;background:#fee2e2;color:#dc2626;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.2s; }
                        .poll-edit-remove:hover { background:#fca5a5; }
                        .poll-edit-remove:disabled { opacity:0.35;cursor:not-allowed; }
                        .poll-votes-badge { font-size:11px;font-weight:700;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:20px;white-space:nowrap; }
                        .poll-add-btn { font-size:13px;font-weight:600;color:#293C79;background:#E7E8F0;border:1.5px dashed #a5b4fc;border-radius:10px;padding:8px 16px;cursor:pointer;width:100%;text-align:center;transition:all 0.2s;margin-top:4px; }
                        .poll-add-btn:hover { background:#e0e7ff; }
                        .poll-section-label { font-size:12px;font-weight:600;color:#6b7280;margin-bottom:6px;margin-top:14px;display:block; }
                        .poll-close-toggle { display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;background:#fff;width:100%;margin-top:10px;transition:all 0.2s; }
                        .poll-close-toggle:hover { border-color:#293C79;background:#f5f3ff; }
                    </style>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poll): ?>
                        
                        <span class="poll-section-label">Opsi Poll</span>
                        <div id="pollExistingOptions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $poll->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="poll-edit-row" id="poll-opt-row-<?php echo e($opt->id); ?>">
                                    <input type="text" name="poll_option_text[<?php echo e($opt->id); ?>]"
                                           class="poll-edit-input"
                                           value="<?php echo e(old('poll_option_text.'.$opt->id, $opt->text)); ?>"
                                           maxlength="150"
                                           placeholder="Teks opsi...">
                                    <span class="poll-votes-badge" title="Jumlah suara"><?php echo e($opt->votes_count); ?> suara</span>
                                    <button type="button" class="poll-edit-remove"
                                            onclick="markDeleteOption(<?php echo e($opt->id); ?>, this)"
                                            <?php echo e($opt->votes_count > 0 ? 'disabled title=Tidak bisa dihapus karena sudah ada suara' : ''); ?>>×</button>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <div id="pollDeleteInputs"></div>

                        
                        <div id="pollNewOptionsContainer"></div>
                        <button type="button" class="poll-add-btn" id="btnAddNewOption" onclick="addNewPollOption()">
                            + Tambah Opsi Baru
                            <span id="pollNewCount" style="color:#9ca3af;"></span>
                        </button>

                        
                        <span class="poll-section-label">Batas Waktu Poll</span>
                        <input type="datetime-local" name="poll_expires_at" class="poll-edit-input"
                               style="width:auto;"
                               value="<?php echo e(old('poll_expires_at', $poll->expires_at?->format('Y-m-d\TH:i'))); ?>"
                               min="<?php echo e(now()->addHours(1)->format('Y-m-d\TH:i')); ?>">

                        
                        <button type="button" class="poll-close-toggle" id="btnClosePoll" onclick="toggleClosePoll()">
                            <span id="closePollIcon">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($poll->isClosed()): ?>
                                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'check-circle','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'minus-circle','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'minus-circle','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span id="closePollLabel">
                                <?php echo e($poll->isClosed() ? 'Buka kembali poll' : 'Tutup poll sekarang'); ?>

                            </span>
                        </button>
                        <input type="hidden" name="poll_is_closed" id="pollIsClosedInput"
                               value="<?php echo e(old('poll_is_closed', $poll->isClosed() ? '1' : '0')); ?>">

                    <?php else: ?>
                        
                        <input type="hidden" name="has_poll" id="hasPollInput" value="0">
                        <span class="poll-section-label">Opsi Poll (min. 2, maks. 6)</span>
                        <div id="pollOptionsContainer">
                            <div class="poll-edit-row">
                                <input type="text" name="poll_options[]" class="poll-edit-input" placeholder="Opsi 1" maxlength="150">
                                <button type="button" class="poll-edit-remove" onclick="removePollOption(this)" style="visibility:hidden;">×</button>
                            </div>
                            <div class="poll-edit-row">
                                <input type="text" name="poll_options[]" class="poll-edit-input" placeholder="Opsi 2" maxlength="150">
                                <button type="button" class="poll-edit-remove" onclick="removePollOption(this)" style="visibility:hidden;">×</button>
                            </div>
                        </div>
                        <button type="button" class="poll-add-btn" id="btnAddPollOption" onclick="addPollOption()">
                            + Tambah Opsi <span id="pollOptionCount" style="color:#9ca3af;">(2/6)</span>
                        </button>
                        <span class="poll-section-label">Batas Waktu (opsional)</span>
                        <input type="datetime-local" name="poll_expires_at" class="poll-edit-input"
                               style="width:auto;" min="<?php echo e(now()->addHours(1)->format('Y-m-d\TH:i')); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
                <a href="<?php echo e(route('manajemenmahasiswa.forum.show', $thread->id)); ?>"
                    class="btn-action btn-cancel text-decoration-none shadow-sm">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'minus-circle','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'minus-circle','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?> Batal
                </a>
                <button type="submit" class="btn-action btn-post shadow-sm px-4">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'download-01','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'download-01','size' => '16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // ---- Toggle Sections ----
            function toggleSection(section) {
                const content = document.getElementById(`section${section.charAt(0).toUpperCase() + section.slice(1)}`);
                const toggle = document.getElementById(`toggle${section.charAt(0).toUpperCase() + section.slice(1)}`);
                content.classList.toggle('open');
                toggle.classList.toggle('active');
            }

            // ---- Poll Section Toggle ----
            let pollOpen = <?php echo e($poll ? 'true' : 'false'); ?>;
            function togglePollSection() {
                pollOpen = !pollOpen;
                const content  = document.getElementById('sectionPoll');
                const chevron  = document.getElementById('pollToggleChevron');
                const btn      = document.getElementById('togglePoll');
                content.classList.toggle('open', pollOpen);
                btn.classList.toggle('active', pollOpen);
                chevron.textContent = pollOpen ? '▲' : '▼';
                // Aktifkan has_poll jika bukan edit poll yang sudah ada
                const hasPoll = document.getElementById('hasPollInput');
                if (hasPoll) hasPoll.value = pollOpen ? '1' : '0';
            }

            <?php if($poll): ?>
            // ---- Edit existing poll ----
            const deletedOptions = new Set();

            function markDeleteOption(optId, btn) {
                const row = document.getElementById('poll-opt-row-' + optId);
                if (deletedOptions.has(optId)) {
                    // Undo delete
                    deletedOptions.delete(optId);
                    row.style.opacity = '1';
                    row.querySelector('input').disabled = false;
                    btn.title = '';
                    btn.textContent = '×';
                    const existing = document.getElementById('del-input-' + optId);
                    if (existing) existing.remove();
                } else {
                    deletedOptions.add(optId);
                    row.style.opacity = '0.4';
                    row.querySelector('input').disabled = true;
                    btn.title = 'Klik lagi untuk batal';
                    btn.textContent = '↩';
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'poll_delete_options[]';
                    inp.value = optId;
                    inp.id = 'del-input-' + optId;
                    document.getElementById('pollDeleteInputs').appendChild(inp);
                }
            }

            let newOptionCount = 0;
            const MAX_NEW = 4;
            function addNewPollOption() {
                const existingCount = document.querySelectorAll('#pollExistingOptions .poll-edit-row').length - deletedOptions.size;
                const total = existingCount + newOptionCount;
                if (total >= 6) return;

                newOptionCount++;
                const container = document.getElementById('pollNewOptionsContainer');
                const div = document.createElement('div');
                div.className = 'poll-edit-row';
                div.innerHTML = `
                    <input type="text" name="poll_new_options[]" class="poll-edit-input"
                           placeholder="Opsi baru ${newOptionCount}" maxlength="150">
                    <button type="button" class="poll-edit-remove" onclick="this.closest('.poll-edit-row').remove(); newOptionCount--; updateNewCount();">×</button>`;
                container.appendChild(div);
                updateNewCount();
                div.querySelector('input').focus();
            }
            function updateNewCount() {
                const el = document.getElementById('pollNewCount');
                if (el) el.textContent = newOptionCount > 0 ? `(+${newOptionCount})` : '';
            }

            // ---- Close/reopen poll toggle ----
            let isClosed = <?php echo e($poll->isClosed() ? 'true' : 'false'); ?>;
            function toggleClosePoll() {
                isClosed = !isClosed;
                document.getElementById('pollIsClosedInput').value = isClosed ? '1' : '0';
                document.getElementById('closePollLabel').textContent = isClosed ? 'Buka kembali poll' : 'Tutup poll sekarang';
                document.getElementById('closePollIcon').innerHTML = isClosed
                    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 6.48 2 12s4.48 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>`
                    : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
            }

            <?php else: ?>
            // ---- Create new poll (thread belum punya poll) ----
            const MAX_OPTS = 6;
            function addPollOption() {
                const container = document.getElementById('pollOptionsContainer');
                const count = container.querySelectorAll('.poll-edit-row').length;
                if (count >= MAX_OPTS) return;
                const row = document.createElement('div');
                row.className = 'poll-edit-row';
                row.innerHTML = `
                    <input type="text" name="poll_options[]" class="poll-edit-input"
                           placeholder="Opsi ${count + 1}" maxlength="150">
                    <button type="button" class="poll-edit-remove" onclick="removePollOption(this)">×</button>`;
                container.appendChild(row);
                updatePollCount();
                row.querySelector('input').focus();
            }
            function removePollOption(btn) {
                const rows = document.querySelectorAll('#pollOptionsContainer .poll-edit-row');
                if (rows.length <= 2) return;
                btn.closest('.poll-edit-row').remove();
                document.querySelectorAll('#pollOptionsContainer .poll-edit-input').forEach((inp, i) => {
                    if (!inp.value) inp.placeholder = `Opsi ${i + 1}`;
                });
                updatePollCount();
            }
            function updatePollCount() {
                const count = document.querySelectorAll('#pollOptionsContainer .poll-edit-row').length;
                const el = document.getElementById('pollOptionCount');
                if (el) el.textContent = `(${count}/${MAX_OPTS})`;
                document.querySelectorAll('#pollOptionsContainer .poll-edit-remove').forEach((btn, _, all) => {
                    btn.style.visibility = all.length > 2 ? 'visible' : 'hidden';
                });
            }
            <?php endif; ?>

            // ---- Character Counter ----
            const judulInput = document.getElementById('inputJudul');
            const judulCount = document.getElementById('judulCount');
            judulCount.textContent = judulInput.value.length;
            judulInput.addEventListener('input', function() {
                judulCount.textContent = this.value.length;
            });

            // ---- Existing Media Removal ----
            let removedMediaUrls = [];
            let existingKeptCount = <?php echo e(count($existingMedia)); ?>;

            function removeExistingMedia(index, url) {
                document.getElementById(`existing-media-${index}`).remove();
                removedMediaUrls.push(url);
                existingKeptCount--;

                // Add hidden input
                const container = document.getElementById('removeMediaInputs');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_media[]';
                input.value = url;
                container.appendChild(input);

                updateCounter();
            }

            // ---- New Media Upload ----
            const mediaFileInput = document.getElementById('mediaFileInput');
            const mediaDropzone = document.getElementById('mediaDropzone');
            const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
            const mediaCounter = document.getElementById('mediaCounter');
            let selectedFiles = [];
            const MAX_FILES = 5;
            const MAX_SIZE = 10 * 1024 * 1024;

            mediaDropzone.addEventListener('dragover', (e) => { e.preventDefault(); mediaDropzone.classList.add('dragover'); });
            mediaDropzone.addEventListener('dragleave', () => mediaDropzone.classList.remove('dragover'));
            mediaDropzone.addEventListener('drop', () => mediaDropzone.classList.remove('dragover'));

            mediaFileInput.addEventListener('change', function() {
                addMediaFiles(this.files);
            });

            function addMediaFiles(fileList) {
                const totalAllowed = MAX_FILES - existingKeptCount;
                for (const file of fileList) {
                    if (selectedFiles.length >= totalAllowed) {
                        alert(`Maksimal ${MAX_FILES} file total (${existingKeptCount} existing + ${totalAllowed} baru).`);
                        break;
                    }
                    if (file.size > MAX_SIZE) {
                        alert(`File "${file.name}" terlalu besar. Maksimal 10MB per file.`);
                        continue;
                    }
                    if (!file.type.match(/^(image|video)\//)) {
                        alert(`File "${file.name}" bukan gambar/video yang didukung.`);
                        continue;
                    }
                    selectedFiles.push(file);
                }
                syncFileInput();
                renderPreviews();
                updateCounter();
            }

            function removeMediaFile(index) {
                selectedFiles.splice(index, 1);
                syncFileInput();
                renderPreviews();
                updateCounter();
            }

            function syncFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                mediaFileInput.files = dt.files;
            }

            function updateCounter() {
                const total = existingKeptCount + selectedFiles.length;
                if (total === 0) {
                    mediaCounter.innerHTML = '';
                    return;
                }
                let cls = 'ok';
                if (total >= 4) cls = 'warn';
                if (total >= MAX_FILES) cls = 'full';
                mediaCounter.innerHTML = `<span class="media-counter ${cls}">${total}/${MAX_FILES} file (${existingKeptCount} existing + ${selectedFiles.length} baru)</span>`;
            }

            function renderPreviews() {
                mediaPreviewGrid.innerHTML = '';
                selectedFiles.forEach((file, idx) => {
                    const item = document.createElement('div');
                    item.className = 'media-preview-item';

                    const thumb = document.createElement('div');
                    thumb.className = 'media-thumb';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-media';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = () => removeMediaFile(idx);
                    thumb.appendChild(removeBtn);

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.onload = () => URL.revokeObjectURL(img.src);
                        thumb.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.muted = true;
                        video.onloadeddata = () => { video.currentTime = 1; };
                        thumb.appendChild(video);
                    }

                    item.appendChild(thumb);

                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'file-info';

                    const nameLabel = document.createElement('div');
                    nameLabel.className = 'file-name';
                    nameLabel.textContent = file.name;
                    nameLabel.title = file.name;

                    const sizeLabel = document.createElement('div');
                    sizeLabel.className = 'file-size';
                    const kb = file.size / 1024;
                    sizeLabel.textContent = kb >= 1024
                        ? (kb / 1024).toFixed(1) + ' MB'
                        : kb.toFixed(1) + ' KB';

                    fileInfo.appendChild(nameLabel);
                    fileInfo.appendChild(sizeLabel);
                    item.appendChild(fileInfo);

                    mediaPreviewGrid.appendChild(item);
                });
            }

            // Init counter
            updateCounter();
            // ---- Prevent Double Submit ----
            const form = document.getElementById('editPostForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Loading...';
            });
        </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal28fe751225ca81f38990401ea46d9b64)): ?>
<?php $attributes = $__attributesOriginal28fe751225ca81f38990401ea46d9b64; ?>
<?php unset($__attributesOriginal28fe751225ca81f38990401ea46d9b64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal28fe751225ca81f38990401ea46d9b64)): ?>
<?php $component = $__componentOriginal28fe751225ca81f38990401ea46d9b64; ?>
<?php unset($__componentOriginal28fe751225ca81f38990401ea46d9b64); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\forum\edit.blade.php ENDPATH**/ ?>