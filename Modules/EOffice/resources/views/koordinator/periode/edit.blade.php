<x-eoffice::layouts.koordinator title="Edit Periode">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.koordinator.periode') }}"
            class="text-[#666D80] hover:text-[#0D0D12] transition-colors"
            style="font-family:'Inter Tight',sans-serif;">Periode</a>
        <span class="text-[#E2E8F0] mx-2">/</span>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Edit Periode</span>
    @endsection

    {{-- Wizard Container with Alpine --}}
    <div x-data="periodeWizard()" x-init="initDates()" class="w-full pb-20">

        {{-- Header & Back Button --}}
        <div class="mb-6">
            <a href="{{ route('eoffice.kp.koordinator.periode') }}" style="
                display:inline-flex; align-items:center; gap:8px;
                padding:8px 14px;
                background:#fff; border:1px solid #E4E7EC; border-radius:8px;
                font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                transition:background 0.2s;
            " class="hover:bg-gray-50">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12;">
                    Edit Periode
                </h1>
                <p style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#666D80; margin-top:4px;">
                    Ubah konfigurasi dari periode ini.
                </p>
            </div>

            {{-- Submit/Next Button top right (optional, keep at bottom if preferred, but image has Lanjut at top right)
            --}}
            <button type="button" x-show="step < 4" @click="nextStep()"
                class="flex items-center justify-center px-4 py-2 bg-slate-900 border border-transparent text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm text-sm font-semibold">
                Lanjut
            </button>
            <button type="button" x-show="step === 4" @click="$refs.form.submit()"
                class="flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent text-white rounded-xl hover:bg-emerald-700 transition-colors shadow-sm text-sm font-semibold">
                Simpan
            </button>
        </div>

        {{-- Validation Errors if any (from server) --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pengisian form</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Stepper Tabs --}}
        <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2">
            <template x-for="(tab, index) in tabs" :key="index">
                <div class="flex items-center">
                    <button type="button" @click="goToStep(index + 1)"
                        :class="step === index + 1 ? 'border-[#0B266E] text-[#0B266E] bg-[#F8F5FF]' : (step > index + 1 ? 'border-transparent text-[#666D80] hover:text-[#0D0D12]' : 'border-transparent text-[#A0AABF]')"
                        style="
                        display:flex; align-items:center; gap:8px;
                        padding:8px 12px;
                        border-radius:12px; font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600;
                        transition:all 0.2s;
                        ">
                        <template x-if="tab !== 'Review'">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="tab === 'Review'">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </template>
                        <span x-text="tab"></span>
                    </button>
                    <div x-show="index < tabs.length - 1" class="px-2 text-[#E2E8F0]">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </template>
        </div>

        {{-- Form Area --}}
        <form x-ref="form" action="{{ route('eoffice.kp.koordinator.periode.update', $periode->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div
                style="background:#fff; border:1px solid #EAECF0; border-radius:12px; box-shadow:0 1px 2px rgba(16, 24, 40, 0.05); padding:24px;">

                {{-- STEP 1: INFORMASI DASAR --}}
                <div x-show="step === 1" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        <div class="md:col-span-4">
                            <h2
                                style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                                Informasi Dasar</h2>
                            <p
                                style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                                Konfigurasi detail dari periode baru.</p>
                        </div>
                        <div class="md:col-span-8 flex flex-col gap-6">

                            {{-- Nama Periode / Semester --}}
                            <div>
                                <label
                                    style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Semester
                                    <span class="text-red-500">*</span></label>
                                <select name="semester" x-model="formData.semester"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tahun
                                    Ajaran <span class="text-red-500">*</span></label>
                                <input type="text" name="tahun_ajaran" x-model="formData.tahun_ajaran"
                                    placeholder="contoh: 2026/2027"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                            </div>

                            <div>
                                <label
                                    style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Pilihan
                                    Kelas yang Dibuka
                                    <span class="text-gray-400 font-normal text-xs ml-1">(Pisahkan dengan
                                        koma)</span></label>
                                <input type="text" name="kelas_dibuka" x-model="formData.kelas_dibuka"
                                    placeholder="contoh: A, B, C, Paralel, Unggulan"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                            </div>

                            {{-- Dates --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Start
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_buka" x-model="formData.tanggal_buka"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium text-gray-700"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">End
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_tutup" x-model="formData.tanggal_tutup"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium text-gray-700"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                            </div>

                            {{-- Aktif Toggle --}}
                            <div>
                                <label
                                    style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#272835; margin-bottom:12px; display:block;">Set
                                    Aktif <span class="text-red-500">*</span></label>
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" value="1" x-model="formData.is_active"
                                            class="sr-only">
                                        <div class="block w-14 h-8 rounded-full transition-colors duration-300"
                                            :class="formData.is_active ? 'bg-[#0B266E]' : 'bg-gray-300'"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform duration-300"
                                            :class="formData.is_active ? 'translate-x-6' : ''"></div>
                                    </div>
                                    <div class="ml-3 font-medium text-gray-700"
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px;"
                                        x-text="formData.is_active ? 'Aktif' : 'Nonaktif'"></div>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- STEP 2: TANGGAL FASE --}}
                <div x-show="step === 2" style="display:none;" x-transition>

                    {{-- Pra KP --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-10 pb-10 border-b border-[#F1F1F3]">
                        <div class="md:col-span-4">
                            <h2
                                style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                                Pra KP</h2>
                            <p
                                style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                                Set tanggal untuk jangka waktu sebelum KP.</p>
                        </div>
                        <div class="md:col-span-8 flex flex-col gap-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="pra_kp_mulai" x-model="formData.pra_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Berakhir <span class="text-red-500">*</span></label>
                                    <input type="date" name="pra_kp_akhir" x-model="formData.pra_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="pra_kp_pengingat" x-model="formData.pra_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                            </div>
                        </div>
                    </div>

                    {{-- Saat KP --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-10 pb-10 border-b border-[#F1F1F3]">
                        <div class="md:col-span-4">
                            <h2
                                style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                                Saat KP</h2>
                            <p
                                style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                                Set tanggal untuk jangka waktu KP berjalan.</p>
                        </div>
                        <div class="md:col-span-8 flex flex-col gap-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="saat_kp_mulai" x-model="formData.saat_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Berakhir <span class="text-red-500">*</span></label>
                                    <input type="date" name="saat_kp_akhir" x-model="formData.saat_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="saat_kp_pengingat" x-model="formData.saat_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                            </div>
                        </div>
                    </div>

                    {{-- Pasca KP --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        <div class="md:col-span-4">
                            <h2
                                style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                                Pasca KP</h2>
                            <p
                                style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                                Set tanggal untuk jangka waktu sesudah laporan KP selesai.</p>
                        </div>
                        <div class="md:col-span-8 flex flex-col gap-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="pasca_kp_mulai" x-model="formData.pasca_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#272835; margin-bottom:6px; display:block;">Tanggal
                                        Berakhir <span class="text-red-500">*</span></label>
                                    <input type="date" name="pasca_kp_akhir" x-model="formData.pasca_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                        style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="pasca_kp_pengingat" x-model="formData.pasca_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1"
                                    style="font-family:'Inter Tight',sans-serif; font-size:13px;">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- STEP 3: RUBRIK PENILAIAN --}}
            <div x-show="step === 3" style="display:none;" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <div class="md:col-span-4">
                        <h2
                            style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                            Rubrik Penilaian
                        </h2>
                        <p
                            style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                            Atur komponen penilaian apa saja yang dinilai selama masa KP beserta persentase bobotnya.
                        </p>
                    </div>
                    <div class="md:col-span-8">
                        {{-- Fitur Salin Penilaian --}}
                        <div class="mb-6 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">Salin Penilaian</h3>
                                    <p class="text-xs text-gray-500 mt-1">Salin seluruh konfigurasi penilaian dari periode lain.</p>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <select @change="copyFromPeriode($event.target.value)"
                                        class="w-full text-sm border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 bg-gray-50 font-medium text-gray-700">
                                        <option value="">Pilih sumber periode</option>
                                        <template x-for="p in allPeriodes" :key="p.id">
                                            <option :value="p.id" x-text="`Semester ${p.semester} ${p.tahun_ajaran}`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-gray-800">Daftar Komponen</h3>
                                <button type="button" @click="addComponent"
                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-[#0B266E] rounded-lg hover:bg-slate-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(comp, index) in formData.komponen_penilaian" :key="index">
                                    <div
                                        class="flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-white p-4 rounded-lg border border-gray-200 shadow-sm relative group">
                                        <input type="hidden" :name="`komponen_penilaian[${index}][id]`"
                                            :value="comp.id">

                                        <div class="w-full sm:w-1/3">
                                            <label
                                                class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama
                                                Komponen</label>
                                            <input type="text" :name="`komponen_penilaian[${index}][nama_komponen]`"
                                                x-model="comp.nama_komponen" placeholder="Contoh: Laporan KP"
                                                class="w-full text-sm border-gray-200 rounded-md px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1">
                                        </div>

                                        <div class="w-full sm:w-1/4">
                                            <label
                                                class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Bobot
                                                (%)</label>
                                            <div class="relative">
                                                <input type="number" :name="`komponen_penilaian[${index}][bobot]`"
                                                    x-model.number="comp.bobot" min="0" max="100"
                                                    class="w-full text-sm border-gray-200 rounded-md px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 pr-6">
                                                <span class="absolute right-2 top-2 text-gray-400 text-sm">%</span>
                                            </div>
                                        </div>

                                        <div class="w-full sm:w-1/3">
                                            <label
                                                class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Role
                                                Penilai</label>
                                            <select :name="`komponen_penilaian[${index}][role_penilai]`"
                                                x-model="comp.role_penilai"
                                                class="w-full text-sm border-gray-200 rounded-md px-3 py-2 outline-none focus:border-[#0B266E] focus:ring-1 text-slate-700">
                                                <option value="dosen_pembimbing">Dosen Pembimbing</option>
                                                <option value="koordinator">Koordinator KP</option>
                                            </select>
                                        </div>

                                        <button type="button" @click="removeComponent(index)"
                                            class="sm:absolute sm:-right-3 sm:-top-3 sm:hidden group-hover:flex mt-2 sm:mt-0 p-1.5 bg-red-100 text-red-600 rounded-full hover:bg-red-200 shadow-sm border border-red-200 transition-colors"
                                            title="Hapus Komponen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <template x-if="formData.komponen_penilaian.length === 0">
                                    <div
                                        class="text-center py-6 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-lg">
                                        Belum ada rubrik komponen penilaian yang ditambahkan.
                                    </div>
                                </template>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center text-sm">
                                <span class="font-semibold text-gray-600">Total Bobot:</span>
                                <span class="font-bold px-3 py-1 rounded-lg"
                                    :class="totalBobot() === 100 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                    x-text="totalBobot() + '%'"></span>
                            </div>
                            <p x-show="totalBobot() !== 100" class="text-xs text-red-500 mt-2 font-medium">Peringatan:
                                Total
                                bobot harus tepat 100% sebelum disimpan.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 4: REVIEW --}}
            <div x-show="step === 4" style="display:none;" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <div class="md:col-span-4">
                        <h2
                            style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:700; color:#0D0D12; margin-bottom:8px;">
                            Review</h2>
                        <p
                            style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                            Tinjau konfigurasi Anda sebelum menyimpan.</p>
                    </div>
                    <div class="md:col-span-8 flex flex-col gap-8">

                        {{-- Review List --}}
                        <div>
                            <h3
                                class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                Konfigurasi Periode Baru</h3>
                            <ul class="space-y-4 font-medium text-sm text-[#0D0D12]"
                                style="font-family:'Inter Tight',sans-serif;">
                                <li class="flex justify-between items-center">
                                    <span class="text-[#666D80]">Nama Periode</span>
                                    <span
                                        x-text="'Semester ' + formData.semester + ' ' + (formData.tahun_ajaran || '-')"></span>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span class="text-[#666D80]">Durasi Pendaftaran</span>
                                    <span
                                        x-text="(formatDate(formData.tanggal_buka)) + ' - ' + (formatDate(formData.tanggal_tutup))"></span>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span class="text-[#666D80]">Status</span>
                                    <span x-show="formData.is_active"
                                        class="px-3 py-1 bg-green-50 text-green-700 text-xs rounded-full border border-green-200">Aktif</span>
                                    <span x-show="!formData.is_active"
                                        class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full border border-gray-200">Nonaktif</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                Tanggal Fase</h3>
                            <div class="grid grid-cols-1 gap-4 font-medium text-sm text-[#0D0D12]"
                                style="font-family:'Inter Tight',sans-serif;">

                                <div class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[#666D80] font-semibold w-1/3">Pra KP</span>
                                    <div class="w-full text-right flex flex-col gap-1">
                                        <span>Mulai: <span x-text="formatDate(formData.pra_kp_mulai)"></span></span>
                                        <span>Berakhir: <span x-text="formatDate(formData.pra_kp_akhir)"></span></span>
                                        <span>Pengingat: <span class="text-orange-500"
                                                x-text="formatDate(formData.pra_kp_pengingat)"></span></span>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[#666D80] font-semibold w-1/3">Saat KP</span>
                                    <div class="w-full text-right flex flex-col gap-1">
                                        <span>Mulai: <span x-text="formatDate(formData.saat_kp_mulai)"></span></span>
                                        <span>Berakhir: <span x-text="formatDate(formData.saat_kp_akhir)"></span></span>
                                        <span>Pengingat: <span class="text-orange-500"
                                                x-text="formatDate(formData.saat_kp_pengingat)"></span></span>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[#666D80] font-semibold w-1/3">Pasca KP</span>
                                    <div class="w-full text-right flex flex-col gap-1">
                                        <span>Mulai: <span x-text="formatDate(formData.pasca_kp_mulai)"></span></span>
                                        <span>Berakhir: <span
                                                x-text="formatDate(formData.pasca_kp_akhir)"></span></span>
                                        <span>Pengingat: <span class="text-orange-500"
                                                x-text="formatDate(formData.pasca_kp_pengingat)"></span></span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

    </div>

    </form>

    </div>

    <script>
        function periodeWizard() {
            return {
                step: 1,
                tabs: ['Informasi Dasar', 'Tanggal Fase', 'Rubrik Penilaian', 'Review'],
                formData: {
                    semester: '{{ old('semester', $periode->semester) }}',
                    tahun_ajaran: '{{ old('tahun_ajaran', $periode->tahun_ajaran) }}',
                    kelas_dibuka: '{{ is_array(old('kelas_dibuka', $periode->kelas_dibuka)) ? implode(', ', old('kelas_dibuka', $periode->kelas_dibuka)) : old('kelas_dibuka', $periode->kelas_dibuka) }}',
                    tanggal_buka: '{{ old('tanggal_buka', $periode->tanggal_buka) }}',
                    tanggal_tutup: '{{ old('tanggal_tutup', $periode->tanggal_tutup) }}',
                    is_active: {{ old('is_active', $periode->is_active) ? 'true' : 'false' }},
                    pra_kp_mulai: '{{ old('pra_kp_mulai', $periode->pra_kp_mulai?->format('Y-m-d')) }}',
                    pra_kp_akhir: '{{ old('pra_kp_akhir', $periode->pra_kp_akhir?->format('Y-m-d')) }}',
                    pra_kp_pengingat: '{{ old('pra_kp_pengingat', $periode->pra_kp_pengingat?->format('Y-m-d')) }}',
                    saat_kp_mulai: '{{ old('saat_kp_mulai', $periode->saat_kp_mulai?->format('Y-m-d')) }}',
                    saat_kp_akhir: '{{ old('saat_kp_akhir', $periode->saat_kp_akhir?->format('Y-m-d')) }}',
                    saat_kp_pengingat: '{{ old('saat_kp_pengingat', $periode->saat_kp_pengingat?->format('Y-m-d')) }}',
                    pasca_kp_mulai: '{{ old('pasca_kp_mulai', $periode->pasca_kp_mulai?->format('Y-m-d')) }}',
                    pasca_kp_akhir: '{{ old('pasca_kp_akhir', $periode->pasca_kp_akhir?->format('Y-m-d')) }}',
                    pasca_kp_pengingat: '{{ old('pasca_kp_pengingat', $periode->pasca_kp_pengingat?->format('Y-m-d')) }}',
                    komponen_penilaian: {!! json_encode(old('komponen_penilaian', $periode->komponenNilai->map(function ($k) {
    return clone $k; }))) !!} || []
                },
                allPeriodes: {!! json_encode($allPeriodes->map(function($p) {
                    return [
                        'id' => $p->id,
                        'semester' => $p->semester,
                        'tahun_ajaran' => $p->tahun_ajaran,
                        'komponen' => $p->komponenNilai->map(function($k) {
                            return [
                                'nama_komponen' => $k->nama_komponen,
                                'bobot' => $k->bobot,
                                'role_penilai' => $k->role_penilai
                            ];
                        })
                    ];
                })) !!},
                copyFromPeriode(periodeId) {
                    if (!periodeId) return;
                    const source = this.allPeriodes.find(p => p.id == periodeId);
                    if (source) {
                        // Deep clone so they get inserted as fresh new components without old IDs
                        this.formData.komponen_penilaian = JSON.parse(JSON.stringify(source.komponen));
                    }
                },
                initDates() {
                    // Initialize if needed
                },
                nextStep() {
                    this.goToStep(this.step + 1);
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                },
                goToStep(targetStep) {
                    // Cek validasi step 1 jika ingin pindah ke step 2 atau 3
                    if (targetStep > 1) {
                        if (!this.formData.tahun_ajaran || !this.formData.tanggal_buka || !this.formData.tanggal_tutup) {
                            alert('Harap lengkapi Tahun Ajaran dan Tanggal Pendaftaran Dasar terlebih dahulu.');
                            return;
                        }
                    }
                    if (targetStep >= 1 && targetStep <= 4) {
                        this.step = targetStep;
                    }
                },
                addComponent() {
                    this.formData.komponen_penilaian.push({
                        id: null,
                        nama_komponen: '',
                        bobot: 0,
                        role_penilai: 'dosen_pembimbing'
                    });
                },
                removeComponent(index) {
                    this.formData.komponen_penilaian.splice(index, 1);
                },
                totalBobot() {
                    return this.formData.komponen_penilaian.reduce((sum, item) => sum + (parseInt(item.bobot) || 0), 0);
                },
                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    const options = { day: '2-digit', month: 'long', year: 'numeric' };
                    return date.toLocaleDateString('id-ID', options);
                }
            }
        }
    </script>
</x-eoffice::layouts.koordinator>