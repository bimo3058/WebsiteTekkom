<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Audit Logs</h1>
                <p class="text-slate-400">Total {{ $query->total() }} aktivitas tercatat</p>
            </div>

            {{-- Filter --}}
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-8">
                <form method="GET" action="{{ route('superadmin.audit-logs') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Modul</label>
                            <select name="module" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                                <option value="">Semua Modul</option>
                                @foreach($modules as $mod)
                                <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>
                                    {{ match($mod) {
                                        'auth' => 'Auth', 
                                        'bank_soal' => 'Bank Soal',
                                        'capstone' => 'Capstone',
                                        'eoffice' => 'E-Office',
                                        'manajemen_mahasiswa' => 'Manajemen Mahasiswa',
                                        'user_management' => 'User Management',
                                        default => ucfirst($mod),
                                    } }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Action</label>
                            <select name="action" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                                <option value="">Semua Action</option>
                                @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">User</label>
                            <select name="user_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                                <option value="">Semua User</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Tampilkan</label>
                            <select name="per_page" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-blue-500">
                                @foreach([25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 25) === $size ? 'selected' : '' }}>{{ $size }} data</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition">Filter</button>
                            <a href="{{ route('superadmin.audit-logs') }}" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition text-center">Reset</a>
                        </div>
                    </div>

                    {{-- Search description --}}
                    <div class="mt-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari berdasarkan deskripsi aktivitas..."
                            class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-700 bg-slate-700/50">
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">Waktu</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">User</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">Modul</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">Action</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">Deskripsi</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-slate-300">Subject</th>
                                <th class="px-4 py-4 text-center text-sm font-semibold text-slate-300">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($query as $log)
                            <tr class="border-b border-slate-700 hover:bg-slate-700/30 transition">

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <p class="text-slate-300 text-sm">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-slate-500 text-xs">{{ $log->created_at->format('H:i:s') }}</p>
                                </td>

                                <td class="px-4 py-4">
                                    @if($log->user)
                                    <p class="text-white text-sm font-medium">{{ $log->user->name }}</p>
                                    <p class="text-slate-500 text-xs">{{ $log->user->email }}</p>
                                    @else
                                    <span class="text-slate-500 text-xs italic">System / Deleted</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                        $moduleColor = match($log->module) {
                                            'auth' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            'bank_soal' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                            'capstone' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                                            'eoffice' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            'manajemen_mahasiswa' => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
                                            'user_management' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                            default => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $moduleColor }}">
                                        {{ $log->module_label }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                        $actionColor = match($log->action) {
                                            'CREATE' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            'UPDATE' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                            'DELETE' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                            'VIEW' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                            'LOGIN' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            default => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $actionColor }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 max-w-xs">
                                    <span class="text-slate-300 text-sm">{{ $log->description }}</span>
                                </td>

                                <td class="px-4 py-4">
                                    @if($log->subject_type)
                                    <p class="text-slate-400 text-xs">{{ $log->subject_type }}</p>
                                    <p class="text-slate-500 text-xs">#{{ $log->subject_id }}</p>
                                    @else
                                    <span class="text-slate-600 text-xs">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <span class="text-slate-600 text-xs">—</span>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium">Tidak ada log ditemukan</p>
                                    <p class="text-sm mt-1 text-slate-500">Coba ubah filter pencarian</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8" id="paginationWrapper">{{ $query->links() }}</div>

        </div>
    </div>

    <script>
    // Auto-submit filter dropdowns
    ['select[name="module"]', 'select[name="action"]', 'select[name="user_id"]', 'select[name="per_page"]'].forEach(selector => {
        document.querySelector(selector)?.addEventListener('change', function () {
            let pageInput = this.form.querySelector('input[name="page"]');
            if (!pageInput) {
                pageInput = document.createElement('input');
                pageInput.type = 'hidden';
                pageInput.name = 'page';
                this.form.appendChild(pageInput);
            }
            pageInput.value = 1;
            this.form.submit();
        });
    });

    // Pagination bawa filter
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationWrapper a');
        if (!link) return;
        e.preventDefault();
        const page = new URL(link.href).searchParams.get('page') ?? 1;
        const form = document.querySelector('form[method="GET"]');
        let pageInput = form.querySelector('input[name="page"]');
        if (!pageInput) {
            pageInput = document.createElement('input');
            pageInput.type = 'hidden';
            pageInput.name = 'page';
            form.appendChild(pageInput);
        }
        pageInput.value = page;
        form.submit();
    });
    </script>
</x-sidebar>
</x-app-layout>