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
        .form-input { width:100%; border-radius:8px; padding:8px 12px; font-size:14px; border: 1px solid var(--grey-200); background:#fff; }
        .form-input:focus { outline:none; border-color:var(--primary-500); box-shadow:0 0 0 2px var(--primary-100); }
        
        /* History card hover */
        .proposal-history-item { transition: box-shadow .15s, background .15s; }
        .proposal-history-item:hover { background: #f8faff; box-shadow: 0 2px 8px 0 rgba(79,70,229,.07); }
        
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
            'breadcrumbs' => ['Informasi', 'Proposal KP']
        ])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" x-data="proposalManager({
            defaultPerusahaan: '{{ $kp ? ($kp->tempat_fix ?? ($kp->rencana_tempat ?? "")) : "" }}'
        })">

            {{-- VIEWS --}}
            <template x-if="view === 'list'">
                <div>
                    {{-- Page heading --}}
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold" style="color:var(--grey-900);">Proposal KP</h1>
                        <p class="text-sm mt-1" style="color:var(--grey-500);">Buat, kelola, dan unduh proposal rencana kegiatan Kerja Praktik Anda.</p>
                    </div>

                    <div class="space-y-8">
                        
                        {{-- Proposal KP Section --}}
                        <div class="sikape-card p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <h2 class="text-lg font-bold text-grey-900">Pembuatan Proposal</h2>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">Wajib</span>
                                    </div>
                                    <p class="text-sm text-grey-500 leading-relaxed max-w-2xl">Lengkapi form identitas, data perusahaan, serta isi latar belakang hingga rencana kegiatan untuk menggenerate proposal resmi.</p>
                                </div>
                                <button @click="buatProposal()" class="sikape-btn-primary bg-emerald-600 hover:bg-emerald-700 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Buat Proposal
                                </button>
                            </div>

                            {{-- History Proposal --}}
                            <div class="border rounded-xl border-grey-200 overflow-hidden">
                                <div class="bg-grey-50 px-4 py-3 border-b border-grey-200 flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-grey-600 uppercase tracking-wider">Riwayat Pembuatan</h3>
                                    <span class="text-xs text-grey-400" x-show="proposalHistory.length > 0">
                                        <span x-text="proposalHistory.length"></span> proposal tersimpan
                                    </span>
                                </div>

                                {{-- Empty State --}}
                                <div class="py-10 px-4 text-center" x-show="proposalHistory.length === 0">
                                    <svg class="w-10 h-10 mx-auto text-grey-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm text-grey-400 italic">Belum ada riwayat. Klik <strong>Buat Proposal</strong> untuk mulai.</p>
                                </div>

                                {{-- History Items --}}
                                <ul class="divide-y divide-grey-100" x-show="proposalHistory.length > 0">
                                    <template x-for="item in [...proposalHistory].reverse()" :key="item.id">
                                        <li class="proposal-history-item p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            {{-- Info --}}
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-600 mt-0.5">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-bold text-grey-800 truncate" x-text="item.judul || 'Draft Tanpa Judul'"></h4>
                                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                                        <span class="text-xs text-grey-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                            <span x-text="item.instansi || '-'"></span>
                                                        </span>
                                                        <template x-if="item.nim">
                                                            <span class="text-xs text-grey-400 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                                <span x-text="item.nim"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="item.tgl_mulai && item.tgl_selesai">
                                                            <span class="text-xs text-grey-400 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <span x-text="item.tgl_mulai + ' — ' + item.tgl_selesai"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <p class="text-[10px] text-grey-400 mt-1.5" x-show="item.diperbarui_pada">
                                                        Terakhir diperbarui: <span x-text="formatTanggal(item.diperbarui_pada)"></span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <button @click="editProposal(item)" class="sikape-btn-outline text-xs py-1.5 px-3">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                <button @click="generateUlang(item)" class="sikape-btn-primary text-xs py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Unduh
                                                </button>
                                                <button @click="hapusProposal(item.id)" class="sikape-btn-ghost text-red-500 hover:text-red-700 hover:bg-red-50 text-xs py-1.5 px-3">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </template>


            {{-- PROPOSAL KP STEPPER (Form-Based, No Rich Text Editor) --}}
            <template x-if="view === 'formProposal'">
                <div class="max-w-4xl mx-auto">
                    {{-- Header & Stepper --}}
                    <div class="mb-8">
                        <button @click="batalProposal()" class="text-sm font-semibold text-grey-500 hover:text-grey-800 flex items-center gap-1 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Kembali ke Daftar Dokumen
                        </button>
                        <h1 class="text-2xl font-bold text-grey-900 mb-6">
                            <span x-text="proposalForm.id && proposalHistory.find(i => i.id === proposalForm.id) ? 'Edit Proposal KP' : 'Buat Proposal KP'"></span>
                        </h1>

                        {{-- Stepper --}}
                        <div class="flex items-center justify-between max-w-lg mx-auto mb-8">
                            <div class="step-item" :class="{ 'completed': proposalStep > 1, 'active': proposalStep === 1, 'pending': proposalStep < 1 }">
                                <div class="step-circle">
                                    <template x-if="proposalStep > 1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                                    <template x-if="proposalStep <= 1"><span>1</span></template>
                                </div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Identitas & Instansi</span>
                            </div>
                            <div class="step-item" :class="{ 'completed': proposalStep > 2, 'active': proposalStep === 2, 'pending': proposalStep < 2 }">
                                <div class="step-circle">
                                    <template x-if="proposalStep > 2"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                                    <template x-if="proposalStep <= 2"><span>2</span></template>
                                </div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Isi Proposal</span>
                            </div>
                            <div class="step-item" :class="{ 'completed': proposalStep > 3, 'active': proposalStep === 3, 'pending': proposalStep < 3 }">
                                <div class="step-circle">3</div>
                                <span class="text-[11px] font-semibold text-grey-600 mt-2 block">Review & Unduh</span>
                            </div>
                        </div>
                    </div>

                    <div class="sikape-card overflow-hidden shadow-sm">

                        {{-- ===== STEP 1: Identitas & Data Instansi ===== --}}
                        <div x-show="proposalStep === 1" class="p-6 md:p-8">
                            <h2 class="text-base font-bold text-grey-800 border-b border-grey-100 pb-3 mb-6 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">1</span>
                                Identitas Mahasiswa & Data Instansi
                            </h2>

                            <div class="space-y-6">
                                {{-- Judul Proposal --}}
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1.5">Judul / Topik Proposal <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="proposalForm.judul" required
                                        class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                        placeholder="Contoh: Rencana Kerja Praktik di PT. ABC Indonesia — Divisi IT"
                                        style="border-color:var(--grey-200); background:#fdfdff;">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- NIM --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">NIM <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="proposalForm.nim" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Nomor Induk Mahasiswa"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Semester --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Semester <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="proposalForm.semester" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Contoh: 6 (Enam)"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Nama Instansi --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Nama Instansi / Perusahaan <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="proposalForm.instansi" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Contoh: PT. ABC Indonesia"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Bidang / Divisi --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Bidang / Divisi yang Dituju</label>
                                        <input type="text" x-model="proposalForm.bidang"
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Contoh: Divisi Teknologi Informasi"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Alamat Instansi --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Alamat Lengkap Instansi <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="proposalForm.alamat_instansi" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Jl. Sudirman No. 10, Jakarta Pusat, DKI Jakarta"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Tanggal Mulai --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                                        <input type="date" x-model="proposalForm.tgl_mulai" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                    {{-- Tanggal Selesai --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                                        <input type="date" x-model="proposalForm.tgl_selesai" required
                                            class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            style="border-color:var(--grey-200); background:#fdfdff;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== STEP 2: Isi Proposal ===== --}}
                        <div x-show="proposalStep === 2" class="p-6 md:p-8" style="display:none;">
                            <h2 class="text-base font-bold text-grey-800 border-b border-grey-100 pb-3 mb-6 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">2</span>
                                Isi Dokumen Proposal
                            </h2>
                            <p class="text-xs text-grey-500 mb-6 leading-relaxed bg-amber-50 border border-amber-100 rounded-lg px-4 py-3">
                                <strong>ℹ️ Info:</strong> Isi setiap kolom di bawah ini. Sistem akan otomatis menyusun dokumen proposal KP yang terformat sesuai standar departemen.
                            </p>

                            <div class="space-y-6">
                                {{-- Latar Belakang --}}
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1">Latar Belakang <span class="text-red-500">*</span></label>
                                    <p class="text-xs text-grey-400 mb-2">Jelaskan alasan dan motivasi memilih instansi/perusahaan tersebut untuk kegiatan KP.</p>
                                    <textarea x-model="proposalForm.latar_belakang" rows="5" required
                                        class="w-full rounded-lg px-4 py-3 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-y leading-relaxed"
                                        placeholder="Tuliskan latar belakang pemilihan instansi, relevansi dengan bidang studi, dan gambaran umum perusahaan..."
                                        style="border-color:var(--grey-200); background:#fdfdff;"></textarea>
                                </div>

                                {{-- Tujuan --}}
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1">Tujuan KP <span class="text-red-500">*</span></label>
                                    <p class="text-xs text-grey-400 mb-2">Apa yang ingin dicapai dari pelaksanaan Kerja Praktik ini?</p>
                                    <textarea x-model="proposalForm.tujuan" rows="4" required
                                        class="w-full rounded-lg px-4 py-3 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-y leading-relaxed"
                                        placeholder="1. Mendapatkan pengalaman kerja nyata di bidang...
2. Memahami proses bisnis di industri...
3. Menerapkan ilmu yang dipelajari di kampus..."
                                        style="border-color:var(--grey-200); background:#fdfdff;"></textarea>
                                </div>

                                {{-- Manfaat --}}
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1">Manfaat KP</label>
                                    <p class="text-xs text-grey-400 mb-2">Manfaat yang diharapkan bagi mahasiswa, instansi, dan institusi pendidikan.</p>
                                    <textarea x-model="proposalForm.manfaat" rows="4"
                                        class="w-full rounded-lg px-4 py-3 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-y leading-relaxed"
                                        placeholder="Bagi Mahasiswa: ...
Bagi Instansi: ...
Bagi Departemen: ..."
                                        style="border-color:var(--grey-200); background:#fdfdff;"></textarea>
                                </div>

                                {{-- Rencana Kegiatan --}}
                                <div>
                                    <label class="block text-sm font-semibold text-grey-700 mb-1">Rencana Kegiatan / Jadwal</label>
                                    <p class="text-xs text-grey-400 mb-2">Uraikan kegiatan atau jadwal yang direncanakan selama masa KP.</p>
                                    <textarea x-model="proposalForm.kegiatan" rows="5"
                                        class="w-full rounded-lg px-4 py-3 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-y leading-relaxed"
                                        placeholder="Minggu 1-2 : Orientasi dan pengenalan lingkungan kerja
Minggu 3-6 : Pelaksanaan tugas di divisi ...
Minggu 7-8 : Penyusunan laporan akhir KP"
                                        style="border-color:var(--grey-200); background:#fdfdff;"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ===== STEP 3: Review & Unduh ===== --}}
                        <div x-show="proposalStep === 3" class="p-6 md:p-8" style="display:none;">
                            {{-- Success Banner --}}
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4 text-emerald-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h2 class="text-xl font-bold text-grey-900">Proposal Siap Digenerate!</h2>
                                <p class="text-sm text-grey-500 mt-1">Data telah tersimpan dalam riwayat. Klik tombol di bawah untuk mengunduh dokumen.</p>
                            </div>

                            {{-- Summary Card --}}
                            <div class="bg-grey-50 border border-grey-200 rounded-xl p-5 mb-6 space-y-3">
                                <h3 class="text-xs font-bold text-grey-500 uppercase tracking-wider mb-3">Ringkasan Data Proposal</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div><span class="text-grey-500">Judul:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.judul || '-'"></span></div>
                                    <div><span class="text-grey-500">NIM:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.nim || '-'"></span></div>
                                    <div><span class="text-grey-500">Instansi:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.instansi || '-'"></span></div>
                                    <div><span class="text-grey-500">Bidang:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.bidang || '-'"></span></div>
                                    <div><span class="text-grey-500">Mulai:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.tgl_mulai || '-'"></span></div>
                                    <div><span class="text-grey-500">Selesai:</span> <span class="font-semibold text-grey-800" x-text="proposalForm.tgl_selesai || '-'"></span></div>
                                </div>
                            </div>

                            {{-- Export Buttons --}}
                            <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                                <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="format" value="pdf">
                                    <input type="hidden" name="judul" :value="proposalForm.judul">
                                    <input type="hidden" name="instansi" :value="proposalForm.instansi">
                                    <input type="hidden" name="content" :value="buildProposalContent()">
                                    <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Generate & Unduh PDF
                                    </button>
                                </form>

                                <form action="{{ route('eoffice.kp.mahasiswa.proposal.export') }}" method="POST" target="_blank" class="w-full sm:w-auto">
                                    @csrf
                                    <input type="hidden" name="format" value="word">
                                    <input type="hidden" name="judul" :value="proposalForm.judul">
                                    <input type="hidden" name="instansi" :value="proposalForm.instansi">
                                    <input type="hidden" name="content" :value="buildProposalContent()">
                                    <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Generate & Unduh Word
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Footer Navigation --}}
                        <div class="px-6 py-4 border-t border-grey-100 bg-grey-50 flex items-center justify-between">
                            {{-- Kembali --}}
                            <button @click="proposalStep > 1 ? proposalStep-- : batalProposal()" class="sikape-btn-outline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span x-text="proposalStep === 1 ? 'Batal' : 'Kembali'"></span>
                            </button>

                            {{-- Lanjut --}}
                            <template x-if="proposalStep < 3">
                                <button @click="lanjutProposalStep()" class="sikape-btn-primary bg-indigo-600 hover:bg-indigo-700 px-6">
                                    <span x-text="proposalStep === 2 ? 'Simpan & Lanjut' : 'Lanjut'"></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </template>

                            {{-- Selesai (Step 3) --}}
                            <template x-if="proposalStep === 3">
                                <button @click="view = 'list'" class="sikape-btn-outline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

        </main>
    </div>
