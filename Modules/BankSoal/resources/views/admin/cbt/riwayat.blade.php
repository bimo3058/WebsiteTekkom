<x-banksoal::layouts.admin>
    <div class="w-full space-y-6">

        {{-- ===== Page Header ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">Riwayat Ujian Komprehensif</h1>
                <p class="text-sm text-slate-500 mt-0.5">Rekapitulasi nilai akhir mahasiswa yang telah menyelesaikan ujian.</p>
            </div>
        </div>

        {{-- ===== Filter Bar ===== --}}
        <form method="GET" action="{{ route('banksoal.admin.cbt.riwayat') }}"
              class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">

            {{-- Search NIM / Nama --}}
            <div class="relative flex-1 min-w-0">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari NIM atau nama mahasiswa..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 bg-white text-slate-800">
            </div>

            {{-- Filter Periode --}}
            <select name="periode_id"
                    class="text-sm border border-slate-200 rounded-lg bg-white py-2 pl-3 pr-8 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 min-w-[180px]">
                <option value="">Semua Periode</option>
                @foreach($periodes as $periode)
                    <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                        {{ $periode->nama_periode }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Keterangan --}}
            <select name="keterangan"
                    class="text-sm border border-slate-200 rounded-lg bg-white py-2 pl-3 pr-8 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 min-w-[160px]">
                <option value="">Semua Keterangan</option>
                <option value="lulus"     {{ request('keterangan') === 'lulus'     ? 'selected' : '' }}>Lulus</option>
                <option value="mengulang" {{ request('keterangan') === 'mengulang' ? 'selected' : '' }}>Mengulang</option>
            </select>

            {{-- Tombol --}}
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                Cari
            </button>
            @if(request()->hasAny(['q','periode_id','keterangan']))
                <a href="{{ route('banksoal.admin.cbt.riwayat') }}"
                   class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium rounded-lg transition-colors shrink-0 text-center">
                    Reset
                </a>
            @endif
        </form>

        {{-- ===== Table ===== --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                            <th class="px-4 py-[14px] w-10 text-center">No.</th>
                            <th class="px-4 py-[14px] whitespace-nowrap">NIM</th>
                            <th class="px-4 py-[14px] whitespace-nowrap">Nama Mahasiswa</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Ujian Ke-</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Total Poin</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Keterangan</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sessions as $index => $session)
                            @php
                                $nim       = $session->user?->student?->student_number ?? '—';
                                $nama      = $session->user?->name ?? '—';
                                $poin      = $session->score;
                                $lulus     = $poin !== null && $poin >= 60;
                                $ujianKe   = $session->ujian_ke ?? '—';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">

                                {{-- No --}}
                                <td class="px-4 py-[14px] text-center text-slate-400 text-xs">
                                    {{ $sessions->firstItem() + $index }}
                                </td>

                                {{-- NIM --}}
                                <td class="px-4 py-[14px] text-slate-700 font-mono text-xs whitespace-nowrap">
                                    {{ $nim }}
                                </td>

                                {{-- Nama --}}
                                <td class="px-4 py-[14px] font-medium text-slate-800 whitespace-nowrap">
                                    {{ $nama }}
                                </td>

                                {{-- Ujian Ke- --}}
                                <td class="px-4 py-[14px] text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                        {{ $ujianKe > 1 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ujianKe }}
                                    </span>
                                </td>

                                {{-- Total Poin --}}
                                <td class="px-4 py-[14px] text-center">
                                    @if($poin !== null)
                                        <span class="text-sm font-bold {{ $lulus ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $poin }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">—</span>
                                    @endif
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-4 py-[14px] text-center">
                                    @if($poin !== null)
                                        @if($lulus)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                LULUS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                              
                                                MENGULANG
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs italic">Belum dikalkulasi</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-[14px] text-center">
                                    <a href="{{ route('banksoal.admin.cbt.detail', $session->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 border border-slate-100">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 text-sm font-medium">Belum ada riwayat ujian.</p>
                                        @if(request()->hasAny(['q','periode_id','keterangan']))
                                            <p class="text-slate-400 text-xs mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($sessions->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>

        {{-- ===== Ringkasan hasil filter ===== --}}
        @if($sessions->total() > 0)
            <p class="text-xs text-slate-400">
                Menampilkan {{ $sessions->firstItem() }}–{{ $sessions->lastItem() }} dari {{ $sessions->total() }} data
                @if(request()->hasAny(['q','periode_id','keterangan'])) (difilter) @endif
            </p>
        @endif

        {{-- ============================================== --}}
        {{-- ZONA BERBAHAYA: Reset Data Ujian              --}}
        {{-- ============================================== --}}
        <div id="reset-section" x-data="{ showResetModal: false }" class="mt-4">
            <div class="border border-red-200 bg-red-50 rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-red-800">Reset Semua Data Ujian</h3>
                            <p class="text-sm text-red-600 mt-1">Menghapus <strong>seluruh</strong> data periode, jadwal, pendaftar, sesi ujian, jawaban, dan log pelanggaran secara permanen. Aksi ini <strong>tidak dapat dibatalkan</strong>.</p>
                        </div>
                    </div>
                    <button @click="showResetModal = true" type="button"
                        class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm shadow-red-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                        </svg>
                        Reset Semua Data
                    </button>
                </div>

                @if($errors->has('konfirmasi_password'))
                    <div class="mt-4 p-3 bg-red-100 border border-red-300 rounded-xl text-sm text-red-700 font-medium">
                        ⚠️ {{ $errors->first('konfirmasi_password') }}
                    </div>
                @endif
            </div>

            {{-- Modal Konfirmasi Password --}}
            <div x-show="showResetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showResetModal = false"></div>
                <div x-show="showResetModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

                    <div class="bg-red-600 px-6 py-5 flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Konfirmasi Reset Data</h3>
                            <p class="text-red-200 text-xs mt-0.5">Aksi ini permanen dan tidak dapat dibatalkan</p>
                        </div>
                    </div>

                    <form action="{{ route('banksoal.admin.cbt.reset-semua') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                            <p class="font-bold mb-2">Data yang akan dihapus permanen:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-700">
                                <li>Semua Periode Ujian</li>
                                <li>Semua Jadwal & Token Sesi</li>
                                <li>Semua Data Pendaftar</li>
                                <li>Semua Sesi & Jawaban Ujian Mahasiswa</li>
                                <li>Semua Log Pelanggaran (Cheat Logs)</li>
                            </ul>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700">Masukkan password akun Anda untuk konfirmasi:</label>
                            <input type="password" name="konfirmasi_password" required
                                placeholder="Password Anda"
                                class="w-full h-11 px-4 border border-slate-200 rounded-xl text-sm focus:border-red-400 focus:ring-4 focus:ring-red-400/15 outline-none transition-all">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showResetModal = false"
                                class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-colors text-sm shadow-sm">
                                Ya, Reset Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-banksoal::layouts.admin>
