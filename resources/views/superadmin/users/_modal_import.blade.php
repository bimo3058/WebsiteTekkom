<div id="modalImportUser" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalImportUser')"></div>
    
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
                    <span class="material-symbols-outlined text-emerald-600" style="font-size:20px">database</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Bulk Import User</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Upload file ke bucket: <span class="font-bold text-emerald-600">data_user</span></p>
                </div>
            </div>
            <button onclick="closeModal('modalImportUser')" class="text-slate-400 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('superadmin.users.bulkImport') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File CSV</label>
                <div class="relative group">
                    <input type="file" name="file" accept=".csv,.txt" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-1 group-hover:border-blue-400 transition-all">
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Format: <code class="bg-slate-100 px-1 rounded">.csv</code> atau <code class="bg-slate-100 px-1 rounded">.txt</code> (Maks. 10MB)
                </p>
            </div>

            {{-- Informasi Format --}}
            <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-100">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-blue-600" style="font-size:18px">info</span>
                    <div class="text-xs text-blue-700 leading-relaxed">
                        Pastikan urutan kolom: <br>
                        <strong>name, email, password, role, external_id, student_number, cohort_year</strong>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="button" onclick="closeModal('modalImportUser')"
                    class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-200 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:20px">upload</span>
                    Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>