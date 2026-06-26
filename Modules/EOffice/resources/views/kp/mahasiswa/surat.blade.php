<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Surat Pengantar</title>
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
    </style>
</head>
<body class="bg-grey-50" style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', [
            'breadcrumbs' => ['Informasi', 'Surat Pengantar']
        ])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" x-data="suratManager({
            defaultPerusahaan: '{{ $kp ? ($kp->instansi_kp ?? "") : "" }}'
        })">

            <div>
                {{-- Page heading --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-bold" style="color:var(--grey-900);">Surat Pengantar &amp; Template</h1>
                    <p class="text-sm mt-1" style="color:var(--grey-500);">Buat pengajuan surat pengantar ke instansi secara mandiri dan unduh template dokumen lainnya.</p>
                </div>

                <div class="space-y-8">
                    
                    {{-- Navbar Surat --}}
                    <div class="sikape-card overflow-hidden">
                        <!-- Tab Headers (Navbar Surat) -->
                        <div class="border-b border-grey-200 bg-grey-50 px-4 flex flex-wrap gap-2">
                            <button type="button" @click="activeSuratTab = 'pengantar'" 
                                    :class="activeSuratTab === 'pengantar' ? 'border-indigo-500 text-indigo-600 font-semibold' : 'border-transparent text-grey-500 hover:text-grey-700'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition-all focus:outline-none">
                                Surat Pengantar Departemen
                            </button>
                            <button type="button" @click="activeSuratTab = 'template_lain'" 
                                    :class="activeSuratTab === 'template_lain' ? 'border-indigo-500 text-indigo-600 font-semibold' : 'border-transparent text-grey-500 hover:text-grey-700'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-1.5 focus:outline-none">
                                Template Lainnya 
                                @if($templatesKeperluan->count() > 0)
                                    <span class="bg-indigo-100 text-indigo-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold">
                                        {{ $templatesKeperluan->count() }}
                                    </span>
                                @endif
                            </button>
                        </div>

                        <!-- Tab Contents -->
                        <div class="p-6 bg-white">
                            <!-- Tab 1: Surat Pengantar Departemen -->
                            <div x-show="activeSuratTab === 'pengantar'">
                                <div class="mb-6 pb-6 border-b border-grey-100 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-base font-bold text-grey-900 mb-1">Formulir Pengajuan Surat Pengantar</h3>
                                        <p class="text-sm text-grey-500 max-w-2xl leading-relaxed">
                                            Isi formulir di bawah ini untuk mengunduh Surat Pengantar Departemen resmi (Word/PDF). Anda juga dapat menambahkan anggota jika melakukan KP secara berkelompok.
                                        </p>
                                    </div>
                                    <a href="{{ route('eoffice.kp.mahasiswa.dokumen.template', 'surat_pengantar') }}" class="sikape-btn-outline text-xs flex-shrink-0 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh Template Asli
                                    </a>
                                </div>

                                <!-- Formulir Pembuatan -->
                                <form action="{{ route('eoffice.kp.mahasiswa.surat_pengantar.export') }}" method="POST" target="_blank">
                                    @csrf
                                    <input type="hidden" name="format" :value="suratForm.format">
                                    <input type="hidden" name="anggota" :value="JSON.stringify(suratForm.anggota)">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-grey-700 mb-1.5">Nama Instansi / Perusahaan <span class="text-red-500">*</span></label>
                                            <input type="text" name="instansi" x-model="suratForm.instansi" required class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-grey-50" placeholder="Contoh: PT. ABC Indonesia" style="border-color:var(--grey-200);">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-grey-700 mb-1.5">Alamat Lengkap Instansi <span class="text-red-500">*</span></label>
                                            <input type="text" name="alamat" x-model="suratForm.alamat" required class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-grey-50" placeholder="Contoh: Jl. Sudirman No. 12, Jakarta" style="border-color:var(--grey-200);">
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-grey-700 mb-1.5">Durasi &amp; Waktu Pelaksanaan <span class="text-red-500">*</span></label>
                                        <input type="text" name="durasi" x-model="suratForm.durasi" required class="w-full rounded-lg px-4 py-2.5 text-sm border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-grey-50" placeholder="Contoh: 1 Juni 2026 s.d 31 Agustus 2026" style="border-color:var(--grey-200);">
                                    </div>

                                    <!-- Group Members / Anggota -->
                                    <div class="mb-6">
                                        <div class="flex items-center justify-between mb-4 border-b border-grey-100 pb-2">
                                            <h3 class="text-sm font-bold text-grey-800">Daftar Anggota Kelompok KP</h3>
                                            <button type="button" @click="tambahAnggota()" class="sikape-btn-outline text-xs py-1.5 px-3">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Tambah Anggota
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <template x-for="(mhs, idx) in suratForm.anggota" :key="idx">
                                                <div class="bg-grey-50/50 border border-grey-100 rounded-xl p-4 relative">
                                                    <div class="absolute top-4 right-4" x-show="suratForm.anggota.length > 1">
                                                        <button type="button" @click="hapusAnggota(idx)" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-semibold text-grey-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                                            <input type="text" x-model="mhs.nama" required class="w-full rounded-lg px-3 py-2 text-xs border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="Nama Lengkap Mahasiswa" style="border-color:var(--grey-200);">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-grey-600 mb-1">NIM <span class="text-red-500">*</span></label>
                                                            <input type="text" x-model="mhs.nim" required class="w-full rounded-lg px-3 py-2 text-xs border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="NIM" style="border-color:var(--grey-200);">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-grey-600 mb-1">Semester <span class="text-red-500">*</span></label>
                                                            <input type="text" x-model="mhs.semester" required class="w-full rounded-lg px-3 py-2 text-xs border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="Contoh: 4 (Empat)" style="border-color:var(--grey-200);">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-semibold text-grey-600 mb-1">No. HP / Telepon <span class="text-red-500">*</span></label>
                                                            <input type="text" x-model="mhs.no_hp" required class="w-full rounded-lg px-3 py-2 text-xs border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="No. HP" style="border-color:var(--grey-200);">
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="block text-xs font-semibold text-grey-600 mb-1">Alamat Domisili <span class="text-red-500">*</span></label>
                                                            <input type="text" x-model="mhs.alamat" required class="w-full rounded-lg px-3 py-2 text-xs border focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="Alamat lengkap mahasiswa" style="border-color:var(--grey-200);">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Export Buttons -->
                                    <div class="flex items-center gap-3 border-t border-grey-100 pt-4 mt-6">
                                        <button type="submit" @click="suratForm.format = 'pdf'" class="sikape-btn-primary bg-red-600 hover:bg-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Unduh PDF
                                        </button>
                                        <button type="submit" @click="suratForm.format = 'word'" class="sikape-btn-primary bg-blue-600 hover:bg-blue-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Unduh Word
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab 2: Template Lainnya -->
                            <div x-show="activeSuratTab === 'template_lain'" style="display: none;">
                                @if($templatesKeperluan->count() > 0)
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach($templatesKeperluan as $template)
                                            <div class="p-4 border border-grey-200 rounded-xl bg-white hover:shadow-sm transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div>
                                                    <h4 class="text-sm font-bold text-grey-900 mb-1">{{ $loop->iteration }}. {{ $template->judul }}</h4>
                                                    <p class="text-xs text-grey-500 leading-relaxed">{{ $template->konten }}</p>
                                                </div>
                                                @if($template->lampiran)
                                                    <a href="{{ url('storage/' . $template->lampiran) }}" target="_blank" download class="sikape-btn-primary bg-indigo-600 hover:bg-indigo-700 text-xs py-2 px-4 flex-shrink-0">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Unduh
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 text-grey-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-sm text-grey-500 italic">Belum ada template dokumen tambahan dari Koordinator.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<script>
function suratManager(params = {}) {
    return {
        activeSuratTab: 'pengantar',

        // ── Surat Pengantar Form ──────────────────────────────────────────────
        suratForm: {
            format: 'pdf',
            instansi: params.defaultPerusahaan || '',
            alamat: '',
            durasi: '',
            anggota: [
                {
                    nama: '{{ $mahasiswa->nama_lengkap ?? $user->name }}',
                    nim: '{{ $mahasiswa->nim ?? "" }}',
                    semester: '4 (Empat)',
                    alamat: '',
                    no_hp: ''
                }
            ]
        },

        tambahAnggota() {
            this.suratForm.anggota.push({ nama: '', nim: '', semester: '4 (Empat)', alamat: '', no_hp: '' });
        },
        hapusAnggota(idx) {
            this.suratForm.anggota.splice(idx, 1);
        }
    }
}
</script>
</body>
</html>
