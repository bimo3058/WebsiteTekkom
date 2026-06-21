<x-app-layout>
    <x-sidebar :user="auth()->user()">
        <div class="min-h-screen" style="background: var(--c-bg);">
            <div class="py-6" x-data="cvWizard()">
                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="page-title" style="font-size: 1.5rem;">CV Builder</h2>
                            <p class="page-subtitle">Lengkapi data Anda untuk menghasilkan CV profesional.</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            class="btn-secondary text-sm">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali ke Profil
                        </a>
                    </div>

                    <!-- Stepper Header -->
                    <div class="card mb-6">
                        <div class="p-6">
                            <div class="relative flex justify-between items-center w-full">
                                <div
                                    class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-slate-100 z-0 rounded-full">
                                </div>
                                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 z-0 rounded-full transition-all duration-500 ease-out"
                                    style="background: var(--c-primary);"
                                    :style="`width: ${((step - 1) / (steps.length - 1)) * 100}%`"></div>

                                <template x-for="(s, index) in steps" :key="index">
                                    <div class="relative z-10 flex flex-col items-center">
                                        <button @click="goToStep(index + 1)"
                                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                                            :class="[
                                            step > index + 1 ? 'text-white shadow-md' :
                                            step === index + 1 ? 'bg-white shadow-sm scale-110' :
                                            'bg-white border-2 border-slate-200 text-slate-400'
                                        ]"
                                            :style="step > index + 1 ? 'background: var(--c-primary);' : (step === index + 1 ? 'border: 4px solid var(--c-primary); color: var(--c-primary);' : '')"
                                            :disabled="index + 1 > maxStep">
                                            <template x-if="step > index + 1"><span
                                                    class="material-symbols-outlined text-[18px]">check</span></template>
                                            <template x-if="step <= index + 1"><span x-text="index + 1"></span></template>
                                        </button>
                                        <span
                                            class="absolute top-12 whitespace-nowrap text-[11px] font-bold tracking-wide uppercase transition-colors duration-300"
                                            :class="step >= index + 1 ? 'text-slate-800' : 'text-slate-400'"
                                            x-text="s.title"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="h-8"></div>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="card p-8 relative min-h-[400px]">

                        <!-- Loading State -->
                        <div x-show="loading"
                            class="absolute inset-0 bg-white/80 z-20 rounded-2xl flex flex-col items-center justify-center">
                            <div class="w-8 h-8 border-4 rounded-full animate-spin"
                                style="border-color: var(--c-primary-subtle); border-top-color: var(--c-primary);">
                            </div>
                            <p class="mt-4 text-sm font-bold text-slate-500">Memuat data...</p>
                        </div>

                        <!-- Error Alert -->
                        <div x-show="error"
                            class="mb-6 p-4 rounded-xl flex items-start gap-3"
                            style="background: var(--c-error-subtle); border: 1px solid var(--c-error);">
                            <span class="material-symbols-outlined" style="color: var(--c-error);">error</span>
                            <p class="text-sm font-medium" style="color: var(--c-error);" x-text="errorMsg"></p>
                            <button @click="error = false" class="ml-auto" style="color: var(--c-error); opacity: 0.6;">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>

                        <!-- Step 1: Data Pribadi -->
                        <div x-show="step === 1 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-1')
                        </div>

                        <!-- Step 2: Pendidikan & Bahasa -->
                        <div x-show="step === 2 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-2')
                        </div>

                        <!-- Step 3: Pengalaman & Organisasi -->
                        <div x-show="step === 3 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-3')
                        </div>

                        <!-- Step 4: Proyek & Sertifikasi & Prestasi -->
                        <div x-show="step === 4 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-4')
                        </div>

                        <!-- Step 5: Keahlian -->
                        <div x-show="step === 5 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-5')
                        </div>

                        <!-- Step 6: Preview -->
                        <div x-show="step === 6 && !loading" x-transition.opacity.duration.300ms style="display: none;">
                            @include('profile.cv.steps.step-6')
                        </div>
                    </div>

                    <!-- Footer Navigation -->
                    <div class="mt-6 flex justify-between items-center">
                        <button @click="goToStep(step - 1)" x-show="step > 1"
                            class="btn-secondary text-sm">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Sebelumnya
                        </button>
                        <div x-show="step === 1"></div>

                        <button @click="saveAndNext()" x-show="step < 6"
                            class="btn-primary text-sm shadow-sm">
                            Simpan & Lanjut
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-sidebar>

    <script>
        function cvWizard() {
            return {
                step: 1,
                maxStep: 1,
                loading: false,
                error: false,
                errorMsg: '',
                steps: [
                    { title: 'Data Pribadi' },
                    { title: 'Pdk & Bahasa' },
                    { title: 'Kerja & Org' },
                    { title: 'Proyek & Sert' },
                    { title: 'Keahlian' },
                    { title: 'Preview' }
                ],

                // State Data
                data: {
                    user: {},
                    cv: {
                        tentang_diri: '',
                        pendidikan: [],
                        pengalaman_kerja: [],
                        kegiatan_organisasi: [],
                        proyek: [],
                        sertifikasi: [],
                        bahasa: [],
                        keahlian: []
                    },
                    pendidikan_sync: [],
                    pengalaman_sync: [],
                    kegiatan_sync: [],
                    prestasi_sync: []
                },

                // UI Helpers for Arrays
                newEdu: { institusi: '', jurusan: '', tahun_masuk: '', tahun_lulus: '' },
                newExp: { perusahaan: '', posisi: '', tahun_mulai: '', tahun_selesai: '', deskripsi: '' },
                newOrg: { organisasi: '', peran: '', tahun_mulai: '', tahun_selesai: '', deskripsi: '' },
                newProj: { nama: '', peran: '', tahun: '', deskripsi: '', tautan: '' },
                newCert: { nama: '', penerbit: '', tahun: '' },
                newLang: { nama: '', level: 'Menengah', skor: '' },
                newSkill: { nama: '', level: 'Beginner' },

                init() {
                    this.loadStepData(1);
                },

                async loadStepData(targetStep) {
                    this.loading = true;
                    this.error = false;

                    try {
                        const response = await fetch(`/profile/cv/step/${targetStep}`);
                        const resData = await response.json();

                        if (targetStep === 1) {
                            this.data.user = resData.user || {};
                            this.data.cv.tentang_diri = resData.cv?.tentang_diri || '';
                            this.data.cv.cv_domisili = resData.cv?.cv_domisili || '';
                            this.data.cv.cv_portfolio = resData.cv?.cv_portfolio || '';
                        } else if (targetStep === 2) {
                            this.data.pendidikan_sync = resData.pendidikan_sync || [];
                            this.data.cv.pendidikan = resData.cv?.pendidikan || [];
                            this.data.cv.bahasa = resData.cv?.bahasa || [];
                        } else if (targetStep === 3) {
                            this.data.pengalaman_sync = resData.pengalaman_sync || [];
                            this.data.kegiatan_sync = resData.kegiatan_sync || [];
                            this.data.cv.pengalaman_kerja = resData.cv?.pengalaman_kerja || [];
                            this.data.cv.kegiatan_organisasi = resData.cv?.kegiatan_organisasi || [];
                        } else if (targetStep === 4) {
                            this.data.prestasi_sync = resData.prestasi_sync || [];
                            this.data.cv.proyek = resData.cv?.proyek || [];
                            this.data.cv.sertifikasi = resData.cv?.sertifikasi || [];
                        } else if (targetStep === 5) {
                            this.data.cv.keahlian = resData.cv?.keahlian || [];
                        }

                        this.step = targetStep;
                        if (targetStep > this.maxStep) this.maxStep = targetStep;
                    } catch (err) {
                        this.error = true;
                        this.errorMsg = 'Gagal memuat data. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },

                // Bangun payload untuk step tertentu (hanya step 1-5 yang punya data).
                buildPayload(step) {
                    const payload = {};
                    if (step === 1) {
                        payload.tentang_diri = this.data.cv.tentang_diri;
                        payload.personal_email = this.data.user.personal_email;
                        payload.whatsapp = this.data.user.whatsapp;
                        payload.cv_domisili = this.data.cv.cv_domisili;
                        payload.cv_portfolio = this.data.cv.cv_portfolio;
                    }
                    if (step === 2) {
                        payload.pendidikan = this.data.cv.pendidikan;
                        payload.bahasa = this.data.cv.bahasa;
                    }
                    if (step === 3) {
                        payload.pengalaman_kerja = this.data.cv.pengalaman_kerja;
                        payload.kegiatan_organisasi = this.data.cv.kegiatan_organisasi;
                    }
                    if (step === 4) {
                        payload.proyek = this.data.cv.proyek;
                        payload.sertifikasi = this.data.cv.sertifikasi;
                    }
                    if (step === 5) {
                        payload.keahlian = this.data.cv.keahlian;
                    }
                    return payload;
                },

                // Simpan step yang sedang aktif. Return true jika sukses, false jika gagal.
                // Step 6 (preview) tidak punya data untuk disimpan.
                async persistCurrentStep() {
                    if (this.step < 1 || this.step > 5) return true;

                    try {
                        const response = await fetch(`/profile/cv/step/${this.step}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.buildPayload(this.step))
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            // Handle validation errors (422) or server errors
                            const msg = result.errors
                                ? Object.values(result.errors).flat().join(', ')
                                : (result.message || 'Gagal menyimpan data.');
                            throw new Error(msg);
                        }

                        if (!result.success) {
                            throw new Error(result.message || 'Gagal menyimpan data.');
                        }

                        return true;
                    } catch (err) {
                        this.error = true;
                        this.errorMsg = err.message || 'Terjadi kesalahan jaringan.';
                        return false;
                    }
                },

                async saveAndNext() {
                    this.loading = true;
                    this.error = false;

                    if (await this.persistCurrentStep()) {
                        this.loadStepData(this.step + 1);
                    } else {
                        this.loading = false;
                    }
                },

                // Pindah step (lewat lingkaran stepper / tombol Sebelumnya). Simpan dulu
                // step aktif agar input yang belum disimpan tidak hilang; batalkan pindah
                // jika penyimpanan gagal (validasi) supaya pesan error tetap terlihat.
                async goToStep(target) {
                    if (target < 1 || target > this.maxStep || target === this.step) return;

                    this.loading = true;
                    this.error = false;

                    if (await this.persistCurrentStep()) {
                        this.loadStepData(target);
                    } else {
                        this.loading = false;
                    }
                },

                // Array Helpers
                addEdu() {
                    if (!this.newEdu.institusi || !this.newEdu.jurusan) return;
                    this.data.cv.pendidikan.push({ ...this.newEdu });
                    this.newEdu = { institusi: '', jurusan: '', tahun_masuk: '', tahun_lulus: '' };
                },
                removeEdu(index) {
                    this.data.cv.pendidikan.splice(index, 1);
                },

                addExp() {
                    if (!this.newExp.perusahaan || !this.newExp.posisi) return;
                    this.data.cv.pengalaman_kerja.push({ ...this.newExp });
                    this.newExp = { perusahaan: '', posisi: '', tahun_mulai: '', tahun_selesai: '', deskripsi: '' };
                },
                removeExp(index) {
                    this.data.cv.pengalaman_kerja.splice(index, 1);
                },

                addOrg() {
                    if (!this.newOrg.organisasi || !this.newOrg.peran) return;
                    this.data.cv.kegiatan_organisasi.push({ ...this.newOrg });
                    this.newOrg = { organisasi: '', peran: '', tahun_mulai: '', tahun_selesai: '', deskripsi: '' };
                },
                removeOrg(index) {
                    this.data.cv.kegiatan_organisasi.splice(index, 1);
                },

                addProj() {
                    if (!this.newProj.nama || !this.newProj.deskripsi) return;
                    this.data.cv.proyek.push({ ...this.newProj });
                    this.newProj = { nama: '', peran: '', tahun: '', deskripsi: '', tautan: '' };
                },
                removeProj(index) {
                    this.data.cv.proyek.splice(index, 1);
                },

                addCert() {
                    if (!this.newCert.nama || !this.newCert.penerbit) return;
                    this.data.cv.sertifikasi.push({ ...this.newCert });
                    this.newCert = { nama: '', penerbit: '', tahun: '' };
                },
                removeCert(index) {
                    this.data.cv.sertifikasi.splice(index, 1);
                },

                addLang() {
                    if (!this.newLang.nama || !this.newLang.level) return;
                    this.data.cv.bahasa.push({ ...this.newLang });
                    this.newLang = { nama: '', level: 'Menengah', skor: '' };
                },
                removeLang(index) {
                    this.data.cv.bahasa.splice(index, 1);
                },

                addSkill() {
                    if (!this.newSkill.nama) return;
                    this.data.cv.keahlian.push({ ...this.newSkill });
                    this.newSkill = { nama: '', level: 'Beginner' };
                },
                removeSkill(index) {
                    this.data.cv.keahlian.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>