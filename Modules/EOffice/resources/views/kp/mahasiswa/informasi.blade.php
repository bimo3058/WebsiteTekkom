<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Keperluan Perusahaan</title>
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
            --success-0:#f0fdf4;--success-50:#dcfce7;--success-300:#16a34a;
            --warning-0:#fffbeb;--warning-50:#fef3c7;--warning-300:#d97706;
            --error-0:#fff1f2;--error-50:#ffe4e6;--error-200:#f87171;
            --sky-200:#bae6fd;--sky-600:#0284c7;
        }
        .sikape-card { background:#fff; border:1px solid var(--grey-200); border-radius:12px; }
        .sikape-btn-primary { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary-500); color:#fff; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-primary:hover { background:#4338ca; }
        .sikape-btn-outline { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:transparent; color:var(--grey-700); border-radius:8px; font-size:14px; font-weight:600; border:1.5px solid var(--grey-200); cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-outline:hover { background:var(--grey-50); border-color:var(--grey-300); }
        .sikape-btn-ghost { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:transparent; color:var(--grey-600); border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-ghost:hover { background:var(--grey-100); }
        .ql-toolbar { border-radius:8px 8px 0 0 !important; border-color:var(--grey-200) !important; }
        .ql-container { border-radius:0 0 8px 8px !important; border-color:var(--grey-200) !important; font-size:15px; min-height:320px; }
    </style>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-grey-50" style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', [
            'breadcrumbs' => ['Informasi', 'Keperluan Perusahaan']
        ])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            {{-- Flash --}}
            @if(session('success'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-xl border" style="background:var(--success-0);border-color:var(--success-50);color:var(--success-300);">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Page heading --}}
            <div class="mb-6">
                <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Keperluan Perusahaan</h1>
                <p class="text-sm mt-1" style="color:var(--grey-500);">Informasi persuratan dan panduan kebutuhan dokumen untuk instansi KP Anda.</p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- KIRI: Persuratan + Editor --}}
                <div class="xl:col-span-2 space-y-6">

                    {{-- SECTION 1 — Informasi Persuratan --}}
                    <div class="sikape-card p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-base font-semibold" style="color:var(--grey-800);">Informasi Persuratan KP</h2>
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full" style="background:var(--warning-50);color:var(--warning-300);">Pra KP</span>
                        </div>

                        {{-- Alert penting --}}
                        <div class="flex gap-3 p-4 rounded-xl mb-5" style="background:var(--warning-0);border:1px solid var(--warning-50);">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:var(--warning-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div>
                                <p class="text-sm font-semibold" style="color:var(--warning-300);">Perhatian</p>
                                <p class="text-xs mt-0.5" style="color:#92400e;">Surat pengantar harus diajukan minimal <strong>2 minggu</strong> sebelum tanggal mulai KP yang direncanakan.</p>
                            </div>
                        </div>

                        {{-- Daftar dokumen --}}
                        <h3 class="text-sm font-semibold mb-3" style="color:var(--grey-700);">Dokumen yang Diperlukan Perusahaan</h3>
                        <div class="space-y-3 mb-5">
                            @php
                                $docs = [
                                    ['icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','title'=>'Surat Pengantar Departemen','desc'=>'Dikeluarkan oleh TU Departemen Teknik Komputer, ditandatangani Kepala Departemen.','badge'=>'Wajib','badge_style'=>'background:#fce7f3;color:#be185d;'],
                                    ['icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z','title'=>'Proposal KP','desc'=>'Berisi rencana topik, timeline, dan tujuan kegiatan KP. Bisa dibuat di bawah.','badge'=>'Wajib','badge_style'=>'background:#fce7f3;color:#be185d;'],
                                    ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','title'=>'Transkrip Nilai Terakhir','desc'=>'Print transkip resmi yang dicetak dari sistem akademik, minimum 80 SKS.','badge'=>'Wajib','badge_style'=>'background:#fce7f3;color:#be185d;'],
                                    ['icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','title'=>'CV / Curriculum Vitae','desc'=>'Dokumen opsional namun sangat dianjurkan oleh sebagian besar instansi.','badge'=>'Opsional','badge_style'=>'background:var(--grey-100);color:var(--grey-500);'],
                                ];
                            @endphp
                            @foreach($docs as $doc)
                            <div class="flex items-start gap-4 p-4 rounded-xl border transition-all hover:border-indigo-200" style="border-color:var(--grey-100);">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--primary-50);">
                                    <svg class="w-5 h-5" style="color:var(--primary-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $doc['icon'] }}"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-sm font-semibold" style="color:var(--grey-800);">{{ $doc['title'] }}</p>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="{{ $doc['badge_style'] }}">{{ $doc['badge'] }}</span>
                                    </div>
                                    <p class="text-xs" style="color:var(--grey-500);">{{ $doc['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Pengumuman dari Koordinator --}}
                        @if($infoPersuratan->isNotEmpty())
                        <h3 class="text-sm font-semibold mb-3" style="color:var(--grey-700);">Pengumuman Koordinator</h3>
                        <div class="space-y-2">
                            @foreach($infoPersuratan as $info)
                            <div class="p-3 rounded-lg border-l-4" style="background:var(--grey-50);border-left-color:var(--primary-500);">
                                <p class="text-sm font-medium" style="color:var(--grey-800);">{{ $info->judul }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--grey-500);">{{ Str::limit($info->konten, 120) }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- SECTION 2 — Rich Text Editor --}}
                    <div class="sikape-card overflow-hidden" x-data="proposalEditor()">
                        {{-- Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b" style="border-color:var(--grey-100);">
                            <div>
                                <h2 class="text-base font-semibold" style="color:var(--grey-800);">Membuat Proposal KP</h2>
                                <p class="text-xs mt-0.5" style="color:var(--grey-400);">
                                    <span x-show="saved" class="text-green-600 font-medium">✓ Tersimpan otomatis</span>
                                    <span x-show="!saved" style="color:var(--grey-400);">Draft tidak tersimpan</span>
                                    &nbsp;·&nbsp; <span x-text="lastEdited"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" style="background:var(--grey-100);color:var(--grey-500);">Draft</span>
                                <button @click="handlePreview()" class="sikape-btn-ghost text-sm py-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Preview
                                </button>
                            </div>
                        </div>

                        {{-- Meta fields --}}
                        <div class="px-5 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4">
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color:var(--grey-600);">Judul Proposal</label>
                                <input type="text" x-model="title" placeholder="Masukkan judul proposal KP..." class="w-full rounded-lg px-3 py-2 text-sm border focus:outline-none focus:ring-2 focus:ring-indigo-500" style="border-color:var(--grey-200);background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color:var(--grey-600);">Nama Instansi / Perusahaan</label>
                                <input type="text" x-model="company" placeholder="Contoh: PT Telkom Indonesia..." class="w-full rounded-lg px-3 py-2 text-sm border focus:outline-none focus:ring-2 focus:ring-indigo-500" style="border-color:var(--grey-200);background:#fff;">
                            </div>
                        </div>

                        {{-- Quill Editor --}}
                        <div class="px-5 pb-2">
                            <label class="block text-xs font-semibold mb-1.5" style="color:var(--grey-600);">Isi Proposal</label>
                            <div id="proposal-editor" style="min-height:280px;"></div>
                        </div>

                        {{-- Footer actions --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t" style="border-color:var(--grey-100);">
                            <p class="text-xs" style="color:var(--grey-400);" x-text="charCount + ' karakter'"></p>
                            <div class="flex gap-2">
                                <button @click="saveDraft()" class="sikape-btn-ghost text-sm py-2 px-4">Simpan Draft</button>
                                <button class="sikape-btn-primary text-sm py-2 px-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Export Proposal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KANAN: Download cards --}}
                <div class="space-y-5">
                    <div class="sikape-card p-5">
                        <h2 class="text-sm font-semibold mb-4" style="color:var(--grey-800);">Unduh Dokumen</h2>
                        <div class="space-y-3">
                            @php
                                $downloads = [
                                    ['type'=>'laporan','label'=>'Template Laporan KP','ext'=>'.docx','size'=>'245 KB','color'=>'#2563eb','bg'=>'#eff6ff'],
                                    ['type'=>'makalah','label'=>'Template Makalah IEEE','ext'=>'.docx','size'=>'187 KB','color'=>'#7c3aed','bg'=>'#f5f3ff'],
                                    ['type'=>'a2','label'=>'Form A2 Logbook','ext'=>'.pdf','size'=>'120 KB','color'=>'#059669','bg'=>'#ecfdf5'],
                                    ['type'=>'b1','label'=>'Permohonan Seminar B1','ext'=>'.pdf','size'=>'98 KB','color'=>'#d97706','bg'=>'#fffbeb'],
                                    ['type'=>'b2','label'=>'Form Kehadiran B2','ext'=>'.pdf','size'=>'88 KB','color'=>'#dc2626','bg'=>'#fff1f2'],
                                ];
                            @endphp
                            @foreach($downloads as $dl)
                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', $dl['type']) }}"
                               class="flex items-center gap-3 p-3 rounded-xl border transition-all hover:shadow-sm"
                               style="border-color:var(--grey-100);">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $dl['bg'] }};">
                                    <svg class="w-4 h-4" style="color:{{ $dl['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold truncate" style="color:var(--grey-800);">{{ $dl['label'] }}</p>
                                    <p class="text-[10px] mt-0.5" style="color:var(--grey-400);">{{ $dl['ext'] }} · {{ $dl['size'] }}</p>
                                </div>
                                <svg class="w-4 h-4 flex-shrink-0" style="color:var(--grey-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="rounded-xl p-5 border" style="background:var(--primary-50);border-color:var(--primary-100);">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" style="color:var(--primary-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold" style="color:var(--primary-500);">Butuh bantuan?</p>
                        </div>
                        <p class="text-xs" style="color:#4338ca;">Hubungi Koordinator KP atau kunjungi tata usaha departemen pada jam kerja untuk meminta surat pengantar resmi.</p>
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
        title: '',
        company: '',
        charCount: 0,
        saved: false,
        lastEdited: 'Belum ada perubahan',
        quill: null,
        init() {
            this.quill = new Quill('#proposal-editor', {
                theme: 'snow',
                placeholder: 'Tuliskan latar belakang, tujuan, rencana kegiatan, dan jadwal KP Anda di sini...',
                modules: {
                    toolbar: [
                        [{ heading: [1,2,3,false] }],
                        ['bold','italic','underline'],
                        [{ list:'ordered' },{ list:'bullet' }],
                        [{ align: [] }],
                        ['link','image'],
                        ['clean']
                    ]
                }
            });
            this.quill.on('text-change', () => {
                this.charCount = this.quill.getText().trim().length;
                this.saved = false;
                this.lastEdited = 'Baru saja diedit';
            });
        },
        saveDraft() {
            localStorage.setItem('sikape_proposal_draft', JSON.stringify({
                title: this.title,
                company: this.company,
                content: this.quill.root.innerHTML
            }));
            this.saved = true;
            this.lastEdited = new Date().toLocaleTimeString('id-ID');
        },
        handlePreview() {
            alert('Fitur preview akan membuka jendela baru dengan tampilan proposal. (Akan diimplementasikan dengan library PDF)');
        }
    }
}
</script>
</body>
</html>
