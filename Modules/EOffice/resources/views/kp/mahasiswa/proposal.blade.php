<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Proposal KP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        :root {
            --primary-50:#eef2ff;--primary-100:#e0e7ff;--primary-500:#4f46e5;
            --grey-50:#f9fafb;--grey-100:#f3f4f6;--grey-200:#e5e7eb;
            --grey-400:#9ca3af;--grey-500:#6b7280;--grey-600:#4b5563;
            --grey-700:#374151;--grey-800:#1f2937;--grey-900:#030712;
        }
        .sikape-card { background:#fff; border:1px solid var(--grey-200); border-radius:12px; }
        .sikape-btn-primary { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary-500); color:#fff; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-primary:hover { background:#4338ca; }
        .sikape-btn-outline { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:transparent; color:var(--grey-700); border-radius:8px; font-size:14px; font-weight:600; border:1.5px solid var(--grey-200); cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-outline:hover { background:var(--grey-50); border-color:var(--grey-300); }
        .ql-toolbar { border-radius:8px 8px 0 0 !important; border-color:var(--grey-200) !important; }
        .ql-container { border-radius:0 0 8px 8px !important; border-color:var(--grey-200) !important; font-size:15px; min-height:400px; }
    </style>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-grey-50" style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', [
            'breadcrumbs' => ['Informasi', 'Proposal KP']
        ])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" x-data="proposalEditor()">

            <div class="mb-6">
                <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Proposal Kerja Praktik</h1>
                <p class="text-sm mt-1" style="color:var(--grey-500);">Buat rancangan proposal KP yang memuat latar belakang, tujuan, dan timeline kegiatan.</p>
            </div>

            <div class="sikape-card overflow-hidden">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-grey-100 bg-grey-50">
                    <div>
                        <h2 class="text-base font-semibold text-grey-800">Editor Proposal</h2>
                        <p class="text-xs mt-0.5 text-grey-400">
                            <span x-show="saved" class="text-green-600 font-medium">✓ Tersimpan otomatis</span>
                            <span x-show="!saved">Draft tidak tersimpan</span>
                        </p>
                    </div>
                    <button @click="loadTemplate()" class="sikape-btn-outline text-xs py-1.5 px-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Muat Struktur Template
                    </button>
                </div>

                {{-- Meta fields --}}
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4">
                    <div>
                        <label class="block text-xs font-semibold text-grey-600 mb-1.5">Judul Proposal</label>
                        <input type="text" x-model="formData.judul" @input="dirty()" placeholder="Rencana Kerja Praktik di..." class="w-full rounded-lg px-3 py-2 text-sm border focus:outline-none focus:ring-1 focus:ring-indigo-500 border-grey-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-grey-600 mb-1.5">Nama Instansi</label>
                        <input type="text" x-model="formData.instansi" @input="dirty()" placeholder="Contoh: PT ABC..." class="w-full rounded-lg px-3 py-2 text-sm border focus:outline-none focus:ring-1 focus:ring-indigo-500 border-grey-200">
                    </div>
                </div>

                {{-- Quill Editor --}}
                <div class="px-5 pb-5">
                    <label class="block text-xs font-semibold text-grey-600 mb-1.5">Isi Proposal (Latar Belakang s.d Penutup)</label>
                    <div id="proposal-editor" style="background:#fff;"></div>
                </div>

                {{-- Footer Actions --}}
                <div class="px-5 py-4 border-t border-grey-100 bg-grey-50 flex items-center justify-between gap-3">
                    <button @click="saveDraft()" class="sikape-btn-outline text-sm">Simpan Draft</button>
                    
                    <div class="flex items-center gap-2">
                        <!-- PDF -->
                        <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="inline">
                            @csrf
                            <input type="hidden" name="format" value="pdf">
                            <input type="hidden" name="judul" :value="formData.judul">
                            <input type="hidden" name="instansi" :value="formData.instansi">
                            <input type="hidden" name="content" :value="getContent()">
                            <button type="submit" class="sikape-btn-primary bg-red-600 hover:bg-red-700 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Unduh PDF
                            </button>
                        </form>

                        <!-- Word -->
                        <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="inline">
                            @csrf
                            <input type="hidden" name="format" value="word">
                            <input type="hidden" name="judul" :value="formData.judul">
                            <input type="hidden" name="instansi" :value="formData.instansi">
                            <input type="hidden" name="content" :value="getContent()">
                            <button type="submit" class="sikape-btn-primary bg-blue-600 hover:bg-blue-700 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Unduh Word
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function proposalEditor() {
    return {
        saved: true,
        quill: null,
        formData: {
            judul: '',
            instansi: ''
        },
        templateHTML: `
            <h2>1. Latar Belakang</h2><p><br></p>
            <h2>2. Rumusan Masalah</h2><p><br></p>
            <h2>3. Batasan Masalah</h2><p><br></p>
            <h2>4. Tujuan Kerja Praktik</h2><p><br></p>
            <h2>5. Bentuk Kegiatan</h2><p><br></p>
            <h2>6. Tempat dan Waktu Pelaksanaan</h2><p><br></p>
            <h2>7. Penutup</h2><p><br></p>
        `,
        init() {
            this.quill = new Quill('#proposal-editor', {
                theme: 'snow',
                placeholder: 'Ketik isi proposal di sini...',
                modules: {
                    toolbar: [
                        [{ heading: [1,2,3,false] }],
                        ['bold','italic','underline'],
                        [{ list:'ordered' },{ list:'bullet' }],
                        [{ align: [] }],
                        ['clean']
                    ]
                }
            });
            
            this.quill.on('text-change', () => {
                this.dirty();
            });

            // Load draft
            const draft = localStorage.getItem('sikape_proposal_kp');
            if (draft) {
                try {
                    const data = JSON.parse(draft);
                    this.formData.judul = data.judul || '';
                    this.formData.instansi = data.instansi || '';
                    if (data.content) {
                        this.quill.root.innerHTML = data.content;
                    }
                } catch(e) {}
            } else {
                this.loadTemplate();
            }
        },
        dirty() {
            this.saved = false;
        },
        saveDraft() {
            localStorage.setItem('sikape_proposal_kp', JSON.stringify({
                judul: this.formData.judul,
                instansi: this.formData.instansi,
                content: this.quill.root.innerHTML
            }));
            this.saved = true;
        },
        loadTemplate() {
            if(confirm("Memuat struktur template akan mengganti isi saat ini. Lanjutkan?")) {
                this.quill.root.innerHTML = this.templateHTML;
                this.dirty();
            }
        },
        getContent() {
            return this.quill ? this.quill.root.innerHTML : '';
        }
    }
}
</script>
</body>
</html>
