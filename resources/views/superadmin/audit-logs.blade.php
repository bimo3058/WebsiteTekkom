<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="max-w-full mx-auto">

            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Audit Log System</h1>
                    <p class="text-slate-500 text-[11px] mt-0.5 font-medium">
                        Total <span class="text-blue-600">{{ $query->total() }}</span> aktivitas tercatat dalam sistem
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('superadmin.dashboard') }}"
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-bold px-3 py-1.5 rounded-lg transition-all text-[11px] border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined" style="font-size:16px">dashboard</span>
                        Dashboard
                    </a>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
                <form method="GET" action="{{ route('superadmin.audit-logs') }}" id="auditFilterForm">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        {{-- Row 1 --}}
                        <div class="md:col-span-3">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Modul</label>
                            <select name="module" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs appearance-none">
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

                        <div class="md:col-span-2">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Action</label>
                            <select name="action" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs appearance-none">
                                <option value="">Semua Action</option>
                                @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">User</label>
                            <select name="user_id" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs appearance-none">
                                <option value="">Semua User</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 outline-none transition-all text-xs">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 focus:border-blue-400 outline-none transition-all text-xs">
                        </div>

                        {{-- Row 2 --}}
                        <div class="md:col-span-9">
                            <label class="block text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1.5">Pencarian Deskripsi</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">search</span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari kata kunci aktivitas..."
                                    class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-1.5 text-slate-800 placeholder-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs">
                            </div>
                        </div>

                        <div class="md:col-span-3 flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded-lg transition-all text-[11px] shadow-sm">
                                Filter
                            </button>
                            <a href="{{ route('superadmin.audit-logs') }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-all">
                                <span class="material-symbols-outlined" style="font-size:18px">refresh</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">User</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Modul</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Action</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Subject</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($query as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-slate-700 font-bold text-[11px]">{{ $log->created_at->format('d/m/Y') }}</span>
                                        <span class="text-slate-400 text-[10px] italic">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <div class="flex flex-col">
                                            <span class="text-slate-800 font-bold text-[11px]">{{ $log->user->name }}</span>
                                            <span class="text-slate-400 text-[10px]">{{ $log->user->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-300 text-[10px] italic">System / Deleted</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $moduleColor = match($log->module) {
                                            'auth'                 => 'bg-green-50 text-green-600 border-green-100',
                                            'bank_soal'            => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                            'capstone'             => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                            'eoffice'              => 'bg-purple-50 text-purple-600 border-purple-100',
                                            'manajemen_mahasiswa'  => 'bg-orange-50 text-orange-600 border-orange-100',
                                            'user_management'      => 'bg-blue-50 text-blue-600 border-blue-100',
                                            default                => 'bg-slate-50 text-slate-500 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black border uppercase {{ $moduleColor }}">
                                        {{ str_replace('_', ' ', $log->module) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $actionColor = match($log->action) {
                                            'CREATE' => 'text-green-600 font-bold',
                                            'UPDATE' => 'text-yellow-600 font-bold',
                                            'DELETE' => 'text-red-600 font-bold',
                                            'VIEW'   => 'text-blue-600 font-bold',
                                            'LOGIN'  => 'text-purple-600 font-bold',
                                            default  => 'text-slate-500 font-bold',
                                        };
                                    @endphp
                                    <span class="text-[10px] tracking-tight {{ $actionColor }} uppercase">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-slate-600 text-[11px] leading-relaxed max-w-xs line-clamp-2" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    @if($log->subject_type)
                                        <div class="flex flex-col">
                                            <span class="text-slate-500 text-[10px] font-medium">{{ class_basename($log->subject_type) }}</span>
                                            <span class="text-slate-300 text-[9px]">ID: #{{ $log->subject_id }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-200 text-[10px]">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-[11px] italic">
                                    Tidak ada catatan aktivitas yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6" id="paginationWrapper">
                {{ $query->links() }}
            </div>

        </div>
    </div>

    <script>
    // Auto submit on select change
    ['select[name="module"]', 'select[name="action"]', 'select[name="user_id"]'].forEach(selector => {
        document.querySelector(selector)?.addEventListener('change', function () {
            this.form.submit();
        });
    });

    // Pagination AJAX Helper (Optional - keeps same style as User Management)
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationWrapper a');
        if (!link) return;
        e.preventDefault();
        const url = new URL(link.href);
        const page = url.searchParams.get('page') ?? 1;
        const form = document.getElementById('auditFilterForm');
        
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