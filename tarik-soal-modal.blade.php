<!-- Tarik Soal Modal -->
<div id="tarikSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all scale-100 opacity-100 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tarik Soal</h3>
                <p class="text-sm text-slate-500 mt-0.5">Atur parameter untuk mengekstrak soal</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="document.getElementById('tarikSoalModal').classList.add('hidden'); document.getElementById('tarikSoalModal').classList.remove('flex');">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto">
            <form action="#" method="POST" id="formTarikSoal">
                <!-- Jenis Soal -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Soal</label>
                    <select multiple class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2 px-3 shadow-sm h-32" name="jenis_soal[]">
                        <option value="Pilihan Ganda">Pilihan Ganda</option>
                        <option value="Essay">Essay</option>
                        <option value="Benar/Salah">Benar/Salah</option>
                        <option value="Isian Singkat">Isian Singkat</option>
                        <option value="Menjodohkan">Menjodohkan</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-2">Tahan Ctrl (atau Cmd) untuk memilih lebih dari satu.</p>
                </div>

                <!-- CPL -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPL (Capaian Pembelajaran Lulusan)</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-4 pr-10 shadow-sm appearance-none" name="cpl_id">
                            <option value="">Pilih CPL</option>
                            <option value="1">CPL-1</option>
                            <option value="2">CPL-2</option>
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
                            <option value="1">CPMK-1</option>
                            <option value="2">CPMK-2</option>
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
            <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors" onclick="document.getElementById('tarikSoalModal').classList.add('hidden'); document.getElementById('tarikSoalModal').classList.remove('flex');">
                Batal
            </button>
            <button type="submit" form="formTarikSoal" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                <i class="fas fa-check"></i> Proses Tarik Soal
            </button>
        </div>

    </div>
</div>

<script>
    function openTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>
