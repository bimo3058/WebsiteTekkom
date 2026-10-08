    <x-capstone::dialog id="group-delete" title="Hapus Kelompok">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-600">Hapus permanen <span class="font-semibold text-slate-900" x-text="deleteTarget ? (deleteTarget.code || ('Group '+deleteTarget.id)) : ''"></span> (<span x-text="(deleteTarget?.members||[]).length+' anggota'"></span>, status <span class="font-semibold" x-text="deleteTarget?.status"></span>)?</p>
            <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-[13px] text-red-700">Seluruh data ikut terhapus permanen: anggota, judul usulan mahasiswa, pembimbing, dokumen, jadwal, dan penilaian. Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
            <textarea x-model="deleteReason" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-200 p-3 text-sm" placeholder="Alasan penghapusan (wajib, min. 10 karakter)..." aria-label="Alasan penghapusan"></textarea>
            <p class="text-xs text-slate-400" x-text="deleteReason.trim().length+'/1000 (min. 10 karakter)'"></p>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="closeDelete()">Batal</x-capstone::button>
                <x-capstone::button variant="destructive" @click="confirmDelete()" ::disabled="saving || !deleteReasonValid">Lanjut</x-capstone::button>
            </div>
        </div>
    </x-capstone::dialog>

    <x-capstone::dialog id="group-delete-confirm" title="Konfirmasi Terakhir">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-600">Anda yakin ingin menghapus permanen <span class="font-semibold text-slate-900" x-text="deleteTarget ? (deleteTarget.code || ('Group '+deleteTarget.id)) : ''"></span>?</p>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-[13px] text-slate-600">
                <p class="font-medium text-slate-500">Alasan:</p>
                <p class="mt-1 text-slate-800" x-text="deleteReason"></p>
            </div>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="backToDeleteReason()">Kembali</x-capstone::button>
                <x-capstone::button variant="destructive" @click="sendDelete()" ::disabled="saving">Ya, Hapus Permanen</x-capstone::button>
            </div>
        </div>
    </x-capstone::dialog>
