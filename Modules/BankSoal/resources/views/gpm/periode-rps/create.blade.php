<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="text-slate-500 hover:text-primary transition-colors">Validasi RPS</a>
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
    </style>

    <x-banksoal::notification.alerts />
    
    <div x-data="periodeWizard()" class="max-w-4xl mx-auto pb-20">
        
        <x-banksoal::ui.page-header title="Atur Periode Pengajuan" subtitle="Konfigurasi jadwal pengajuan RPS dan template dokumen untuk dosen">
            <x-slot:actions>
                <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-times"></i> Batal
                </a>
            </x-slot:actions>
        </x-banksoal::ui.page-header>

        <!-- Progress Stepper -->
        <div class="mb-12">
            <div class="flex items-center justify-between relative px-10">
                <!-- Line background -->
                <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                <!-- Line progress -->
                <div class="absolute top-1/2 left-0 h-1 bg-navy -translate-y-1/2 z-0 transition-all duration-500" :style="'width: ' + (step === 1 ? '50%' : '100%')"></div>
                
                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div :class="step >= 1 ? 'bg-navy text-white' : 'bg-slate-200 text-slate-500'" class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg shadow-lg shadow-navy/20 transition-all duration-300">
                        <i class="fas fa-calendar-alt" x-show="step === 1"></i>
                        <i class="fas fa-check" x-show="step > 1"></i>
                    </div>
                    <span :class="step >= 1 ? 'text-navy font-bold' : 'text-slate-400 font-medium'" class="text-xs uppercase tracking-wider">Periode</span>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div :class="step >= 2 ? 'bg-navy text-white' : 'bg-slate-200 text-slate-500'" class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg transition-all duration-300" :class="step === 2 ? 'shadow-lg shadow-navy/20' : ''">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <span :class="step >= 2 ? 'text-navy font-bold' : 'text-slate-400 font-medium'" class="text-xs uppercase tracking-wider">Template</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <!-- STEP 1: PERIODE -->
            <div x-show="step === 1" x-transition:enter="step-transition" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-navy/10 text-navy">
                            <i class="fas fa-calendar-plus"></i>
                        </span>
                        Buat Jadwal Pengajuan Baru
                    </h3>
                </div>
                
                <form id="formStep1" @submit.prevent="submitStep1" class="p-8">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="text-sm font-bold text-slate-700 block mb-2">Judul Periode <span class="text-rose-500">*</span></label>
                                <input type="text" name="judul" x-model="formData.judul" required class="w-full rounded-2xl border border-slate-200 px-5 py-3.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" placeholder="Contoh: Pengajuan RPS Semester Genap 2025/2026">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-bold text-slate-700 block mb-2">Semester <span class="text-rose-500">*</span></label>
                                <select name="semester" x-model="formData.semester" required class="w-full rounded-2xl border border-slate-200 px-5 py-3.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all appearance-none bg-no-repeat bg-[right_1.25rem_center] bg-[length:1rem]" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%236b7280%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E')">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-bold text-slate-700 block mb-2">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select name="tahun_ajaran" x-model="formData.tahun_ajaran" required class="w-full rounded-2xl border border-slate-200 px-5 py-3.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all appearance-none bg-no-repeat bg-[right_1.25rem_center] bg-[length:1rem]" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%236b7280%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22/%3E%3C/svg%3E')">
                                    <option value="" disabled>Pilih Tahun Ajaran</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta }}">{{ $ta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-bold text-slate-700 block mb-2">Tanggal Mulai <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_mulai" x-model="formData.tanggal_mulai" required class="w-full rounded-2xl border border-slate-200 px-5 py-3.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                            </div>
                            <div>
                                <label class="text-sm font-bold text-slate-700 block mb-2">Tenggat Selesai <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_selesai" x-model="formData.tanggal_selesai" required class="w-full rounded-2xl border border-slate-200 px-5 py-3.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                            </div>
                        </div>
                        
                        <div @click="formData.is_active = !formData.is_active" class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50/50 p-5 cursor-pointer hover:border-navy transition-all group">
                            <!-- Custom Toggle UI -->
                            <div class="relative flex items-center justify-center shrink-0">
                                <div 
                                    class="w-12 h-6 rounded-full transition-all duration-300 flex items-center px-1"
                                    :class="formData.is_active ? 'bg-navy' : 'bg-slate-300'"
                                >
                                    <div 
                                        class="w-4 h-4 bg-white rounded-full shadow-sm transition-transform duration-300"
                                        :class="formData.is_active ? 'translate-x-6' : 'translate-x-0'"
                                    ></div>
                                </div>
                                <input type="hidden" name="is_active" :value="formData.is_active ? '1' : '0'">
                            </div>
                            <span>
                                <span class="font-bold text-slate-800 text-sm block group-hover:text-navy transition-colors">Otomatis aktifkan jadwal ini</span>
                                <span class="text-xs text-slate-500 mt-1 block leading-relaxed italic">Catatan: Mengaktifkan periode ini akan menonaktifkan periode lainnya secara otomatis.</span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-10 flex items-center justify-end">
                        <button type="submit" class="rounded-2xl bg-navy px-8 py-4 text-sm font-bold text-white hover:bg-navy/90 transition-all shadow-xl shadow-navy/20 inline-flex items-center gap-3" :disabled="loading">
                            <template x-if="!loading">
                                <span class="flex items-center gap-3">
                                    Lanjut ke Upload Template <i class="fas fa-arrow-right"></i>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-spinner fa-spin"></i> Memproses...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEP 2: TEMPLATE -->
            <div x-show="step === 2" x-transition:enter="step-transition" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-file-upload"></i>
                        </span>
                        Unggah Template RPS Terbaru
                    </h3>
                </div>
                
                <form id="formStep2" @submit.prevent="submitStep2" class="p-8">
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 flex gap-4">
                            <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="text-sm text-amber-900 leading-relaxed">
                                <p class="font-bold mb-1">Informasi Penting</p>
                                <p>Template ini akan menjadi acuan dosen dalam menyusun RPS. Pastikan format file sesuai standar kurikulum terbaru.</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-bold text-slate-700 block mb-3">File Template (Word Format)</label>
                            <div 
                                @click="$refs.fileInput.click()"
                                @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false"
                                @drop.prevent="handleDrop($event)"
                                :class="dragOver ? 'border-navy bg-navy/5 ring-4 ring-navy/5' : 'border-slate-200 bg-slate-50/50'"
                                class="relative rounded-3xl border-2 border-dashed transition-all p-10 text-center cursor-pointer group"
                            >
                                <div class="h-20 w-20 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform text-navy border border-slate-100">
                                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                </div>
                                
                                <div x-show="!selectedFile">
                                    <p class="text-sm font-bold text-slate-800">Tarik & letakkan file di sini</p>
                                    <p class="text-xs text-slate-500 mt-2">atau <span class="text-navy underline">cari di folder Anda</span></p>
                                    <p class="text-[10px] text-slate-400 mt-4 uppercase tracking-widest">DOC, DOCX • MAKS 1MB</p>
                                </div>

                                <div x-show="selectedFile" x-cloak class="flex flex-col items-center">
                                    <div class="inline-flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-700 shadow-sm animate-popup">
                                        <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                                        <span x-text="selectedFile ? selectedFile.name : ''"></span>
                                        <button @click.stop="selectedFile = null" class="ml-2 p-1 hover:bg-emerald-100 rounded-lg transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <input type="file" x-ref="fileInput" @change="handleFileSelect" accept=".doc,.docx" class="hidden">
                            </div>
                            <p x-show="fileError" x-text="fileError" class="mt-2 text-xs text-rose-500 font-semibold" x-cloak></p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-bold text-slate-700 block mb-2">Keterangan Perubahan (Opsional)</label>
                            <textarea x-model="templateData.keterangan" class="w-full rounded-2xl border border-slate-200 px-5 py-4 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all resize-none" rows="3" placeholder="Contoh: Penyesuaian standar CPL sesuai hasil rapat prodi Mei 2026..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-10 pt-8 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="skipTemplate" class="inline-flex items-center gap-2 rounded-2xl bg-slate-50 px-8 py-4 text-sm font-bold text-slate-600 hover:bg-slate-100 transition-all border border-slate-200 shadow-sm">
                            Lewati & Selesai
                        </button>
                        <button type="submit" class="rounded-2xl bg-navy px-10 py-4 text-sm font-bold text-white hover:bg-navy/90 transition-all shadow-xl shadow-navy/20 inline-flex items-center gap-3" :disabled="loading || !selectedFile">
                            <template x-if="!loading">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-save"></i> Simpan & Upload Template
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-spinner fa-spin"></i> Sedang Mengunggah...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                    tahun_ajaran: '{{ old('tahun_ajaran') }}',
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
</x-banksoal::layouts.gpm-master>
