<x-eoffice::manajemen-ruangan.layout pageTitle="Tambah Ruangan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Tambah Ruangan</h1>
            <p class="mp-page-sub">Isi detail ruangan fisik berikut lalu simpan untuk menambah database.</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" class="mp-btn secondary md">Batal</a>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 20px; max-width: 800px;">
        <form method="POST" action="{{ route('eoffice.peminjaman.admin.ruangan.store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="mp-card-body" style="display:flex; flex-direction:column; gap:20px; padding: 24px;">

                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Nama Ruangan
                            <span style="color:red">*</span></label>
                        <input type="text" name="nama" class="mp-input" placeholder="Misal: A201"
                            value="{{ old('nama') }}" required>
                        @error('nama') <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lokasi /
                            Gedung <span style="color:red">*</span></label>
                        <input type="text" name="lokasi" class="mp-input" placeholder="Misal: Gedung A"
                            value="{{ old('lokasi') }}" required>
                        @error('lokasi') <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="width: 120px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lantai</label>
                        <input type="number" name="lantai" class="mp-input" placeholder="Misal: 2"
                            value="{{ old('lantai') }}">
                    </div>
                    <div style="width: 150px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Kapasitas
                            (Orang) <span style="color:red">*</span></label>
                        <input type="number" name="kapasitas" class="mp-input" min="1" value="{{ old('kapasitas', 0) }}"
                            required>
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:10px;">Fasilitas
                        Tersedia</label>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        @php
                            $opsiFasilitas = ['AC', 'Proyektor', 'Papan Tulis / Whiteboard', 'Sound System', 'Koneksi WiFi', 'PC / Komputer Desktop', 'Kursi Rapat', 'Stop Kontak Ekstra'];
                        @endphp
                        @foreach($opsiFasilitas as $opsi)
                            <label
                                style="display:flex; align-items:center; gap:8px; font-size:13px; color:#353849; cursor:pointer;">
                                <input type="checkbox" name="fasilitas[]" value="{{ $opsi }}">
                                {{ $opsi }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top: 5px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Foto Ruangan
                        Terkini (Maks. 5MB per foto)</label>

                    <input type="file" name="fotos[]" multiple accept="image/png, image/jpeg, image/jpg"
                        class="mp-input" style="padding: 6px;" id="fotoInput" onchange="previewImages(event)">
                    <p style="font-size:11px; color:#A0A4B8; margin-top:4px; margin-bottom:12px;">Pilih banyak foto
                        sekaligus. Format: JPG,
                        PNG. Opsional, namun disarankan untuk referensi peminjam.</p>

                    <div id="galleryLabelContainer" style="margin-bottom: 8px;">
                        <span style="font-size:11px; color:#353849; font-weight:600;">Daftar Foto (Tarik / Drag untuk
                            mengurutkan. Foto pertama adalah Cover UI Mahasiswa):</span>
                    </div>

                    <!-- Container for client-side preview -->
                    <div id="previewContainer" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                    </div>

                    @error('fotos') <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                    @error('fotos.*') <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="border-top:1px solid #DFE1E7; padding-top:16px;">
                    <label
                        style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#0D0D12; cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Tandai Ruangan ini Aktif & Bisa Dipinjam
                    </label>
                </div>

            </div>

            <div
                style="padding:16px 24px; border-top:1px solid #F0F1F4; background:#FAFBFC; display:flex; justify-content:flex-end;">
                <button type="submit" class="mp-btn primary md">Simpan Ruangan</button>
            </div>
        </form>
    </div>

    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        let accumulatedFiles = new DataTransfer();
        let sortableInstance = null;

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
                var reader = new FileReader();
                reader.onload = function (e) {
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
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.draggable = false;

                    var removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.type = 'button';
                    removeBtn.style = 'position: absolute; top: 4px; right: 4px; background: rgba(223, 28, 65, 0.9); color: white; border: none; border-radius: 4px; width: 22px; height: 22px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; z-index: 20;';
                    removeBtn.onclick = function () { removeFile(div.getAttribute('data-index')); };

                    div.appendChild(img);
                    div.appendChild(removeBtn);
                    container.appendChild(div);

                    updateCoverBadgeClient();
                }
                reader.readAsDataURL(file);
            });

            if (sortableInstance) {
                sortableInstance.destroy();
            }

            if (typeof Sortable !== 'undefined') {
                sortableInstance = Sortable.create(container, {
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
            document.querySelectorAll('.client-cover-badge').forEach(e => e.remove());
            var firstItem = document.querySelector('.client-gallery-item');

            if (firstItem) {
                var badge = document.createElement('div');
                badge.className = 'client-cover-badge';
                badge.style = "position: absolute; bottom: 0; left: 0; right: 0; background: rgba(11, 38, 110, 0.8); color: white; font-size: 9px; text-align: center; padding: 2px; z-index: 10;";
                badge.innerText = "COVER";
                firstItem.appendChild(badge);
            }
        }
    </script>
</x-eoffice::manajemen-ruangan.layout>