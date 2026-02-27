<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Capstone - Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-gray-600 dark:text-gray-400 mb-8">Manage capstone projects and evaluations</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Total Groups</h3>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \DB::table('capstone_groups')->count() }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Active Periods</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ \DB::table('capstone_periods')->where('is_active', true)->count() }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Total Titles</h3>
                    <p class="text-3xl font-bold text-green-600">{{ \DB::table('capstone_titles')->count() }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Pending Evaluations</h3>
                    <p class="text-3xl font-bold text-orange-600">0</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                        Create Period
                    </a>
                    <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                        Manage Titles
                    </a>
                    <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                        Evaluate Groups
                    </a>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg text-center transition">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>