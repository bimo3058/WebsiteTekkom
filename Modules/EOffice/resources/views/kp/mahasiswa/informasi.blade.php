<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Dokumen Perusahaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
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
        }
        .sikape-card { background:#fff; border:1px solid var(--grey-200); border-radius:12px; }
        .sikape-btn-primary { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:8px 16px; background:var(--primary-500); color:#fff; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-primary:hover { background:#4338ca; }
        .sikape-btn-primary:disabled { opacity:0.6; cursor:not-allowed; }
        .sikape-btn-outline { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:8px 16px; background:transparent; color:var(--grey-700); border-radius:8px; font-size:14px; font-weight:600; border:1.5px solid var(--grey-200); cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-outline:hover { background:var(--grey-50); border-color:var(--grey-300); }
        .sikape-btn-ghost { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:transparent; color:var(--grey-600); border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
        .sikape-btn-ghost:hover { background:var(--grey-100); }
        .form-input { w-full; rounded-lg; px-3; py-2; text-sm; border: 1px solid var(--grey-200); background:#fff; }
        .form-input:focus { outline:none; border-color:var(--primary-500); box-shadow:0 0 0 2px var(--primary-100); }
        .ql-toolbar { border-radius:8px 8px 0 0 !important; border-color:var(--grey-200) !important; background: var(--grey-50); }
        .ql-container { border-radius:0 0 8px 8px !important; border-color:var(--grey-200) !important; font-size:15px; min-height:400px; background: #fff; }
        
        /* A4 WYSIWYG Styling */
        .a4-editor-wrapper {
            width: 100%;
            overflow-x: auto;
            background: var(--grey-100);
            padding: 24px;
            border-radius: 8px;
            border: 1px solid var(--grey-200);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .a4-toolbar-container {
            width: 21cm;
            max-width: 100%;
            background: #fff;
            border: 1px solid var(--grey-300);
            border-bottom: none;
        }
        #proposal-editor {
            width: 21cm;
            min-height: 29.7cm;
            padding: 4cm 3cm 3cm 4cm; /* Top Right Bottom Left */
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--grey-300) !important;
            border-radius: 0 !important;
            font-family: 'Times New Roman', Times, serif; /* Sesuai standar tulisan ilmiah */
            font-size: 12pt;
            line-height: 1.5;
        }
        @media (max-width: 21cm) {
            .a4-editor-wrapper { align-items: flex-start; }
        }
        
        /* Stepper Styles */
        .step-item { flex: 1; text-align: center; position: relative; }
        .step-item:not(:last-child)::after { content: ''; position: absolute; top: 14px; left: 50%; width: 100%; height: 2px; background: var(--grey-200); z-index: 0; }
        .step-item.active:not(:last-child)::after, .step-item.completed:not(:last-child)::after { background: var(--primary-500); }
        .step-circle { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin: 0 auto 8px; position: relative; z-index: 1; transition: all 0.3s; }
        .step-item.active .step-circle { background: var(--primary-500); color: white; border: 2px solid var(--primary-500); box-shadow: 0 0 0 4px var(--primary-50); }
        .step-item.completed .step-circle { background: white; color: var(--primary-500); border: 2px solid var(--primary-500); }
        .step-item.pending .step-circle { background: white; color: var(--grey-400); border: 2px solid var(--grey-200); }
    </style>
</head>
<body class="bg-grey-50" style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', [
            'breadcrumbs' => ['Informasi', 'Keperluan Perusahaan']
        ])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" x-data="documentManager({
            defaultPerusahaan: '{{ $kp ? ($kp->tempat_fix ?? ($kp->rencana_tempat ?? '')) : '' }}'
        })">

            {{-- VIEWS --}}
            <template x-if="view === 'list'">
                <div>
                    {{-- Page heading --}}
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold" style="color:var(--grey-900);">Dokumen Perusahaan</h1>
                        <p class="text-sm mt-1" style="color:var(--grey-500);">Panduan dan pembuatan dokumen untuk keperluan pengajuan KP ke instansi.</p>
                    </div>

                    <div class="space-y-8">
                        
                        {{-- Surat Pengantar Section --}}
                        <div class="sikape-card p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <h2 class="text-lg font-bold text-grey-900">1. Surat Pengantar Departemen</h2>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">Wajib</span>
                                    </div>
                                    <p class="text-sm text-grey-500 leading-relaxed max-w-2xl">Dikeluarkan resmi oleh Departemen Teknik Komputer. Surat ini berisi permohonan agar mahasiswa dapat diterima KP di instansi yang dituju.</p>
                                </div>
                                <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'surat_pengantar') }}" class="sikape-btn-primary bg-indigo-600 hover:bg-indigo-700 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Template Surat
                                </a>
                            </div>
                        </div>

                        {{-- Proposal KP Section --}}
                        <div class="sikape-card p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <h2 class="text-lg font-bold text-grey-900">2. Proposal KP</h2>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">Wajib</span>
                                    </div>
                                    <p class="text-sm text-grey-500 leading-relaxed max-w-2xl">Berisi latar belakang, rencana topik, dan timeline kegiatan KP. Dokumen ini dilampirkan bersama surat pengantar.</p>
                                </div>
                                <button @click="buatProposal()" class="sikape-btn-primary bg-emerald-600 hover:bg-emerald-700 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Buat Proposal
                                </button>
                            </div>

                            {{-- History Proposal --}}
                            <div class="border rounded-xl border-grey-200 overflow-hidden">
                                <div class="bg-grey-50 px-4 py-3 border-b border-grey-200">
                                    <h3 class="text-xs font-bold text-grey-600 uppercase tracking-wider">Riwayat Pembuatan</h3>
                                </div>
                                <div class="p-4" x-show="proposalHistory.length === 0">
                                    <p class="text-sm text-grey-400 text-center italic">Belum ada riwayat pembuatan proposal.</p>
                                </div>
                                <ul class="divide-y divide-grey-100" x-show="proposalHistory.length > 0">
                                    <template x-for="item in proposalHistory" :key="item.id">
                                        <li class="p-4 hover:bg-grey-50 transition-colors flex flex-col sm:flex-row items-center justify-between gap-4">
                                            <div>
                                                <h4 class="text-sm font-bold text-grey-800" x-text="item.judul || 'Draft Tanpa Judul'"></h4>
                                                <p class="text-xs text-grey-500 mt-1" x-text="item.instansi || 'Tanpa Instansi'"></p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="editProposal(item)" class="sikape-btn-outline text-xs py-1.5 px-3">Edit</button>
                                                <button @click="hapusProposal(item.id)" class="sikape-btn-ghost text-red-500 hover:text-red-700 hover:bg-red-50 text-xs py-1.5 px-3">Hapus</button>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        {{-- Presensi KP (A2) Section --}}
                        <div class="sikape-card p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <h2 class="text-lg font-bold text-grey-900">3. Presensi KP (A2)</h2>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">Wajib</span>
                                    </div>
                                    <p class="text-sm text-grey-500 leading-relaxed max-w-2xl">Formulir kehadiran dan penilaian lapangan kerja praktik. Dokumen ini digenerate secara dinamis dari template yang diunggah oleh Koordinator.</p>
                                </div>
                                <button @click="modalA2Open = true" class="sikape-btn-primary bg-amber-600 hover:bg-amber-700 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Buat Absensi (A2)
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </template>


            {{-- PROPOSAL KP STEPPER --}}
            <template x-if="view === 'formProposal'">
                <div class="max-w-4xl mx-auto">
                    {{-- Header & Stepper --}}
                    <div class="mb-8">
                        <button @click="view = 'list'; simpanProposal()" class="text-sm font-semibold text-grey-500 hover:text-grey-800 flex items-center gap-1 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Kembali ke Daftar Dokumen
                        </button>
                        <h1 class="text-2xl font-bold text-grey-900 mb-6">Pembuatan Proposal KP</h1>
                        
                        <div class="flex items-center justify-between max-w-lg mx-auto mb-8">
                            <div class="step-item" :class="{ 'completed': proposalStep > 1, 'active': proposalStep === 1, 'pending': proposalStep < 1 }">
                                <div class="step-circle">1</div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Informasi Dasar</span>
                            </div>
                            <div class="step-item" :class="{ 'completed': proposalStep > 2, 'active': proposalStep === 2, 'pending': proposalStep < 2 }">
                                <div class="step-circle">2</div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Editor Isi</span>
                            </div>
                            <div class="step-item" :class="{ 'completed': proposalStep > 3, 'active': proposalStep === 3, 'pending': proposalStep < 3 }">
                                <div class="step-circle">3</div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Review & Unduh</span>
                            </div>
                        </div>
                    </div>

                    <div class="sikape-card overflow-hidden shadow-sm">
                        
                        {{-- Step 1: Info Dasar Proposal --}}
                        <div x-show="proposalStep === 1" class="p-6 md:p-8">
                            <h2 class="text-lg font-bold text-grey-800 border-b border-grey-100 pb-3 mb-6">Detail Dokumen Proposal</h2>
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1.5">Judul Proposal <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="proposalForm.judul" class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-grey-50" placeholder="Rencana Kerja Praktik di..." style="border-color:var(--grey-200);">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1.5">Nama Instansi <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="proposalForm.instansi" class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-grey-50" placeholder="Contoh: PT ABC..." style="border-color:var(--grey-200);">
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Editor --}}
                        <div x-show="proposalStep === 2" class="p-6" style="display: none;">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-bold text-grey-800">Isi Proposal</h2>
                                <button @click="loadProposalTemplate()" class="sikape-btn-outline text-xs py-1.5 px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Muat Struktur Template
                                </button>
                            </div>
                            <div class="a4-editor-wrapper">
                                <div id="proposal-editor" style="background:#fff;"></div>
                            </div>
                        </div>

                        {{-- Step 3: Review --}}
                        <div x-show="proposalStep === 3" class="p-6 md:p-8" style="display: none;">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4 text-green-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h2 class="text-xl font-bold text-grey-900">Proposal Siap Diekspor</h2>
                                <p class="text-sm text-grey-500 mt-2">Draf telah tersimpan otomatis di perangkat Anda. Silakan unduh dokumen dalam format yang Anda butuhkan.</p>
                            </div>

                            <div class="bg-indigo-50 rounded-xl p-6 flex flex-col sm:flex-row gap-4 items-center justify-center max-w-lg mx-auto border border-indigo-100">
                                <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="format" value="pdf">
                                    <input type="hidden" name="judul" :value="proposalForm.judul">
                                    <input type="hidden" name="instansi" :value="proposalForm.instansi">
                                    <input type="hidden" name="content" :value="proposalForm.content">
                                    <button type="submit" class="sikape-btn-primary w-full bg-red-600 hover:bg-red-700 py-3 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Unduh PDF
                                    </button>
                                </form>

                                <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="format" value="word">
                                    <input type="hidden" name="judul" :value="proposalForm.judul">
                                    <input type="hidden" name="instansi" :value="proposalForm.instansi">
                                    <input type="hidden" name="content" :value="proposalForm.content">
                                    <button type="submit" class="sikape-btn-primary w-full bg-blue-600 hover:bg-blue-700 py-3 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Unduh Word
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Footer Actions Stepper --}}
                        <div class="px-6 py-4 border-t border-grey-100 bg-grey-50 flex items-center justify-between">
                            <button @click="goToProposalStep(proposalStep - 1); simpanProposal()" x-show="proposalStep > 1" class="sikape-btn-outline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Kembali
                            </button>
                            <div x-show="proposalStep === 1"></div> {{-- Placeholder --}}

                            <button @click="goToProposalStep(proposalStep + 1); simpanProposal()" x-show="proposalStep < 3" class="sikape-btn-primary bg-grey-900 hover:bg-grey-800 px-6">
                                Lanjut
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <button @click="view = 'list'; simpanProposal()" x-show="proposalStep === 3" class="sikape-btn-outline">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Modal Buat Absensi (A2) -->
            <div x-show="modalA2Open" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" style="display: none;">
                <div x-show="modalA2Open" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                <div x-show="modalA2Open" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     @click.away="modalA2Open = false"
                     class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-100 z-10">
                    
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Generate Presensi KP (A2)
                        </h3>
                        <button @click="modalA2Open = false" class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    @if(!file_exists(storage_path('app/templates/form_a2.docx')))
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">Template A2 Belum Tersedia</h4>
                            <p class="text-xs text-slate-500 mt-2">Koordinator KP belum mengunggah template Form A2 (.docx). Silakan hubungi Koordinator KP Anda.</p>
                            <div class="mt-6 flex justify-end">
                                <button type="button" @click="modalA2Open = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">Tutup</button>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('eoffice.kp.mahasiswa.dokumen.generate_a2') }}" method="POST">
                            @csrf
                            <div class="p-6 space-y-4">
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Lengkapi data di bawah ini untuk mengisi variabel pada template Word **Presensi KP (A2)** secara otomatis.
                                </p>
                                
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nama Pembimbing Lapangan <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_pembimbing" x-model="a2Form.nama_pembimbing" required placeholder="Contoh: John Doe, S.T."
                                               class="w-full rounded-xl border border-slate-200 py-2.5 px-4 text-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">NIP / ID Pembimbing Lapangan <span class="text-red-500">*</span></label>
                                        <input type="text" name="nip_pembimbing" x-model="a2Form.nip_pembimbing" required placeholder="Contoh: 1987654321 atau -"
                                               class="w-full rounded-xl border border-slate-200 py-2.5 px-4 text-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Jabatan Pembimbing Lapangan <span class="text-red-500">*</span></label>
                                        <input type="text" name="jabatan_pembimbing" x-model="a2Form.jabatan_pembimbing" required placeholder="Contoh: Senior Software Engineer"
                                               class="w-full rounded-xl border border-slate-200 py-2.5 px-4 text-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nama Perusahaan / Instansi <span class="text-red-500">*</span></label>
                                        <input type="text" name="perusahaan" x-model="a2Form.perusahaan" required placeholder="Contoh: PT Technology Indonesia"
                                               class="w-full rounded-xl border border-slate-200 py-2.5 px-4 text-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex gap-3 justify-end">
                                <button type="button" @click="modalA2Open = false" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl shadow-sm shadow-amber-200 transition-colors">Unduh Dokumen A2</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>

