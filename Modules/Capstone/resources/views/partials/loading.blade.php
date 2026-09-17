<div x-show="loading" role="status" class="flex flex-col items-center justify-center gap-3 py-12">
    <span class="capstone-loader text-primary" aria-hidden="true"></span>
    <span class="text-muted-foreground text-sm">Memuat data...</span>
</div>
<div x-show="error" x-cloak role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><span x-text="error"></span><button type="button" class="ml-3 underline" @click="load()">Coba lagi</button></div>
