{{-- resources/views/superadmin/dashboard.blade.php --}}
{{-- Menggunakan x-ui.* shadcn Blade components + Tailwind design tokens --}}

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
                <div>
                    <h1 class="text-2xl font-bold text-foreground tracking-tight mt-1 mb-0.5">Superadmin Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        Selamat datang kembali, <strong class="text-primary font-semibold">{{ auth()->user()->name }}</strong>
                    </p>
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

            {{-- 2 kolom sejajar --}}
            <div class="flex flex-col lg:flex-row gap-5 mb-7 items-stretch">

                {{-- ① User Baru Terdaftar --}}
                <x-ui.card class="flex-1 min-w-0 shadow-sm border-border/60 overflow-hidden flex flex-col">
                    <x-ui.card-header class="!flex !flex-row !items-center !px-5 !pt-4 !pb-3 border-b border-border bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-success-500 text-[18px]">person_add</span>
                            <x-ui.card-title class="!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">
                                User Baru Terdaftar
                            </x-ui.card-title>
                        </div>
                    </x-ui.card-header>
                    <div class="flex-1">
                        <x-ui.table>
                            <x-ui.table-body>
                                @forelse($new_registrations as $user)
                                <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                                    <x-ui.table-cell class="!py-3 !px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full flex items-center justify-center border border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D] flex-shrink-0">
                                                <span class="text-[10px] font-semibold uppercase">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $user->name }}</p>
                                                <p class="text-[10px] text-muted-foreground mt-0.5 font-medium">Joined {{ $user->created_at->format('d M Y') }}</p>
                                            </div>
                                            @php
                                                $roleName = strtolower($user->roles->first()->name ?? '');
                                                $roleColors = match($roleName) {
                                                    'superadmin'         => 'bg-purple-50 text-purple-600 border-purple-100',
                                                    'dosen'              => 'bg-green-50 text-green-600 border-green-100',
                                                    'mahasiswa'          => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    'gpm'                => 'bg-teal-50 text-teal-600 border-teal-100',
                                                    'admin_banksoal'     => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                    'admin_capstone'     => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                                    'admin_eoffice'      => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                    'admin_kemahasiswaan'=> 'bg-rose-50 text-rose-600 border-rose-100',
                                                    default              => 'bg-blue-50 text-blue-600 border-blue-100',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black border uppercase shrink-0 {{ $roleColors }}">
                                                {{ $user->roles->first()->name ?? 'USER' }}
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

                {{-- ② Log Aktivitas Terbaru --}}
                <x-ui.card class="flex-1 min-w-0 shadow-sm border-border/60 overflow-hidden flex flex-col">
                    <x-ui.card-header class="!flex !flex-row !items-center !justify-between !px-5 !pt-4 !pb-3 border-b border-border bg-muted/30">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-500 text-[18px]">history</span>
                            <x-ui.card-title class="!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">
                                Log Aktivitas Terbaru
                            </x-ui.card-title>
                        </div>
                        <a href="{{ route('superadmin.audit-logs') }}" class="text-[10px] font-semibold text-primary hover:text-primary-400 flex items-center gap-0.5 transition-colors">
                            Lihat semua
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </x-ui.card-header>
                    <div class="flex-1 overflow-x-auto">
                        <x-ui.table>
                            <x-ui.table-header>
                                <x-ui.table-row class="bg-muted/20 border-b border-border/40 hover:bg-muted/20">
                                    <x-ui.table-head class="!px-4 !py-2 text-[9px] font-bold uppercase tracking-widest text-muted-foreground w-[76px]">Waktu</x-ui.table-head>
                                    <x-ui.table-head class="!px-4 !py-2 text-[9px] font-bold uppercase tracking-widest text-muted-foreground">User</x-ui.table-head>
                                    <x-ui.table-head class="!px-4 !py-2 text-[9px] font-bold uppercase tracking-widest text-muted-foreground w-[68px]">Aksi</x-ui.table-head>
                                    <x-ui.table-head class="!px-4 !py-2 text-[9px] font-bold uppercase tracking-widest text-muted-foreground">Deskripsi</x-ui.table-head>
                                </x-ui.table-row>
                            </x-ui.table-header>
                            <x-ui.table-body>
                                @forelse($recent_logs->take(6) as $log)
                                @php
                                    $actionVariant = match(strtoupper($log->action)) {
                                        'CREATE'  => 'success',
                                        'UPDATE'  => 'sky',
                                        'DELETE'  => 'destructive',
                                        'LOGIN'   => 'purple',
                                        'LOGOUT'  => 'secondary',
                                        'VIEW'    => 'sky',
                                        default   => 'secondary',
                                    };
                                @endphp
                                <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                                    <x-ui.table-cell class="!px-4 !py-3 align-middle">
                                        <span class="text-[10px] font-medium text-grey-700 block leading-tight">{{ $log->created_at->format('d/m/Y') }}</span>
                                        <span class="text-[9px] text-muted-foreground block leading-tight">{{ $log->created_at->format('H:i') }}</span>
                                    </x-ui.table-cell>
                                    <x-ui.table-cell class="!px-4 !py-3 align-middle">
                                        <span class="text-[11px] font-semibold text-grey-800 block leading-tight truncate max-w-[110px]">{{ $log->user->name ?? 'System' }}</span>
                                        @if($log->user)
                                        <span class="text-[9px] text-muted-foreground block leading-tight truncate max-w-[110px]">{{ Str::limit($log->user->email, 20) }}</span>
                                        @endif
                                    </x-ui.table-cell>
                                    <x-ui.table-cell class="!px-4 !py-3 align-middle">
                                        <x-ui.badge :variant="$actionVariant" class="!text-[8px] !px-1.5 !py-0 font-black leading-none">
                                            {{ $log->action }}
                                        </x-ui.badge>
                                    </x-ui.table-cell>
                                    <x-ui.table-cell class="!px-4 !py-3 align-middle">
                                        <span class="text-[10px] text-grey-600 leading-snug line-clamp-2 block" title="{{ $log->description }}">
                                            {{ Str::limit($log->description, 48) }}
                                        </span>
                                        @if($log->module)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 mt-0.5 leading-none">
                                            {{ str_replace('_', ' ', $log->module) }}
                                        </span>
                                        @endif
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                                @empty
                                <x-ui.table-row>
                                    <x-ui.table-cell colspan="4" class="text-center py-10 text-[11px] text-muted-foreground italic">
                                        Tidak ada log aktivitas terbaru.
                                    </x-ui.table-cell>
                                </x-ui.table-row>
                                @endforelse
                            </x-ui.table-body>
                        </x-ui.table>
                    </div>
                </x-ui.card>

            </div>

            {{-- ── Cloud Storage Test ──────────────────────────── --}}
            <div class="rounded-lg bg-gradient-to-br from-primary-400 to-primary-500 p-8 sm:p-9 text-white relative overflow-hidden shadow-[0_8px_24px_-4px_rgba(94,83,244,.25)]">
                <div class="absolute -top-1/2 -right-[8%] w-80 h-80 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-[40%] right-[20%] w-52 h-52 rounded-full bg-white/[.03]"></div>

                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h2 class="text-[22px] font-bold tracking-tight mb-2">Cloud Storage Test</h2>
                        <p class="text-[13px] text-white/80 leading-relaxed mb-5">
                            Pilih berkas untuk melihat pratinjau detail sebelum diunggah ke Supabase Storage.
                        </p>

                        <form action="{{ route('superadmin.storage.upload') }}" method="POST" enctype="multipart/form-data" id="storage-form">
                            @csrf
                            <input type="file" name="file" id="file-input" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.docx">

                            <div class="flex flex-col gap-2.5">
                                <button type="button" onclick="document.getElementById('file-input').click()"
                                        class="inline-flex items-center gap-2 bg-white/[.12] hover:bg-white/20 text-white text-xs font-semibold px-5 py-2.5 rounded-md border border-white/20 transition-colors w-fit">
                                    <span class="material-symbols-outlined text-[16px]">attachment</span>
                                    Pilih Berkas
                                </button>

                                <div id="file-preview" class="hidden bg-white/10 backdrop-blur-sm border border-white/15 rounded-lg px-4 py-3 mt-1">
                                    <div class="flex items-center gap-2.5">
                                        <span class="material-symbols-outlined text-white/60 text-[20px]">description</span>
                                        <div class="flex-1 min-w-0">
                                            <p id="preview-name" class="text-xs font-semibold truncate"></p>
                                            <p id="preview-size" class="text-[10px] text-white/70"></p>
                                        </div>
                                        <button type="button" onclick="cancelUpload()" class="text-white/60 hover:text-white p-0 bg-transparent border-none cursor-pointer">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" id="submit-upload"
                                        class="hidden inline-flex items-center gap-2 bg-white text-primary-500 text-xs font-bold px-5 py-2.5 rounded-md hover:bg-grey-25 transition-colors shadow-md w-fit">
                                    <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                                    Unggah Sekarang
                                </button>
                            </div>
                        </form>
                    </div>

                    @if(session('upload_success'))
                    <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-success-200 text-[20px]">check_circle</span>
                            <span class="text-[11px] font-bold uppercase tracking-wider">Upload Berhasil</span>
                        </div>
                        <p class="text-[11px] text-white/70 truncate mb-1"><span class="text-white/50">Path:</span> {{ session('upload_path') }}</p>
                        <p class="text-[11px] text-white/70 mb-3.5"><span class="text-white/50">Size:</span> {{ number_format(session('upload_size') / 1024, 1) }} KB</p>
                        <div class="flex gap-2">
                            <a href="{{ session('upload_url') }}" target="_blank"
                               class="flex-1 text-center bg-white/[.12] hover:bg-white/[.22] text-white text-[10px] font-bold py-2 rounded-md border border-white/15 transition-colors">
                                BUKA FILE
                            </a>
                            <form action="{{ route('superadmin.storage.delete') }}" method="POST" class="flex-1 m-0">
                                @csrf @method('DELETE')
                                <input type="hidden" name="path" value="{{ session('upload_path') }}">
                                <button type="submit"
                                        class="w-full bg-destructive-200/10 hover:bg-destructive-200/25 text-[#ffc4b8] text-[10px] font-bold py-2 rounded-md border border-destructive-200/20 transition-colors">
                                    HAPUS
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</x-sidebar>

<script>
const fileInput   = document.getElementById('file-input');
const previewArea = document.getElementById('file-preview');
const previewName = document.getElementById('preview-name');
const previewSize = document.getElementById('preview-size');
const submitBtn   = document.getElementById('submit-upload');

fileInput?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    previewName.textContent = file.name;
    previewSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
    previewArea.classList.remove('hidden');
    submitBtn.classList.remove('hidden');
});

function cancelUpload() {
    fileInput.value = '';
    previewArea.classList.add('hidden');
    submitBtn.classList.add('hidden');
}
</script>
</x-app-layout>