<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Superadmin Dashboard</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Welcome back, <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span></p>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                {{-- Total Users --}}
                <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-slate-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Total Users</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total_users }}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12 14a8 8 0 00-8 8v2h16v-2a8 8 0 00-8-8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Superadmins --}}
                <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-slate-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Superadmins</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total_superadmins }}</p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-xl border border-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Dosen --}}
                <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-slate-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Dosen</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total_lecturers }}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-xl border border-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Mahasiswa --}}
                <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-md hover:border-slate-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Mahasiswa</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total_students }}</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-xl border border-orange-100">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modules Section --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-800">Modules</h2>
                    <a href="{{ route('superadmin.modules') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">View All →</a>
                </div>

                {{-- Alert Sukses --}}
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4 flex items-center gap-3 text-emerald-700 text-sm">
                        <span class="material-symbols-outlined text-emerald-500" style="font-size:18px">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($modules as $moduleKey => $module)
                    @php
                        $moduleColors = match($moduleKey) {
                            'bank_soal' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-100', 'text' => 'text-yellow-600'],
                            'capstone' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-100', 'text' => 'text-cyan-600'],
                            'eoffice' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-100', 'text' => 'text-purple-600'],
                            'manajemen_mahasiswa' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-100', 'text' => 'text-orange-600'],
                            default => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'text' => 'text-blue-600'],
                        };
                        $isActive = $module['is_active'];
                    @endphp
                    <div class="bg-white border {{ $isActive ? 'border-slate-200 hover:border-slate-300' : 'border-slate-200 opacity-60' }} rounded-xl overflow-hidden hover:shadow-md transition-all">
                        <div class="p-5">
                            {{-- Top Row: Icon + Toggle --}}
                            <div class="flex justify-between items-start mb-4">
                                <div class="{{ $moduleColors['bg'] }} p-2.5 rounded-xl border {{ $moduleColors['border'] }}">
                                    <svg class="w-5 h-5 {{ $moduleColors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>

                                <div class="flex flex-col items-end gap-1.5">
                                    <form action="{{ route('superadmin.modules.toggle', $module['slug']) }}" method="POST">
                                        @csrf
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" onchange="this.form.submit()" class="sr-only peer"
                                                {{ $isActive ? 'checked' : '' }}>
                                            <div class="w-10 h-5 bg-slate-200 rounded-full peer
                                                        peer-checked:bg-emerald-500
                                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                        after:bg-white after:rounded-full after:h-4 after:w-4
                                                        after:transition-all peer-checked:after:translate-x-5
                                                        transition-colors">
                                            </div>
                                        </label>
                                    </form>
                                    <span class="text-[11px] font-semibold {{ $isActive ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Module Info --}}
                            <h3 class="text-base font-semibold text-slate-800 mb-1">{{ $module['name'] }}</h3>
                            <p class="text-slate-500 text-xs mb-4 leading-relaxed">{{ $module['description'] ?? 'Module description goes here' }}</p>

                            {{-- Meta Info --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Version</p>
                                    <p class="text-slate-700 text-xs font-semibold">{{ $module['version'] ?? '1.0.0' }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Status</p>
                                    <p class="{{ $isActive ? 'text-emerald-600' : 'text-slate-500' }} text-xs font-semibold">
                                        {{ $isActive ? 'Ready' : 'Disabled' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <a href="{{ $isActive ? route('superadmin.modules') : '#' }}" 
                               class="block w-full py-2.5 {{ $isActive ? 'bg-slate-800 hover:bg-slate-700' : 'bg-slate-200 cursor-not-allowed' }} text-white rounded-lg font-medium transition-colors text-center text-sm">
                                Manage Module
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Users & Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Users Card --}}
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Recent Users</h2>
                            <p class="text-xs text-slate-400 mt-0.5">User terakhir yang bergabung</p>
                        </div>
                        <a href="{{ route('superadmin.users.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">View All →</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recent_users->take(5) as $user)
                        @php
                            $roleColor = match($user->roles->first()->name ?? '') {
                                'superadmin' => 'bg-purple-50 text-purple-600 border-purple-100',
                                'dosen' => 'bg-green-50 text-green-600 border-green-100',
                                'mahasiswa' => 'bg-orange-50 text-orange-600 border-orange-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100',
                            };
                        @endphp
                        <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-800 text-sm font-medium truncate">{{ $user->name }}</p>
                                <p class="text-slate-400 text-xs truncate">{{ $user->email }}</p>
                            </div>
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold border {{ $roleColor }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                        @empty
                        <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada user</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity Card --}}
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Recent Activity</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Aktivitas sistem terakhir</p>
                        </div>
                        <a href="{{ route('superadmin.audit-logs') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">View All →</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recent_logs->take(5) as $log)
                        @php
                            $actionColor = match($log->action) {
                                'CREATE' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'UPDATE' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'DELETE' => 'bg-red-50 text-red-600 border-red-100',
                                'LOGIN' => 'bg-purple-50 text-purple-600 border-purple-100',
                                default => 'bg-blue-50 text-blue-600 border-blue-100',
                            };
                        @endphp
                        <div class="px-5 py-3 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold border {{ $actionColor }}">
                                    {{ $log->action }}
                                </span>
                                <span class="text-slate-400 text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-600 text-xs truncate">{{ $log->description }}</p>
                        </div>
                        @empty
                        <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada aktivitas</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Supabase Storage Section --}}
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Supabase Storage</h2>
                        <p class="text-slate-500 text-sm mt-0.5">Test upload file ke Supabase Storage</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Upload Form Card --}}
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                        <div class="p-5">
                            <h3 class="text-base font-semibold text-slate-800 mb-1">Upload File</h3>
                            <p class="text-slate-500 text-xs mb-4">Format: JPG, PNG, WEBP, PDF, DOC, DOCX — maks 10 MB</p>

                            <form action="{{ route('superadmin.storage.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                                @csrf

                                <div id="drop-zone" onclick="document.getElementById('file-input').click()"
                                    class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4 hover:border-blue-400 hover:bg-blue-50/20 transition-all cursor-pointer">
                                    <span class="material-symbols-outlined text-slate-400 text-3xl mb-2">cloud_upload</span>
                                    <p class="text-slate-500 text-sm" id="file-label">Klik untuk pilih file</p>
                                    <p class="text-slate-400 text-xs mt-0.5">atau drag & drop di sini</p>
                                    <input id="file-input" type="file" name="file" class="hidden" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                                </div>

                                <div id="file-detail" class="hidden mb-4 bg-slate-50 border border-slate-200 rounded-lg p-3 flex items-center gap-3">
                                    <div class="bg-blue-50 p-2 rounded-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-blue-600" style="font-size:20px">description</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="detail-name" class="text-slate-800 text-sm font-medium truncate"></p>
                                        <p id="detail-meta" class="text-slate-400 text-xs mt-0.5"></p>
                                    </div>
                                    <button type="button" id="clear-file" class="text-slate-400 hover:text-red-500 transition p-1">
                                        <span class="material-symbols-outlined" style="font-size:18px">close</span>
                                    </button>
                                </div>

                                <button type="submit" id="upload-btn" disabled
                                    class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 disabled:bg-slate-200 disabled:cursor-not-allowed text-white disabled:text-slate-400 rounded-lg font-medium transition-colors text-sm">
                                    <span id="upload-btn-label">Pilih file terlebih dahulu</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Upload Result Card --}}
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                        <div class="p-5">
                            <h3 class="text-base font-semibold text-slate-800 mb-4">Hasil Upload</h3>
                            @if(session('upload_success'))
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                    <div class="flex items-center gap-2 text-emerald-600 text-sm font-medium mb-3">
                                        <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
                                        Upload berhasil!
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Ukuran</span>
                                            <span class="text-slate-700 font-medium">{{ number_format(session('upload_size') / 1024, 1) }} KB</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Path</span>
                                            <span class="text-slate-700 text-xs truncate max-w-[200px]">{{ session('upload_path') }}</span>
                                        </div>
                                        <a href="{{ session('upload_url') }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-xs break-all block">Lihat file →</a>
                                    </div>
                                    <form action="{{ route('superadmin.storage.delete') }}" method="POST" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ session('upload_path') }}">
                                        <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-sm font-medium transition-colors">
                                            Hapus file
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2">cloud_off</span>
                                    <p class="text-sm">Belum ada file yang diupload</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const input = document.getElementById('file-input');
        const dropZone = document.getElementById('drop-zone');
        const fileDetail = document.getElementById('file-detail');
        const detailName = document.getElementById('detail-name');
        const detailMeta = document.getElementById('detail-meta');
        const clearBtn = document.getElementById('clear-file');
        const uploadBtn = document.getElementById('upload-btn');
        const btnLabel = document.getElementById('upload-btn-label');

        function showFile(file) {
            detailName.textContent = file.name;
            detailMeta.textContent = (file.size/1024).toFixed(1) + ' KB';
            fileDetail.classList.remove('hidden');
            uploadBtn.disabled = false;
            btnLabel.textContent = 'Upload ke Supabase';
        }

        input.addEventListener('change', () => { 
            if (input.files.length > 0) showFile(input.files[0]); 
        });

        clearBtn.addEventListener('click', (e) => { 
            e.stopPropagation(); 
            input.value = ''; 
            fileDetail.classList.add('hidden');
            uploadBtn.disabled = true;
            btnLabel.textContent = 'Pilih file terlebih dahulu';
        });

        // Drag & Drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-blue-50/20');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50/20');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50/20');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                showFile(files[0]);
            }
        });
    })();
    </script>
</x-sidebar>
</x-app-layout>