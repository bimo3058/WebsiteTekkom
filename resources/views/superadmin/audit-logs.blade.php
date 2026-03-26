<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="p-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-xl font-semibold text-slate-800">Audit Log</h1>
                <p class="text-slate-500 text-sm mt-0.5">Total {{ $query->total() }} aktivitas tercatat</p>
            </div>

            {{-- Filter --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 mb-5 shadow-sm">
                <form method="GET" action="{{ route('superadmin.audit-logs') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Modul</label>
                            <select name="module" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
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
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Action</label>
                            <select name="action" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                <option value="">Semua Action</option>
                                @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">User</label>
                            <select name="user_id" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                <option value="">Semua User</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Tampilkan</label>
                            <select name="per_page" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                @foreach([25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 25) === $size ? 'selected' : '' }}>{{ $size }} data</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">Filter</button>
                            <a href="{{ route('superadmin.audit-logs') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium py-2 px-3 rounded-lg transition-colors text-center">Reset</a>
                        </div>
                    </div>

                    <div class="mt-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari berdasarkan deskripsi aktivitas..."
                            class="w-full bg-white border border-slate-200 rounded-lg px-4 py-2 text-slate-700 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Modul</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Subject</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($query as $log)
                            <tr class="hover:bg-slate-50 transition-colors">

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-slate-700 text-sm">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-slate-400 text-xs">{{ $log->created_at->format('H:i:s') }}</p>
                                </td>

                                <td class="px-4 py-3">
                                    @if($log->user)
                                    <p class="text-slate-800 text-sm font-medium">{{ $log->user->name }}</p>
                                    <p class="text-slate-400 text-xs">{{ $log->user->email }}</p>
                                    @else
                                    <span class="text-slate-400 text-xs italic">System / Deleted</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $moduleColor = match($log->module) {
                                            'auth'                 => 'bg-green-50 text-green-700 border-green-200',
                                            'bank_soal'            => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                            'capstone'             => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            'eoffice'              => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'manajemen_mahasiswa'  => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'user_management'      => 'bg-blue-50 text-blue-700 border-blue-200',
                                            default                => 'bg-slate-50 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $moduleColor }}">
                                        {{ $log->module_label }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $actionColor = match($log->action) {
                                            'CREATE' => 'bg-green-50 text-green-700 border-green-200',
                                            'UPDATE' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                            'DELETE' => 'bg-red-50 text-red-700 border-red-200',
                                            'VIEW'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'LOGIN'  => 'bg-purple-50 text-purple-700 border-purple-200',
                                            default  => 'bg-slate-50 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $actionColor }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 max-w-xs">
                                    <span class="text-slate-700 text-sm">{{ $log->description }}</span>
                                </td>

                                <td class="px-4 py-3">
                                    @if($log->subject_type)
                                    <p class="text-slate-500 text-xs">{{ $log->subject_type }}</p>
                                    <p class="text-slate-400 text-xs">#{{ $log->subject_id }}</p>
                                    @else
                                    <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="text-slate-300 text-xs">—</span>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-slate-500 text-sm font-medium">Tidak ada log ditemukan</p>
                                    <p class="text-slate-400 text-xs mt-1">Coba ubah filter pencarian</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5" id="paginationWrapper">{{ $query->links() }}</div>

        </div>
    </div>

    <script>
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