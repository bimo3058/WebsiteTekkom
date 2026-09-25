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
                        @error('nama')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Kategori Ruangan
                            <span style="color:red">*</span></label>
                        <div x-data="{ 
                                open: false, 
                                selected: '{{ old('kategori') }}',
                                options: ['Kelas', 'Laboratorium', 'Sidang'],
                                get selectedLabel() {
                                    return this.selected ? this.selected : 'Pilih Kategori';
                                }
                            }" 
                            class="relative" 
                            @click.away="open = false">
                            
                            <!-- Visually hidden input for browser validation -->
                            <input type="text" name="kategori" x-model="selected" class="absolute w-0 h-0 opacity-0 pointer-events-none" required tabindex="-1">
                        
                            <button type="button" @click="open = !open" 
                                class="mp-input w-full flex justify-between items-center text-left"
                                style="background: white; cursor: pointer;">
                                <span x-text="selectedLabel" :style="selected ? 'color: #0D0D12' : 'color: #72778F'"></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    style="transition: transform 0.2s;" :style="open ? 'transform: rotate(180deg)' : ''">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                        
                            <div x-show="open" 
                                x-transition.opacity.duration.200ms
                                class="absolute z-[100] w-full mt-1 bg-white border rounded-md shadow-lg overflow-hidden"
                                style="display: none; border-color: #DFE1E7;">
                                <ul class="py-1 text-sm m-0 p-0" style="list-style: none;">
                                    <template x-for="option in options" :key="option">
                                        <li>
                                            <button type="button" @click="selected = option; open = false"
                                                class="w-full px-4 py-2.5 text-left hover:bg-slate-50 focus:outline-none flex items-center justify-between transition-colors"
                                                :class="selected === option ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700'">
                                                <span x-text="option"></span>
                                                <svg x-show="selected === option" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @error('kategori')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lokasi /
                            Gedung <span style="color:red">*</span></label>
                        <input type="text" name="lokasi" class="mp-input" placeholder="Misal: Gedung A"
                            value="{{ old('lokasi') }}" required>
                        @error('lokasi')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
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
                            $opsiFasilitas = \Modules\EOffice\Models\Fasilitas::orderBy('nama_fasilitas')->pluck('nama_fasilitas')->toArray();
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
                        Terkini (Maks. 1MB per foto)</label>

                    <input type="file" name="fotos[]" multiple accept="image/png, image/jpeg, image/jpg"
                        class="mp-input cursor-pointer" style="padding: 6px;" id="fotoInput" onchange="previewImages(event)">
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

                    @error('fotos')
                        <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                    @error('fotos.*')
                        <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
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

        function showCustomToast(message) {
            const oldToast = document.getElementById('client-toast');
            if (oldToast) oldToast.remove();

            const toast = document.createElement('div');
            toast.id = 'client-toast';
            toast.className = 'mp-flash mp-flash-error';
            toast.style.position = 'fixed';
            toast.style.top = '24px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.zIndex = '99999';
            toast.style.justifyContent = 'space-between';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            toast.style.minWidth = '320px';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 300ms ease, top 300ms ease';

            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>${message}</span>
                </div>
                <button type="button" onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(), 300)" style="background:transparent; border:none; cursor:pointer; color:inherit; padding:0; display:flex; align-items:center; opacity:0.7;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            `;

            document.body.appendChild(toast);
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.top = '32px';
            });
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    toast.style.opacity = '0';
                    toast.style.top = '24px';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        function previewImages(event) {
            var files = event.target.files;
            if (files && files.length > 0) {
                Array.from(files).forEach(function (file) {
                    if (file.size > 1024 * 1024) {
                        showCustomToast('Ukuran foto "' + file.name + '" terlalu besar (Maks. 1MB). Foto diabaikan.');
                    } else if (!file.type.match('image.*')) {
                        showCustomToast('Format file "' + file.name + '" tidak didukung. Foto diabaikan.');
                    } else {
                        accumulatedFiles.items.add(file);
                    }
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