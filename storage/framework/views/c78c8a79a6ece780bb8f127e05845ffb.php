<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Edit Ruangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Edit Ruangan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Edit Ruangan: <?php echo e($ruangan->nama); ?></h1>
            <p class="mp-page-sub">Ubah spesifikasi ruangan atau ubah status aktif/non-aktifnya.</p>
        </div>
        <div class="mp-page-actions">
            <a href="<?php echo e(route('eoffice.peminjaman.admin.ruangan.index')); ?>" class="mp-btn secondary md">Batal</a>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 20px; max-width: 800px;">
        <form method="POST" id="editForm" action="<?php echo e(route('eoffice.peminjaman.admin.ruangan.update', $ruangan->id)); ?>"
            enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="mp-card-body" style="display:flex; flex-direction:column; gap:20px; padding: 24px;">

                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Nama Ruangan
                            <span style="color:red">*</span></label>
                        <input type="text" name="nama" class="mp-input" value="<?php echo e(old('nama', $ruangan->nama)); ?>"
                            required>
                    </div>
                </div>

                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lokasi /
                            Gedung <span style="color:red">*</span></label>
                        <input type="text" name="lokasi" class="mp-input" value="<?php echo e(old('lokasi', $ruangan->lokasi)); ?>"
                            required>
                    </div>
                    <div style="width: 120px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lantai</label>
                        <input type="number" name="lantai" class="mp-input"
                            value="<?php echo e(old('lantai', $ruangan->lantai)); ?>">
                    </div>
                    <div style="width: 150px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Kapasitas
                            <span style="color:red">*</span></label>
                        <input type="number" name="kapasitas" class="mp-input" min="1"
                            value="<?php echo e(old('kapasitas', $ruangan->kapasitas)); ?>" required>
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:10px;">Fasilitas
                        Tersedia</label>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        <?php
                            $opsiFasilitas = ['AC', 'Proyektor', 'Papan Tulis / Whiteboard', 'Sound System', 'Koneksi WiFi', 'PC / Komputer Desktop', 'Kursi Rapat', 'Stop Kontak Ekstra'];
                            $currentFasilitas = is_array($ruangan->fasilitas) ? $ruangan->fasilitas : [];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $opsiFasilitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opsi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <label
                                style="display:flex; align-items:center; gap:8px; font-size:13px; color:#353849; cursor:pointer;">
                                <input type="checkbox" name="fasilitas[]" value="<?php echo e($opsi); ?>" <?php echo e(in_array($opsi, $currentFasilitas) ? 'checked' : ''); ?>>
                                <?php echo e($opsi); ?>

                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 5px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Foto Ruangan
                        Terkini (Maks. 5MB per foto)</label>

                    <input type="file" name="fotos[]" multiple accept="image/png, image/jpeg, image/jpg"
                        class="mp-input" style="padding: 6px;" id="fotoInput" onchange="previewImages(event)">
                    <p style="font-size:11px; color:#A0A4B8; margin-top:4px; margin-bottom:12px;">Pilih banyak foto
                        sekaligus. Format: JPG,
                        PNG.</p>

                    <div id="galleryLabelContainer" style="margin-bottom: 8px;">
                        <span style="font-size:11px; color:#353849; font-weight:600;">Daftar Foto (Tarik / Drag untuk
                            mengurutkan. Foto pertama adalah Cover UI Mahasiswa):</span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ruangan->fotos && $ruangan->fotos->count() > 0): ?>
                        <div id="gallery-sortable" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangan->fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="gallery-item" data-id="<?php echo e($foto->id); ?>"
                                    style="position: relative; cursor: grab; width: 120px; height: 90px; border-radius: 6px; overflow: hidden; border: 2px solid #DFE1E7;">
                                    <img src="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($foto->path_foto)); ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;" draggable="false">
                                    <button type="button" onclick="deleteFoto(<?php echo e($foto->id); ?>)"
                                        style="position: absolute; top: 4px; right: 4px; background: rgba(223, 28, 65, 0.9); color: white; border: none; border-radius: 4px; width: 22px; height: 22px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;">&times;</button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loop->first): ?>
                                        <div class="cover-badge"
                                            style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(11, 38, 110, 0.8); color: white; font-size: 9px; text-align: center; padding: 2px;">
                                            COVER</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <input type="hidden" name="foto_order" id="foto_order" value="">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Container for client-side preview -->
                    <div id="previewContainer" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fotos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:red; font-size:11px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fotos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:red; font-size:11px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div style="border-top:1px solid #DFE1E7; padding-top:16px;">
                    <label
                        style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#0D0D12; cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" <?php echo e($ruangan->is_active ? 'checked' : ''); ?>>
                        Tandai Ruangan ini Aktif & Bisa Dipinjam
                    </label>
                </div>

            </div>

            <div
                style="padding:16px 24px; border-top:1px solid #F0F1F4; background:#FAFBFC; display:flex; justify-content:flex-end;">
                <button type="submit" class="mp-btn primary md">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Hidden form for deleting photo without submitting the main form -->
    <form id="deleteFotoForm" method="POST" style="display:none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>

    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('gallery-sortable');
            if (el) {
                var sortable = Sortable.create(el, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        updateOrder();
                        updateCoverBadge();
                    }
                });
                updateOrder();
            }
        });

        function updateOrder() {
            var order = [];
            var items = document.querySelectorAll('.gallery-item');
            items.forEach(function (item) {
                order.push(item.getAttribute('data-id'));
            });
            document.getElementById('foto_order').value = JSON.stringify(order);
        }

        function updateCoverBadge() {
            document.querySelectorAll('.cover-badge').forEach(e => e.remove());
            var firstItem = document.querySelector('.gallery-item');
            if (firstItem) {
                var badge = document.createElement('div');
                badge.className = 'cover-badge';
                badge.style = "position: absolute; bottom: 0; left: 0; right: 0; background: rgba(11, 38, 110, 0.8); color: white; font-size: 9px; text-align: center; padding: 2px;";
                badge.innerText = "COVER";
                firstItem.appendChild(badge);
            }
        }

        function deleteFoto(id) {
            if (confirm('Yakin ingin menghapus foto ini?')) {
                var form = document.getElementById('deleteFotoForm');
                form.action = "<?php echo e(route('eoffice.peminjaman.admin.ruangan.index')); ?>/foto/" + id;
                form.submit();
            }
        }

        let accumulatedFiles = new DataTransfer();
        let sortableClientInstance = null;

        function previewImages(event) {
            var files = event.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach(function (file) {
                    accumulatedFiles.items.add(file);
                });
                document.getElementById('fotoInput').files = accumulatedFiles.files;
            }
            renderPreviews();
        }

        function renderPreviews() {
            var container = document.getElementById('previewContainer');
            container.innerHTML = '';

            Array.from(accumulatedFiles.files).forEach(function (file, index) {
                var div = document.createElement('div');
                div.className = 'client-gallery-item';
                div.setAttribute('data-index', index);
                div.style.width = '120px';
                div.style.height = '90px';
                div.style.borderRadius = '6px';
                div.style.overflow = 'hidden';
                div.style.border = '2px solid #DFE1E7';
                div.style.position = 'relative';
                div.style.cursor = 'grab';

                var img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.draggable = false;

                var reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                }
                reader.readAsDataURL(file);

                var removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.type = 'button';
                removeBtn.style = 'position: absolute; top: 4px; right: 4px; background: rgba(223, 28, 65, 0.9); color: white; border: none; border-radius: 4px; width: 22px; height: 22px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; z-index: 20;';
                removeBtn.onclick = function () { removeFile(div.getAttribute('data-index')); };

                div.appendChild(img);
                div.appendChild(removeBtn);
                container.appendChild(div);
            });

            updateCoverBadgeClient();

            if (sortableClientInstance) {
                sortableClientInstance.destroy();
            }

            if (typeof Sortable !== 'undefined') {
                sortableClientInstance = Sortable.create(container, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function () {
                        syncOrder();
                    }
                });
            }
        }

        function syncOrder() {
            var newDataTransfer = new DataTransfer();
            var items = document.querySelectorAll('.client-gallery-item');
            var oldFilesArray = Array.from(accumulatedFiles.files);

            items.forEach(function (item, newIndex) {
                var oldIndex = parseInt(item.getAttribute('data-index'));
                newDataTransfer.items.add(oldFilesArray[oldIndex]);
                item.setAttribute('data-index', newIndex);
            });

            accumulatedFiles = newDataTransfer;
            document.getElementById('fotoInput').files = accumulatedFiles.files;
            updateCoverBadgeClient();
        }

        function removeFile(indexToRemove) {
            var parsedIndex = parseInt(indexToRemove);
            var newDataTransfer = new DataTransfer();
            Array.from(accumulatedFiles.files).forEach(function (file, index) {
                if (index !== parsedIndex) {
                    newDataTransfer.items.add(file);
                }
            });
            accumulatedFiles = newDataTransfer;
            document.getElementById('fotoInput').files = accumulatedFiles.files;
            renderPreviews();
        }

        function updateCoverBadgeClient() {
            document.querySelectorAll('.client-cover-badge').forEach(function (e) { e.remove(); });
            var firstItem = document.querySelector('.client-gallery-item');
            // Hanya tempel COVER untuk preview client jika belum ada foto cover dari DB/Server di edit.blade.php
            var serverCover = document.querySelector('.cover-badge');
            if (firstItem && !serverCover) {
                var badge = document.createElement('div');
                badge.className = 'client-cover-badge';
                badge.style = "position: absolute; bottom: 0; left: 0; right: 0; background: rgba(11, 38, 110, 0.8); color: white; font-size: 9px; text-align: center; padding: 2px; z-index: 10;";
                badge.innerText = "COVER";
                firstItem.appendChild(badge);
            }
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\ruangan\edit.blade.php ENDPATH**/ ?>