@php
    $pageTitle = ($praktikum->nama ?? 'Praktikum') . ' / Pengumuman';
@endphp
<x-eoffice::manajemen-praktikum.layout :pageTitle="$pageTitle">

    <div x-data="pengumumanManager()">
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
            <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="pengumuman" />
        @endif

        @if($praktikumList->isEmpty())
            <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai asisten praktikum di praktikum manapun.
                Hubungi koordinator untuk aktivasi.</div>
        @else

            {{-- -- Daftar Pengumuman -- --}}
            <div style="display:flex; flex-direction:column; gap:16px;">
                @forelse($pengumumans as $p)
                    @php
                        $isMine = $p->user_id === auth()->id();
                    @endphp
                    <div style="border:1px solid #DFE1E7; background:#fff; border-radius:14px; padding:15px 24px; position:relative; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition:all 0.2s ease;"
                        onmouseover="this.style.boxShadow='0 4px 14px rgba(17,24,39,0.05)';"
                        onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">

                        {{-- Pengarang --}}
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                {{-- Gray Circle Avatar --}}
                                <div
                                    style="width:40px; height:40px; border-radius:50%; background:#D1D5DB; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    @if($p->user?->name)
                                        @php
                                            $nameParts = explode(' ', $p->user->name);
                                            $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                                        @endphp
                                        <span style="font-size:14px; font-weight:700; color:#6B7280;">{{ $initials }}</span>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:14px; color:#111827;">
                                        {{ $p->user?->name ?? 'Sistem' }}
                                    </div>
                                    <div style="font-size:12px; color:#6B7280;">
                                        {{ $p->created_at?->format('d M Y, H:i') ?? '-' }}
                                        @if($p->updated_at && $p->updated_at->gt($p->created_at))
                                            <span style="font-style:italic; margin-left:4px;">(Diedit
                                                {{ $p->updated_at->format('d M Y, H:i') }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Edit / Delete Actions di Kanan Atas --}}
                            <div style="display:flex; gap:8px;">
                                @if($isMine)
                                    <button type="button" data-judul="{{ $p->judul }}" data-konten="{{ $p->konten }}"
                                        @click="openEditModal({{ $p->id }}, $el.dataset.judul, $el.dataset.konten)"
                                        title="Edit Pengumuman"
                                        style="background:#E0E7FF; color:#293C79; border:none; border-radius:6px; padding:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
                                        onmouseover="this.style.background='#C7D2FE'" onmouseout="this.style.background='#E0E7FF'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.destroy', $p->id) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');"
                                        style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Pengumuman"
                                            style="background:#FFE4E6; color:#DF1C41; border:none; border-radius:6px; padding:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
                                            onmouseover="this.style.background='#FECDD3'"
                                            onmouseout="this.style.background='#FFE4E6'">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div style="margin:10px 0 0 0;">
                            <h3
                                style="font-size:14px; font-weight:700; color:#111827; margin:0 0 4px 0; line-height:1.4; padding:0;">
                                {{ $p->judul }}</h3>
                            <p style="font-size:12px; color:#374151; margin:0; padding:0; line-height:1.6;">{{ $p->konten }}</p>
                        </div>

                        {{-- Lampiran if any --}}
                        @if(!empty($p->lampiran))
                            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top: 10px;">
                                @foreach($p->lampiran as $lamp)
                                    <a href="{{ Storage::url($lamp['path']) }}" target="_blank"
                                        style="display:flex; align-items:center; gap:6px; padding:6px 12px; background:#F3F4F6; border:1px solid #E5E7EB; border-radius:8px; text-decoration:none; font-size:12px; color:#374151; transition:all 0.15s;"
                                        onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path
                                                d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                        </svg>
                                        {{ $lamp['name'] ?? basename($lamp['path']) }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div style="display:none;"></div>
                        @endif
                    </div>
                @empty
                    <div class="mp-card flex-shrink-0"
                        style="min-height:180px;display:flex;align-items:center;justify-content:center;">
                        <div style="padding:36px;text-align:center;">
                            <div
                                style="width:48px;height:48px;border-radius:12px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                    stroke-linecap="round">
                                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                </svg>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman
                            </div>
                            <div style="font-size:12px;color:#666D80;">Jadilah yang pertama menulis pengumuman untuk praktikum
                                ini.</div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Floating Buat Pengumuman Button --}}
            <button @click="showModal = true" type="button"
                style="position:fixed; bottom:32px; right:40px; z-index:50; background:#293C79; color:white; border:none; border-radius:8px; padding:10px 20px; font-size:14px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); transition:transform 0.2s, background 0.2s;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.background='#1F2D59';"
                onmouseout="this.style.transform='translateY(0)'; this.style.background='#293C79';">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Buat Pengumuman
            </button>

            {{-- Modal Form Buat Pengumuman (Admin-style animation) --}}
            <div x-show="showModal" style="display:none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
                x-cloak>
                <div class="relative bg-white rounded-xl shadow-2xl w-[600px] max-w-[90%] max-h-[90vh] overflow-y-auto"
                    style="border:1px solid #E5E7EB;" @click.away="showModal = false" x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div
                        style="padding:20px 24px; border-bottom:1px solid #E5E7EB; display:flex; justify-content:space-between; align-items:center;">
                        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Tulis Pengumuman Baru</h2>
                        <button type="button" @click="showModal = false"
                            style="background:none; border:none; cursor:pointer; color:#6B7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>

                    <div style="padding:24px;">
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.store') }}"
                            enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $asprak->praktikum_id ?? $praktikum->id }}">

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Judul Pengumuman <span style="color:#DC2626;">*</span>
                                </label>
                                <input name="judul" required placeholder="Masukkan judul..."
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827;"
                                    value="{{ old('judul') }}">
                            </div>

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Isi Pengumuman <span style="color:#DC2626;">*</span>
                                </label>
                                <textarea name="konten" rows="3" maxlength="1000" required
                                    placeholder="Tulis isi pengumuman... (Maks. 1000 karakter)"
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; resize:vertical; min-height:80px; max-height:120px;">{{ old('konten') }}</textarea>
                            </div>

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Lampiran (File PDF)
                                </label>

                                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                                    <button type="button" @click="$refs.fileInput.click()"
                                        style="width:100%; padding:10px; border:1px dashed #D1D5DB; border-radius:8px; background:#F9FAFB; color:#293C79; font-size:13px; font-weight:600; cursor:pointer; display:flex; justify-content:center; align-items:center; gap:8px; transition:all 0.2s;"
                                        onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                                        onmouseout="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB';">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Tambah File PDF
                                    </button>
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#9CA3AF;">
                                        <span x-text="files.length + ' / 3 file terpilih'"></span>
                                        <span>Maks. 5MB per file</span>
                                    </div>
                                </div>
                                <input type="file" x-ref="fileInput" style="display:none" multiple accept=".pdf"
                                    @change="addFiles($event, 'create')">
                                <input type="file" id="hidden-lampiran" name="lampiran[]" multiple style="display:none">

                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <template x-for="(file, index) in files" :key="index">
                                        <div
                                            style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:6px;">
                                            <div style="display:flex;align-items:center;gap:8px;overflow:hidden;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7280"
                                                    stroke-width="2" stroke-linecap="round">
                                                    <path
                                                        d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                                </svg>
                                                <span x-text="file.name"
                                                    style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index, 'create')" title="Hapus"
                                                style="color:#DC2626;background:none;border:none;cursor:pointer;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div style="display:flex;justify-content:flex-end; gap:12px; margin-top:8px;">
                                <button type="submit"
                                    style="padding:10px 20px; font-size:14px; font-weight:600; color:white; background:#293C79; border:none; border-radius:8px; cursor:pointer;"
                                    onmouseover="this.style.background='#1F2D59'"
                                    onmouseout="this.style.background='#293C79'">Kirim Pengumuman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Form Edit Pengumuman --}}
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
                        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Edit Pengumuman</h2>
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
                        <form method="POST"
                            :action="`{{ route('eoffice.manprak.asprak.pengumuman.index') }}/${editModal.id}`"
                            enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="praktikum_id" value="{{ $asprak->praktikum_id ?? $praktikum->id }}">

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Judul Pengumuman <span style="color:#DC2626;">*</span>
                                </label>
                                <input name="judul" x-model="editModal.judul" required placeholder="Masukkan judul..."
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827;">
                            </div>

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Isi Pengumuman <span style="color:#DC2626;">*</span>
                                </label>
                                <textarea name="konten" x-model="editModal.konten" rows="3" maxlength="1000" required
                                    placeholder="Tulis isi pengumuman... (Maks. 1000 karakter)"
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; resize:vertical; min-height:80px; max-height:120px;"></textarea>
                            </div>

                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Lampiran Baru (File PDF)
                                    <br><span style="font-size:11px; font-weight:400; color:#6B7280;">Mengunggah file baru
                                        akan menimpa semua lampiran lama pengumuman ini.</span>
                                </label>

                                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                                    <button type="button" @click="$refs.fileInputEdit.click()"
                                        style="width:100%; padding:10px; border:1px dashed #D1D5DB; border-radius:8px; background:#F9FAFB; color:#293C79; font-size:13px; font-weight:600; cursor:pointer; display:flex; justify-content:center; align-items:center; gap:8px; transition:all 0.2s;"
                                        onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                                        onmouseout="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB';">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Tambah File PDF
                                    </button>
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#9CA3AF;">
                                        <span x-text="editFiles.length + ' / 3 file terpilih'"></span>
                                        <span>Maks. 5MB per file</span>
                                    </div>
                                </div>
                                <input type="file" x-ref="fileInputEdit" style="display:none" multiple accept=".pdf"
                                    @change="addFiles($event, 'edit')">
                                <input type="file" id="hidden-lampiran-edit" name="lampiran[]" multiple
                                    style="display:none">

                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <template x-for="(file, index) in editFiles" :key="index">
                                        <div
                                            style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:6px;">
                                            <div style="display:flex;align-items:center;gap:8px;overflow:hidden;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7280"
                                                    stroke-width="2" stroke-linecap="round">
                                                    <path
                                                        d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                                </svg>
                                                <span x-text="file.name"
                                                    style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index, 'edit')" title="Hapus"
                                                style="color:#DC2626;background:none;border:none;cursor:pointer;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>


                            <div style="display:flex;justify-content:flex-end; gap:12px; margin-top:8px;">
                                <button type="submit"
                                    style="padding:10px 20px; font-size:14px; font-weight:600; color:white; background:#293C79; border:none; border-radius:8px; cursor:pointer;"
                                    onmouseover="this.style.background='#1F2D59'"
                                    onmouseout="this.style.background='#293C79'">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endif {{-- end if praktikumList not empty --}}

    </div>

    <script>
        function pengumumanManager() {
            return {
                showModal: false,
                files: [],
                editFiles: [],
                editModal: {
                    open: false,
                    id: null,
                    judul: '',
                    konten: ''
                },
                addFiles(e, type) {
                    let selectedFiles = Array.from(e.target.files);
                    let currentFiles = type === 'edit' ? this.editFiles : this.files;
                    let totalFiles = currentFiles.length + selectedFiles.length;

                    if (totalFiles > 3) {
                        alert('Maksimal 3 file yang dapat diunggah!');
                        selectedFiles = selectedFiles.slice(0, 3 - currentFiles.length);
                    }

                    if (type === 'edit') {
                        this.editFiles = [...this.editFiles, ...selectedFiles];
                    } else {
                        this.files = [...this.files, ...selectedFiles];
                    }

                    this.syncInput(type);
                    e.target.value = ''; // reset so same file can be picked again
                },
                removeFile(index, type) {
                    if (type === 'edit') {
                        this.editFiles.splice(index, 1);
                    } else {
                        this.files.splice(index, 1);
                    }
                    this.syncInput(type);
                },
                syncInput(type) {
                    let dt = new DataTransfer();
                    let currentFiles = type === 'edit' ? this.editFiles : this.files;

                    currentFiles.forEach(file => dt.items.add(file));

                    if (type === 'edit') {
                        document.getElementById('hidden-lampiran-edit').files = dt.files;
                    } else {
                        document.getElementById('hidden-lampiran').files = dt.files;
                    }
                },
                openEditModal(id, judul, konten) {
                    this.editModal.id = id;
                    this.editModal.judul = judul;
                    this.editModal.konten = konten;
                    this.editFiles = [];
                    this.syncInput('edit');
                    this.editModal.open = true;
                }
            }
        }
    </script>
</x-eoffice::manajemen-praktikum.layout>