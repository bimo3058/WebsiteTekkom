<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Superadmin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-gray-600 dark:text-gray-400 mb-8">Welcome, {{ auth()->user()->name }}</p>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $total_users }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12 14a8 8 0 00-8 8v2h16v-2a8 8 0 00-8-8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Superadmins -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Superadmins</p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $total_superadmins }}</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Lecturers -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Lecturers (DOSEN)</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $total_lecturers }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.248 6.253 2 10.998 2 16.5c0 5.502 4.248 10.247 10 10.247s10-4.745 10-10.247c0-5.502-4.248-10.247-10-10.247z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Students -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Students (MAHASISWA)</p>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2">{{ $total_students }}</p>
                        </div>
                        <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.248 6.253 2 10.998 2 16.5c0 5.502 4.248 10.247 10 10.247s10-4.745 10-10.247c0-5.502-4.248-10.247-10-10.247z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Overview -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Modules</h2>
                    <a href="{{ route('superadmin.modules') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($modules as $moduleKey => $module)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $module['name'] }}
                                </h3>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Module Stats -->
                        <div class="space-y-2 mb-4">
                            @if(isset($module['pertanyaan']))
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Questions</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $module['pertanyaan'] }}</span>
                            </div>
                            @endif

                            @if(isset($module['mata_kuliah']))
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Courses</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $module['mata_kuliah'] }}</span>
                            </div>
                            @endif

                            @if(isset($module['groups']))
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Groups</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $module['groups'] }}</span>
                            </div>
                            @endif

                            @if(isset($module['students']))
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Students</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $module['students'] }}</span>
                            </div>
                            @endif

                            @if(isset($module['alumni']))
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Alumni</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $module['alumni'] }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route($module['route']) }}" 
                           class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                            Manage
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Users -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recent Users</h2>
                        <a href="{{ route('superadmin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                            View All →
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Roles</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_users as $user)
                                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4">
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $user->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ $user->email }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2 flex-wrap">
                                                @forelse($user->roles as $role)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                    {{ $role->name }}
                                                </span>
                                                @empty
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">No role</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-600 dark:text-gray-400 text-sm">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No users yet
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Quick Actions</h2>

                    <div class="space-y-4">
                        <!-- User Management -->
                        <a href="{{ route('superadmin.users.index') }}" 
                           class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-500 transition group">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-gray-900 dark:text-white font-semibold group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Manage Users</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Add, edit, or remove users</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-600 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- Audit Logs -->
                        <a href="{{ route('superadmin.audit-logs') }}" 
                           class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-green-500 dark:hover:border-green-500 transition group">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-gray-900 dark:text-white font-semibold group-hover:text-green-600 dark:group-hover:text-green-400 transition">Audit Logs</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">View system activity</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-600 group-hover:text-green-600 dark:group-hover:text-green-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- System Settings -->
                        <a href="{{ route('superadmin.modules') }}" 
                           class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-purple-500 dark:hover:border-purple-500 transition group">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-gray-900 dark:text-white font-semibold group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">Module Settings</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage system modules</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-600 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- User Profile -->
                        <a href="{{ route('profile.edit') }}" 
                           class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-orange-500 dark:hover:border-orange-500 transition group">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-gray-900 dark:text-white font-semibold group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">My Profile</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Edit your account</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-600 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>