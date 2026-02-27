<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Capstone - Mahasiswa Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-gray-600 dark:text-gray-400 mb-8">Manage your capstone project</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">My Group</h3>
                    <p class="text-3xl font-bold text-blue-600">-</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Status</h3>
                    <p class="text-3xl font-bold text-yellow-600">-</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Progress</h3>
                    <p class="text-3xl font-bold text-green-600">0%</p>
                </div>
            </div>

            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Project Details</h2>
                <p class="text-gray-600 dark:text-gray-400">Join or create a group to get started</p>
            </div>
        </div>
    </div>
</x-app-layout>