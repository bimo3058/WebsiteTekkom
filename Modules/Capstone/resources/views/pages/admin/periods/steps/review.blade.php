<div class="grid grid-cols-1 gap-5 p-6 sm:p-8 lg:grid-cols-[280px_1fr]">
    <div>
        <h2 class="text-base font-bold text-gray-900">Review</h2>
        <p class="mt-1 text-sm text-gray-500">Tinjau konfigurasi Anda sebelum menyimpan.</p>
    </div>
    <div class="text-sm">
        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Konfigurasi Periode Baru</h3>
        <dl class="mt-3 space-y-3">
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Nama Periode</dt><dd class="font-medium text-gray-900" x-text="form.name || '-'"></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Durasi</dt><dd class="font-medium text-gray-900" x-text="reviewDuration"></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Status</dt><dd><span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700" x-show="form.is_active"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif</span><span class="inline-flex items-center rounded-full border border-gray-300 bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600" x-show="!form.is_active">Nonaktif</span></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Bidding</dt><dd class="font-medium text-gray-900" x-text="(form.bidding_start || '-')+' - '+(form.bidding_end || '-')"></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Jumlah Anggota</dt><dd class="font-medium text-gray-900" x-text="form.min_group_size+'-'+form.max_group_size+' Anggota/Group'"></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Maximal Dosen Pembimbing</dt><dd class="font-medium text-gray-900" x-text="form.max_supervisor_load+' Group/Dosen'"></dd></div>
        </dl>
        <div class="my-5 h-px bg-gray-100"></div>
        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Setup Evaluasi</h3>
        <dl class="mt-3 space-y-3">
            <template x-for="type in types" :key="type"><div class="flex items-center justify-between gap-4"><dt class="text-gray-500" x-text="reviewLabel(type)"></dt><dd class="text-right font-medium text-gray-900" x-text="templateCodes(type).join(', ') || '-'"></dd></div></template>
            <div class="flex items-center justify-between gap-4"><dt class="text-gray-500">Peer Review</dt><dd class="text-right font-medium text-gray-900" x-text="peerCodes.join(', ') || '-'"></dd></div>
        </dl>
    </div>
</div>
