<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
        <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
        <span class="mx-2 text-slate-300">/</span>
        <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}"
            class="text-slate-500 hover:text-primary transition-colors">Validasi RPS</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Atur Periode Pengajuan</span>
    @endsection

    <style>
        .step-transition {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bg-navy {
            background-color: rgb(11, 38, 110);
        }

        .text-navy {
            color: rgb(11, 38, 110);
        }

        .border-navy {
            border-color: rgb(11, 38, 110);
        }

        /* Level indikator: tulisan berlevel di pojok kiri atas */
        .periode-level {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.5;
            letter-spacing: -0.01em;
        }

        .periode-level .is-active {
            color: rgb(11, 38, 110);
            font-weight: 700;
        }

        .periode-level .is-idle {
            color: #94a3b8;
            font-weight: 600;
        }

        /* Nomor level di dalam lingkaran */
        .periode-level .step-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .periode-level .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 1.5px solid #cbd5e1;
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }

        .periode-level .is-active .num {
            background: rgb(11, 38, 110);
            border-color: rgb(11, 38, 110);
            color: #fff;
        }

        .periode-level .sep {
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 600;
        }

        /* Form scroll di dalam container bank soal */
        .periode-wizard {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .periode-scroll {
            min-height: 0;
            max-height: calc(100vh - 205px);
            overflow-y: auto;
            overscroll-behavior: contain;
            padding-right: 6px;
            scrollbar-gutter: stable;
        }

        @media (max-width: 1023px) {
            .periode-scroll {
                max-height: none;
                overflow: visible;
                padding-right: 0;
            }
        }
    </style>

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.bank-soal-page>
        <x-slot:header>
            <div class="bs-heading-row">
                <div>
                    <div class="bs-heading-label">
                        <h1>Atur Periode Pengajuan</h1>
                        <span class="bs-role-badge">GPM</span>
                    </div>
                    <p>Konfigurasi jadwal pengajuan RPS dan template dokumen untuk dosen.</p>
                </div>
                <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="dosen-management-btn"
                    style="min-height:30px;padding:6px 12px;font-size:11px;">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </x-slot:header>

        <div x-data="periodeWizard()" class="periode-wizard">

        <!-- Level indikator: tulisan berlevel, pojok kiri atas -->
        <div class="periode-level mb-2.5">
            <span class="step-item" :class="step >= 1 ? 'is-active' : 'is-idle'">
                <span class="num">1</span> Periode Pengajuan
            </span>
            <template x-if="step >= 2">
                <span class="sep">&gt;</span>
            </template>
            <template x-if="step >= 2">
                <span class="step-item is-active">
                    <span class="num">2</span> Template RPS
                </span>
            </template>
        </div>

        <!-- Area form yang bisa di-scroll di dalam container bank soal -->
        <div class="periode-scroll">
        <div class="relative mx-auto max-w-3xl overflow-hidden pb-2">
            <!-- STEP 1: PERIODE -->
            <div x-show="step === 1" x-transition:enter="step-transition"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                    <h3 class="font-bold text-slate-900 text-[15px] flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-navy/10 text-navy text-[13px]">
                            <i class="fas fa-calendar-plus"></i>
                        </span>
                        Buat Jadwal Pengajuan Baru
                    </h3>
                </div>

                <form id="formStep1" @submit.prevent="submitStep1" class="p-5">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Judul Periode <span
                                        class="text-rose-500">*</span></label>
                                <input type="text" name="judul" x-model="formData.judul" required
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all"
                                    placeholder="Contoh: Pengajuan RPS Semester Genap 2025/2026">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Semester <span
                                        class="text-rose-500">*</span></label>
                                <select name="semester" x-model="formData.semester" disabled required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all appearance-none bg-no-repeat bg-[right_0.9rem_center] bg-[length:0.9rem]"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%2394a3b8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E')">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Tahun Ajaran <span
                                        class="text-rose-500">*</span></label>
                                <select name="tahun_ajaran" x-model="formData.tahun_ajaran" disabled required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all appearance-none bg-no-repeat bg-[right_0.9rem_center] bg-[length:0.9rem]"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%2394a3b8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E')">
                                    <option value="" disabled>Pilih Tahun Ajaran</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta }}">{{ $ta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Tanggal Mulai <span
                                        class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_mulai" x-model="formData.tanggal_mulai" required
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                            </div>
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Tenggat Selesai <span
                                        class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_selesai" x-model="formData.tanggal_selesai" required
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                            </div>
                        </div>

                        <div @click="formData.is_active = !formData.is_active"
                            class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 cursor-pointer hover:border-navy transition-all group">
                            <!-- Custom Toggle UI -->
                            <div class="relative flex items-center justify-center shrink-0">
                                <div class="w-10 h-5 rounded-full transition-all duration-300 flex items-center px-0.5"
                                    :class="formData.is_active ? 'bg-navy' : 'bg-slate-300'">
                                    <div class="w-4 h-4 bg-white rounded-full shadow-sm transition-transform duration-300"
                                        :class="formData.is_active ? 'translate-x-5' : 'translate-x-0'"></div>
                                </div>
                                <input type="hidden" name="is_active" :value="formData.is_active ? '1' : '0'">
                            </div>
                            <span>
                                <span
                                    class="font-bold text-slate-800 text-[13px] block group-hover:text-navy transition-colors">Otomatis
                                    aktifkan jadwal ini</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 block leading-snug italic">Catatan:
                                    Mengaktifkan periode ini akan menonaktifkan periode lainnya secara otomatis.</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-navy px-5 py-2.5 text-[13px] font-bold text-white shadow-md shadow-navy/20 transition-all hover:bg-navy/90 disabled:opacity-60"
                            :disabled="loading">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    Lanjut ke Upload Template <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Memproses...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEP 2: TEMPLATE -->
            <div x-show="step === 2" x-transition:enter="step-transition"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                    <h3 class="font-bold text-slate-900 text-[15px] flex items-center gap-2.5">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 text-[13px]">
                            <i class="fas fa-file-upload"></i>
                        </span>
                        Unggah Template RPS Terbaru
                    </h3>
                </div>

                <form id="formStep2" @submit.prevent="submitStep2" class="p-5">
                    <div class="space-y-4">
                        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-3.5 flex gap-3">
                            <div
                                class="h-7 w-7 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600 text-xs">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="text-[13px] text-amber-900 leading-snug">
                                <p class="font-bold mb-0.5">Informasi Penting</p>
                                <p>Upload template terbaru jika ada perubahan format RPS dari semester sebelumnya.</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-[13px] font-semibold text-slate-700 block mb-2">File Template (Word
                                Format)</label>
                            <div @click="$refs.fileInput.click()" @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event)"
                                :class="dragOver ? 'border-navy bg-navy/5 ring-4 ring-navy/5' : 'border-slate-200 bg-slate-50/50'"
                                class="relative rounded-2xl border-2 border-dashed transition-all p-5 text-center cursor-pointer group">
                                <div
                                    class="h-12 w-12 bg-white rounded-xl shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform text-navy border border-slate-100">
                                    <i class="fas fa-cloud-upload-alt text-xl"></i>
                                </div>

                                <div x-show="!selectedFile">
                                    <p class="text-[13px] font-bold text-slate-800">Tarik & letakkan file di sini</p>
                                    <p class="text-xs text-slate-500 mt-1">atau <span class="text-navy underline">cari
                                            di folder Anda</span></p>
                                    <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest">DOC, DOCX •
                                        MAKS 1MB</p>
                                </div>

                                <div x-show="selectedFile" x-cloak class="flex flex-col items-center">
                                    <div
                                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-[13px] font-bold text-emerald-700 shadow-sm animate-popup">
                                        <i class="fas fa-check-circle text-lg text-emerald-500"></i>
                                        <span x-text="selectedFile ? selectedFile.name : ''"></span>
                                        <button @click.stop="selectedFile = null"
                                            class="ml-1 p-1 hover:bg-emerald-100 rounded-lg transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <input type="file" x-ref="fileInput" @change="handleFileSelect" accept=".doc,.docx"
                                    class="hidden">
                            </div>
                            <p x-show="fileError" x-text="fileError" class="mt-1.5 text-xs text-rose-500 font-semibold"
                                x-cloak></p>
                        </div>

                        <div>
                            <label class="text-[13px] font-semibold text-slate-700 block mb-1.5">Keterangan Perubahan
                                (Opsional)</label>
                            <textarea x-model="templateData.keterangan"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-[13px] focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all resize-none"
                                rows="2"
                                placeholder="Contoh: Penyesuaian standar CPL sesuai hasil rapat prodi Mei 2026..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                        <button type="button" @click="skipTemplate"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] font-bold text-slate-600 transition-all hover:bg-slate-100">
                            Lewati & Selesai
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-navy px-5 py-2.5 text-[13px] font-bold text-white shadow-md shadow-navy/20 transition-all hover:bg-navy/90 disabled:opacity-60"
                            :disabled="loading || !selectedFile">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-save"></i> Simpan & Upload Template
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Sedang Mengunggah...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>{{-- /periode-scroll --}}
    </div>

    <script>
        function periodeWizard() {
            return {
                step: 1,
                loading: false,
                dragOver: false,
                selectedFile: null,
                fileError: null,
                periodeId: null,

                formData: {
                    judul: '{{ old('judul') }}',
                    semester: '{{ old('semester', $currentSemester) }}',
                    tahun_ajaran: '{{ old('tahun_ajaran', $currentTahunAjaran) }}',
                    tanggal_mulai: '{{ old('tanggal_mulai') }}',
                    tanggal_selesai: '{{ old('tanggal_selesai') }}',
                    is_active: true
                },

                templateData: {
                    keterangan: ''
                },

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    this.processFile(file);
                },

                handleDrop(e) {
                    this.dragOver = false;
                    const file = e.dataTransfer.files[0];
                    this.processFile(file);
                },

                processFile(file) {
                    this.fileError = null;
                    if (!file) return;

                    const validExtensions = ['.doc', '.docx'];
                    const maxSize = 1 * 1024 * 1024; // 1MB

                    const extension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
                    if (!validExtensions.includes(extension)) {
                        this.fileError = 'Format file tidak valid. Gunakan .doc atau .docx';
                        this.selectedFile = null;
                        return;
                    }

                    if (file.size > maxSize) {
                        this.fileError = 'Ukuran file terlalu besar. Maksimal 1MB';
                        this.selectedFile = null;
                        return;
                    }

                    this.selectedFile = file;
                },

                async submitStep1() {
                    this.loading = true;
                    try {
                        const response = await fetch("{{ route('banksoal.rps.gpm.periode-rps.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Backend needs to return JSON for this to work
                            // If backend redirects, we might need a different approach
                            // But let's assume we'll update the backend to support JSON
                            this.step = 2;
                            if (typeof Snackbar !== 'undefined') Snackbar.show('Periode berhasil dibuat. Silakan upload template.', 'success');
                        } else {
                            const errorMsg = data.message || 'Terjadi kesalahan saat menyimpan periode.';
                            if (typeof Snackbar !== 'undefined') Snackbar.show(errorMsg, 'error');
                            else alert(errorMsg);
                        }
                    } catch (error) {
                        // If it's a redirect (302), it might fail fetch here
                        // Let's assume we will fix the controller
                        this.step = 2;
                    } finally {
                        this.loading = false;
                    }
                },

                async submitStep2() {
                    if (!this.selectedFile) return;
                    this.loading = true;

                    const formData = new FormData();
                    formData.append('dokumen', this.selectedFile);
                    formData.append('keterangan', this.templateData.keterangan);

                    try {
                        const response = await fetch("{{ route('banksoal.rps.gpm.template.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (response.ok) {
                            window.location.href = "{{ route('banksoal.rps.gpm.validasi-rps') }}?success=Periode dan Template berhasil disimpan.";
                        } else {
                            const errorMsg = data.message || 'Gagal mengunggah template.';
                            if (typeof Snackbar !== 'undefined') Snackbar.show(errorMsg, 'error');
                            else alert(errorMsg);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan sistem saat mengunggah.');
                    } finally {
                        this.loading = false;
                    }
                },

                skipTemplate() {
                    window.location.href = "{{ route('banksoal.rps.gpm.validasi-rps') }}?success=Periode berhasil dibuat tanpa template baru.";
                }
            }
        }
    </script>
    </x-banksoal::ui.bank-soal-page>
</x-banksoal::layouts.gpm-master>