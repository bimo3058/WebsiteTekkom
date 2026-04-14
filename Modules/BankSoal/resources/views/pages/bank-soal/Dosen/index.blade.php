<x-banksoal::layouts.dosen-admin>

<x-banksoal::ui.page-header title="Manajemen Bank Soal" subtitle="Kelola dan organisir repositori pertanyaan Anda">
    <x-slot:actions>
        @can('banksoal.edit')
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
                <i class="fas fa-paper-plane"></i> Ajukan Soal
            </a>
            <a href="{{ route('banksoal.soal.dosen.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2.5 font-medium transition-colors">
                <i class="fas fa-plus"></i> Buat Soal
            </a>
        @else
            <div class="text-sm text-slate-500 italic">
                <i class="fas fa-info-circle"></i> Mode Lihat Saja — Anda tidak memiliki izin untuk menambah/mengubah soal
            </div>
        @endcan
    </x-slot:actions>
</x-banksoal::ui.page-header>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h2 class="text-lg font-semibold text-slate-900">Daftar Soal</h2>
    </div>

    <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="p-6 border-b border-slate-200 flex flex-col sm:flex-row gap-3 flex-wrap">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="searchSoal" value="{{ request('searchSoal') }}" placeholder="Cari soal, kursus, atau topik..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        </div>

        <select name="mk_id" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">Semua Mata Kuliah...</option>
            @foreach($mataKuliahDosen as $mk)
                <option value="{{ $mk->id }}" {{ request('mk_id') == $mk->id ? 'selected' : '' }}>{{ $mk->nama }}</option>
            @endforeach
        </select>

        <select name="kesulitan" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <option value="">Semua Kesulitan...</option>
            <option value="easy" {{ request('kesulitan') == 'easy' ? 'selected' : '' }}>Mudah</option>
            <option value="intermediate" {{ request('kesulitan') == 'intermediate' ? 'selected' : '' }}>Sedang</option>
            <option value="advanced" {{ request('kesulitan') == 'advanced' ? 'selected' : '' }}>Sulit</option>
        </select>

        <button type="submit" class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl px-4 py-2.5 font-medium transition-colors border border-blue-200">
            <i class="fas fa-filter"></i> Filter
        </button>

        @if(request()->hasAny(['searchSoal', 'mk_id', 'kesulitan']))
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl px-4 py-2.5 font-medium transition-colors border border-red-200">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kursus</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Topik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tingkat Kesulitan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse(($soals ?? collect()) as $soal)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $soal->kode_soal }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $soal->mataKuliah->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ strip_tags(\Illuminate\Support\Str::limit($soal->soal, 50)) }}</td>
                        <td class="px-6 py-4">
                            @php $diff = strtolower($soal->kesulitan ?? ''); @endphp
                            @if($diff === 'easy')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @elseif($diff === 'advanced')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @include('banksoal::partials.dosen._soal-actions', ['soal' => $soal])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada soal di dalam bank soal.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($soals) && $soals->count() > 0)
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <span class="text-sm text-slate-600">
                Showing {{ $soals->firstItem() }} to {{ $soals->lastItem() }} of {{ $soals->total() }} questions
            </span>
            <div>
                {{ $soals->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

@can('banksoal.edit')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Ekstraksi Soal (Tarik Soal)</h2>
                <p class="text-sm text-slate-600 mt-1">Tarik kumpulan soal untuk digunakan pada ujian atau asesmen.</p>
            </div>
            <button type="button" onclick="openTarikModal()" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-xl px-4 py-2.5 font-medium transition-colors shadow-sm">
                <i class="fas fa-download"></i> Tarik Soal
            </button>
        </div>

        <form class="p-6 border-b border-slate-200 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Cari paket soal, kode mata kuliah, atau nama..." id="searchPackages">
            </div>
            <button class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors">
                <i class="fas fa-sliders-h"></i> Filter
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode MK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Terkait CPL</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Terkait CPMK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(($packages ?? collect()) as $pkg)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $pkg->kode }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $pkg->nama }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($pkg->str_cpls !== '-')
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 whitespace-normal break-words max-w-[150px] leading-relaxed block">{{ $pkg->str_cpls }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dipetakan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($pkg->str_cpmks !== '-')
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-emerald-50 text-emerald-700 whitespace-normal break-words max-w-[150px] leading-relaxed block">{{ $pkg->str_cpmks }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dipetakan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-900">{{ $pkg->jumlah_soal }}</span> Set
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat"><i class="fas fa-eye text-sm"></i></button>
                                    <button class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Tarik"><i class="fas fa-download text-sm"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-box-open text-4xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Belum ada paket soal yang tersedia untuk ditarik.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($packages) && $packages->count() > 0)
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-sm text-slate-600">
                    Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of {{ $packages->total() }} packages
                </span>
                <div>
                    {{ $packages->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden opacity-50">
        <div class="px-6 py-12 text-center text-slate-600">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-lock text-4xl text-slate-300 mb-3"></i>
                <p class="font-semibold text-lg">Fitur Ekstraksi Soal Terkunci</p>
                <p class="text-sm">Anda memerlukan izin <strong>Edit</strong> untuk melakukan penarikan soal.</p>
            </div>
        </div>
    </div>
@endcan

<script src="{{ asset('modules/banksoal/js/Banksoal/shared/SearchHandler.js') }}"></script>

<!-- Tarik Soal Modal -->
<div id="tarikSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="tarikSoalModalContent" class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tarik Soal</h3>
                <p class="text-sm text-slate-500 mt-0.5">Atur parameter untuk mengekstrak soal</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeTarikModal()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto w-full">
            <form action="{{ route('banksoal.soal.dosen.ekstrak') }}" method="POST" id="formTarikSoal">
                @csrf
                
                <!-- Mata Kuliah -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Mata Kuliah</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-4 pr-10 shadow-sm appearance-none" name="mk_id" id="tarikMkId" required onchange="loadCplCpmk(this.value)">
                            <option value="">Pilih Mata Kuliah</option>
                            @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Jenis Soal -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Jenis Soal</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Pilihan Ganda" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-slate-700">Pilihan Ganda</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Essay" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-slate-700">Essay</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Benar/Salah" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-slate-700">Benar / Salah</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Isian Singkat" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-slate-700">Isian Singkat</span>
                        </label>
                        <label class="col-span-2 flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Menjodohkan" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-slate-700">Menjodohkan</span>
                        </label>
                    </div>
                </div>

                <!-- CPL -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPL (Capaian Pembelajaran Lulusan)</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-4 pr-10 shadow-sm appearance-none" name="cpl_id">
                            <option value="">Pilih CPL</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- CPMK -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPMK (Capaian Pembelajaran Mata Kuliah)</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-4 pr-10 shadow-sm appearance-none" name="cpmk_id">
                            <option value="">Pilih CPMK</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Bobot Total -->
                <div class="mb-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bobot Total</label>
                    <div class="relative">
                        <input type="number" name="bobot_total" class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-4 pr-12 shadow-sm" placeholder="Contoh: 100">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">Pts</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 rounded-b-2xl flex items-center justify-end gap-3 mt-auto">
            <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors" onclick="closeTarikModal()">
                Batal
            </button>
            <button type="submit" form="formTarikSoal" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                <i class="fas fa-check"></i> Proses Tarik Soal
            </button>
        </div>

    </div>
</div>

<script>
    const mkData = {!! $mataKuliahDosen->toJson() !!};

    function loadCplCpmk(mk_id) {
        const cplSelect = document.querySelector('select[name="cpl_id"]');
        const cpmkSelect = document.querySelector('select[name="cpmk_id"]');
        
        cplSelect.innerHTML = '<option value="">Pilih CPL</option>';
        cpmkSelect.innerHTML = '<option value="">Pilih CPMK</option>';
        
        if (!mk_id) return;
        
        const selectedMk = mkData.find(mk => mk.id == mk_id);
        if (selectedMk) {
            if (selectedMk.all_cpls && selectedMk.all_cpls.length > 0) {
                selectedMk.all_cpls.forEach(cpl => {
                    cplSelect.innerHTML += `<option value="${cpl.id}">${cpl.kode}</option>`;
                });
            }
            if (selectedMk.all_cpmks && selectedMk.all_cpmks.length > 0) {
                selectedMk.all_cpmks.forEach(cpmk => {
                    cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode}</option>`;
                });
            }
        }
    }

    function openTarikModal(mk_id = null) {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Timeout to trigger transition after display swap
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
        
        if (mk_id) {
            document.getElementById('tarikMkId').value = mk_id;
            loadCplCpmk(mk_id);
        }
    }

    function closeTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        
        // Start exit animation
        modal.classList.add('opacity-0');
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        
        // Hide elements after animation finishes
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    // AJAX for Form Tarik Soal
    function handleTarikSoalSubmit(e, formEl = null) {
        if(e) e.preventDefault();
        
        const form = formEl || document.getElementById('formTarikSoal');
        const formData = new FormData(form);
        // Cari tombol submit, entah di dalam form atau di luar dengan atribut form="..."
        const submitBtn = document.querySelector('button[form="formTarikSoal"]') || form.querySelector('[type="submit"]');
        let originalText = '';
        
        if (submitBtn) {
            originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            
            if (data.success) {
                closeTarikModal();
                // Pass extra param for modal filler
                data.mk_id = formData.get('mk_id');
                data.bobot_total = formData.get('bobot_total');
                setTimeout(() => {
                    openReviewModal(data);
                }, 300);
            } else {
                alert(data.message || 'Terjadi kesalahan saat menarik soal.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            alert('Terjadi kesalahan tidak terduga pada server.');
        });
    }

    document.getElementById('formTarikSoal').addEventListener('submit', handleTarikSoalSubmit);
</script>

@include('banksoal::pages.bank-soal.Dosen._review-modal')

</x-banksoal::layouts.dosen-admin>
