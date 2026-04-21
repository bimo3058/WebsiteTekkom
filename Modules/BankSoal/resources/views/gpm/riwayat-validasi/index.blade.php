<x-banksoal::layouts.gpm-master>
    <x-banksoal::ui.page-header title="Menu Riwayat Validasi" subtitle="Pilih kategori riwayat dokumen yang ingin Anda pantau." />

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center flex flex-col items-center hover:border-blue-200 hover:shadow-lg transition-all">
            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                <i class="far fa-file-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Riwayat Validasi RPS</h3>
            <p class="text-sm text-slate-600 mb-6">
                Pantau status, tanggal diajukan, dan hasil review dokumen Rencana Pembelajaran Semester (RPS) untuk setiap program studi.
            </p>
            <a href="{{ route('banksoal.rps.gpm.riwayat-validasi.rps') }}" class="mt-auto inline-flex items-center justify-center w-full rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buka Riwayat RPS <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center flex flex-col items-center hover:border-blue-200 hover:shadow-lg transition-all">
            <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                <i class="far fa-question-circle text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Riwayat Validasi Bank Soal</h3>
            <p class="text-sm text-slate-600 mb-6">
                Pantau status, jumlah butir soal, dan hasil review untuk paket Bank Soal mata kuliah pada tiap semester aktif.
            </p>
            <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="mt-auto inline-flex items-center justify-center w-full rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buka Riwayat Bank Soal <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <x-banksoal::ui.stat-card label="Menunggu Review" value="12 Dokumen" icon="fa-clipboard-check" tone="blue" />
        <x-banksoal::ui.stat-card label="Selesai Validasi" value="48 Dokumen" icon="fa-check-circle" tone="green" />
        <x-banksoal::ui.stat-card label="Update Terakhir" value="2 Jam Lalu" icon="fa-calendar" tone="amber" />
    </div>
</x-banksoal::layouts.gpm-master>