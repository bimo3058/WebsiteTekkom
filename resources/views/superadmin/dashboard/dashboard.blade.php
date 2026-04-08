{{-- resources/views/superadmin/dashboard/dashboard.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <div class="min-h-screen bg-background font-sans">
        <div class="max-w-full px-7 py-7 pb-12">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-[13px] text-muted-foreground mb-1">
                <a href="/" class="hover:text-primary transition-colors">Home</a>
                <span class="text-grey-200">/</span>
                <span class="text-grey-700 font-medium">Dashboard</span>
            </nav>

            {{-- Header --}}
            <div class="flex items-start justify-between mb-7 gap-4 flex-wrap">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-foreground tracking-tight mt-1 mb-0.5">Superadmin Dashboard</h1>
                        <p class="text-sm text-muted-foreground">
                            Selamat datang kembali, <strong class="text-primary font-semibold">{{ auth()->user()->name }}</strong>
                        </p>
                    </div>

                    {{-- Import Progress Compact — di sebelah judul --}}
                    <div id="importProgressContainer"
                        data-import-id="{{ $activeImportId ?? session('import_id') ?? '' }}"
                        class="hidden items-center gap-2 bg-white border border-[#DEE2E6] rounded-xl px-3 py-2 shadow-sm">
                        <span class="material-symbols-outlined text-[#5E53F4] text-[16px] animate-spin shrink-0">sync</span>
                        <span id="importPercentText" class="text-[12px] font-bold text-[#5E53F4] tabular-nums">0%</span>
                        <button type="button" id="btnCancelImportHeader"
                            class="shrink-0 p-0.5 text-[#ADB5BD] hover:text-rose-500 transition-colors rounded"
                            title="Batalkan impor">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                        </button>
                    </div>
                </div>

                <div class="flex gap-2.5 items-center flex-wrap">
                    <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.audit-logs') }}">
                        <span class="material-symbols-outlined text-[16px]">history</span>
                        Audit Logs
                    </x-ui.button>
                    <x-ui.button size="sm" as="a" href="{{ route('superadmin.permissions') }}">
                        <span class="material-symbols-outlined text-[16px]">shield_person</span>
                        Manage Permissions
                    </x-ui.button>
                </div>
            </div>

            {{-- ── Quick Stats ─────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-7">
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Total Users</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_users) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-sky-50 text-sky-300 flex items-center justify-center shrink-0 group-hover:bg-sky-300 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">group</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Superadmins</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_superadmins) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-primary-50 text-primary-400 flex items-center justify-center shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">verified_user</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Admin Modul</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_admin_modul) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-indigo-50 text-indigo-400 flex items-center justify-center shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Dosen</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_lecturers) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-success-50 text-success-300 flex items-center justify-center shrink-0 group-hover:bg-success-300 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">school</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">GPM</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_gpm) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-rose-50 text-rose-400 flex items-center justify-center shrink-0 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
                    <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Mahasiswa</p>
                            <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_students) }}</p>
                        </div>
                        <div class="size-10 rounded-lg bg-warning-50 text-warning-300 flex items-center justify-center shrink-0 group-hover:bg-warning-300 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
            </div>

            {{-- ── Section: Import User ─────────────────── --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
                <span class="text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Import User</span>
                <x-ui.separator class="flex-1" />
            </div>

            <div class="bg-white border border-[#DEE2E6] rounded-2xl p-6 shadow-sm mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">upload_file</span>
                        </div>
                        <div>
                            <p class="text-[13px] font-semibold text-[#1A1C1E] tracking-tight">Import via CSV</p>
                            <p class="text-[12px] text-muted-foreground mt-0.5">Upload file CSV untuk menambahkan banyak user sekaligus ke sistem.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <x-ui.button variant="outline" size="sm" as="a" href="{{ route('superadmin.users.index') }}">
                            <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                            Kelola Users
                        </x-ui.button>
                        <x-ui.button size="sm" onclick="openModal('modalImportUser')">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Import Sekarang
                        </x-ui.button>
                    </div>
                </div>

                {{-- Progress body — selalu render, JS show/hide --}}
                <div id="importProgressBody" class="{{ ($activeImportId ?? session('import_id')) ? '' : 'hidden' }} mt-5 pt-5 border-t border-[#DEE2E6]">
                    <div class="flex items-center justify-between mb-2">
                        <p id="importStatusTextBody" class="text-[12px] font-semibold text-[#1A1C1E]">Memproses impor...</p>
                        <span id="importPercentTextBody" class="text-[11px] font-bold text-primary">0%</span>
                    </div>
                    <div class="h-2 bg-[#F8F9FA] rounded-full overflow-hidden border border-[#DEE2E6]">
                        <div id="importProgressBarBody" class="h-full bg-primary transition-all duration-500 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            {{-- ── Section: System Modules ─────────────────────── --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
                <span class="text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">System Modules</span>
                <x-ui.separator class="flex-1" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                @foreach($modules as $moduleKey => $module)
                @php
                    $variant = match($moduleKey) {
                        'bank_soal'           => 'warning',
                        'capstone'            => 'sky',
                        'eoffice'             => 'purple',
                        'manajemen_mahasiswa' => 'success',
                        default               => 'secondary',
                    };
                    $isActive = $module['is_active'];
                @endphp
                <x-ui.card class="flex flex-col hover:border-primary-100 hover:shadow-md transition-all">
                    <x-ui.card-content class="flex flex-col flex-1 !px-5 !py-5">
                        <div class="flex items-center justify-between mb-3.5">
                            <x-ui.badge :variant="$variant">{{ str_replace('_', ' ', $moduleKey) }}</x-ui.badge>
                            <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="relative w-9 h-5 rounded-full transition-colors {{ $isActive ? 'bg-[#5E53F4]' : 'bg-[#DEE2E6]' }}"
                                        title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <div class="absolute top-[3px] left-[3px] w-3.5 h-3.5 rounded-full bg-white shadow-sm transition-transform {{ $isActive ? 'translate-x-4' : '' }}"></div>
                                </button>
                            </form>
                        </div>
                        <h3 class="text-sm font-semibold text-grey-800 mb-1">{{ $module['name'] }}</h3>
                        <p class="text-xs text-muted-foreground leading-relaxed line-clamp-2 min-h-[36px]">
                            {{ $module['description'] ?? 'Manajemen fungsional untuk modul ' . $module['name'] . '.' }}
                        </p>
                        <div class="mt-auto pt-3.5 border-t border-border flex items-center justify-between">
                            <span class="text-[10px] font-semibold text-grey-300 uppercase tracking-wider">v{{ $module['version'] ?? '1.0' }}</span>
                            <a href="{{ route('superadmin.modules') }}" class="text-[11px] font-semibold text-primary hover:text-primary-400 transition-colors">Settings →</a>
                        </div>
                    </x-ui.card-content>
                </x-ui.card>
                @endforeach
            </div>

            {{-- ── Section: Aktivitas Terkini ──────────────────── --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
                <span class="text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Aktivitas Terkini</span>
                <x-ui.separator class="flex-1" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-7 items-stretch">

                {{-- ① User Online --}}
                <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
                    <x-ui.card-header class="!flex !flex-row !items-center !justify-between !px-5 !pt-4 !pb-3 border-b border-border bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500 text-[18px]">sensors</span>
                            <x-ui.card-title class="!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">Monitoring Online</x-ui.card-title>
                        </div>
                        <a href="{{ route('superadmin.users.online') }}" class="text-[10px] font-semibold text-primary hover:text-primary-400 flex items-center gap-0.5 transition-colors">
                            Detail <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </x-ui.card-header>
                    <div class="flex-1">
                        <x-ui.table>
                            <x-ui.table-body>
                                @php
                                    $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
                                        ->with('roles')->latest('last_login')->take(6)->get();
                                @endphp
                                @forelse($online_users as $onlineUser)
                                <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                                    <x-ui.table-cell class="!py-3 !px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <div class="size-8 rounded-full flex items-center justify-center border border-emerald-100 bg-emerald-50 text-emerald-600 overflow-hidden">
                                                    @if($onlineUser->avatar_url)
                                                        <img src="{{ $onlineUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-[10px] font-semibold uppercase">{{ substr($onlineUser->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full bg-emerald-500 border border-white"></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $onlineUser->name }}</p>
                                                @php
                                                    $rn = strtolower($onlineUser->roles->first()->name ?? '');
                                                    $rs = match(true) {
                                                        $rn === 'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                                        $rn === 'dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                                        $rn === 'mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                                        default              => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
                                                    };
                                                @endphp
                                                <span class="mt-1 inline-block px-1.5 py-0.5 rounded-full text-[8px] font-bold border uppercase tracking-wider {{ $rs }}">
                                                    {{ $onlineUser->roles->first()->name ?? 'User' }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 shrink-0">
                                                {{ $onlineUser->last_login ? $onlineUser->last_login->diffForHumans(null, true) : 'Active' }}
                                            </span>
                                        </div>
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                                @empty
                                <x-ui.table-row>
                                    <x-ui.table-cell class="text-center py-10 text-[11px] text-muted-foreground italic">Tidak ada user online.</x-ui.table-cell>
                                </x-ui.table-row>
                                @endforelse
                            </x-ui.table-body>
                        </x-ui.table>
                    </div>
                </x-ui.card>

                {{-- ② User Baru --}}
                <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
                    <x-ui.card-header class="!flex !flex-row !items-center !px-5 !pt-4 !pb-3 border-b border-border bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-success-500 text-[18px]">person_add</span>
                            <x-ui.card-title class="!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">User Baru Terdaftar</x-ui.card-title>
                        </div>
                    </x-ui.card-header>
                    <div class="flex-1">
                        <x-ui.table>
                            <x-ui.table-body>
                                @forelse($new_registrations as $newUser)
                                <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                                    <x-ui.table-cell class="!py-3 !px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full flex items-center justify-center border border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D] flex-shrink-0 overflow-hidden">
                                                @if($newUser->avatar_url)
                                                    <img src="{{ $newUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-[10px] font-semibold uppercase">{{ substr($newUser->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $newUser->name }}</p>
                                                <p class="text-[10px] text-muted-foreground mt-0.5 font-medium">Joined {{ $newUser->created_at->format('d M Y') }}</p>
                                            </div>
                                            @php
                                                $rn = strtolower($newUser->roles->first()->name ?? '');
                                                $rs = match(true) {
                                                    $rn === 'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                                    $rn === 'dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                                    $rn === 'mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                                    default              => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wider {{ $rs }} shrink-0">
                                                {{ $newUser->roles->first()->name ?? 'USER' }}
                                            </span>
                                        </div>
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                                @empty
                                <x-ui.table-row>
                                    <x-ui.table-cell class="text-center py-10 text-[11px] text-muted-foreground italic">Belum ada user baru.</x-ui.table-cell>
                                </x-ui.table-row>
                                @endforelse
                            </x-ui.table-body>
                        </x-ui.table>
                    </div>
                </x-ui.card>

                {{-- ③ Log Aktivitas --}}
                <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
                    <x-ui.card-header class="!flex !flex-row !items-center !justify-between !px-5 !pt-4 !pb-3 border-b border-border bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-500 text-[18px]">history</span>
                            <x-ui.card-title class="!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">Log Aktivitas Terbaru</x-ui.card-title>
                        </div>
                        <a href="{{ route('superadmin.audit-logs') }}" class="text-[10px] font-semibold text-primary hover:text-primary-400 flex items-center gap-0.5 transition-colors">
                            Lihat semua <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </x-ui.card-header>
                    <div class="flex-1 overflow-x-auto scrollbar-none">
                        <x-ui.table>
                            <x-ui.table-body>
                                @forelse($recent_logs->take(6) as $log)
                                @php
                                    $actionVariant = match(strtoupper($log->action)) {
                                        'CREATE' => 'success', 'UPDATE' => 'sky',
                                        'DELETE' => 'destructive', 'LOGIN' => 'purple',
                                        default  => 'secondary',
                                    };
                                @endphp
                                <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                                    <x-ui.table-cell class="!py-3 !px-5">
                                        <div class="flex items-center gap-3">
                                            @if($log->user)
                                                @php
                                                    $logSa = $log->user->hasRole('superadmin');
                                                    $logAv = $logSa ? '!bg-[#F1E9FF] border-[#D1BFFF] text-[#5E53F4]' : 'border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D]';
                                                @endphp
                                                <div class="size-8 rounded-full flex items-center justify-center border flex-shrink-0 overflow-hidden {{ $logAv }}">
                                                    @if($log->user->avatar_url)
                                                        <img src="{{ $log->user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-[10px] font-semibold uppercase">{{ substr($log->user->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="size-8 rounded-full flex items-center justify-center border border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D] flex-shrink-0">
                                                    <span class="material-symbols-outlined text-[16px]">settings_suggest</span>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $log->user->name ?? 'System' }}</p>
                                                <p class="text-[10px] text-muted-foreground mt-0.5 truncate">{{ $log->description }}</p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <x-ui.badge :variant="$actionVariant" class="!text-[8px] !px-1.5 !py-0 font-black leading-none block mb-1">
                                                    {{ $log->action }}
                                                </x-ui.badge>
                                                <span class="text-[9px] text-muted-foreground font-medium">{{ $log->created_at->diffForHumans(null, true) }}</span>
                                            </div>
                                        </div>
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                                @empty
                                <x-ui.table-row>
                                    <x-ui.table-cell class="text-center py-10 text-[11px] text-muted-foreground italic">Tidak ada log aktivitas.</x-ui.table-cell>
                                </x-ui.table-row>
                                @endforelse
                            </x-ui.table-body>
                        </x-ui.table>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>

    {{-- Modal Import di-extend dari user management --}}
    @include('superadmin.users._modal_import')

</x-sidebar>

<script>
// ── Modal ──────────────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('modalImportUser'); });

// ── Progress helpers ───────────────────────────────────────────
let importTimer = null;

function showProgressUI(importId) {
    const container = document.getElementById('importProgressContainer');
    if (container) {
        container.setAttribute('data-import-id', importId);
        container.classList.remove('hidden');
        container.classList.add('flex');
    }
    // Tombol cancel buka modal dulu
    const btnCancel = document.getElementById('btnCancelImportHeader');
    if (btnCancel) btnCancel.onclick = () => {
        if (typeof confirmCancelProgressModal === 'function') {
            confirmCancelProgressModal(importId);
        } else {
            cancelImport(importId); // fallback
        }
    };
    document.getElementById('importProgressBody')?.classList.remove('hidden');
}

function setProgress(pct) {
    const barH = document.getElementById('importProgressBar');
    const barB = document.getElementById('importProgressBarBody');
    const pctH = document.getElementById('importPercentText');
    const pctB = document.getElementById('importPercentTextBody');
    if (barH) barH.style.width = pct + '%';
    if (barB) barB.style.width = pct + '%';
    if (pctH) pctH.textContent = pct + '%';
    if (pctB) pctB.textContent = pct + '%';
}

function setStatus(html) {
    const h = document.getElementById('importStatusText');
    const b = document.getElementById('importStatusTextBody');
    if (h) h.innerHTML = html;
    if (b) b.innerHTML = html;
}

function setBarColor(color) {
    const cls = `h-full bg-${color}-500 transition-all duration-500 rounded-full`;
    const barH = document.getElementById('importProgressBar');
    const barB = document.getElementById('importProgressBarBody');
    if (barH) barH.className = cls;
    if (barB) barB.className = cls;
}

// ── Polling ────────────────────────────────────────────────────
function stopPolling() {
    if (importTimer) { clearInterval(importTimer); importTimer = null; }
}

function startPolling(importId) {
    if (!importId || importId === 'null' || importId === '') return;
    if (importTimer) clearInterval(importTimer);

    showProgressUI(importId);

    importTimer = setInterval(async () => {
        try {
            const res = await fetch(`/superadmin/import-status/${importId}`);
            const ct  = res.headers.get('content-type');
            if (!ct?.includes('application/json')) { stopPolling(); return; }
            if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
            if (!res.ok) { stopPolling(); return; }

            const data = await res.json();
            if (!data || typeof data !== 'object') { stopPolling(); return; }

            const pct = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;
            setProgress(pct);

            if (data.status === 'processing') {
                setStatus(`Memproses: ${data.processed} / ${data.total} user...`);
            } else if (data.status === 'completed') {
                stopPolling();
                setProgress(100);
                setBarColor('emerald');
                setStatus('<span class="text-emerald-600 font-bold">Impor Berhasil Selesai!</span>');
                setTimeout(async () => {
                    // Bust cache via server dulu sebelum reload
                    await fetch('/superadmin/clear-import-session', { method: 'POST' });
                    await fetch('/superadmin/bust-stats-cache', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
                    });
                    window.location.reload();
                }, 1500);
            } else if (data.status === 'failed') {
                stopPolling();
                setBarColor('red');
                setStatus(`<span class="text-red-600 font-bold">❌ Gagal: ${data.error_message || 'Import gagal'}</span>`);
                setTimeout(() => {
                    fetch('/superadmin/clear-import-session', { method: 'POST' });
                    window.location.reload();
                }, 3000);
            }
        } catch (e) {
            console.error('Polling error:', e);
            stopPolling();
        }
    }, 2000);
}

async function cancelImport(importId) {
    if (!importId) return;
    try {
        const res = await fetch(`/superadmin/import-status/${importId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        if (res.ok) {
            stopPolling();
            setBarColor('red');
            setStatus('<span class="text-red-600 font-bold">Impor dibatalkan</span>');
            setTimeout(async () => {
                await fetch('/superadmin/clear-import-session', { method: 'POST' });
                // Tunggu 3 detik setelah cancel sebelum bust cache + reload
                // agar Job sempat berhenti dan tidak ada insert tambahan
                await new Promise(resolve => setTimeout(resolve, 3000));
                await fetch('/superadmin/bust-stats-cache', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                window.location.reload();
            }, 1500);
        }
    } catch (e) { console.error('Cancel error:', e); }
}

// ── Init ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Jika ada import aktif dari session saat halaman load
    const container = document.getElementById('importProgressContainer');
    const sessionId = container?.getAttribute('data-import-id') || '';
    if (sessionId && sessionId !== '') startPolling(sessionId);

    // AJAX submit form import
    const importForm = document.getElementById('formImportUser');
    if (!importForm) return;

    importForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn            = document.getElementById('btnSubmitImport');
        const errorContainer = document.getElementById('importErrorContainer');
        const errorMessage   = document.getElementById('importErrorMessage');
        const dupeContainer  = document.getElementById('importDuplicateContainer');
    
        // Reset semua error
        if (errorContainer) errorContainer.classList.add('hidden');
        if (dupeContainer)  dupeContainer.classList.add('hidden');
    
        btn.disabled  = true;
        btn.innerHTML = '<span class="animate-spin material-symbols-outlined" style="font-size:18px">sync</span> Memvalidasi...';
    
        try {
            const res  = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();
    
            // ── Duplikat ditemukan ──────────────────────────────────
            if (data.status === 'duplicate') {
                handleImportDuplicateResponse(data);
                return;
            }
    
            // ── Error validasi biasa ────────────────────────────────
            if (!res.ok) throw new Error(data.message || 'Gagal memproses file.');
    
            // ── Sukses ─────────────────────────────────────────────
            if (data.import_id) {
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Berhasil!';
                closeModal('modalImportUser');
                startPolling(data.import_id);       // dashboard.blade.php
                // window.location.reload();        // index.blade.php — pakai ini
            } else if (data.status === 'success') {
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Berhasil!';
                window.location.reload();
            }
    
        } catch (err) {
            if (errorMessage)   errorMessage.textContent = err.message;
            if (errorContainer) errorContainer.classList.remove('hidden');
            btn.disabled  = false;
            btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Mulai Impor';
        }
    });
});
</script>
</x-app-layout>