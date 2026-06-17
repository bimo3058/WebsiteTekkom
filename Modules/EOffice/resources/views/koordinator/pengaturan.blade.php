<x-eoffice::layouts.koordinator title="Pengaturan KP">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Pengaturan</span>
    @endsection

    <!-- Page Header -->
    <div class="mb-6 lg:mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Pengaturan KP</h1>
        <p class="text-sm text-slate-500 mt-1 leading-relaxed">Kelola periode pendaftaran dan konfigurasi sistem Kerja
            Praktik.</p>
    </div>

    @if(session('success'))
        <div
            class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Content Area -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="border-b border-slate-100 p-6 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-xl flex items-center justify-center border border-indigo-200 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Periode Pendaftaran KP</h2>
                        <p class="text-xs text-slate-500 font-medium">Buka atau tutup form pendaftaran KP bagi mahasiswa
                            baru.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('eoffice.kp.koordinator.pengaturan.store') }}" method="POST" class="p-8">
                @csrf

                <!-- Toggle Switch -->
                <div class="mb-8" x-data="{ isOpen: {{ $isOpen == '1' ? 'true' : 'false' }} }">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="pendaftaran_kp_buka" value="0">
                            <input type="checkbox" name="pendaftaran_kp_buka" value="1" class="sr-only"
                                x-model="isOpen">
                            <div class="block w-14 h-8 rounded-full transition-colors duration-300 shadow-inner"
                                :class="isOpen ? 'bg-indigo-500' : 'bg-slate-300'"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 flex items-center justify-center shadow-md"
                                :class="isOpen ? 'transform translate-x-6' : ''">
                                <svg x-show="isOpen" class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <svg x-show="!isOpen" class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold" :class="isOpen ? 'text-indigo-700' : 'text-slate-600'">Status:
                                <span x-text="isOpen ? 'Terbuka' : 'Ditutup'"></span></p>
                            <p class="text-xs text-slate-500 mt-0.5">Jika ditutup, mahasiswa sama sekali tidak dapat
                                mengakses form pendaftaran.</p>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mulai Pendaftaran</label>
                        <input type="date" name="pendaftaran_kp_mulai" value="{{ $startDate }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-800">
                        @error('pendaftaran_kp_mulai')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Selesai Pendaftaran</label>
                        <input type="date" name="pendaftaran_kp_selesai" value="{{ $endDate }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-800">
                        @error('pendaftaran_kp_selesai')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-semibold mb-1">Catatan Sistem:</p>
                            <ul class="list-disc pl-5 text-xs text-blue-700 space-y-1">
                                <li>Status <b>Terbuka/Ditutup</b> adalah pengaturan mutlak (master switch).</li>
                                <li>Bila status <b>Terbuka</b>, sistem akan mengecek tanggal mulai dan selesai. Jika
                                    hari ini berada di luar rentang tanggal tersebut, pendaftaran akan otomatis ditutup.
                                </li>
                                <li>Kosongkan tanggal jika ingin membuka pendaftaran tanpa batas waktu tertentu.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-slate-100 pt-6 mt-6">
                    <button type="submit"
                        class="flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 font-bold text-sm focus:ring-4 focus:ring-indigo-100 outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-eoffice::layouts.koordinator>