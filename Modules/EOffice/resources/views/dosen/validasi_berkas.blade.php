<x-eoffice::layouts.dosen title="Penilaian Laporan">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Penilaian Laporan</span>
    @endsection

                <!-- Flash Messages -->
                @if(session('success'))
                    <div
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div
                        class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900">Penilaian Laporan</h1>
                    <p class="text-sm text-slate-500 mt-1">Tinjau dan setujui laporan serta makalah KP mahasiswa
                        bimbingan Anda.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-slate-500">Total Dokumen</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl border border-amber-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-amber-600">Menunggu Review</p>
                        <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-emerald-600">Disetujui (ACC)</p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="bg-red-50 rounded-xl border border-red-200 p-4 shadow-sm">
                        <p class="text-xs font-medium text-red-600">Perlu Revisi</p>
                        <p class="text-2xl font-bold text-red-700 mt-1">{{ $stats['rejected'] }}</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h2 class="text-base font-semibold text-slate-800">Daftar Dokumen Masuk</h2>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-56">
                                <input type="text" id="searchInput" placeholder="Cari mahasiswa..."
                                    class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="filterTable()">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($dokumens->isEmpty())
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-medium text-slate-900">Belum ada dokumen yang diunggah</p>
                            <p class="text-xs text-slate-500 mt-1">Mahasiswa bimbingan Anda belum mengunggah Laporan atau
                                Makalah KP.</p>
                        </div>
                    @else
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200" id="dokumenTable">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Mahasiswa</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Jenis Dokumen</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            File</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Tgl Upload</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($dokumens as $dok)
                                        <tr class="hover:bg-slate-50 transition-colors dok-row"
                                            data-name="{{ strtolower($dok->nama_mahasiswa ?? '') }} {{ strtolower($dok->nim ?? '') }}">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                        {{ strtoupper(substr($dok->nama_mahasiswa ?? 'M', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-slate-900">
                                                            {{ $dok->nama_mahasiswa ?? 'Mahasiswa' }}</p>
                                                        <p class="text-xs text-slate-500">{{ $dok->nim }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold
                                                {{ $dok->jenis_dokumen == 'Laporan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $dok->jenis_dokumen }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                @if($dok->file_path)
                                                    <a href="{{ $dok->file_url }}" target="_blank"
                                                        class="text-xs text-blue-600 hover:text-blue-800 underline font-medium flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ basename($dok->file_path) }}
                                                    </a>
                                                @else
                                                    <span class="text-xs text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                                {{ $dok->tanggal_upload ? \Carbon\Carbon::parse($dok->tanggal_upload)->format('d M Y') : '—' }}
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                @if($dok->approval_status == 'pending')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>Menunggu
                                                        Review
                                                    </span>
                                                @elseif($dok->approval_status == 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>Disetujui
                                                    </span>
                                                @elseif($dok->approval_status == 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5"></span>Perlu Revisi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                                @if($dok->approval_status == 'pending')
                                                    <div class="flex items-center justify-end gap-2">
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$dok->kp_id, $dok->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                                                Revisi
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$dok->kp_id, $dok->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                ACC
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($dok->approval_status == 'approved')
                                                    <span class="text-xs text-slate-400">Sudah di-ACC</span>
                                                @else
                                                    <span class="text-xs text-slate-400">Menunggu revisi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List -->
                        <div class="md:hidden divide-y divide-slate-100" id="mobileList">
                            @foreach($dokumens as $dok)
                                <div class="p-4 dok-row"
                                    data-name="{{ strtolower($dok->nama_mahasiswa ?? '') }} {{ strtolower($dok->nim ?? '') }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-9 w-9 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                {{ strtoupper(substr($dok->nama_mahasiswa ?? 'M', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ $dok->nama_mahasiswa ?? 'Mahasiswa' }}</p>
                                                <p class="text-xs text-slate-500">{{ $dok->nim }} · {{ $dok->jenis_dokumen }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($dok->approval_status == 'pending')
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700">Pending</span>
                                        @elseif($dok->approval_status == 'approved')
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">ACC</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700">Revisi</span>
                                        @endif
                                    </div>
                                    @if($dok->file_path)
                                        <a href="{{ $dok->file_url }}" target="_blank"
                                            class="text-xs text-blue-600 underline mb-3 block">
                                            {{ basename($dok->file_path) }}
                                        </a>
                                    @endif
                                    @if($dok->approval_status == 'pending')
                                        <div class="flex gap-2 mt-2">
                                            <form
                                                action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.reject', [$dok->kp_id, $dok->id]) }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button
                                                    class="w-full py-2 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg">Revisi</button>
                                            </form>
                                            <form
                                                action="{{ route('eoffice.kp.dosen.bimbingan.dokumen.approve', [$dok->kp_id, $dok->id]) }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button
                                                    class="w-full py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg">ACC</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer -->
                        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                            <p class="text-xs text-slate-500">Total {{ $dokumens->count() }} dokumen ditemukan</p>
                        </div>
                    @endif
                </div>
    @push('scripts')
    <script>
        function filterTable() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.dok-row').forEach(row => {
                row.style.display = row.dataset.name.includes(keyword) ? '' : 'none';
            });
        }
    </script>
    @endpush
</x-eoffice::layouts.dosen>