<script>
function documentManager(params = {}) {
    return {
        view: 'list', // 'list', 'formSurat', 'formProposal'
        modalA2Open: false,
        a2Form: {
            nama_pembimbing: '',
            nip_pembimbing: '',
            jabatan_pembimbing: '',
            perusahaan: params.defaultPerusahaan || ''
        },


        // Proposal State
        proposalStep: 1, 
        proposalHistory: [],
        proposalForm: {
            id: null,
            judul: '',
            instansi: '',
            content: ''
        },
        quill: null,
        proposalTemplate: {!! json_encode($templateContent ?? '') !!},

        init() {
            this.loadHistory();
        },
        
        loadHistory() {
            try {
                this.proposalHistory = JSON.parse(localStorage.getItem('sikape_proposal_history')) || [];
            } catch(e) {
                this.proposalHistory = [];
            }
        },

        saveProposalHistory() {
            localStorage.setItem('sikape_proposal_history', JSON.stringify(this.proposalHistory));
        },



        // PROPOSAL ACTIONS
        buatProposal() {
            this.proposalForm = {
                id: Date.now(),
                judul: '',
                instansi: '',
                content: this.proposalTemplate
            };
            this.proposalStep = 1;
            this.view = 'formProposal';
        },

        editProposal(item) {
            this.proposalForm = JSON.parse(JSON.stringify(item));
            this.proposalStep = 1;
            this.view = 'formProposal';
        },

        hapusProposal(id) {
            if(confirm('Hapus riwayat pembuatan proposal ini?')) {
                this.proposalHistory = this.proposalHistory.filter(i => i.id !== id);
                this.saveProposalHistory();
            }
        },

        simpanProposal() {
            if (this.quill && this.proposalStep >= 2) {
                this.proposalForm.content = this.quill.root.innerHTML;
            }
            const idx = this.proposalHistory.findIndex(i => i.id === this.proposalForm.id);
            if (idx >= 0) {
                this.proposalHistory[idx] = JSON.parse(JSON.stringify(this.proposalForm));
            } else {
                this.proposalHistory.push(JSON.parse(JSON.stringify(this.proposalForm)));
            }
            this.saveProposalHistory();
        },

        goToProposalStep(step) {
            this.proposalStep = step;
            if (step === 2) {
                setTimeout(() => {
                    if (!this.quill) {
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
                            this.proposalForm.content = this.quill.root.innerHTML;
                        });
                    }
                    if (this.proposalForm.content) {
                        this.quill.root.innerHTML = this.proposalForm.content;
                    }
                }, 100);
            } else if (step === 3 && this.quill) {
                // Pastikan data tersimpan saat lanjut ke review
                this.proposalForm.content = this.quill.root.innerHTML;
            }
        },

        loadProposalTemplate() {
            if(confirm("Memuat struktur template akan mengganti isi saat ini. Lanjutkan?")) {
                if (this.quill) {
                    this.quill.root.innerHTML = this.proposalTemplate;
                    this.proposalForm.content = this.proposalTemplate;
                }
            }
        }
    }
}
</script>
</body>
</html>
