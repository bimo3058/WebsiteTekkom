<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manajemen Mahasiswa - Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</dd>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">-</dd>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">Active</dd>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Profile Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="/" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>