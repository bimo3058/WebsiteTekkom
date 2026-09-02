<x-eoffice::layouts.koordinator title="Detail Mahasiswa">
    @section('breadcrumbs')
        <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}"
            class="text-slate-400 hover:text-slate-800 transition-colors">Data Mahasiswa</a>
        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-800 font-semibold truncate max-w-[200px]">{{ $m->nama }}</span>
    @endsection

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        </style>
    @endpush

    <!-- Header Alert -->
    @if(session('success'))
        <div
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-bold">{{ session('success') }}</p>
                @if(session('info'))
                    <p class="text-[13px] mt-1">{{ session('info') }}</p>
                @endif
            </div>
        </div>
    @endif
    @if(session('error'))
        <div
            class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start mb-8">
        <!-- Sidebar Kiri : Profil (Blok 1) -->
        <div class="w-full lg:w-1/3 flex flex-col gap-6 shrink-0 lg:sticky lg:top-24">

            <!-- Foto & Detail Mahasiswa -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col items-center">
                <div
                    class="w-20 h-20 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center text-3xl font-black text-indigo-600 mb-4 shadow-inner">
                    {{ substr($m->nama, 0, 1) }}
                </div>
                <h2 class="text-lg font-extrabold text-slate-800 text-center leading-tight mb-1">{{ $m->nama }}</h2>
                <span
                    class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 font-mono tracking-widest mb-4 border border-slate-200 shadow-sm">{{ $m->nim }}</span>

                <div class="w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent mb-4"></div>

                <div class="w-full space-y-3">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider shrink-0">Status
                            KP</span>
                        <span
                            class="text-xs font-bold px-2 py-0.5 rounded-full whitespace-nowrap {{ $m->status_kp === 'Pra KP' ? 'bg-amber-50 text-amber-600' : ($m->status_kp === 'Saat KP' ? 'bg-blue-50 text-blue-600' : (in_array($m->status_kp, ['Dibatalkan', 'Gagal']) ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600')) }}">
                            {{ $m->status_kp }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1 text-left">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Dosen
                            Pembimbing</span>
                        <span
                            class="text-[13px] font-bold text-slate-800 leading-normal">{{ $m->dosen_pembimbing }}</span>
                    </div>
                    <div class="flex flex-col gap-1 text-left">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tempat KP
                            (Perusahaan)</span>
                        <span class="text-[13px] font-bold text-slate-800 leading-normal">{{ $m->tempat_kp }}</span>
                    </div>
                    <div class="flex flex-col gap-1 text-left">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Judul
                            Laporan</span>
                        <span class="text-[13px] font-bold text-slate-800 leading-normal">{{ $m->judul_kp }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4 mt-2">
                        <div class="flex flex-col gap-1 text-left w-1/2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Periode
                                KP</span>
                            <span
                                class="text-[13px] font-bold text-slate-800 leading-normal">{{ $m->periode_name }}</span>
                        </div>
                        <div class="flex flex-col gap-1 text-left w-1/2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">IPK /
                                SKS</span>
                            <span class="text-[13px] font-bold text-slate-800 leading-normal">{{ $m->ipk }} /
                                {{ $m->sks_diambil }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Edit Konfigurasi Khusus -->
            <form action="{{ route('eoffice.kp.koordinator.data_mahasiswa.update', $m->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
                    x-data="{ expanded: false }">
                    <button type="button" @click="expanded = !expanded"
                        class="w-full p-4 flex items-center justify-between text-left hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3 text-slate-700">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                                <svg class="w-4 h-4 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="font-bold text-[13px]">Kongurasi & Migrasi</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                            :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" x-collapse x-cloak
                        class="border-t border-slate-100 p-5 bg-slate-50 space-y-4">
                        <!-- Override DOSEN -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Override Dosen
                                Pembimbing</label>
                            <select name="dosen_pembimbing_id"
                                class="w-full text-xs font-semibold text-slate-600 border border-slate-200 rounded-lg px-2.5 py-2 shadow-sm focus:ring-1 focus:ring-indigo-500">
                                <option value="">-- Kosongi untuk lepas --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ $m->dosen_pembimbing_id == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Override KELAS -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alokasi Kelas</label>
                            <select name="kelas"
                                class="w-full text-xs font-semibold text-slate-600 border border-slate-200 rounded-lg px-2.5 py-2 shadow-sm focus:ring-1 focus:ring-indigo-500">
                                <option value="" {{ $m->kelas === '-' ? 'selected' : '' }}>-- Belum Dialokasikan --
                                </option>
                                @if(!empty($m->kelas_dibuka))
                                    @foreach($m->kelas_dibuka as $k)
                                        <option value="{{ $k }}" {{ $m->kelas === $k ? 'selected' : '' }}>{{ $k }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>-- Periode Tidak Membuka Kelas --</option>
                                @endif
                            </select>
                        </div>
                        <hr class="border-rose-100">
                        <!-- Override Periode -->
                        <div>
                            <label class="block text-xs font-bold text-rose-700 mb-1.5">Override Periode
                                (Migrasi)</label>
                            <select name="force_periode"
                                class="w-full text-xs font-semibold text-rose-600 border border-rose-200 bg-rose-50 rounded-lg px-2.5 py-2 hover:bg-rose-100 focus:ring-0 outline-none mb-3">
                                <option value="">-- Tetap Pada Periode Saat Ini --</option>
                                @foreach($periodes as $p)
                                    <option value="{{ $p->id }}" {{ $m->periode_id == $p->id ? 'selected' : '' }}>
                                        Sem. {{ $p->semester }} {{ $p->tahun_ajaran }} {{ $p->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <hr class="border-rose-100">
                        <!-- Override Status KP -->
                        <div x-data="{ forceStatus: '' }">
                            <label class="block text-xs font-bold text-rose-700 mb-1.5 mt-1">Override Status
                                Akademik</label>
                            <select name="force_status" x-model="forceStatus"
                                class="w-full text-xs font-semibold text-rose-600 border border-rose-200 bg-rose-50 rounded-lg px-2.5 py-2 hover:bg-rose-100 focus:ring-0 outline-none">
                                <option value="" {{ !in_array($m->status_kp, ['Dibatalkan', 'Selesai']) ? 'selected' : '' }}>-- Ikuti Sistem (Otomatis) --</option>
                                <option value="Dibatalkan" {{ $m->status_kp === 'Dibatalkan' ? 'selected' : '' }}>Batalkan
                                    Pelaksanaan KP (Gagal)</option>
                                <option value="Selesai" {{ $m->status_kp === 'Selesai' ? 'selected' : '' }}>Luluskan /
                                    Selesai</option>
                            </select>

                            <div x-show="forceStatus === 'Dibatalkan' || '{{ $m->status_kp }}' === 'Dibatalkan'"
                                x-transition x-cloak class="mt-3">
                                <label class="block text-[11px] font-bold text-rose-700 mb-1">Catatan Pembatalan <span
                                        class="text-rose-500">*</span></label>
                                <textarea name="keterangan_status" rows="2"
                                    class="w-full text-[11px] text-slate-700 border border-rose-200 bg-rose-50 rounded-lg p-2 focus:ring-1 focus:ring-rose-500"
                                    placeholder="Wajib diisi jika gagal/batal. Contoh: DO dari perusahaan.">{{ $m->keterangan_status ?? '' }}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full mt-2 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-bold shadow-sm transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan : Dokumen & Penilaian -->
        <div class="w-full lg:w-2/3 flex flex-col gap-6">

            <!-- BLOK 2: Validasi Koordinator -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-emerald-50/30 flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                        <svg class="w-4 h-4 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Validasi Koordinator KP</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Berisi daftar dokumen milik
                            mahasiswa (contoh: Surat Pengantar) yang secara struktural harus disetujui / dievaluasi oleh
                            tangan Anda (Koordinator).</p>
                    </div>
                </div>

                <div class="p-0">
                    @if($dokumenKoor->isEmpty())
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <p class="text-[13px] font-semibold text-slate-500">Belum ada dokumen yang perlu divalidasi oleh
                                Anda.</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse table-auto">
                            <tbody class="border-t-0 border-slate-200 divide-y divide-slate-100">
                                @foreach($dokumenKoor as $dok)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 w-1/2">
                                            <div class="flex items-start gap-3">
                                                @php
                                                    $ext = strtolower(pathinfo($dok->file_path, PATHINFO_EXTENSION));
                                                    $isPdf = $ext === 'pdf';
                                                @endphp
                                                <div
                                                    class="mt-0.5 shrink-0 w-8 h-8 rounded-lg {{ $isPdf ? 'bg-rose-50 text-rose-500' : 'bg-blue-50 text-blue-500' }} flex items-center justify-center shadow-sm border {{ $isPdf ? 'border-rose-100' : 'border-blue-100' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-[13px] font-extrabold text-slate-800 mb-1 leading-tight">
                                                        {{ $dok->jenis_dokumen }}
                                                    </p>
                                                    <span
                                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold tracking-widest uppercase shadow-sm border border-slate-200 {{ $dok->approval_status === 'approved' || $dok->status_validasi === 'disetujui' ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : ($dok->approval_status === 'rejected' || $dok->status_validasi === 'ditolak' ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-slate-100 text-slate-500') }}">
                                                        {{ $dok->approval_status === 'approved' || $dok->status_validasi === 'disetujui' ? 'Disetujui' : ($dok->approval_status === 'rejected' || $dok->status_validasi === 'ditolak' ? 'Revisi' : 'Menunggu') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(($dok->approval_status === 'rejected' || $dok->status_validasi === 'ditolak') && $dok->revision_note)
                                                <div
                                                    class="mt-3 text-xs bg-rose-50/50 text-rose-700 py-1.5 px-3 rounded-md border border-rose-100 font-medium italic">
                                                    <span class="font-bold">📝 Catatan:</span> {{ $dok->revision_note }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                                <!-- Tombol Buka/Download File -->
                                                <a href="{{ $dok->file_url }}" target="_blank"
                                                    class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-[13px] font-bold shadow-sm hover:text-indigo-600 hover:border-indigo-200 hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Buka
                                                </a>

                                                <!-- Tombol Aksi Koor -->
                                                @if($dok->approval_status !== 'approved' && $dok->status_validasi !== 'disetujui')
                                                    <!-- Reject Modal Alpine -->
                                                    <div x-data="{ openRejectModal: false, note: '' }" class="inline-block">
                                                        <form
                                                            action="{{ route('eoffice.kp.koordinator.data_mahasiswa.dokumen.reject', ['kp_id' => $kp->id, 'dokumen_id' => $dok->id]) }}"
                                                            method="POST" x-ref="rejectForm" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="revision_note" x-model="note">
                                                            <button type="button" @click="openRejectModal = true"
                                                                class="px-3 py-1.5 bg-white border border-slate-200 text-red-500 rounded-lg text-[13px] font-bold shadow-sm hover:border-red-200 hover:bg-red-50 transition-colors flex items-center gap-1.5">
                                                                Tolak
                                                            </button>

                                                            <div x-show="openRejectModal" class="relative z-[100]"
                                                                aria-labelledby="modal-title" role="dialog" aria-modal="true"
                                                                style="display: none;">
                                                                <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity"
                                                                    x-show="openRejectModal"
                                                                    x-transition:enter="ease-out duration-300"
                                                                    x-transition:enter-start="opacity-0"
                                                                    x-transition:enter-end="opacity-100"
                                                                    x-transition:leave="ease-in duration-200"
                                                                    x-transition:leave-start="opacity-100"
                                                                    x-transition:leave-end="opacity-0"></div>
                                                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                                    <div
                                                                        class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                                        <div x-show="openRejectModal"
                                                                            x-transition:enter="ease-out duration-300"
                                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                            x-transition:leave="ease-in duration-200"
                                                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                                            <div
                                                                                class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-t-0 shadow-sm border-slate-100">
                                                                                <div class="sm:flex sm:items-start">
                                                                                    <div
                                                                                        class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                                        <svg class="h-5 w-5 text-red-600"
                                                                                            fill="none" viewBox="0 0 24 24"
                                                                                            stroke-width="2.5"
                                                                                            stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div
                                                                                        class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                                                        <h3
                                                                                            class="text-sm font-bold leading-6 text-gray-900 mb-2">
                                                                                            Tolak Dokumen Koor
                                                                                            ({{ $dok->jenis_dokumen }})</h3>
                                                                                        <div>
                                                                                            <label for="catatan"
                                                                                                class="block text-xs font-semibold text-slate-500 mb-1 pointer-events-none">Alasan
                                                                                                Penolakan / Catatan Revisi:</label>
                                                                                            <textarea x-model="note" rows="3"
                                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-20 transition-all font-medium"
                                                                                                placeholder="Tuliskan alasan penolakan dokumen agar mahasiswa dapat merevisinya..."></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                                                                                <button type="button"
                                                                                    @click="$refs.rejectForm.submit()"
                                                                                    class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Kirim
                                                                                    Revisi</button>
                                                                                <button type="button"
                                                                                    @click="openRejectModal = false; note = ''"
                                                                                    class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- Approve Buton -->
                                                    <form
                                                        action="{{ route('eoffice.kp.koordinator.data_mahasiswa.dokumen.approve', ['kp_id' => $kp->id, 'dokumen_id' => $dok->id]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Anda yakin menyetujui dokumen ini?')"
                                                            class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 border border-emerald-500 text-white rounded-lg text-[13px] font-bold shadow-sm transition-colors flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- BLOK 3: Pantauan Dosen (Read Only) -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-2">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                            <svg class="w-4 h-4 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Pantauan Riwayat Pembimbing (Read Only)</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Berisi daftar dokumen
                                laporan/makalah akademis yang ranah periksanya (approver_role) diserahkan penuh secara
                                struktural ke Dosen Pembimbing.</p>
                        </div>
                    </div>
                </div>

                <div class="p-0">
                    @if($dokumenDosen->isEmpty())
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <p class="text-[13px] font-semibold text-slate-500 mb-2">Mahasiswa ini belum mengirimkan dokumen
                                ber-atribut (Approver: Dosen).</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse table-auto">
                            <tbody class="border-t-0 border-slate-200 divide-y divide-slate-100">
                                @foreach($dokumenDosen as $dok)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-start gap-3">
                                                @php
                                                    $ext = strtolower(pathinfo($dok->file_path, PATHINFO_EXTENSION));
                                                    $isPdf = $ext === 'pdf';
                                                @endphp
                                                <div
                                                    class="mt-0.5 shrink-0 w-8 h-8 rounded-lg {{ $isPdf ? 'bg-rose-50 text-rose-500' : 'bg-blue-50 text-blue-500' }} flex items-center justify-center shadow-sm border {{ $isPdf ? 'border-rose-100' : 'border-blue-100' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-[13px] font-extrabold text-slate-800 mb-1 leading-tight">
                                                        {{ $dok->jenis_dokumen }}
                                                    </p>
                                                    <span
                                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold tracking-widest uppercase shadow-sm border border-slate-200 {{ $dok->approval_status === 'approved' || $dok->status_validasi === 'disetujui' ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : ($dok->approval_status === 'rejected' || $dok->status_validasi === 'ditolak' ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-slate-100 text-slate-500') }}">
                                                        {{ $dok->approval_status === 'approved' || $dok->status_validasi === 'disetujui' ? 'Disetujui' : ($dok->approval_status === 'rejected' || $dok->status_validasi === 'ditolak' ? 'Revisi' : 'Menunggu Dosen') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                                <a href="{{ $dok->file_url }}" target="_blank"
                                                    class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-[13px] font-bold shadow-sm hover:text-indigo-600 hover:border-indigo-200 hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Pantau File
                                                </a>
                                                @if($dok->approval_status === 'approved' || $dok->approval_status === 'rejected' || $dok->status_validasi === 'disetujui' || $dok->status_validasi === 'ditolak')
                                                    <form
                                                        action="{{ route('eoffice.kp.koordinator.data_mahasiswa.dokumen.reset', ['kp_id' => $kp->id, 'dokumen_id' => $dok->id]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Reset validasi? Dokumen akan kembali berstatus Menunggu untuk diproses ulang oleh Dosen.')"
                                                            class="px-3 py-1.5 bg-white border border-amber-300 text-amber-600 rounded-lg text-[13px] font-bold shadow-sm hover:border-amber-400 hover:bg-amber-50 transition-colors flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            Batal Validasi
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- BLOK 4: Rubrik Koordinator -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-2">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-100">
                        <svg class="w-4 h-4 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Rubrik Penilaian Akhir (Koordinator)</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Formulir isian nilai akhir (0-100)
                            bagi komponen penilaian yang role evaluator nya diserahkan kepada formatur instansi /
                            koordinator magang.</p>
                    </div>
                </div>

                <div class="p-6">
                    @if($komponenKoor->isEmpty())
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <p class="text-[13px] font-semibold text-slate-500 mb-2">Periode akademik saat ini tidak
                                memiliki komponen matriks rubrik yang bisa Anda isi. <br>Anda bisa mengonfigurasinya melalui
                                menu Pengaturan <span class="font-bold">Periode KP (Master Rubrik)</span>.</p>
                        </div>
                    @else
                        <form action="{{ route('eoffice.kp.koordinator.data_mahasiswa.update', $m->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4 max-w-lg">
                                @foreach($komponenKoor as $komp)
                                    <div>
                                        <label
                                            class="block text-[13px] font-bold text-slate-700 mb-1.5">{{ $komp->nama_komponen }}
                                            <span class="text-xs text-slate-400 font-normal italic ml-1">(Bobot:
                                                {{ $komp->bobot }}%)</span></label>
                                        <div class="relative">
                                            <input type="number" step="0.01" min="0" max="100" name="nilai_{{ $komp->id }}"
                                                value="{{ $komp->nilai_angka ?? '' }}"
                                                class="w-full text-sm font-bold text-indigo-700 bg-indigo-50/30 border border-slate-200 rounded-lg px-4 py-2.5 shadow-sm focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-20 transition-all font-mono"
                                                placeholder="Contoh: 85.50">
                                            @if(!empty($komp->nilai_angka))
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-slate-100 my-5">

                            <div class="flex items-center justify-end">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Simpan Nilai
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-eoffice::layouts.koordinator>