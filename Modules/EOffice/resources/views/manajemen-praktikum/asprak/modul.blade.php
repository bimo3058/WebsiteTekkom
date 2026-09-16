@php
    $pageTitle = ($praktikum->nama ?? 'Praktikum') . ' / Modul';
@endphp
<x-eoffice::manajemen-praktikum.layout :pageTitle="$pageTitle">
    <style>
        .modul-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .modul-accordion-content.is-open {
            max-height: 2500px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
    </style>
    <div x-data="modulManager()">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mp-flash mp-flash-success flex-shrink-0 mb-[16px]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mp-flash mp-flash-error flex-shrink-0 mb-[16px]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(isset($praktikum) && $praktikum)
            <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="modul" />
        @endif

        @if($praktikumList->isEmpty())
            <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai asisten praktikum di praktikum manapun.
                Hubungi koordinator untuk aktivasi.</div>
        @else


            <div style="display:flex; flex-direction:column; gap:24px;">
                @forelse($moduls as $modul)
                    @php $isMine = $assignedModulIds->contains($modul->id); @endphp
                    <div x-data="{ modulOpen: false }"
                        style="border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background:#fff; overflow: hidden; @if(!$isMine) opacity:0.8; @endif">

                        {{-- Header Modul --}}
                        <div @click="modulOpen = !modulOpen" style="display:flex; justify-content:space-between; align-items:center; padding: 16px 24px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                            <div style="flex:1; min-width:0; display:flex; align-items: center; justify-content: space-between;">
                                <div>
                                <div style="font-size:16px; font-weight:700; color:#111827; margin:0 0 4px 0;">
                                    {{ $modul->nama }}
                                </div>
                                <div style="font-size:12px; color:#374151; font-weight:400; margin-bottom:4px;">
                                    Asisten:
                                    @forelse($modul->modulAsprak as $ma)
                                        <span>{{ $ma->asprak?->user?->name ?? '—' }}</span>@if(!$loop->last), @endif
                                    @empty
                                        <span>—</span>
                                    @endforelse
                                </div>
                                @php
                                    $firstMateri = $modul->materi->sortBy('created_at')->first();
                                    $sudahAdaInteraksi = $firstMateri || ($modul->updated_at->timestamp - $modul->created_at->timestamp > 3);
                                    // Waktu dasar = Kapan file pertama kali ada (atau fallback ke updated_at jika cm ganti deskripsi)
                                    $baseTime = $firstMateri ? $firstMateri->created_at : $modul->updated_at;
                                @endphp

                                @if($sudahAdaInteraksi)
                                    <div style="font-size:12px; color:#6B7280; font-weight:400; margin-top:4px;">
                                        {{ \Carbon\Carbon::parse($baseTime)->locale('id')->translatedFormat('d M Y, H:i') }}
                                        @if($modul->updated_at->timestamp - $baseTime->timestamp > 3)
                                            <span style="font-style:italic;">(Diedit
                                                {{ \Carbon\Carbon::parse($modul->updated_at)->locale('id')->translatedFormat('d M Y, H:i') }})</span>
                                        @endif
                                    </div>
                                @endif
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($isMine)
                                    <button type="button" data-modul-id="{{ $modul->id }}" data-modul-nama="{{ $modul->nama }}"
                                        data-modul-deskripsi="{{ $modul->deskripsi ?? '' }}"
                                        @click.stop="openEditModal($el.dataset.modulId, $el.dataset.modulNama, $el.dataset.modulDeskripsi, {{ json_encode($modul->materi->map(function ($m) {
                                return ['id' => $m->id, 'name' => $m->judul ?? basename($m->file_path ?? 'File'), 'url' => app(\App\Services\SupabaseStorage::class)->publicUrl($m->file_path, 'eoffice')]; })) }})"
                                        title="Edit Modul"
                                        style="background:#EEF2FF; border:none; padding:10px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; flex-shrink:0; margin-left:12px; width:40px; height:40px;"
                                        onmouseover="this.style.background='#E0E7FF'" onmouseout="this.style.background='#EEF2FF'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                @endif
                                <div style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; color: #666D80;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="modulOpen ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Expanded Content --}}
                        <div class="modul-accordion-content" :class="{ 'is-open': modulOpen }">
                            <div style="padding: 20px 24px 24px 24px; background: #fff; border-top: 1px solid var(--c-border);">
                                @if($modul->materi->isEmpty())
                                    <div style="font-size: 14px; color: #6B7280; font-style: italic;">Modul belum diunggah</div>
                                @else
                                    {{-- Deskripsi --}}
                                    @if($modul->deskripsi)
                                        <div style="font-size: 14px; color: #374151; margin-bottom: 20px; line-height: 1.5; text-align: left; margin-left: 0;">{!! nl2br(e(trim($modul->deskripsi))) !!}</div>
                                    @endif

                                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                        @foreach($modul->materi as $materi)
                                            <a href="{{ $materi->file_path ? app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice') : '#' }}"
                                                target="_blank"
                                                style="display:flex; flex-direction:column; width:140px; height:140px; border:1px solid #DFE1E7; border-radius:8px; overflow:hidden; text-decoration:none; background:#fff; transition:transform 0.15s, box-shadow 0.15s;"
                                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                                                {{-- Top Empty Area --}}
                                                <div style="flex:1; display:flex; align-items:center; justify-content:center; background:#FAFAFA;">
                                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                        stroke-width="1.5" stroke-linecap="round">
                                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                                        <polyline points="14 2 14 8 20 8" />
                                                    </svg>
                                                </div>

                                                {{-- Bottom Name Area --}}
                                                <div style="background:#293C79; padding:12px 14px; display:flex; align-items:center;">
                                                    <span style="color:#FFF; font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%;"
                                                        title="{{ $materi->judul ?? basename($materi->file_path ?? 'File') }}">
                                                        {{ $materi->judul ?? basename($materi->file_path ?? 'File') }}
                                                    </span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        style="min-height:180px;display:flex;align-items:center;justify-content:center;border:1px dashed #DFE1E7;border-radius:12px;">
                        <div style="padding:36px;text-align:center;">
                            <div
                                style="width:48px;height:48px;border-radius:12px;background:#FAFAFA;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border:1px solid #DFE1E7;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"
                                    stroke-linecap="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <path d="M3 9h18M9 21V9" />
                                </svg>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#111827;margin-bottom:4px;">Belum Ada Modul</div>
                            <div style="font-size:12px;color:#6B7280;">Belum ada modul yang di-assign ke Anda.</div>
                        </div>
                    </div>
                @endforelse
            </div>



            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- Modal: Edit Modul --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div x-show="editModal.open" style="display:none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
                x-cloak>

                <div class="relative bg-white rounded-xl shadow-2xl w-[600px] max-w-[90%] max-h-[90vh] overflow-y-auto"
                    style="border:1px solid #E5E7EB;" @click.away="editModal.open = false" x-show="editModal.open"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div
                        style="padding:20px 24px; border-bottom:1px solid #E5E7EB; display:flex; justify-content:space-between; align-items:center;">
                        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Edit Modul</h2>
                        <button type="button" @click="editModal.open = false"
                            style="background:none; border:none; cursor:pointer; color:#6B7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>

                    <div style="padding:24px;">
                        <form id="formEditModul" action="#" method="POST" enctype="multipart/form-data"
                            style="display:flex; flex-direction:column; gap:16px;"
                            x-on:submit="$el.action = `/eoffice/manprak/asprak/modul/${editModal.id}`">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="praktikum_id" :value="'{{ $asprak?->praktikum_id ?? '' }}'">

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Nama Modul <span style="color:#DF1C41;">*</span>
                                </label>
                                <select name="nama_display" disabled
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; background:#F9FAFB; appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22><polyline points=%226 9 12 15 18 9%22/></svg>'); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; cursor:not-allowed;">
                                    @foreach($moduls as $modul)
                                        @if($assignedModulIds->contains($modul->id))
                                            <option value="{{ $modul->id }}">{{ $modul->nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="hidden" name="nama" x-model="editModal.nama">
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Deskripsi <span style="font-weight:400; color:#9CA3AF;">(Opsional, maks. 500
                                        karakter)</span>
                                </label>
                                <textarea name="deskripsi" rows="3" maxlength="500" placeholder="Deskripsi singkat..."
                                    x-model="editModal.deskripsi"
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; resize:vertical; min-height:80px; max-height:120px; box-sizing:border-box;"></textarea>
                            </div>

                            {{-- Lampiran Berkas --}}
                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Berkas Lampiran <span style="color:#DF1C41;">*</span>
                                </label>

                                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                                    <button type="button" @click="$refs.fileInputEdit.click()"
                                        style="width: 100%; border: 1px dashed var(--c-border); border-radius: 8px; padding: 14px; background: #fff; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; font-weight: 500; color: #293C79; cursor: pointer; transition: background 0.15s;"
                                        onmouseover="this.style.background='#F6F8FA'"
                                        onmouseout="this.style.background='#fff'">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Tambah Berkas
                                    </button>
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#9CA3AF;">
                                        <span x-text="'Batas akumulasi maksimal lampiran adalah 3 file per tugas.'"></span>
                                        <span>Maks. 5MB per file</span>
                                    </div>
                                </div>
                                <input type="file" x-ref="fileInputEdit" style="display:none" multiple accept="*/*"
                                    @change="addFiles($event, 'edit')">
                                <input type="file" id="hidden-lampiran-edit" name="lampiran[]" multiple
                                    style="display:none">

                                {{-- Files to upload (New) --}}
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <template x-for="(file, index) in editFiles" :key="index">
                                        <div
                                            style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border: 1px solid var(--c-border); border-radius: 6px; background: #F9FAFB; margin-bottom: 5px;">
                                            <div
                                                style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #111827;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z">
                                                    </path>
                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                </svg>
                                                <span x-text="file.name"
                                                    style="max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index, 'edit')" title="Hapus"
                                                style="color:#DF1C41; background:none; border:none; cursor:pointer; outline:none;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Existing Files Preview --}}
                                <template x-if="editModal.existingFiles.filter(f => !f.deleted).length > 0">
                                    <div style="margin-top:10px;">
                                        <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                            Berkas Saat Ini:</div>
                                        <div style="display:flex;flex-direction:column;gap:6px;">
                                            <template x-for="(file, index) in editModal.existingFiles" :key="index">
                                                <div x-show="!file.deleted">
                                                    <div
                                                        style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border: 1px solid var(--c-border); border-radius: 6px; background: #F9FAFB; margin-bottom: 5px;">
                                                        <div
                                                            style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #111827;">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path
                                                                    d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z">
                                                                </path>
                                                                <polyline points="13 2 13 9 20 9"></polyline>
                                                            </svg>
                                                            <a :href="file.url" target="_blank"
                                                                style="font-size:13px;color:#293C79;text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0;"
                                                                x-text="file.name"></a>
                                                        </div>
                                                        <button type="button" @click="removeExistingFile(index)"
                                                            title="Hapus Berkas"
                                                            style="color:#DF1C41; background:none; border:none; cursor:pointer; outline:none;">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round">
                                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                                <line x1="6" y1="6" x2="18" y2="18" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-for="(file, index) in editModal.existingFiles" :key="'del-'+index">
                                    <input type="hidden" name="deleted_files[]" :value="file.id" :disabled="!file.deleted">
                                </template>
                            </div>

                            <div style="display:flex;justify-content:flex-end; gap:12px; margin-top:8px;">
                                <button type="submit"
                                    style="padding:10px 20px; font-size:14px; font-weight:600; color:white; background:#293C79; border:none; border-radius:8px; cursor:pointer;"
                                    onmouseover="this.style.background='#1F2D59'"
                                    onmouseout="this.style.background='#293C79'">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endif {{-- end if praktikumList not empty --}}
    </div>

    <script>
        function modulManager() {
            return {
                createModal: { open: false, modulId: '' },
                editModal: { open: false, id: null, nama: '', deskripsi: '', existingFiles: [] },
                createFiles: [],
                editFiles: [],

                openEditModal(id, nama, deskripsi, existingFiles) {
                    this.editModal.id = id;
                    this.editModal.nama = nama;
                    this.editModal.deskripsi = deskripsi;
                    this.editModal.existingFiles = existingFiles ? existingFiles.map(f => ({ ...f, deleted: false })) : [];
                    this.editFiles = [];
                    this.syncInput('edit');
                    this.editModal.open = true;
                },

                removeExistingFile(index) {
                    this.editModal.existingFiles[index].deleted = true;
                },

                addFiles(e, type) {
                    let selectedFiles = Array.from(e.target.files);
                    let currentFiles = type === 'edit' ? this.editFiles : this.createFiles;
                    let existingFileCount = type === 'edit' ? this.editModal.existingFiles.filter(f => !f.deleted).length : 0;
                    let totalFiles = currentFiles.length + selectedFiles.length + existingFileCount;

                    if (totalFiles > 3) {
                        alert('Maksimal 3 file yang dapat dilampirkan!');
                        let available = 3 - (currentFiles.length + existingFileCount);
                        selectedFiles = available > 0 ? selectedFiles.slice(0, available) : [];
                    }

                    if (type === 'edit') {
                        this.editFiles = [...this.editFiles, ...selectedFiles];
                    } else {
                        this.createFiles = [...this.createFiles, ...selectedFiles];
                    }

                    this.syncInput(type);
                    e.target.value = ''; // reset so same file can be picked again
                },

                removeFile(index, type) {
                    if (type === 'edit') {
                        this.editFiles.splice(index, 1);
                    } else {
                        this.createFiles.splice(index, 1);
                    }
                    this.syncInput(type);
                },

                syncInput(type) {
                    let dt = new DataTransfer();
                    let currentFiles = type === 'edit' ? this.editFiles : this.createFiles;

                    currentFiles.forEach(file => dt.items.add(file));

                    if (type === 'edit') {
                        document.getElementById('hidden-lampiran-edit').files = dt.files;
                    } else {
                        document.getElementById('hidden-lampiran-create').files = dt.files;
                    }
                }
            };
        }
    </script>
</x-eoffice::manajemen-praktikum.layout>