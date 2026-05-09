<x-banksoal::layouts.admin>
    <!-- Header Title -->
    <div class="mb-6 lg:mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Manajemen Soal</h1>
            <p class="text-slate-500 text-sm mt-2">Kelola dan organisir seluruh soal dari Dosen dalam bank soal</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="openTarikModal()" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-xl px-4 py-2.5 font-medium transition-colors shadow-sm">
                <i class="fas fa-download"></i> Tarik Soal (Print)
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" method="GET" class="mb-6 flex gap-3">
        <input type="text" name="searchSoal" value="{{ request('searchSoal') }}" placeholder="Cari soal..." class="w-full rounded-xl border border-slate-300 text-sm px-4 py-2.5 outline-none focus:border-[#0f172a] focus:ring-1 focus:ring-[#0f172a]">
        <select name="filterMK" class="w-full rounded-xl border border-slate-300 text-sm px-4 py-2.5 outline-none focus:border-[#0f172a] focus:ring-1 focus:ring-[#0f172a]">
            <option value="">-- Semua Mata Kuliah --</option>
            @foreach($mataKuliahAll as $mk)
                <option value="{{ $mk->id }}" @selected(request('filterMK') == $mk->id)>{{ $mk->kode }} - {{ $mk->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl font-medium">Filter</button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tipe Soal</th>
                        <th class="px-6 py-4 font-semibold">Soal</th>
                        <th class="px-6 py-4 font-semibold">Mata Kuliah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($soals as $soal)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}</td>
                        <td class="px-6 py-4">{!! Str::limit(strip_tags($soal->soal), 80) !!}</td>
                        <td class="px-6 py-4 text-xs font-semibold">{{ $soal->mataKuliah ? $soal->mataKuliah->nama : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 font-medium z-10">Belum ada soal tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/80">
            {{ $soals->appends(request()->query())->links() }}
        </div>
    </div>


<!-- Tarik Soal Modal -->
<div id="tarikSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="tarikSoalModalContent" class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tarik Soal (Cetak)</h3>
                <p class="text-sm text-slate-500 mt-0.5">Pilih mata kuliah yang soalnya ingin dicetak</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 rounded-lg p-2 transition-colors" onclick="closeTarikModal()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto w-full">
            <form action="{{ route('banksoal.admin.kontrol-banksoal.soal.ekstrak') }}" method="POST" id="formTarikSoal">
                @csrf
                <div class="space-y-5">
                    
                    <!-- MK Selection -->
                    <div>
                        <label for="mkSelect" class="block text-sm font-medium text-slate-700 mb-1.5 flex justify-between">Mata Kuliah <span class="text-red-500">*</span></label>
                        <select name="mk_id" id="mkSelect" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:border-[#0f172a] focus:ring-1 focus:ring-[#0f172a] transition-shadow">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliahAll as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 rounded-b-2xl flex justify-end gap-3 mt-auto">
            <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors" onclick="closeTarikModal()">Batal</button>
            <button type="submit" form="formTarikSoal" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                <i class="fas fa-check"></i> Proses Tarik Soal
            </button>
        </div>

    </div>
</div>

<script>
    function openTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }
    
    function closeTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
</x-banksoal::layouts.admin>
