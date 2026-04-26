<x-banksoal::layouts.admin>
    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Aktivasi Sesi & Token Ujian</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola status jadwal ujian, bangkitkan token, dan izinkan mahasiswa untuk memulai CBT.</p>
            </div>
            
            <form action="{{ route('banksoal.aktivasi.index') }}" method="GET">
                <select name="periode_id" onchange="this.form.submit()" class="text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white py-2 pl-3 pr-8 shadow-sm">
                    @foreach($periodes as $periode)
                        <option value="{{ $periode->id }}" {{ $selectedPeriodeId == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama_periode }} ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jadwals as $jadwal)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($jadwal->status === 'aktif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 shadow-sm animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> AKTIF
                            </span>
                        @elseif($jadwal->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                Menunggu Jadwal
                            </span>
                        @endif
                    </div>

                    <div class="p-5 flex-1">
                        <div class="text-xs font-bold tracking-wider text-blue-600 uppercase mb-1">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('l, d M Y') }}</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $jadwal->nama_sesi }}</h3>
                        
                        <div class="flex items-center gap-2 text-sm text-slate-600 mb-1">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            {{ $jadwal->pendaftars_count }} / {{ $jadwal->kuota }} Mahasiswa
                        </div>

                        <!-- Token Section -->
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mt-auto">
                            <div class="text-xs font-medium text-slate-500 mb-1">Token Ujian Mahasiswa</div>
                            @if($jadwal->token)
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xl font-black text-slate-800 tracking-widest">{{ $jadwal->token }}</span>
                                </div>
                            @else
                                <span class="text-sm font-medium text-slate-400 italic">Belum dibangkitkan.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-slate-50 border-t border-slate-200 px-5 py-3">
                        @if($jadwal->status === 'menunggu_jadwal')
                            <form action="{{ route('banksoal.aktivasi.toggle', $jadwal->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit" class="w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Aktifkan Sesi & Generate Token
                                </button>
                            </form>
                        @elseif($jadwal->status === 'aktif')
                            <form action="{{ route('banksoal.aktivasi.toggle', $jadwal->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit" class="w-full text-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm font-semibold rounded-lg transition-colors">
                                    Tutup Sesi
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full text-center px-4 py-2 bg-slate-100 text-slate-400 cursor-not-allowed text-sm font-medium rounded-lg">
                                Sesi Telah Ditutup
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200 border-dashed">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-slate-500 font-medium">Belum ada jadwal ujian pada periode ini.</p>
                </div>
            @endforelse
        </div>
        
    </div>
</x-banksoal::layouts.admin>
