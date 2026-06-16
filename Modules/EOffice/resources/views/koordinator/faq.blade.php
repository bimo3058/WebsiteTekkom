<x-eoffice::layouts.koordinator title="FAQ & Timeline KP">
    @section('breadcrumbs')
        <span class="text-slate-500">Informasi</span>
        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-800 font-semibold">FAQ & Dokumen</span>
    @endsection

    @push('styles')
        <style>
            .drag-active {
                border-color: #3b82f6 !important;
                background-color: #eff6ff !important;
            }

            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush

    <div
        x-data="{ activeTab: 'dokumen', faqModalOpen: false, deleteFaqOpen: false, deleteDokumenOpen: false, deleteId: null }">

        <!-- Toast Notification (Session) -->
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

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">FAQ & Timeline KP</h1>
            <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Kelola dokumen panduan mahasiswa dan daftar
                pertanyaan yang sering ditanyakan (FAQ) seputar pelaksanaan Kerja Praktik/Magang.</p>
        </div>

        <!-- Custom Tabs -->
        <div class="flex items-center space-x-1 bg-slate-200/50 p-1 rounded-xl w-fit mb-8 border border-slate-200">
            <button @click="activeTab = 'dokumen'"
                :class="activeTab === 'dokumen' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="px-6 py-2 rounded-lg text-sm transition-all focus:outline-none">Timeline KP</button>
            <button @click="activeTab = 'faq'"
                :class="activeTab === 'faq' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="px-6 py-2 rounded-lg text-sm transition-all focus:outline-none">Manajemen FAQ</button>
        </div>

        <!-- TAB: DOKUMEN PANDUAN -->
        <div x-show="activeTab === 'dokumen'" x-transition.opacity.duration.300ms>

            <!-- Form Upload Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">Upload Dokumen Baru</h2>
                    <p class="text-sm text-slate-500 mt-1">Upload dokumen PDF yang akan tampil pada dashboard mahasiswa.
                    </p>
                </div>
                <div class="p-6">
                    <form action="{{ route('eoffice.kp.koordinator.faq.dokumen.store') }}" method="POST"
                        enctype="multipart/form-data" class="flex flex-col md:flex-row gap-6"
                        x-data="{ dragging: false, fileName: '' }">
                        @csrf
                        <div class="flex-1 space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Dokumen</label>
                                <input type="text" name="judul" required
                                    class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-slate-50 hover:bg-white transition-all outline-none"
                                    placeholder="Contoh: Buku Panduan KP 2026">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Versi Dokumen <span
                                        class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="version"
                                    class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-slate-50 hover:bg-white transition-all outline-none"
                                    placeholder="Contoh: v1.0">
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">File Dokumen (PDF/DOC)</label>
                            <div class="mt-1 flex justify-center px-6 pt-6 pb-7 border-2 border-slate-200 border-dashed rounded-xl relative hover:bg-slate-50 transition-colors group cursor-pointer"
                                :class="{ 'drag-active': dragging }" @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; $refs.docInput.files = $event.dataTransfer.files; fileName = $refs.docInput.files[0].name">
                                <input type="file" name="attachment" x-ref="docInput"
                                    @change="fileName = $refs.docInput.files[0] ? $refs.docInput.files[0].name : ''"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".pdf,.doc,.docx" required>
                                <div class="space-y-2 text-center" x-show="!fileName">
                                    <div
                                        class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div class="flex text-sm text-slate-600 justify-center"><span
                                            class="cursor-pointer rounded-md font-semibold text-indigo-600 hover:text-indigo-500">Upload
                                            file</span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF up to 10MB</p>
                                </div>
                                <div class="flex flex-col items-center gap-2 text-center w-full" x-show="fileName"
                                    style="display: none;">
                                    <div
                                        class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 mb-2">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-900 truncate w-full px-4" x-text="fileName">
                                    </p>
                                    <button type="button" @click.stop.prevent="$refs.docInput.value = ''; fileName = ''"
                                        class="text-xs font-semibold text-red-500 hover:text-red-700 z-20 relative uppercase tracking-wider mt-1">Ganti
                                        File</button>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors w-full md:w-auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Dokumen
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- History Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-white flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-900">History Upload Dokumen</h2>
                    <div class="relative w-64 hidden sm:block">
                        <input type="text" placeholder="Cari dokumen..."
                            class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Nama File & Versi</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Waktu Upload</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($dokumens as $doc)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center border border-red-100">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                                    {{ $doc->judul }}</p>
                                                <div class="flex items-center text-xs text-slate-500 mt-0.5 space-x-2">
                                                    <span>{{ $doc->file_name }}</span>
                                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                    <span
                                                        class="font-medium bg-slate-100 px-1.5 rounded">{{ $doc->version ?? 'v1.0' }}</span>
                                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                    <span>{{ $doc->file_size ?? 'Unknown' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm text-slate-900 font-medium">
                                            {{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Oleh: {{ $doc->pembuat->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($doc->is_active)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">Aktif</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-slate-100 text-slate-600 border border-slate-200 uppercase">Archive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button title="Preview PDF"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button title="Hapus"
                                                @click="deleteId = {{ $doc->id }}; deleteDokumenOpen = true"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900">Belum ada dokumen</p>
                                        <p class="text-xs text-slate-500 mt-1">Gunakan form di atas untuk mengupload panduan
                                            pertama.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB: MANAJEMEN FAQ -->
        <div x-show="activeTab === 'faq'" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-900">Daftar Pertanyaan (FAQ)</h2>
                <button @click="faqModalOpen = true"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah FAQ
                </button>
            </div>

            <div class="space-y-4">
                @forelse($faqs as $faq)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                        x-data="{ expanded: false }">
                        <div class="flex items-start justify-between px-6 py-5 cursor-pointer hover:bg-slate-50 transition-colors"
                            @click="expanded = !expanded">
                            <div class="flex-1 pr-4">
                                <h3 class="text-[15px] font-bold text-slate-900 leading-snug"
                                    :class="expanded ? 'text-indigo-700' : ''">{{ $faq->pertanyaan }}</h3>
                                <p class="text-xs text-slate-400 mt-1.5 font-medium flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Update terakhir: {{ \Carbon\Carbon::parse($faq->updated_at)->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="flex gap-1" @click.stop>
                                    <button
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteId = {{ $faq->id }}; deleteFaqOpen = true"
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180 bg-indigo-100 text-indigo-600' : ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div x-show="expanded" x-collapse>
                            <div
                                class="px-6 py-5 bg-slate-50/50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed border-l-4 border-l-indigo-500">
                                {{ $faq->jawaban }}</div>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada FAQ</h3>
                        <p class="text-sm text-slate-500 max-w-sm mb-6">Tambahkan daftar pertanyaan yang sering diajukan
                            untuk mempermudah mahasiswa.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Form FAQ -->
        <div x-show="faqModalOpen" class="relative z-50" style="display: none;">
            <div x-show="faqModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm">
            </div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="faqModalOpen" @click.away="faqModalOpen = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">
                        <form action="{{ route('eoffice.kp.koordinator.faq.store') }}" method="POST">
                            @csrf
                            <div class="px-6 py-5 border-b border-slate-100 bg-white flex items-center justify-between">
                                <h3 class="text-xl font-bold text-slate-900">Tambah FAQ Baru</h3>
                                <button type="button" @click="faqModalOpen = false"
                                    class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 p-2 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="px-6 py-6 space-y-5 bg-white">
                                <div
                                    class="text-amber-600 bg-amber-50 p-3 rounded-xl border border-amber-100 text-xs flex items-start gap-2">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Note: Fitur tambah ke database belum aktif karena tabel eo_faq belum ada di
                                        database. Saat ini digunakan untuk keperluan design UI.</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pertanyaan <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="pertanyaan" required
                                        class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none"
                                        placeholder="Masukkan pertanyaan...">
                                </div>
                                <div x-data="{ content: '' }">
                                    <div class="flex items-end justify-between mb-1.5">
                                        <label class="block text-sm font-bold text-slate-700">Jawaban <span
                                                class="text-red-500">*</span></label>
                                        <span class="text-[11px] text-slate-400 font-medium"
                                            x-text="content.length + '/500'"></span>
                                    </div>
                                    <textarea name="jawaban" x-model="content" required rows="5" maxlength="500"
                                        class="block w-full rounded-xl border-slate-200 py-3 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none resize-y"
                                        placeholder="Masukkan jawaban secara lengkap..."></textarea>
                                </div>
                            </div>
                            <div
                                class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                                <button type="button" @click="faqModalOpen = false"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm">Batal</button>
                                <button type="submit"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan FAQ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus FAQ -->
        <div x-show="deleteFaqOpen" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus FAQ</h3>
                    <p class="text-sm text-slate-500 mb-6">Apakah Anda yakin ingin menghapus FAQ ini? Tindakan ini tidak
                        dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button @click="deleteFaqOpen = false"
                            class="flex-1 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 border border-slate-300 hover:bg-slate-50">Batal</button>
                        <form x-bind:action="`/eoffice/kp/koordinator/faq/${deleteId}`" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus Dokumen -->
        <div x-show="deleteDokumenOpen" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Dokumen</h3>
                    <p class="text-sm text-slate-500 mb-6">Dokumen ini akan dihapus dari sistem. Mahasiswa tidak bisa
                        melihatnya lagi.</p>
                    <div class="flex gap-3">
                        <button @click="deleteDokumenOpen = false"
                            class="flex-1 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 border border-slate-300 hover:bg-slate-50">Batal</button>
                        <form x-bind:action="`/eoffice/kp/koordinator/faq/dokumen/${deleteId}`" method="POST"
                            class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-eoffice::layouts.koordinator>