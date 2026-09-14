<x-eoffice::layouts.dosen title="Penilaian Seminar">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Penilaian Seminar</span>
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
                    <h1 class="text-2xl font-bold text-slate-900">Penilaian Seminar & Approval Jadwal</h1>
                    <p class="text-sm text-slate-500 mt-1">Tinjau jadwal seminar yang diajukan dan berikan nilai seminar
                        mahasiswa.</p>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h2 class="text-base font-semibold text-slate-800">Daftar Seminar KP Mahasiswa</h2>
                    </div>

                    @if($seminars->isEmpty())
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm font-medium text-slate-900">Belum ada pengajuan seminar</p>
                            <p class="text-xs text-slate-500 mt-1">Mahasiswa bimbingan Anda belum mengajukan jadwal seminar
                                KP.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Mahasiswa</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Jadwal Seminar</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Tempat / Ruangan</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Penilaian</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Approval Jadwal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($seminars as $sem)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded bg-primary-100 flex items-center justify-center text-primary-500 font-bold text-xs">
                                                        {{ strtoupper(substr($sem->nama_mahasiswa ?? 'M', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-slate-900">
                                                            {{ $sem->nama_mahasiswa ?? 'Mahasiswa' }}</p>
                                                        <p class="text-xs text-slate-500">{{ $sem->nim }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ $sem->tanggal_seminar ? \Carbon\Carbon::parse($sem->tanggal_seminar)->format('d M Y') : '-' }}
                                                </p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    @php
                                                        $startTime = $sem->waktu_seminar;
                                                        $endTime = $startTime ? \Carbon\Carbon::parse($startTime)->addHours(2)->format('H:i') : '';
                                                    @endphp
                                                    Jam:
                                                    {{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '-' }} -
                                                    {{ $endTime ? $endTime : '-' }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-sm text-slate-700">{{ $sem->ruangan ?? 'Belum ditentukan' }}</span>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                @if($sem->nilai_seminar_pembimbing !== null)
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-lg border border-emerald-100 shadow-sm"
                                                        @click="openDrawer({{ $sem->kp_id }}, '{{ $sem->nama_mahasiswa }}', '{{ $sem->nim }}', {{ $sem->nilai_seminar_pembimbing }})"
                                                        style="cursor: pointer;" title="Edit Nilai">
                                                        {{ $sem->nilai_seminar_pembimbing }}
                                                    </span>
                                                @else
                                                    <button
                                                        @click="openDrawer({{ $sem->kp_id }}, '{{ $sem->nama_mahasiswa }}', '{{ $sem->nim }}', null)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-primary-50 text-primary-500 hover:bg-primary-100 border border-primary-200 rounded-lg text-xs font-semibold transition-colors">
                                                        Beri Nilai
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                @if($sem->status_validasi_dosen == 'pending')
                                                    <div class="flex items-center justify-center gap-2">
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.penilaian_seminar.reject', $sem->kp_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" title="Tolak"
                                                                class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.penilaian_seminar.approve', $sem->kp_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" title="Setujui"
                                                                class="p-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($sem->status_validasi_dosen == 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Disetujui
                                                    </span>
                                                @elseif($sem->status_validasi_dosen == 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

    <!-- Slide Over Drawer (From Left to Right) -->
    <div x-cloak x-show="drawerOpen" class="relative z-50" aria-labelledby="slide-over-title" role="dialog"
        aria-modal="true">
        <div x-show="drawerOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeDrawer()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Slide from RIGHT -->
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div x-show="drawerOpen"
                        x-transition:enter="transform transition ease-out duration-300 sm:duration-400"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in duration-300"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        class="pointer-events-auto w-screen max-w-lg">

                        <div
                            class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-200">

                            <!-- Drawer Header -->
                            <div
                                class="px-6 py-6 border-b border-slate-100 bg-slate-50/50 sticky top-0 z-10 backdrop-blur-md flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900" id="slide-over-title">Form Penilaian
                                        Seminar</h2>
                                    <p class="text-sm text-slate-500 mt-1">Beri atau ubah nilai seminar mahasiswa.</p>
                                </div>
                                <button type="button" @click="closeDrawer()"
                                    class="rounded-lg bg-white p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <span class="sr-only">Tutup</span>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body -->
                            <div class="flex-1 px-6 py-6 space-y-6">
                                <!-- Info Mahasiswa -->
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-500 font-bold text-lg border-2 border-white shadow-sm flex-shrink-0"
                                        x-text="selectedStudentName.charAt(0).toUpperCase()">
                                    </div>
                                    <div>
                                        <h1 class="text-lg font-bold text-slate-900" x-text="selectedStudentName"></h1>
                                        <p class="text-sm text-slate-500">NIM: <span
                                                class="font-semibold text-slate-700" x-text="selectedStudentNim"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-primary-50 border border-primary-200 rounded-xl p-4 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary-500 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-primary-500">Nilai Dosen Pembimbing</p>
                                        <p class="text-xs text-primary-500 mt-1">Bagian ini hanya untuk <strong>Nilai
                                                Seminar Pembimbing</strong>. Nilai Lapangan diisi oleh Koordinator KP.
                                        </p>
                                    </div>
                                </div>

                                <form :action="'/eoffice/kp/dosen/bimbingan/' + selectedKpId + '/penilaian'"
                                    method="POST">
                                    @csrf
                                    <div>
                                        <label for="nilai_seminar_pembimbing"
                                            class="block text-sm font-semibold text-slate-700 mb-2">
                                            Nilai Seminar <span class="text-red-500">*</span>
                                            <span class="text-slate-400 font-normal">(0 - 100)</span>
                                        </label>
                                        <input type="number" id="nilai_seminar_pembimbing"
                                            name="nilai_seminar_pembimbing" min="0" max="100" step="0.01"
                                            x-model="selectedNilai" placeholder="Contoh: 85"
                                            class="w-full px-4 py-3 text-2xl font-bold text-slate-900 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder:text-slate-300 placeholder:font-normal placeholder:text-base"
                                            required>
                                    </div>

                                    <div class="mt-8 flex gap-3">
                                        <button type="button" @click="closeDrawer()"
                                            class="flex-1 px-6 py-3 border border-slate-300 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors text-sm text-center">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="flex-1 px-6 py-3 bg-primary-500 hover:bg-primary-500 text-white font-bold rounded-xl shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Simpan Nilai
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('penilaianApp', () => ({
                sidebarOpen: false,
                drawerOpen: false,
                selectedKpId: null,
                selectedStudentName: '',
                selectedStudentNim: '',
                selectedNilai: null,

                openDrawer(kpId, name, nim, nilai) {
                    this.selectedKpId = kpId;
                    this.selectedStudentName = name || 'Mahasiswa';
                    this.selectedStudentNim = nim || '';
                    this.selectedNilai = nilai;
                    this.drawerOpen = true;
                },

                closeDrawer() {
                    this.drawerOpen = false;
                    setTimeout(() => {
                        this.selectedKpId = null;
                        this.selectedStudentName = '';
                        this.selectedStudentNim = '';
                        this.selectedNilai = null;
                    }, 300);
                }
            }));
        });
    </script>
    @endpush
</x-eoffice::layouts.dosen>