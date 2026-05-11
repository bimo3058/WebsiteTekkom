<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Riwayat Ujian</span>
    @endsection

    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat Ujian Komprehensif</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar rekapitulasi nilai akhir dari mahasiswa yang telah menyelesaikan ujian.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('banksoal.admin.cbt.riwayat') }}" method="GET" class="flex gap-2">
                    <select name="periode_id" onchange="this.form.submit()" class="text-sm border-slate-200 rounded-lg focus:ring-primary focus:border-primary bg-white py-2 pl-3 pr-8">
                        <option value="">Semua Periode</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </form>
                
                <!-- Nantinya fitur export ke CSV bisa dihidupkan -->
                <button class="flex items-center gap-2 px-4 py-2 bg-primary border border-transparent text-white rounded-lg hover:bg-primary/90 transition-colors shadow-sm text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Rekap (CSV)
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Peserta</th>
                            <th class="px-6 py-4 font-semibold">Sesi</th>
                            <th class="px-6 py-4 font-semibold">Waktu Mulai</th>
                            <th class="px-6 py-4 font-semibold">Waktu Selesai</th>
                            <th class="px-6 py-4 font-semibold">Skor Akhir</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($session->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $session->user->name }}</p>
                                            <p class="text-slate-500 text-xs">{{ $session->user->nim ?? 'NIM tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $session->title }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($session->started_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->score !== null)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold {{ $session->score >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $session->score }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Belum dikalkulasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('banksoal.admin.cbt.detail', $session->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary bg-primary/10 border border-primary/20 rounded-lg hover:bg-primary/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        </div>
                                        <p class="text-slate-500 text-sm font-medium">Belum ada riwayat ujian yang selesai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- ZONA BERBAHAYA: Reset Data Ujian              --}}
        {{-- ============================================== --}}
        <div id="reset-section" x-data="{ showResetModal: false }" class="mt-12">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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

                    {{-- Modal Header --}}
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

                    {{-- Modal Body --}}
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
                            <label class="block text-sm font-bold text-slate-700">
                                Masukkan password akun Anda untuk konfirmasi:
                            </label>
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
