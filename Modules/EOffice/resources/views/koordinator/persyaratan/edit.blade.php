<x-eoffice::layouts.koordinator title="Edit Persyaratan Dokumen">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.koordinator.persyaratan_dokumen', ['periode_id' => $selectedPeriodeId]) }}"
            class="text-[#666D80] hover:text-[#0D0D12] transition-colors">Persyaratan Dokumen</a>
    @endsection

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    <div x-data="{
        docs: {{ \Illuminate\Support\Js::from($templates->map(fn($t) => [
    'id' => $t->id,
    'title' => $t->title,
    'description' => $t->description ?? '',
    'existing_file' => $t->file_name ?: '',
    'existing_path' => $t->file_path ?: '',
    'new_file' => '',
])->values()) }},
        addDoc() {
            this.docs.push({ id: null, title: '', description: '', existing_file: '', existing_path: '', new_file: '' });
        },
        removeDoc(index) {
            this.docs.splice(index, 1);
        }
    }">

        <!-- Page Header & Buttons -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('eoffice.kp.koordinator.persyaratan_dokumen', ['periode_id' => $selectedPeriodeId]) }}"
                class="inline-flex items-center gap-2 text-[13px] font-medium text-[#0D0D12] hover:text-[#1A1C4B] transition-colors bg-white border border-[#E4E7EC] rounded-[8px] px-3.5 py-2 shadow-[0_1px_2px_rgba(16,24,40,0.05)]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <button form="persyaratan-form" type="submit"
                class="inline-flex items-center justify-center px-4 py-2 bg-[#2E3182] border border-transparent rounded-[8px] text-[13px] font-medium text-white hover:bg-[#1f215e] focus:outline-none transition-colors shadow-[0_1px_2px_rgba(16,24,40,0.05)] select-none">
                Simpan
            </button>
        </div>

        <!-- Page Title -->
        <div class="mb-6">
            <h1
                style="font-family:'Inter Tight',sans-serif; font-size:24px; font-weight:700; color:#0D0D12; letter-spacing:-0.01em;">
                Edit Persyaratan Dokumen
            </h1>
            <p class="text-[14px] text-[#666D80] mt-1">Konfigurasikan dokumen yang diperlukan untuk setiap tahapan pada
                setiap periode.</p>
        </div>

        <!-- ═══ Outer Layout (like the Mockup) ═══ -->
        <div
            class="bg-white rounded-[16px] shadow-[0_1px_3px_rgba(16,24,40,0.06),0_1px_2px_rgba(16,24,40,0.04)] border border-[#E4E7EC] overflow-hidden mb-12">
            <div class="flex flex-col lg:flex-row p-4 sm:p-6 lg:p-8 gap-6 lg:gap-8">

                <!-- Left: Phase info -->
                <div class="lg:w-[30%]">
                    <h2 class="text-[18px] font-bold text-[#0D0D12] mb-1.5"
                        style="font-family:'Inter Tight',sans-serif;">{{ strtoupper($phaseLabel) }}</h2>
                    <p class="text-[13px] text-[#666D80]">Kelola jenis dokumen untuk {{ strtoupper($phaseLabel) }}.</p>
                </div>

                <!-- Right: Document types table -->
                <div class="lg:w-[70%]">
                    <form id="persyaratan-form" method="POST" enctype="multipart/form-data"
                        action="{{ route('eoffice.kp.koordinator.persyaratan_dokumen.update', $phase) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">

                        <div class="border border-[#E4E7EC] rounded-[12px] overflow-hidden">
                            <!-- Table Header Block -->
                            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E4E7EC]">
                                <h3 class="text-[14px] font-bold text-[#0D0D12]"
                                    style="font-family:'Inter Tight',sans-serif;">Tipe Dokumen</h3>
                                <button type="button" @click="addDoc()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#2E3182] text-white text-[12px] font-medium rounded-[6px] hover:bg-[#1f215e] transition-colors shadow-sm select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>

                            <!-- Column Headers -->
                            <div
                                class="hidden sm:grid grid-cols-12 gap-3 px-5 py-3 border-b border-[#E4E7EC] bg-[#F9FAFB]">
                                <div class="col-span-5 text-left"><span
                                        class="text-[12px] font-medium text-[#666D80]">Nama Dokumen</span></div>
                                <div class="col-span-6 text-left"><span
                                        class="text-[12px] font-medium text-[#666D80]">Deskripsi</span></div>
                                <div class="col-span-1 text-center"><span
                                        class="text-[12px] font-medium text-[#666D80]">Aksi</span></div>
                            </div>

                            <!-- Document Rows -->
                            <div class="divide-y divide-[#E4E7EC]">
                                <template x-for="(doc, index) in docs" :key="index">
                                    <div class="px-5 py-4 hover:bg-[#F9FAFB]/50 transition-colors">
                                        <div class="flex items-start gap-3 w-full">
                                            <!-- Inputs Group -->
                                            <div class="flex-1 space-y-3">
                                                <!-- Row: Name & Description -->
                                                <div class="grid grid-cols-1 sm:grid-cols-11 gap-3">
                                                    <div class="col-span-5">
                                                        <input type="text" :name="'docs[' + index + '][title]'"
                                                            x-model="doc.title"
                                                            class="w-full border border-[#E4E7EC] rounded-[8px] px-3.5 py-2 text-[13px] text-[#0D0D12] focus:outline-none focus:border-[#2E3182] transition-colors placeholder:text-[#A4ABB8]"
                                                            placeholder="masukkan nama dokumen" required>
                                                    </div>
                                                    <div class="col-span-6">
                                                        <input type="text" :name="'docs[' + index + '][description]'"
                                                            x-model="doc.description"
                                                            class="w-full border border-[#E4E7EC] rounded-[8px] px-3.5 py-2 text-[13px] text-[#0D0D12] focus:outline-none focus:border-[#2E3182] transition-colors placeholder:text-[#A4ABB8]"
                                                            placeholder="(optional)">
                                                    </div>
                                                    <input type="hidden" :name="'docs[' + index + '][existing_file]'"
                                                        :value="doc.existing_file">
                                                    <input type="hidden" :name="'docs[' + index + '][existing_path]'"
                                                        :value="doc.existing_path">
                                                </div>

                                                <!-- Row: File Upload Box -->
                                                <div class="w-full sm:w-[calc(100%-1.5rem)]">
                                                    <!-- State: Empty (No file) -->
                                                    <label x-show="!doc.existing_file && !doc.new_file"
                                                        class="flex justify-between items-center w-full border border-[#E4E7EC] rounded-[8px] px-3.5 py-2 cursor-pointer bg-white hover:bg-[#F8F9FA] transition-colors group">
                                                        <span
                                                            class="text-[13px] text-[#A4ABB8] group-hover:text-[#666D80] truncate select-none">tambahkan
                                                            file form template (optional)</span>
                                                        <svg class="w-4 h-4 text-[#A4ABB8] group-hover:text-[#666D80] shrink-0"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        <input type="file" :name="'files[' + index + ']'" class="hidden"
                                                            @change="if($event.target.files.length) { doc.new_file = $event.target.files[0].name; }"
                                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                                    </label>

                                                    <!-- State: Has File (New or Existing) -->
                                                    <div x-show="doc.existing_file || doc.new_file"
                                                        style="display:none;"
                                                        class="flex justify-between items-center w-full border border-[#E4E7EC] rounded-[8px] px-3.5 py-2 bg-[#F9FAFB]">
                                                        <div class="flex items-center gap-2 overflow-hidden">
                                                            <svg class="w-4 h-4 text-[#2E3182] bg-white rounded-sm shrink-0"
                                                                fill="none" stroke="currentColor" stroke-width="2"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span
                                                                class="text-[13px] text-[#0D0D12] font-medium truncate"
                                                                x-text="doc.new_file || doc.existing_file"></span>
                                                        </div>
                                                        <button type="button"
                                                            @click="$event.target.closest('div[x-show]').previousElementSibling.querySelector('input').value = ''; doc.new_file = ''; doc.existing_file = ''; doc.existing_path = '';"
                                                            class="text-[#F04438] hover:text-[#d92d20] transition-colors p-1 shrink-0 ml-2"
                                                            title="Hapus File">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                stroke-width="2.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Aksi Delete Area -->
                                            <div class="flex justify-center items-start pt-[10px] shrink-0 w-[40px]">
                                                <button type="button" @click="removeDoc(index)"
                                                    class="text-[#F04438] hover:text-[#d92d20] transition-colors"
                                                    title="Hapus Dokumen">
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty state when there are NO docs at all -->
                                <div x-show="docs.length === 0" class="px-5 py-8 text-center bg-[#F9FAFB]">
                                    <p class="text-[13px] text-[#666D80] mb-3">Belum ada dokumen yang ditambahkan.</p>
                                    <button type="button" @click="addDoc()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#E4E7EC] text-[#2E3182] text-[12px] font-medium rounded-[6px] hover:bg-slate-50 transition-colors shadow-sm select-none">
                                        Tambah Pertama
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</x-eoffice::layouts.koordinator>