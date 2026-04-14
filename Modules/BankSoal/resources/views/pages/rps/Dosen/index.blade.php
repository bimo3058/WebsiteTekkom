<x-banksoal::layouts.dosen-admin>
    <x-banksoal::alerts />

    <x-banksoal::dosen.page-header title="Manajemen RPS" subtitle="Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung." />

    <x-banksoal::rps.status-banner
        :activePeriode="$activePeriode ?? null"
        :isUploadOpen="$isUploadOpen ?? false"
        :tenggatH7="$tenggatH7 ?? false"
        :unsubmittedMk="$unsubmittedMk ?? []"
        :daysLeft="$daysLeft ?? 0"
        :isHourFormat="$isHourFormat ?? false"
    />

    <x-banksoal::rps.form
        :mataKuliahs="$mataKuliahs"
        :tahunAjarans="$tahunAjarans"
        :isUploadOpen="$isUploadOpen ?? false"
        :semester="$semester ?? 'Genap'"
        :academicYear="$academicYear ?? date('Y') . '/' . (date('Y') + 1)"
    />

    <x-banksoal::rps.history-table :riwayat="$riwayat" />

    <x-banksoal::rps.document-modal />

    <x-banksoal::rps.scripts />
</x-banksoal::layouts.dosen-admin>
