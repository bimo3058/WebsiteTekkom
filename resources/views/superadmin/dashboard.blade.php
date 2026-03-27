<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header: Konsisten dengan User Management Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Superadmin Dashboard</h1>
                    <p class="text-slate-500 text-sm mt-0.5 font-medium">Selamat datang kembali, <span class="text-blue-600">{{ auth()->user()->name }}</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('superadmin.audit-logs') }}" 
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-bold px-4 py-2 rounded-lg transition-all text-xs border border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                        Audit Logs
                    </a>
                    <a href="{{ route('superadmin.permissions') }}" 
                       class="inline-flex items-center gap-2 bg-slate-800 hover:bg-black text-white font-bold px-4 py-2 rounded-lg transition-all shadow-sm text-xs">
                        <span class="material-symbols-outlined text-[18px]">shield_person</span>
                        Manage Permissions
                    </a>
                </div>
            </div>

            {{-- Quick Stats: Menggunakan skema warna Permission --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                {{-- Total Users --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-lg hover:border-blue-200 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Total Users</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $total_users }}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors text-blue-600">
                            <span class="material-symbols-outlined text-2xl">group</span>
                        </div>
                    </div>
                </div>

                {{-- Superadmins --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-lg hover:border-purple-200 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Superadmins</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $total_superadmins }}</p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-xl border border-purple-100 group-hover:bg-purple-600 group-hover:text-white transition-colors text-purple-600">
                            <span class="material-symbols-outlined text-2xl">verified_user</span>
                        </div>
                    </div>
                </div>

                {{-- Dosen --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-lg hover:border-green-200 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Tenaga Pengajar</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $total_lecturers }}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-xl border border-green-100 group-hover:bg-green-600 group-hover:text-white transition-colors text-green-600">
                            <span class="material-symbols-outlined text-2xl">school</span>
                        </div>
                    </div>
                </div>

                {{-- Mahasiswa --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-lg hover:border-orange-200 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Mahasiswa</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $total_students }}</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-xl border border-orange-100 group-hover:bg-orange-600 group-hover:text-white transition-colors text-orange-600">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Module Status: Layout Grid Horizontal Berpola Permission --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                        System Modules
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($modules as $moduleKey => $module)
                    @php
                        $moduleColors = match($moduleKey) {
                            'bank_soal' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-600', 'btn' => 'bg-yellow-600'],
                            'capstone' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-200', 'text' => 'text-cyan-600', 'btn' => 'bg-cyan-600'],
                            'eoffice' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-600', 'btn' => 'bg-purple-600'],
                            'manajemen_mahasiswa' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-600', 'btn' => 'bg-orange-600'],
                            default => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-600', 'btn' => 'bg-blue-600'],
                        };
                        $isActive = $module['is_active'];
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-4">
                                <div class="{{ $moduleColors['bg'] }} {{ $moduleColors['text'] }} px-3 py-1 rounded-lg text-[9px] font-black uppercase border {{ $moduleColors['border'] }}">
                                    {{ str_replace('_', ' ', $moduleKey) }}
                                </div>
                                <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="relative inline-flex items-center cursor-pointer">
                                        <div class="w-9 h-5 {{ $isActive ? 'bg-emerald-500' : 'bg-slate-200' }} rounded-full transition-colors relative">
                                            <div class="absolute top-1 left-1 bg-white w-3 h-3 rounded-full transition-transform {{ $isActive ? 'translate-x-4' : '' }}"></div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">{{ $module['name'] }}</h3>
                            <p class="text-slate-500 text-[11px] mt-1 line-clamp-2 h-8">{{ $module['description'] ?? 'Manajemen fungsional untuk modul ' . $module['name'] }}</p>
                            
                            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Versi {{ $module['version'] ?? '1.0.2' }}</div>
                                <a href="{{ route('superadmin.modules') }}" class="text-[11px] font-bold text-blue-600 hover:underline">Settings</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recent Users Table: Desain Identik dengan Permission/User Table --}}
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest">Pengguna Terbaru</h2>
                        <a href="{{ route('superadmin.users.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-tight">Kelola Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-slate-100">
                                @foreach($recent_users->take(6) as $user)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold text-[10px]">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-800 truncate">{{ $user->name }}</p>
                                                <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex gap-1">
                                            @foreach($user->roles as $role)
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black border uppercase bg-blue-50 text-blue-600 border-blue-100">
                                                {{ $role->name }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="text-[10px] text-slate-400 italic">{{ $user->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Activity Feed --}}
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest">Log Aktivitas</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        @foreach($recent_logs->take(5) as $log)
                        <div class="relative pl-6 pb-4 border-l border-slate-100 last:border-0 last:pb-0">
                            <div class="absolute left-[-5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-200 border-2 border-white"></div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">{{ $log->action }}</span>
                                <span class="text-[9px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-snug">{{ $log->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Supabase Test Section: Minimalist --}}
            <div class="mt-8">
                <div class="bg-blue-600 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg shadow-blue-200">
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h2 class="text-2xl font-bold mb-2 tracking-tight">Cloud Storage Test</h2>
                            <p class="text-blue-100 text-sm mb-6 leading-relaxed">Pilih berkas untuk melihat pratinjau detail sebelum diunggah ke Supabase Storage.</p>
                            
                            <form action="{{ route('superadmin.storage.upload') }}" method="POST" enctype="multipart/form-data" id="storage-form">
                                @csrf
                                {{-- Input file tersembunyi --}}
                                <input type="file" name="file" id="file-input" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.docx">
                                
                                <div class="flex flex-col gap-3">
                                    {{-- Tombol Pilih File --}}
                                    <button type="button" onclick="document.getElementById('file-input').click()"
                                            class="w-full md:w-fit bg-blue-500/30 hover:bg-blue-500/50 text-white font-bold text-xs uppercase tracking-widest px-6 py-3 rounded-xl transition-all border border-white/20 flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">attachment</span>
                                        Pilih Berkas
                                    </button>

                                    {{-- Area Preview Detail (Hidden by default) --}}
                                    <div id="file-preview" class="hidden bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-4 animate-in fade-in slide-in-from-top-2 duration-300">
                                        <div class="flex items-start gap-3">
                                            <span class="material-symbols-outlined text-blue-200">description</span>
                                            <div class="flex-1 min-w-0">
                                                <p id="preview-name" class="text-xs font-bold truncate"></p>
                                                <p id="preview-size" class="text-[10px] text-blue-200 mt-0.5"></p>
                                            </div>
                                            <button type="button" onclick="cancelUpload()" class="text-blue-200 hover:text-white">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Tombol Eksekusi Upload --}}
                                    <button type="submit" id="submit-upload" class="hidden w-full md:w-fit bg-white text-blue-600 font-black text-xs uppercase tracking-widest px-8 py-3 rounded-xl hover:bg-blue-50 transition-all shadow-lg flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                                        Unggah Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Bagian Hasil Upload Tetap Sama --}}
                        @if(session('upload_success'))
                        <div class="bg-blue-500/30 backdrop-blur-md rounded-2xl p-5 border border-blue-400/30">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="material-symbols-outlined text-emerald-300">check_circle</span>
                                <span class="text-xs font-bold uppercase tracking-widest">Upload Berhasil</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <p class="text-[10px] text-blue-100 truncate"><span class="opacity-50">Path:</span> {{ session('upload_path') }}</p>
                                <p class="text-[10px] text-blue-100"><span class="opacity-50">Size:</span> {{ number_format(session('upload_size') / 1024, 1) }} KB</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ session('upload_url') }}" target="_blank" class="flex-1 bg-white/10 hover:bg-white/20 text-center py-2 rounded-lg text-[10px] font-bold transition-all border border-white/20">BUKA FILE</a>
                                <form action="{{ route('superadmin.storage.delete') }}" method="POST" class="flex-1">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="path" value="{{ session('upload_path') }}">
                                    <button type="submit" class="w-full bg-red-400/10 hover:bg-red-400/30 text-red-200 py-2 rounded-lg text-[10px] font-bold transition-all border border-red-400/20">HAPUS</button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-sidebar>
<script>
    const fileInput = document.getElementById('file-input');
    const previewArea = document.getElementById('file-preview');
    const previewName = document.getElementById('preview-name');
    const previewSize = document.getElementById('preview-size');
    const submitBtn = document.getElementById('submit-upload');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Tampilkan info file
            previewName.textContent = file.name;
            previewSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            
            // Munculkan elemen preview dan tombol submit
            previewArea.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
        }
    });

    function cancelUpload() {
        // Reset input dan sembunyikan preview
        fileInput.value = '';
        previewArea.classList.add('hidden');
        submitBtn.classList.add('hidden');
    }
</script>
</x-app-layout>