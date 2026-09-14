<x-capstone::dialog id="confirm-delete" :title="$deleteTitle ?? 'Hapus Data'">
    <p class="text-muted-foreground text-sm py-4">{{ $deleteDescription ?? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.' }} <strong x-text="deleting?.name || deleting?.code || ''"></strong></p>
    <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Batal</x-capstone::button><x-capstone::button variant="destructive" @click="remove()" ::disabled="saving">Hapus</x-capstone::button></div>
</x-capstone::dialog>