</div>

<script>
function proposalManager(params = {}) {
    return {
        view: 'list',

        // ── Proposal State ────────────────────────────────────────────────────
        proposalStep: 1,
        proposalHistory: [],
        proposalForm: {
            id: null,
            judul: '',
            nim: '{{ $mahasiswa->nim ?? "" }}',
            semester: '',
            instansi: params.defaultPerusahaan || '',
            bidang: '',
            alamat_instansi: '',
            tgl_mulai: '',
            tgl_selesai: '',
            latar_belakang: '',
            tujuan: '',
            manfaat: '',
            kegiatan: '',
            dibuat_pada: null,
            diperbarui_pada: null
        },

        // ── Init ──────────────────────────────────────────────────────────────
        init() {
            this.loadHistory();
        },

        // ── History (localStorage) ────────────────────────────────────────────
        loadHistory() {
            try {
                this.proposalHistory = JSON.parse(localStorage.getItem('sikape_proposal_history_v2')) || [];
            } catch(e) {
                this.proposalHistory = [];
            }
        },

        saveProposalHistory() {
            localStorage.setItem('sikape_proposal_history_v2', JSON.stringify(this.proposalHistory));
        },

        formatTanggal(iso) {
            if (!iso) return '-';
            const d = new Date(iso);
            return d.toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        },

        // ── Build HTML content to pass to export route ────────────────────────
        buildProposalContent() {
            const f = this.proposalForm;
            const durasi = (f.tgl_mulai && f.tgl_selesai)
                ? `${f.tgl_mulai} s.d. ${f.tgl_selesai}`
                : (f.tgl_mulai || '');

            return `
<h1 style="text-align:center;font-size:14pt;font-weight:bold;margin-bottom:4pt;">PROPOSAL KERJA PRAKTIK</h1>
<h2 style="text-align:center;font-size:13pt;font-weight:bold;margin-bottom:20pt;">${f.judul || ''}</h2>

<table style="width:100%;border-collapse:collapse;margin-bottom:16pt;font-size:11pt;">
  <tr><td style="width:40%;padding:3pt 0;"><strong>Nama Instansi</strong></td><td style="width:5%;">:</td><td>${f.instansi || ''}</td></tr>
  <tr><td style="padding:3pt 0;"><strong>Alamat Instansi</strong></td><td>:</td><td>${f.alamat_instansi || ''}</td></tr>
  <tr><td style="padding:3pt 0;"><strong>Bidang / Divisi</strong></td><td>:</td><td>${f.bidang || '-'}</td></tr>
  <tr><td style="padding:3pt 0;"><strong>NIM Mahasiswa</strong></td><td>:</td><td>${f.nim || ''}</td></tr>
  <tr><td style="padding:3pt 0;"><strong>Semester</strong></td><td>:</td><td>${f.semester || ''}</td></tr>
  <tr><td style="padding:3pt 0;"><strong>Rencana Pelaksanaan</strong></td><td>:</td><td>${durasi}</td></tr>
</table>

<h3 style="font-size:12pt;font-weight:bold;margin-top:16pt;margin-bottom:8pt;">I. Latar Belakang</h3>
<p style="text-align:justify;line-height:1.8;font-size:12pt;">${(f.latar_belakang || '').replace(/\n/g, '<br>')}</p>

<h3 style="font-size:12pt;font-weight:bold;margin-top:16pt;margin-bottom:8pt;">II. Tujuan</h3>
<p style="text-align:justify;line-height:1.8;font-size:12pt;">${(f.tujuan || '').replace(/\n/g, '<br>')}</p>

${f.manfaat ? `<h3 style="font-size:12pt;font-weight:bold;margin-top:16pt;margin-bottom:8pt;">III. Manfaat</h3>
<p style="text-align:justify;line-height:1.8;font-size:12pt;">${f.manfaat.replace(/\n/g, '<br>')}</p>` : ''}

${f.kegiatan ? `<h3 style="font-size:12pt;font-weight:bold;margin-top:16pt;margin-bottom:8pt;">IV. Rencana Kegiatan</h3>
<p style="text-align:justify;line-height:1.8;font-size:12pt;">${f.kegiatan.replace(/\n/g, '<br>')}</p>` : ''}
            `.trim();
        },

        // ── Proposal Actions ──────────────────────────────────────────────────
        buatProposal() {
            this.proposalForm = {
                id: Date.now(),
                judul: '',
                nim: '{{ $mahasiswa->nim ?? "" }}',
                semester: '',
                instansi: params.defaultPerusahaan || '',
                bidang: '',
                alamat_instansi: '',
                tgl_mulai: '',
                tgl_selesai: '',
                latar_belakang: '',
                tujuan: '',
                manfaat: '',
                kegiatan: '',
                dibuat_pada: new Date().toISOString(),
                diperbarui_pada: new Date().toISOString()
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
            if (confirm('Hapus riwayat pembuatan proposal ini? Tindakan tidak dapat dibatalkan.')) {
                this.proposalHistory = this.proposalHistory.filter(i => i.id !== id);
                this.saveProposalHistory();
            }
        },

        batalProposal() {
            this.view = 'list';
        },

        generateUlang(item) {
            this.proposalForm = JSON.parse(JSON.stringify(item));
            this.proposalStep = 3;
            this.view = 'formProposal';
        },

        simpanProposal() {
            this.proposalForm.diperbarui_pada = new Date().toISOString();
            const idx = this.proposalHistory.findIndex(i => i.id === this.proposalForm.id);
            if (idx >= 0) {
                this.proposalHistory[idx] = JSON.parse(JSON.stringify(this.proposalForm));
            } else {
                this.proposalHistory.push(JSON.parse(JSON.stringify(this.proposalForm)));
            }
            this.saveProposalHistory();
        },

        lanjutProposalStep() {
            // Validasi step 1
            if (this.proposalStep === 1) {
                if (!this.proposalForm.judul || !this.proposalForm.nim || !this.proposalForm.instansi || !this.proposalForm.tgl_mulai || !this.proposalForm.tgl_selesai) {
                    alert('Harap lengkapi semua field yang wajib diisi (bertanda *) sebelum melanjutkan.');
                    return;
                }
            }
            // Validasi step 2
            if (this.proposalStep === 2) {
                if (!this.proposalForm.latar_belakang || !this.proposalForm.tujuan) {
                    alert('Harap isi Latar Belakang dan Tujuan sebelum melanjutkan.');
                    return;
                }
                this.simpanProposal(); // Simpan history saat masuk step 3
            }
            this.proposalStep++;
        }
    }
}
</script>
</body>
</html>
