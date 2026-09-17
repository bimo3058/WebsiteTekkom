<?php
$jadwalContent = file_get_contents("d:/Kuliah/Season 6/Capstone/WebsiteTekkom/Modules/BankSoal/resources/views/jadwal/index.blade.php");
$alokasiContent = file_get_contents("d:/Kuliah/Season 6/Capstone/WebsiteTekkom/Modules/BankSoal/resources/views/alokasi-sesi/index.blade.php");

// Extract body strings
preg_match("/<div x-data=\"{ openModal: false }\" class=\"w-full\">(.*?)<\/x-banksoal::layouts.admin>/s", $jadwalContent, $m1);
$jadwalBody = $m1[1] ?? "";

// The alokasi hasn't got x-data wrapping the root completely, it has header and other stuff.
preg_match("/<x-banksoal::layouts.admin>(.*?)<\/x-banksoal::layouts.admin>/s", $alokasiContent, $m2);
// strip off the first .mb-8 header div because we have a unified header now
$alokasiBody = preg_replace("/<div class=\"mb-8\">.*?<\/div>/s", "", $m2[1] ?? "", 1);

// We need to remove the Period Dropdown header from jadwalBody because it will be in the global tab wrapper
$jadwalBodyArr = explode("<!-- Session Content Card -->", $jadwalBody);
$jadwalHeaderHTML = $jadwalBodyArr[0];
$jadwalTablesHTML = "<!-- Session Content Card -->\n" . ($jadwalBodyArr[1] ?? "");
// Replace action in Form periode to unified route
$jadwalHeaderHTML = str_replace("route('banksoal.periode.jadwal')", "route('banksoal.pendaftaran.alokasi-sesi.index')", $jadwalHeaderHTML);
$jadwalHeaderHTML = str_replace("Pengaturan Jadwal Sesi", "Manajemen Jadwal & Alokasi Sesi", $jadwalHeaderHTML);
$jadwalHeaderHTML = str_replace("Kelola sesi dan kuota ujian berdasarkan periode.", "Sistem satu atap untuk mengatur ruang sesi ujian dan membagi jadwal peserta.", $jadwalHeaderHTML);

// Replace default wrapper
$jadwalHeaderHTML = preg_replace("/<div x-data=\"{ openModal: false }\" class=\"w-full\">/", "", $jadwalHeaderHTML);

$unified = <<<HTML
<x-banksoal::layouts.admin>
    <div x-data="{ activeTab: 'jadwal', openModal: false }" class="w-full">
        {$jadwalHeaderHTML}

        <!-- TABS NAV -->
        <div class="flex flex-wrap space-x-1 bg-slate-100 p-1 rounded-xl mb-6 w-fit">
            <button type="button" @click="activeTab = 'jadwal'" :class="{'bg-white shadow-sm text-blue-600': activeTab === 'jadwal', 'text-slate-600 hover:text-slate-800': activeTab !== 'jadwal'}" class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all">
                1. Pengaturan Ruang & Jadwal
            </button>
            <button type="button" @click="activeTab = 'alokasi'" :class="{'bg-white shadow-sm text-blue-600': activeTab === 'alokasi', 'text-slate-600 hover:text-slate-800': activeTab !== 'alokasi'}" class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all">
                2. Alokasi Peserta
            </button>
        </div>

        <!-- TAB 1: JADWAL -->
        <div x-show="activeTab === 'jadwal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {$jadwalTablesHTML}
        </div>

        <!-- TAB 2: ALOKASI -->
        <div x-show="activeTab === 'alokasi'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            {$alokasiBody}
        </div>
    </div>
</x-banksoal::layouts.admin>
HTML;

file_put_contents("d:/Kuliah/Season 6/Capstone/WebsiteTekkom/Modules/BankSoal/resources/views/alokasi-sesi/index.blade.php", $unified);
echo "Unified View Generated";
?>
