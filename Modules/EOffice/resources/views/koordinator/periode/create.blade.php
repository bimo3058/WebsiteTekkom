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
        <div class="mb-4">
            <a href="{{ route('eoffice.kp.koordinator.periode') }}" style="
                display:inline-flex; align-items:center; gap:8px;
                padding:8px 16px;
                background:#ffffff; border:1px solid #E2E8F0; border-radius:8px;
                font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#4B5563;
                transition:all 0.2s;
            " class="hover:bg-gray-50">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 style="font-family:'Inter Tight',sans-serif; font-size:24px; font-weight:700; color:#0D0D12;">
                    Tambah Periode Baru
                </h1>
                <p style="font-family:'Inter Tight',sans-serif; font-size:16px; color:#666D80; margin-top:4px;">
                    Buat periode baru dengan konfigurasi langkah demi langkah.
                </p>
            </div>

            {{-- Submit/Next Button top right (optional, keep at bottom if preferred, but image has Lanjut at top right)
            --}}
            <button x-show="step < 3" @click="nextStep()" style="
                padding:10px 24px;
                background:#0B266E;
                color:#ffffff;
                font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600;
                border-radius:10px;
                transition:background 0.2s;
            " class="hover:bg-[#233C7D]">
                Lanjut
            </button>
            <button x-show="step === 3" @click="$refs.form.submit()" style="
                padding:10px 24px;
                background:#15803D;
                color:#ffffff;
                font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600;
                border-radius:10px;
                transition:background 0.2s;
            " class="hover:bg-[#166534]">
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
                        padding:10px 16px;
                        border-radius:10px; border-width:1px;
                        font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600;
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
                style="background:#ffffff; border-radius:16px; border:1px solid #F1F1F3; box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04); padding:32px;">

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
                                    style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#272835; margin-bottom:8px; display:block;">Semester
                                    <span class="text-red-500">*</span></label>
                                <select name="semester" x-model="formData.semester"
                                    class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium"
                                    style="font-family:'Inter Tight',sans-serif; font-size:14px;">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#272835; margin-bottom:8px; display:block;">Tahun
                                    Ajaran <span class="text-red-500">*</span></label>
                                <input type="text" name="tahun_ajaran" x-model="formData.tahun_ajaran"
                                    placeholder="contoh: 2026/2027"
                                    class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium"
                                    style="font-family:'Inter Tight',sans-serif; font-size:14px;">
                            </div>

                            {{-- Dates --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#272835; margin-bottom:8px; display:block;">Start
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_buka" x-model="formData.tanggal_buka"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium text-gray-700"
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px;">
                                </div>
                                <div>
                                    <label
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#272835; margin-bottom:8px; display:block;">End
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_tutup" x-model="formData.tanggal_tutup"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] font-medium text-gray-700"
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px;">
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
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Mulai <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="pra_kp_mulai" x-model="formData.pra_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                                <div>
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Berakhir <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="pra_kp_akhir" x-model="formData.pra_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="pra_kp_pengingat" x-model="formData.pra_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
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
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Mulai <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="saat_kp_mulai" x-model="formData.saat_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                                <div>
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Berakhir <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="saat_kp_akhir" x-model="formData.saat_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="saat_kp_pengingat" x-model="formData.saat_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
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
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Mulai <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="pasca_kp_mulai" x-model="formData.pasca_kp_mulai"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                                <div>
                                    <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                        style="font-family:'Inter Tight',sans-serif;">Tanggal Berakhir <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="pasca_kp_akhir" x-model="formData.pasca_kp_akhir"
                                        class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-sm text-[#272835]"
                                    style="font-family:'Inter Tight',sans-serif;">Tanggal Pengingat <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="pasca_kp_pengingat" x-model="formData.pasca_kp_pengingat"
                                    class="w-full border border-[#E2E8F0] rounded-xl px-4 py-3 outline-none focus:border-[#0B266E] focus:ring-1">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- STEP 3: REVIEW --}}
                <div x-show="step === 3" style="display:none;" x-transition>
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
                tabs: ['Informasi Dasar', 'Tanggal Fase', 'Review'],
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
                    if (targetStep >= 1 && targetStep <= 3) {
                        this.step = targetStep;
                    }
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