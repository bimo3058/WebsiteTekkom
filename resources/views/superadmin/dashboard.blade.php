<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Superadmin Dashboard</h1>
                <p class="text-slate-400">Welcome back, <span class="text-white font-medium">{{ auth()->user()->name }}</span></p>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Total Users</p>
                            <p class="text-3xl font-bold text-white mt-2">{{ $total_users }}</p>
                        </div>
                        <div class="bg-blue-500/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12 14a8 8 0 00-8 8v2h16v-2a8 8 0 00-8-8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Superadmins</p>
                            <p class="text-3xl font-bold text-purple-400 mt-2">{{ $total_superadmins }}</p>
                        </div>
                        <div class="bg-purple-500/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Dosen</p>
                            <p class="text-3xl font-bold text-green-400 mt-2">{{ $total_lecturers }}</p>
                        </div>
                        <div class="bg-green-500/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm font-medium">Mahasiswa</p>
                            <p class="text-3xl font-bold text-orange-400 mt-2">{{ $total_students }}</p>
                        </div>
                        <div class="bg-orange-500/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modules Section --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-white">Modules</h2>
                    <a href="{{ route('superadmin.modules') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">View All →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($modules as $moduleKey => $module)
                    @php
                        $moduleColor = match($moduleKey) {
                            'bank_soal' => ['border' => 'border-yellow-500/30', 'bg' => 'bg-yellow-500/10', 'text' => 'text-yellow-400'],
                            'capstone' => ['border' => 'border-cyan-500/30', 'bg' => 'bg-cyan-500/10', 'text' => 'text-cyan-400'],
                            'eoffice' => ['border' => 'border-purple-500/30', 'bg' => 'bg-purple-500/10', 'text' => 'text-purple-400'],
                            'manajemen_mahasiswa' => ['border' => 'border-orange-500/30', 'bg' => 'bg-orange-500/10', 'text' => 'text-orange-400'],
                            default => ['border' => 'border-blue-500/30', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-400'],
                        };
                    @endphp
                    <div class="bg-slate-800 border {{ $moduleColor['border'] }} rounded-lg p-5 hover:bg-slate-700/50 transition">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">{{ $module['name'] }}</h3>
                            <div class="{{ $moduleColor['bg'] }} p-2 rounded-lg">
                                <svg class="w-5 h-5 {{ $moduleColor['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            @foreach(collect($module)->except(['name', 'route']) as $label => $value)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-400">{{ str_replace('_', ' ', ucfirst($label)) }}</span>
                                    <span class="font-semibold text-white">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route($module['route']) }}" class="block text-center bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition border border-slate-600">
                            Manage
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Users & Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Users --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">Recent Users</h2>
                        <a href="{{ route('superadmin.users.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">View All →</a>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
                        @forelse($recent_users->take(5) as $user)
                        @php
                            $roleColors = [
                                'superadmin' => 'bg-purple-500/20 text-purple-300',
                                'dosen'      => 'bg-green-500/20 text-green-300',
                                'mahasiswa'  => 'bg-orange-500/20 text-orange-300',
                            ];
                        @endphp
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-700 last:border-0 hover:bg-slate-700/30 transition">
                            <div class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-300 font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-white text-sm font-medium truncate">{{ $user->name }}</p>
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $roleColors[$role->name] ?? 'bg-slate-500/20 text-slate-300' }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-slate-500 text-xs truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center text-slate-500 text-sm">Belum ada user</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">Recent Activity</h2>
                        <a href="{{ route('superadmin.audit-logs') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">View All →</a>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
                        @forelse($recent_logs->take(5) as $log)
                        @php
                            $actionColor = match($log->action) {
                                'CREATE' => 'bg-green-500/20 text-green-300',
                                'UPDATE' => 'bg-yellow-500/20 text-yellow-300',
                                'DELETE' => 'bg-red-500/20 text-red-300',
                                'LOGIN'  => 'bg-purple-500/20 text-purple-300',
                                default  => 'bg-blue-500/20 text-blue-300',
                            };
                        @endphp
                        <div class="px-4 py-3 border-b border-slate-700 last:border-0 hover:bg-slate-700/30 transition">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $actionColor }}">
                                    {{ $log->action }}
                                </span>
                                <span class="text-slate-500 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-300 text-xs truncate">{{ $log->description }}</p>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center text-slate-500 text-sm">Belum ada aktivitas</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-sidebar>
</x-app-layout>