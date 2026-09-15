<x-eoffice::layouts.koordinator title="Template Dokumen">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.koordinator.dashboard') }}"
            class="text-slate-400 hover:text-slate-600 transition-colors">Dashboard</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Template Dokumen</span>
    @endsection

    <div x-data="{
        deleteModalOpen: false,
        deleteId: null,
        isEditing: false,
        formAction: '{{ route('eoffice.kp.koordinator.template.store') }}',
        formData: { title: '', phase: 'pra_kp', fileName: '' }
    }">

        <!-- Page Header -->
        <div class="mb-6 lg:mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Manajemen Template Dokumen</h1>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Upload template dokumen sesuai fase Kerja Praktik
                (Pra KP, Saat KP, Pasca KP) untuk didownload oleh mahasiswa.</p>
        </div>

        <!-- Toast Notification -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-20 right-6 lg:right-10 z-50 bg-white border border-emerald-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]">
                <div
                    class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center mt-0.5">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900 mb-0.5">Berhasil!</p>
                    <p class="text-[13px] text-slate-500 leading-relaxed">{{ session('success') }}</p>
                </div>
                <button type="button" @click="show = false"
                    class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error') || session('warning'))
            <div
                class="mb-6 p-4 rounded-xl border {{ session('error') ? 'bg-red-50 border-red-200 text-red-700' : 'bg-amber-50 border-amber-200 text-amber-700' }} flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-bold">{{ session('error') ? 'Error' : 'Peringatan' }}</h4>
                    <p class="text-sm mt-1">{{ session('error') ?? session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-10 overflow-hidden" id="form-section">
            <form :action="formAction" method="POST" enctype="multipart/form-data"
                x-data="{ dragging: false, fileName: '' }">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="p-6 md:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Template <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="formData.title" required
                                class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 border bg-white hover:border-slate-300 transition-all outline-none"
                                placeholder="Contoh: Logbook Harian">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Fase KP <span
                                    class="text-red-500">*</span></label>
                            <select name="phase" x-model="formData.phase" required
                                class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 border bg-white hover:border-slate-300 transition-all outline-none">
                                <option value="pra_kp">Pra KP (Sebelum KP)</option>
                                <option value="saat_kp">Saat KP (Pelaksanaan)</option>
                                <option value="pasca_kp">Pasca KP (Selesai/Seminar)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <!-- Upload Area -->
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Upload Template (.pdf, .doc,
                                .docx) <span class="text-red-500" x-show="!isEditing">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl relative hover:bg-slate-50 transition-colors group cursor-pointer"
                                :class="{ 'drag-active': dragging }" @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name">

                                <input type="file" name="file_path" x-ref="fileInput"
                                    @change="fileName = $refs.fileInput.files[0] ? $refs.fileInput.files[0].name : ''"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".pdf,.doc,.docx" :required="!isEditing">

                                <div class="space-y-2 text-center"
                                    x-show="!fileName && !(isEditing && formData.fileName)">
                                    <div
                                        class="w-12 h-12 bg-primary-50 text-primary-500 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <span
                                            class="relative cursor-pointer rounded-md font-semibold text-primary-500 hover:text-primary-500">Upload
                                            a file</span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF, DOC, DOCX up to 5MB</p>
                                </div>

                                <!-- File yang sudah ada (mode edit) -->
                                <div class="flex items-center gap-3 text-left w-full"
                                    x-show="isEditing && formData.fileName && !fileName" style="display: none;">
                                    <div
                                        class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 truncate"
                                            x-text="formData.fileName"></p>
                                        <p class="text-xs text-emerald-600 font-medium">File saat ini tersimpan — upload
                                            baru untuk mengganti</p>
                                    </div>
                                </div>

                                <!-- File baru dipilih -->
                                <div class="flex items-center gap-3 text-left w-full" x-show="fileName"
                                    style="display: none;">
                                    <div
                                        class="w-10 h-10 bg-primary-100 text-primary-500 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 truncate" x-text="fileName"></p>
                                        <p class="text-xs text-slate-500">File baru siap diupload</p>
                                    </div>
                                    <button type="button"
                                        @click.stop.prevent="$refs.fileInput.value = ''; fileName = ''"
                                        class="text-slate-400 hover:text-red-500 p-1 rounded-md transition-colors z-20 relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex flex-col justify-end h-full space-y-4 md:w-64">
                            <div class="flex gap-2 w-full mt-7">
                                <button type="button" x-show="isEditing"
                                    @click="isEditing = false; formAction = '{{ route('eoffice.kp.koordinator.template.store') }}'; formData = { title: '', phase: 'pra_kp', fileName: '' }"
                                    class="flex-1 px-5 py-3.5 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors focus:ring-4 focus:ring-slate-100 outline-none flex items-center justify-center">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 px-5 py-3.5 bg-primary-500 text-white text-sm font-bold rounded-xl hover:bg-primary-500 transition-colors shadow-sm shadow-primary-200 focus:ring-4 focus:ring-primary-100 outline-none flex items-center justify-center">
                                    <svg x-show="!isEditing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <svg x-show="isEditing" style="display: none;" class="w-4 h-4 mr-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span x-text="isEditing ? 'Update' : 'Upload Template'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Template List -->
        <div class="space-y-4">
            @forelse($templates as $item)
                <div
                    class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-primary-100 transition-all overflow-hidden p-5 flex flex-col md:flex-row gap-5 items-center">
                    <div
                        class="w-14 h-14 bg-primary-50 text-primary-500 rounded-xl flex items-center justify-center shrink-0 border border-primary-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide {{ $item->phase == 'pra_kp' ? 'bg-amber-50 text-amber-700 border-amber-100' : ($item->phase == 'saat_kp' ? 'bg-primary-50 text-primary-500 border-primary-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }} border uppercase">
                                {{ str_replace('_', ' ', $item->phase) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1 truncate">{{ $item->title }}</h3>
                        @if($item->file_path)
                            <a href="{{ Storage::disk('public')->url($item->file_path) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-500 hover:text-primary-500 mt-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $item->file_name ?? basename($item->file_path) }}
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-row items-center gap-2 shrink-0">
                        <button type="button"
                            @click='formData = { title: @json($item->title), phase: @json($item->phase), fileName: @json($item->file_name ?? basename($item->file_path ?? "")) }; formAction = `/eoffice/kp/koordinator/template/{{ $item->id }}`; isEditing = true; document.getElementById("form-section").scrollIntoView({ behavior: "smooth" });'
                            class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 hover:text-primary-500 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </button>
                        <button type="button" @click="deleteId = {{ $item->id }}; deleteModalOpen = true;"
                            class="inline-flex items-center justify-center p-2 bg-white border border-slate-200 text-red-500 rounded-lg hover:bg-red-50 hover:border-red-100 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div
                    class="bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-primary-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Template Dokumen</h3>
                    <p class="text-sm text-slate-500 max-w-sm mb-6">Mulai upload template dokumen untuk masing-masing fase
                        (Pra, Saat, Pasca) KP agar dapat diunduh mahasiswa.</p>
                </div>
            @endforelse
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true" style="display: none;">
            <div x-show="deleteModalOpen" x-transition.opacity
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="deleteModalOpen" @click.away="deleteModalOpen = false" x-transition
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title">Hapus
                                        Template</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus template
                                            dokumen ini? Data yang dihapus tidak dapat dikembalikan lagi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <form x-bind:action="`/eoffice/kp/koordinator/template/${deleteId}`" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto transition-colors">Ya,
                                    Hapus</button>
                            </form>
                            <button type="button" @click="deleteModalOpen = false"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-eoffice::layouts.koordinator>