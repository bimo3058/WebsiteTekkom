<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Global
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Bank Soal -->
                <a href="{{ route('banksoal.dashboard') }}"
                   class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-200 hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        📚 Bank Soal
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Kelola soal, RPS, dan kompre.
                    </p>
                </a>

                <!-- Capstone -->
                <a href="{{ route('capstone.dashboard') }}"
                   class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-200 hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        🎓 Capstone & TA
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Manajemen capstone dan tugas akhir.
                    </p>
                </a>

                <!-- Manajemen Mahasiswa -->
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-200 hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        🏫 Manajemen Mahasiswa
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Kegiatan, alumni, dan forum mahasiswa.
                    </p>
                </a>

                <!-- E-Office -->
                <a href="{{ route('eoffice.dashboard') }}"
                   class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-200 hover:scale-105">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        📄 E-Office
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Manajemen dokumen dan workflow.
                    </p>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>