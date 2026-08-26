<x-eoffice::layouts.dosen title="Ruang Bimbingan">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @section('breadcrumbs')
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}"
                    class="hover:text-indigo-600 transition-colors">Bimbingan Mahasiswa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-slate-900 font-medium">Bimbingan Detail</span>
            </div>
        @endsection
        @if(session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div
                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Card: Student Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div
                class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-gradient-to-br from-indigo-900 to-slate-900 text-white">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-full bg-indigo-500/30 flex items-center justify-center border border-indigo-400/30 text-2xl font-bold">
                        {{ substr($kp->nama_mahasiswa ?? 'M', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ $kp->nama_mahasiswa ?? 'Nama Mahasiswa' }}</h1>
                        <p class="text-indigo-200 text-sm font-medium">{{ $kp->nim ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 md:items-end">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm font-bold shadow-sm uppercase tracking-wider
                        @if($kp->status_kp === 'completed') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                        @elseif($kp->status_kp === 'active') bg-blue-500/20 text-blue-300 border-blue-500/30
                        @else bg-slate-500/20 text-slate-300 border-slate-500/30 @endif">
                        <span
                            class="w-2 h-2 rounded-full @if($kp->status_kp === 'completed') bg-emerald-400 @elseif($kp->status_kp === 'active') bg-blue-400 @else bg-slate-400 @endif"></span>
                        {{ strtoupper($kp->status_kp) }}
                    </div>
                    @if($kp->status_kp === 'pending')
                        <form action="{{ route('eoffice.kp.dosen.bimbingan.approve_pra_kp', $kp->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Setujui Topik dan Tempat KP mahasiswa ini?')"
                                class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 border border-indigo-400 text-white font-bold text-sm rounded-xl transition-all shadow-lg hover:shadow-indigo-500/30">
                                Validasi Topik (Aktifkan KP)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if($seminar)
            <!-- Card: Seminar (Optional) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Jadwal Seminar Akhir
                    </h3>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $seminar->status_validasi_dosen === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($seminar->status_validasi_dosen === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $seminar->status_validasi_dosen === 'pending' ? 'Menunggu Persetujuan' : ($seminar->status_validasi_dosen === 'approved' ? 'Disetujui' : 'Ditolak') }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 text-sm mb-1">Mahasiswa memohon waktu seminar pada:</p>
                            <p class="text-xl font-black text-slate-800">
                                {{ \Carbon\Carbon::parse($seminar->tanggal_seminar)->translatedFormat('l, d M Y') }}
                                <span
                                    class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg text-lg ml-2">{{ $seminar->waktu_seminar }}</span>
                            </p>
                        </div>
                        @if($seminar->status_validasi_dosen === 'pending')
                            <div class="flex gap-2">
                                <form action="{{ route('eoffice.kp.dosen.penilaian_seminar.reject', $kp->id) }}" method="POST"
                                    onsubmit="let note = prompt('Masukkan alasan penolakan dan usulan jadwal baru:'); if(note == null) return false; this.querySelector('.catatan-dosen').value = note;">
                                    @csrf
                                    <input type="hidden" name="catatan_dosen" class="catatan-dosen" value="">
                                    <button type="submit"
                                        class="px-4 py-2 bg-white hover:bg-slate-50 border-2 border-red-200 text-red-600 font-bold text-sm rounded-lg transition-colors">Tolak/Reschedule</button>
                                </form>
                                <form action="{{ route('eoffice.kp.dosen.penilaian_seminar.approve', $kp->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Setujui
                                        Waktu</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @php
            // 1. Validasi Dokumen: Semua file yang diunggah harus disetujui (minimal 1 file harus ada).
            $unapprovedExists = false;
            foreach ($kp->dokumen as $dok) {
                if ($dok->approval_status !== 'approved') {
                    $unapprovedExists = true;
                    break;
                }
            }
            $hasFiles = $kp->dokumen->count() > 0;
            $dokumenTervalidasi = $hasFiles && !$unapprovedExists;

            // 2. Validasi Operasional: Jadwal Seminar harus sudah dikonfirmasi
            $seminarConfirmed = $seminar && $seminar->status_validasi_dosen === 'approved';

            // 3. Double-Lock: Kunci rubrik jika dokumen BELUM tervalidasi ATAU seminar BELUM dikonfirmasi.
            $rubrikLocked = !$dokumenTervalidasi || !$seminarConfirmed;
        @endphp

        <!-- Card: Table Dokumen Syarat -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="px-6 py-5 flex justify-between items-center border-b border-slate-100 bg-slate-50">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Validasi Dokumen Syarat</h3>
                    <p class="text-sm text-slate-500 mt-1">Status semua dokumen harus Disetujui (Approved) sebelum
                        rubrik penilaian terbuka.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider w-1/3">Jenis
                                Dokumen</th>
                            <th
                                class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider whitespace-nowrap">
                                File</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-right">
                                Aksi Dosen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kp->dokumen as $dokumen)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <p class="text-sm font-bold text-slate-800">{{ $dokumen->jenis_dokumen }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Upload:
                                                        {{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y') }}
                                                    </p>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                                                                                                                                                                                                                                                                {{ $dokumen->approval_status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                            ($dokumen->approval_status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                                        @if($dokumen->approval_status === 'approved')
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Disetujui
                                                        @elseif($dokumen->approval_status === 'rejected')
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Revisi
                                                        @else
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Menunggu
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <a href="{{ $dokumen->file_url }}" target="_blank"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-[#0065FF] hover:bg-slate-50 transition-colors shadow-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                        Cetak PDF
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <!-- Reject Action with Alpine Modal -->
                                                        @if($dokumen->approval_status === 'pending' && $dokumen->status_validasi === 'menunggu')
                                                            <div x-data="{ openRejectModal: false, note: '' }">
                                                                <form
                                                                    action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$kp->id, $dokumen->id]) }}"
                                                                    method="POST" x-ref="rejectForm">
                                                                    @csrf
                                                                    <input type="hidden" name="revision_note" x-model="note">
                                                                    <button type="button" @click="openRejectModal = true"
                                                                        class="px-3 py-1.5 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-500 hover:text-red-600 rounded-lg text-sm font-bold transition-all"
                                                                        title="Tolak / Minta Revisi">
                                                                        Revisi
                                                                    </button>

                                                                    <!-- Modal Base -->
                                                                    <div x-show="openRejectModal" class="relative z-[100]"
                                                                        aria-labelledby="modal-title" role="dialog" aria-modal="true"
                                                                        style="display: none;">
                                                                        <!-- Backdrop -->
                                                                        <div x-show="openRejectModal" x-transition:enter="ease-out duration-300"
                                                                            x-transition:enter-start="opacity-0"
                                                                            x-transition:enter-end="opacity-100"
                                                                            x-transition:leave="ease-in duration-200"
                                                                            x-transition:leave-start="opacity-100"
                                                                            x-transition:leave-end="opacity-0"
                                                                            class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity">
                                                                        </div>

                                                                        <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
                                                                            <div
                                                                                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                                                <!-- Modal Panel -->
                                                                                <div x-show="openRejectModal"
                                                                                    @click.away="openRejectModal = false"
                                                                                    x-transition:enter="ease-out duration-300"
                                                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                                    x-transition:leave="ease-in duration-200"
                                                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">

                                                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                                        <div class="sm:flex sm:items-start text-left">
                                                                                            <div
                                                                                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                                                <svg class="h-6 w-6 text-red-600" fill="none"
                                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round"
                                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                                                                    </path>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div
                                                                                                class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                                                <h3 class="text-lg leading-6 font-bold text-slate-900"
                                                                                                    id="modal-title">
                                                                                                    Minta Revisi Dokumen
                                                                                                </h3>
                                                                                                <div class="mt-2">
                                                                                                    <p class="text-sm text-slate-500">
                                                                                                        Apakah Anda yakin meminta mahasiswa
                                                                                                        untuk merevisi dokumen <span
                                                                                                            class="font-bold text-slate-700">{{ $dokumen->jenis_dokumen }}</span>
                                                                                                        ini? Silakan tulis pesan atau catatan
                                                                                                        revisi di bawah.
                                                                                                    </p>
                                                                                                    <textarea x-model="note" rows="3"
                                                                                                        class="mt-4 w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 sm:text-sm font-medium text-slate-800 placeholder-slate-400 p-3"
                                                                                                        placeholder="Contoh: Format laporan salah, tolong ikuti template bab 2..."></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                                                                                        <button type="button" @click="$refs.rejectForm.submit()"
                                                                                            class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                                                                                            Kirim Revisi
                                                                                        </button>
                                                                                        <button type="button"
                                                                                            @click="openRejectModal = false; note = ''"
                                                                                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                                                                            Batal
                                                                                        </button>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endif
                                                        <!-- Approve Action -->
                                                        @if($dokumen->approval_status === 'pending' && $dokumen->status_validasi === 'menunggu')
                                                            <form
                                                                action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$kp->id, $dokumen->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" onclick="return confirm('Setujui dokumen ini?')"
                                                                    class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white border-transparent rounded-lg text-sm font-bold transition-all shadow-sm shadow-emerald-500/20"
                                                                    title="Setujui Dokumen">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    <span>Mahasiswa belum mengunggah dokumen apapun ke sistem.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card: Table Rubrik Penilaian -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">

            @if($rubrikLocked)
                <!-- Educational Banner Alert -->
                <div class="bg-amber-50 border-b border-amber-200 px-6 py-4 flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800">Pengisian Nilai Terkunci</h3>
                        <p class="text-sm text-amber-700 mt-1">
                            Anda dapat meninjau komponen rubrik ini, namun form input baru akan <strong>terbuka
                                otomatis</strong> setelah seluruh dokumen syarat disetujui penuh DAN jadwal pelaksanaan
                            seminar KP telah dikonfirmasi.
                        </p>
                    </div>
                </div>
            @endif

            <div class="px-6 py-5 flex justify-between items-center border-b border-slate-100 bg-slate-50">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Format Rubrik Penilaian Dosen</h3>
                    <p class="text-sm text-slate-500 mt-1">Berikan rentang nilai 0 - 100 untuk setiap aspek elemen di
                        bawah ini.</p>
                </div>
            </div>

            <form action="{{ route('eoffice.kp.dosen.bimbingan.penilaian.store', $kp->id) }}" method="POST"
                id="form-penilaian">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200">
                                <th
                                    class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider w-[5%] text-center">
                                    No</th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider w-[60%]">
                                    Aspek Komponen Penilaian</th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-center">
                                    Bobot</th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-right pr-10">
                                    Nilai (0-100)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rubrikItems as $index => $item)
                                <tr
                                    class="hover:bg-indigo-50/30 transition-colors {{ $item->nilai_angka !== null ? 'bg-indigo-50/10' : '' }}">
                                    <td class="px-6 py-4 text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ $item->deskripsi }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $item->bobot }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end">
                                            <input type="number" step="0.01" min="0" max="100" name="nilai_{{ $item->id }}"
                                                value="{{ old('nilai_' . $item->id, $item->nilai_angka) }}" required
                                                placeholder="0" {{ $rubrikLocked ? 'disabled' : '' }}
                                                class="w-28 text-center rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 font-bold text-lg text-indigo-900 bg-white disabled:bg-slate-100 disabled:text-slate-400">
                                        </div>
                                        @error('nilai_' . $item->id)
                                            <p class="mt-1 text-xs text-red-600 font-medium w-full text-right">{{ $message }}
                                            </p>
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        Format Master Rubrik dari Koordinator belum diatur. Anda tidak dapat melakukan
                                        penilaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(!$rubrikLocked && !empty($rubrikItems))
                    <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end">
                        <button type="submit"
                            onclick="return confirm('Simpan hasil evaluasi matriks ini? (Tindakan ini akan mengoverwrite Nilai Akhir mahasiswa secara otomatis)')"
                            class="px-6 py-2.5 bg-[#0065FF] hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg border border-blue-600 hover:shadow-blue-500/30 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Evaluasi Rubrik Dosen
                        </button>
                    </div>
                @endif
            </form>
        </div>

    </div>
</x-eoffice::layouts.dosen>