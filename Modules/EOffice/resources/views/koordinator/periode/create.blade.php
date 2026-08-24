<x-eoffice::layouts.koordinator title="Tambah Periode Baru">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.koordinator.periode') }}"
            class="text-[#666D80] hover:text-[#0D0D12] transition-colors"
            style="font-family:'Inter Tight',sans-serif;">Periode</a>
        <span class="text-[#E2E8F0] mx-2">/</span>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Tambah Periode Baru</span>
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
                    Tambah Periode Baru
                </h1>
                <p style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#666D80; margin-top:4px;">
                    Buat periode baru dengan konfigurasi langkah demi langkah.
                </p>
            </div>

            {{-- Submit/Next Button top right (optional, keep at bottom if preferred, but image has Lanjut at top right)
            --}}
            <button type="button" x-show="step < 4" @click="nextStep()"
                class="flex items-center justify-center px-4 py-2 bg-slate-900 border border-transparent text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm text-sm font-semibold">
                Lanjut
            </button>
            <button type="button" x-show="step === 4" @click="saveForm()"
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
                        border-radius:10px; border-width:1px;
                        font-size:13px; font-weight:600;
                        transition:all 0.2s;
                        whitespace:nowrap;
                    ">
                        {{-- Determine Icon --}}
                        <template x-if="tab === 'Informasi Dasar'">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </template>
                        <template x-if="tab === 'Tanggal Fase'">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="tab === 'Rubrik Penilaian'">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
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
        <form x-ref="form" action="{{ route('eoffice.kp.koordinator.periode.store') }}" method="POST">
            @csrf

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

                {{-- STEP 3: RUBRIK PENILAIAN --}}
                <div x-show="step === 3" style="display:none;" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                        {{-- Kumpulan Komponen Penilaian (Left Column) --}}
                        <div class="lg:col-span-12 xl:col-span-4 flex flex-col h-full">
                            <h2 class="text-[18px] font-bold text-[#0D0D12] mb-1.5"
                                style="font-family:'Inter Tight',sans-serif;">Kumpulan Komponen Penilaian</h2>
                            <p class="text-[13px] text-[#666D80] mb-6 leading-relaxed"
                                style="font-family:'Inter Tight',sans-serif;">Pilih komponen yang akan digunakan dalam
                                Kerja Praktik untuk periode yang dipilih.</p>

                            <div class="border border-[#E2E8F0] rounded-[16px] p-5 shadow-sm bg-white flex-1"
                                style="font-family:'Inter Tight',sans-serif;">
                                <h3 class="text-[16px] font-bold text-[#0D0D12] mb-4">Ringkasan</h3>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[13px] text-[#848A9C] font-semibold">Total Komponen</span>
                                    <span class="text-[14px] font-bold text-[#0D0D12]"
                                        x-text="formData.komponen_penilaian.length"></span>
                                </div>
                                <!-- The Budget Tracker -->
                                <div class="mb-6 p-4 rounded-xl border transition-colors duration-300"
                                    :class="totalBobot() === 100 ? 'border-[#ABEFC6] bg-[#F6FEF9]' : (totalBobot() > 100 ? 'border-red-200 bg-[#FFF4F3]' : 'border-[#E2E8F0] bg-[#F8F9FA]')">
                                    <div class="flex justify-between items-end mb-3">
                                        <div class="flex flex-col">
                                            <span class="text-[11px] font-bold uppercase tracking-wider mb-0.5"
                                                :class="totalBobot() === 100 ? 'text-[#027A48]' : (totalBobot() > 100 ? 'text-[#B42318]' : 'text-[#344054]')">
                                                Total Bobot
                                            </span>
                                            <span class="text-[12px] font-medium"
                                                :class="totalBobot() === 100 ? 'text-[#027A48]/80' : (totalBobot() > 100 ? 'text-[#B42318]/80' : 'text-[#666D80]')"
                                                x-text="totalBobot() === 100 ? 'Alokasi valid siap disimpan' : (totalBobot() > 100 ? 'Kelebihan ' + (totalBobot() - 100) + '%' : 'Sisa kuota ' + (100 - totalBobot()) + '%')"></span>
                                        </div>
                                        <div class="text-right flex flex-col items-end">
                                            <span class="text-[22px] font-black leading-none"
                                                :class="totalBobot() === 100 ? 'text-[#027A48]' : (totalBobot() > 100 ? 'text-[#B42318]' : 'text-[#0D0D12]')"
                                                x-text="totalBobot() + '%'"></span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-300 ease-out rounded-full"
                                            :style="'width: ' + Math.min(totalBobot(), 100) + '%'"
                                            :class="totalBobot() === 100 ? 'bg-[#12B76A]' : (totalBobot() > 100 ? 'bg-[#F04438]' : 'bg-[#0065FF]')">
                                        </div>
                                    </div>
                                </div>

                                <h4 class="text-[15px] font-bold text-[#0D0D12] mb-4 mt-2">Komponen terpilih :</h4>

                                <div class="border border-[#EAECF0] rounded-[10px] overflow-hidden">
                                    <div
                                        class="flex justify-between items-center bg-[#F8F9FA] px-4 py-2.5 text-[12px] font-semibold text-[#666D80]">
                                        <span>Kode</span>
                                        <span class="text-right">Bobot</span>
                                    </div>
                                    <div class="divide-y divide-[#EAECF0] max-h-[300px] overflow-y-auto">
                                        <template x-for="(comp, i) in formData.komponen_penilaian" :key="i">
                                            <div class="flex justify-between items-center px-4 py-3 bg-white">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-[20px] h-[20px] rounded flex items-center justify-center bg-[#2E3C5B] text-white">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-[13px] font-bold text-[#0D0D12]"
                                                        x-text="comp.kode || (comp.nama_komponen ? comp.nama_komponen.substring(0, 10) : '-')"></span>
                                                    <span
                                                        class="ml-1 px-1.5 py-0.5 border border-gray-200 bg-gray-50 text-gray-500 text-[9px] font-extrabold rounded"
                                                        x-text="comp.role_penilai === 'dosen_pembimbing' ? 'DOSEN' : (comp.role_penilai ? 'KOOR' : '')"
                                                        x-show="comp.role_penilai"></span>
                                                </div>
                                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-[8px]"
                                                    :class="comp.bobot >= 50 ? 'bg-[#FEF3F2] text-[#B42318]' : (comp.bobot >= 25 ? 'bg-[#FFFAEB] text-[#B54708]' : 'bg-[#ECFDF3] text-[#027A48]')"
                                                    x-text="comp.bobot + '%'"></span>
                                            </div>
                                        </template>
                                        <template x-if="formData.komponen_penilaian.length === 0">
                                            <div class="px-4 py-8 text-center text-[12px] text-gray-400 font-medium">
                                                Belum ada komponen terpilih.</div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Data Table (Right Column) --}}
                        <div class="lg:col-span-12 xl:col-span-8 flex flex-col xl:h-[650px] min-h-[500px]"
                            style="font-family:'Inter Tight',sans-serif;">
                            <div
                                class="border border-[#E2E8F0] rounded-[16px] shadow-sm bg-white overflow-hidden flex flex-col h-full">

                                <!-- Table Layout Header (Toolbar) -->
                                <div
                                    class="px-5 py-4 border-b border-[#E2E8F0] flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-3 lg:gap-4">
                                    <h3 class="text-[16px] font-bold text-[#0D0D12] sm:whitespace-nowrap">Pilih Komponen
                                    </h3>

                                    <div
                                        class="flex items-center gap-2.5 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                                        <div class="relative w-full sm:w-[220px]">
                                            <span
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-[#98A2B3]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </span>
                                            <input type="text" x-model="searchRubrik" placeholder="Search"
                                                class="w-full h-[38px] pl-9 pr-3 text-[13px] text-[#0D0D12] border border-[#D0D5DD] rounded-[8px] outline-none focus:border-[#2E3C5B] focus:ring-1 focus:ring-[#2E3C5B] transition-colors placeholder-[#98A2B3] shadow-sm">
                                        </div>

                                        <!-- Filter -->
                                        <button type="button"
                                            class="hidden sm:flex flex-shrink-0 h-[38px] px-3.5 items-center gap-2 border border-[#D0D5DD] rounded-[8px] text-[13px] font-bold text-[#344054] hover:bg-gray-50 shadow-sm transition-colors bg-white">
                                            <svg class="w-4 h-4 text-[#667085]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                                </path>
                                            </svg>
                                            Filter
                                        </button>

                                        <!-- Sort by -->
                                        <button type="button"
                                            class="hidden sm:flex flex-shrink-0 h-[38px] px-3.5 items-center gap-2 border border-[#D0D5DD] rounded-[8px] text-[13px] font-bold text-[#344054] hover:bg-gray-50 shadow-sm transition-colors bg-white">
                                            <svg class="w-4 h-4 text-[#667085]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                            Sort by
                                        </button>
                                    </div>
                                </div>

                                <!-- Table Columns Header -->
                                <div
                                    class="px-5 py-3 bg-[#F9FAFB] border-b border-[#E2E8F0] flex items-center text-[13px] font-semibold text-[#848A9C]">
                                    <div class="w-[45px] flex items-center justify-start">
                                        <input type="checkbox" disabled
                                            class="w-[18px] h-[18px] rounded-[4px] border-[#D0D5DD] text-[#2E3C5B] bg-white">
                                    </div>
                                    <div class="w-[60px]">Kode</div>
                                    <div class="w-[130px]">Penilai</div>
                                    <div class="flex-1 px-2">Deskripsi</div>
                                    <div class="w-[80px] text-right">Bobot</div>
                                </div>

                                <!-- Table body -->
                                <div class="flex-1 overflow-y-auto divide-y divide-[#E2E8F0] bg-white"
                                    style="min-h: 300px;">
                                    <template x-for="mr in paginatedRubriks" :key="mr.id">
                                        <label
                                            class="flex items-start sm:items-center px-5 py-3.5 hover:bg-gray-50 cursor-pointer transition-colors w-full group">
                                            <div
                                                class="w-[45px] pt-1 sm:pt-0 flex items-start sm:items-center justify-start">
                                                <input type="checkbox" :value="mr.id"
                                                    :checked="formData.komponen_penilaian.some(c => c.master_rubrik_id == mr.id)"
                                                    @change="toggleRubrik(mr, $event.target.checked)"
                                                    class="w-[18px] h-[18px] rounded-[4px] border-[#D0D5DD] text-[#0065FF] focus:ring-[#0065FF] focus:border-[#0065FF] focus:ring-offset-0 cursor-pointer bg-white checked:bg-[#0065FF]">
                                            </div>
                                            <div class="w-[60px] text-[13.5px] font-bold text-[#0D0D12]"
                                                x-text="mr.kode"></div>
                                            <div class="w-[130px] pr-2">
                                                <span
                                                    class="px-2 py-0.5 border border-gray-200 bg-gray-50 text-gray-600 text-[10.5px] font-bold rounded-md"
                                                    x-text="mr.role_penilai === 'dosen_pembimbing' ? 'Dosen' : 'Koor'"></span>
                                            </div>
                                            <div class="flex-1 px-2 text-[13.5px] text-[#344054] font-medium leading-[1.6] pr-4 sm:pr-8"
                                                x-text="mr.deskripsi"></div>
                                            <div class="w-[80px] text-right flex-shrink-0">
                                                <span
                                                    class="px-2.5 py-0.5 text-[11.5px] font-bold rounded-[8px] inline-block"
                                                    :class="mr.bobot >= 50 ? 'bg-[#FEF3F2] text-[#B42318]' : (mr.bobot >= 25 ? 'bg-[#FFFAEB] text-[#B54708]' : 'bg-[#ECFDF3] text-[#027A48]')"
                                                    x-text="mr.bobot + '%'"></span>
                                            </div>
                                        </label>
                                    </template>
                                    <template x-if="masterRubriks.length === 0">
                                        <div class="py-20 flex flex-col items-center justify-center text-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                            <span class="text-[13.5px] text-[#666D80] font-semibold">Belum ada komponen
                                                tersedia.</span>
                                        </div>
                                    </template>
                                    <template x-if="masterRubriks.length > 0 && filteredRubriks.length === 0">
                                        <div class="py-20 text-center text-[13.5px] text-[#666D80] font-semibold">
                                            Pencarian tidak ditemukan.</div>
                                    </template>
                                </div>

                                <!-- Footer Real Pagination -->
                                <div
                                    class="px-5 py-3.5 border-t border-[#E2E8F0] flex flex-col sm:flex-row justify-between items-center bg-white gap-4 w-full">
                                    <div
                                        class="flex flex-wrap items-center justify-center sm:justify-start gap-4 w-full sm:w-auto">
                                        <div
                                            class="flex items-center gap-2 border border-[#D0D5DD] rounded-[8px] pl-2.5 pr-2 py-1 shadow-sm bg-white">
                                            <span class="text-[12.5px] font-semibold text-[#344054]">Per page</span>
                                            <select x-model.number="perPage"
                                                class="border-none bg-transparent py-0.5 pl-1 pr-6 focus:ring-0 text-[13px] font-bold text-[#0D0D12] cursor-pointer outline-none">
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                            </select>
                                        </div>
                                        <span class="text-[13px] font-medium text-[#666D80]"
                                            x-text="Showing  to  of,  results"></span>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-center gap-1.5 h-[34px]">
                                        <button type="button" @click="changePage(currentPage - 1)"
                                            :disabled="currentPage === 1"
                                            class="w-[34px] h-[34px] flex items-center justify-center border border-[#D0D5DD] rounded-[8px] text-[#667085] hover:bg-gray-50 transition-colors shadow-sm bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>

                                        <template x-for="page in totalPages" :key="page">
                                            <button type="button" @click="changePage(page)"
                                                class="hidden sm:flex w-[34px] h-[34px] items-center justify-center rounded-[8px] text-[13px] font-semibold transition-colors shadow-sm"
                                                :class="currentPage === page ? 'bg-[#2E3C5B] text-white border-transparent' : 'border border-[#D0D5DD] text-[#344054] hover:bg-gray-50 bg-white'"
                                                x-text="page"
                                                x-show="totalPages <= 5 || page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1"></button>
                                        </template>

                                        <button type="button" @click="changePage(currentPage + 1)"
                                            :disabled="currentPage === totalPages"
                                            class="w-[34px] h-[34px] flex items-center justify-center border border-[#D0D5DD] rounded-[8px] text-[#667085] hover:bg-gray-50 transition-colors shadow-sm bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
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

                                    <div
                                        class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[#666D80] font-semibold w-1/3">Pra KP</span>
                                        <div class="w-full text-right flex flex-col gap-1">
                                            <span>Mulai: <span x-text="formatDate(formData.pra_kp_mulai)"></span></span>
                                            <span>Berakhir: <span
                                                    x-text="formatDate(formData.pra_kp_akhir)"></span></span>
                                            <span>Pengingat: <span class="text-orange-500"
                                                    x-text="formatDate(formData.pra_kp_pengingat)"></span></span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[#666D80] font-semibold w-1/3">Saat KP</span>
                                        <div class="w-full text-right flex flex-col gap-1">
                                            <span>Mulai: <span
                                                    x-text="formatDate(formData.saat_kp_mulai)"></span></span>
                                            <span>Berakhir: <span
                                                    x-text="formatDate(formData.saat_kp_akhir)"></span></span>
                                            <span>Pengingat: <span class="text-orange-500"
                                                    x-text="formatDate(formData.saat_kp_pengingat)"></span></span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col sm:flex-row justify-between gap-1 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-[#666D80] font-semibold w-1/3">Pasca KP</span>
                                        <div class="w-full text-right flex flex-col gap-1">
                                            <span>Mulai: <span
                                                    x-text="formatDate(formData.pasca_kp_mulai)"></span></span>
                                            <span>Berakhir: <span
                                                    x-text="formatDate(formData.pasca_kp_akhir)"></span></span>
                                            <span>Pengingat: <span class="text-orange-500"
                                                    x-text="formatDate(formData.pasca_kp_pengingat)"></span></span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Review Rubrik --}}
                            <div>
                                <h3
                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                    Rubrik Penilaian
                                </h3>
                                <div class="rounded-xl border border-[#EAECF0] overflow-hidden">
                                    <div
                                        class="flex justify-between items-center bg-[#F8F9FA] px-4 py-3 text-[12px] font-semibold text-[#666D80]">
                                        <span>Komponen</span>
                                        <span>Bobot</span>
                                    </div>
                                    <div class="divide-y divide-[#EAECF0]">
                                        <template x-for="(comp, i) in formData.komponen_penilaian" :key="i">
                                            <div class="flex justify-between items-center px-4 py-3 bg-white">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                                                        style="font-family:'Inter Tight',sans-serif;"
                                                        x-text="comp.nama_komponen"></span>
                                                    <span
                                                        class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 font-semibold"
                                                        x-text="comp.kode"></span>
                                                </div>
                                                <span
                                                    class="px-2 py-0.5 bg-[#FEF3F2] text-[#B42318] text-[11px] font-bold rounded"
                                                    x-text="comp.bobot + '%'"></span>
                                            </div>
                                        </template>

                                        <template x-if="formData.komponen_penilaian.length === 0">
                                            <div class="px-4 py-6 text-center text-sm text-gray-500 bg-white">
                                                Tidak ada komponen yang dipilih.
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mt-4 p-4 rounded-xl border"
                                    :class="totalBobot() === 100 ? 'border-[#ABEFC6] bg-[#F6FEF9]' : 'border-red-200 bg-[#FFF4F3]'">
                                    <div class="flex justify-between items-center text-[13px] font-bold">
                                        <span :class="totalBobot() === 100 ? 'text-[#027A48]' : 'text-[#B42318]'">Total
                                            Bobot Keseluruhan:</span>
                                        <span class="text-[16px]"
                                            :class="totalBobot() === 100 ? 'text-[#027A48]' : 'text-[#B42318]'"
                                            x-text="totalBobot() + '%'"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>


            {{-- Hidden Inputs for Komponen Penilaian --}}
            <template x-for="(comp, i) in formData.komponen_penilaian" :key="i">
                <div>
                    <input type="hidden" :name="`komponen_penilaian[${i}][master_rubrik_id]`"
                        :value="comp.master_rubrik_id">
                    <input type="hidden" :name="`komponen_penilaian[${i}][kode]`" :value="comp.kode">
                    <input type="hidden" :name="`komponen_penilaian[${i}][nama_komponen]`" :value="comp.nama_komponen">
                    <input type="hidden" :name="`komponen_penilaian[${i}][bobot]`" :value="comp.bobot">
                    <input type="hidden" :name="`komponen_penilaian[${i}][role_penilai]`" :value="comp.role_penilai">
                    <template x-if="comp.id">
                        <input type="hidden" :name="`komponen_penilaian[${i}][id]`" :value="comp.id">
                    </template>
                </div>
            </template>
        </form>


    </div>

    <script>
        function periodeWizard() {
            return {
                step: 1,
                tabs: ['Informasi Dasar', 'Tanggal Fase', 'Rubrik Penilaian', 'Review'],
                searchRubrik: '',
                currentPage: 1,
                perPage: 10,
                get filteredRubriks() {
                    let result = this.masterRubriks;
                    if (this.searchRubrik) {
                        const s = this.searchRubrik.toLowerCase();
                        result = result.filter(r => (r.kode || '').toLowerCase().includes(s) || (r.deskripsi || '').toLowerCase().includes(s));
                    }
                    return result;
                },
                get paginatedRubriks() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredRubriks.slice(start, start + this.perPage);
                },
                get totalPages() {
                    return Math.ceil(this.filteredRubriks.length / this.perPage) || 1;
                },
                changePage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                    }
                },
                init() {
                    this.$watch('searchRubrik', () => this.currentPage = 1);
                    this.$watch('perPage', () => this.currentPage = 1);
                },
                masterRubriks: @json($masterRubriks),
                formData: {
                    semester: 'Ganjil',
                    tahun_ajaran: '',
                    tanggal_buka: '',
                    tanggal_tutup: '',
                    is_active: true,
                    pra_kp_mulai: '',
                    pra_kp_akhir: '',
                    pra_kp_pengingat: '',
                    saat_kp_mulai: '',
                    saat_kp_akhir: '',
                    saat_kp_pengingat: '',
                    pasca_kp_mulai: '',
                    pasca_kp_akhir: '',
                    pasca_kp_pengingat: '',
                    komponen_penilaian: []
                },
                initDates() {
                    // Initialize if needed
                },
                toggleRubrik(mr, checked) {
                    if (checked) {
                        this.formData.komponen_penilaian.push({
                            master_rubrik_id: mr.id,
                            kode: mr.kode,
                            nama_komponen: mr.deskripsi,
                            bobot: mr.bobot,
                            role_penilai: mr.role_penilai
                        });
                    } else {
                        this.formData.komponen_penilaian = this.formData.komponen_penilaian.filter(c => c.master_rubrik_id !== mr.id);
                    }
                },
                totalBobot() {
                    return this.formData.komponen_penilaian.reduce((sum, comp) => sum + (parseFloat(comp.bobot) || 0), 0);
                },
                nextStep() {
                    this.goToStep(this.step + 1);
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                },
                goToStep(targetStep) {
                    // Cek validasi step 1
                    if (targetStep > 1) {
                        if (!this.formData.tahun_ajaran || !this.formData.tanggal_buka || !this.formData.tanggal_tutup) {
                            alert('Harap lengkapi Tahun Ajaran dan Tanggal Pendaftaran Dasar terlebih dahulu.');
                            return;
                        }
                    }
                    if (targetStep === 4) {
                        if (this.totalBobot() !== 100) {
                            alert('Total bobot penilaian harus tepat 100% sebelum dapat melanjutkan ke Review.');
                            return;
                        }
                    }
                    if (targetStep >= 1 && targetStep <= 4) {
                        this.step = targetStep;
                    }
                },
                saveForm() {
                    if (this.totalBobot() !== 100) {
                        alert('Gagal menyimpan. Total bobot penilaian harus tepat 100%.');
                        return;
                    }

                    // Render hidden inputs for array submission
                    const form = this.$refs.form;

                    // Hapus input array hidden sebelumnya agar tidak terduplikasi
                    form.querySelectorAll('.dyn-comp').forEach(e => e.remove());

                    this.formData.komponen_penilaian.forEach((comp, idx) => {
                        Object.keys(comp).forEach(key => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.className = 'dyn-comp';
                            input.name = `komponen_penilaian[${idx}][${key}]`;
                            input.value = comp[key] !== null ? comp[key] : '';
                            form.appendChild(input);
                        });
                    });

                    form.submit();
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