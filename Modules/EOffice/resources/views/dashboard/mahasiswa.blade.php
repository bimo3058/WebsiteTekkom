<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            E-Office - Mahasiswa Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Available Documents</h3>
                    <p class="text-gray-500 dark:text-gray-400">No documents available</p>
                </div>
            </div>

            <div class="mt-6">
                <a href="/" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>