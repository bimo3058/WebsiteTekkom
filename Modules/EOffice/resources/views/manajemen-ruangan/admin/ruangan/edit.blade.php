<x-eoffice::manajemen-ruangan.layout pageTitle="Edit Ruangan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Edit Ruangan: {{ $ruangan->nama }}</h1>
            <p class="mp-page-sub">Ubah spesifikasi ruangan atau ubah status aktif/non-aktifnya.</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" class="mp-btn secondary md">Batal</a>
        </div>
    </div>

    {{-- DEBUG: action URL = {{ route('eoffice.peminjaman.admin.ruangan.update', $ruangan->id) }} --}}
    <div class="mp-card" style="margin-top: 20px; max-width: 800px;">
        <form method="POST" id="editForm" action="{{ route('eoffice.peminjaman.admin.ruangan.update', $ruangan->id) }}"
            enctype="multipart/form-data"
            onsubmit="console.log('FORM SUBMIT FIRED - action:', this.action); return true;">
            @csrf
            @method('PUT')
            <div class="mp-card-body" style="display:flex; flex-direction:column; gap:20px; padding: 24px;">

                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Nama Ruangan
                            <span style="color:red">*</span></label>
                        <input type="text" name="nama" class="mp-input" value="{{ old('nama', $ruangan->nama) }}"
                            required>
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
                                selected: '{{ old('kategori', $ruangan->kategori ?? '') }}',
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
                        <input type="text" name="lokasi" class="mp-input" value="{{ old('lokasi', $ruangan->lokasi) }}"
                            required>
                        @error('lokasi')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="width: 120px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Lantai</label>
                        <input type="number" name="lantai" class="mp-input"
                            value="{{ old('lantai', $ruangan->lantai) }}">
                        @error('lantai')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="width: 150px;">
                        <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Kapasitas
                            <span style="color:red">*</span></label>
                        <input type="number" name="kapasitas" class="mp-input" min="1"
                            value="{{ old('kapasitas', $ruangan->kapasitas) }}" required>
                        @error('kapasitas')
                            <div style="color:red; font-size:11px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:10px;">Fasilitas
                        Tersedia</label>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        @php
                            $opsiFasilitas = \Modules\EOffice\Models\Fasilitas::orderBy('nama_fasilitas')->pluck('nama_fasilitas')->toArray();
                            $currentFasilitas = is_array($ruangan->fasilitas) ? $ruangan->fasilitas : [];
                        @endphp
                        @foreach($opsiFasilitas as $opsi)
                            <label
                                style="display:flex; align-items:center; gap:8px; font-size:13px; color:#353849; cursor:pointer;">
                                <input type="checkbox" name="fasilitas[]" value="{{ $opsi }}" {{ in_array($opsi, $currentFasilitas) ? 'checked' : '' }}>
                                {{ $opsi }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top: 5px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Foto Ruangan
                        Terkini (Maks. 2MB per foto)</label>

                    <input type="file" name="fotos[]" multiple accept="image/png, image/jpeg, image/jpg"
                        class="mp-input cursor-pointer" style="padding: 6px;" id="fotoInput" onchange="previewImages(event)">
                    <p style="font-size:11px; color:#A0A4B8; margin-top:4px; margin-bottom:12px;">Pilih banyak foto
                        sekaligus. Format: JPG,
                        PNG.</p>

                    <div id="galleryLabelContainer" style="margin-bottom: 8px;">
                        <span style="font-size:11px; color:#353849; font-weight:600;">Daftar Foto (Tarik / Drag untuk
                            mengurutkan. Foto pertama adalah Cover UI Mahasiswa):</span>
                    </div>

                    @if($ruangan->fotos && $ruangan->fotos->count() > 0)
                        <div id="gallery-sortable" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                            @foreach($ruangan->fotos as $foto)
                                <div class="gallery-item" data-id="{{ $foto->id }}" x-data="{ showDeleteModal: false }"
                                    style="position: relative; cursor: grab; width: 120px; height: 90px; border-radius: 6px; overflow: hidden; border: 2px solid #DFE1E7;">
                                    <img src="{{ $foto->foto_url ?? app(\App\Services\SupabaseStorage::class)->getPublicUrl($foto->path_foto, 'eoffice') }}"
                                        style="width: 100%; height: 100%; object-fit: cover;" draggable="false">
                                    <button type="button" @click="showDeleteModal = true"
                                        style="position: absolute; top: 4px; right: 4px; background: rgba(223, 28, 65, 0.9); color: white; border: none; border-radius: 4px; width: 22px; height: 22px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;">&times;</button>
                                    @if($loop->first)
                                        <div class="cover-badge"
                                            style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(11, 38, 110, 0.8); color: white; font-size: 9px; text-align: center; padding: 2px;">
                                            COVER</div>
                                    @endif

                                    <!-- DELETE MODAL -->
                                    <div x-show="showDeleteModal" style="display: none;"
                                        class="fixed inset-0 z-[100] overflow-y-auto text-left whitespace-normal cursor-default"
                                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showDeleteModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md"
                                                aria-hidden="true" @click="showDeleteModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                                aria-hidden="true">&#8203;</span>

                                            <div x-show="showDeleteModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-center align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6 relative">
                                                
                                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4">
                                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-bold text-slate-800 mb-2">Hapus foto ini?</h3>
                                                <p class="text-xs text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin ingin menghapus foto ruangan ini? Tindakan ini tidak dapat dikembalikan.</p>
                                                
                                                <div class="flex justify-center gap-3">
                                                    <button type="button" @click="showDeleteModal = false" class="mp-btn secondary px-4 py-2">Batal</button>
                                                    <form method="POST" action="{{ route('eoffice.peminjaman.admin.ruangan.index') }}/foto/{{ $foto->id }}" style="margin:0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="mp-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white border-transparent" style="border:none;">Hapus Foto</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="foto_order" id="foto_order" value="">
                    @endif

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
                        <input type="checkbox" name="is_active" value="1" {{ $ruangan->is_active ? 'checked' : '' }}>
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



        let accumulatedFiles = new DataTransfer();
        let sortableClientInstance = null;

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
                    if (file.size > 2 * 1024 * 1024) {
                        showCustomToast('Ukuran foto "' + file.name + '" terlalu besar (Maks. 2MB). Foto diabaikan.');
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
</x-eoffice::manajemen-ruangan.layout>