<x-banksoal::layouts.dosen-admin>
    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header title="Manajemen RPS" subtitle="Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung." />

    <x-banksoal::ui.status-banner
        :activePeriode="$activePeriode ?? null"
        :isUploadOpen="$isUploadOpen ?? false"
        :tenggatH7="$tenggatH7 ?? false"
        :unsubmittedMk="$unsubmittedMk ?? []"
        :daysLeft="$daysLeft ?? 0"
        :isHourFormat="$isHourFormat ?? false"
    />

    <x-banksoal::ui.rps-form
        :mataKuliahs="$mataKuliahs"
        :tahunAjarans="$tahunAjarans"
        :isUploadOpen="$isUploadOpen ?? false"
        :semester="$semester ?? 'Genap'"
        :academicYear="$academicYear ?? date('Y') . '/' . (date('Y') + 1)"
    />

    <x-banksoal::ui.rps-history-table :riwayat="$riwayat" />

    <x-banksoal::ui.rps-document-modal />

    <x-banksoal::ui.rps-delete-confirmation-modal />

    <x-banksoal::ui.rps-scripts />
</x-banksoal::layouts.dosen-admin>
