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
            {{-- Storage Test ──────────────────────────────────────────────────────────── --}}
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-white mb-4">Supabase Storage — Test Upload</h2>
            
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
                    {{-- Form Upload --}}
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-1">Upload File</h3>
                        <p class="text-slate-400 text-sm mb-4">Format: JPG, PNG, WEBP, PDF, DOC, DOCX — maks 10 MB</p>
            
                        @if(session('upload_error'))
                            <div class="mb-4 bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400 text-sm">
                                {{ session('upload_error') }}
                            </div>
                        @endif
            
                        @if(session('delete_success'))
                            <div class="mb-4 bg-green-500/10 border border-green-500/30 rounded-lg px-4 py-3 text-green-400 text-sm">
                                {{ session('delete_success') }}
                            </div>
                        @endif
            
                        @if($errors->has('file'))
                            <div class="mb-4 bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 text-red-400 text-sm">
                                {{ $errors->first('file') }}
                            </div>
                        @endif
            
                        {{-- PENTING: enctype wajib ada untuk upload file --}}
                        <form action="{{ route('superadmin.storage.upload') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            id="upload-form">
                            @csrf
            
                            {{-- Drop zone --}}
                            <div class="border-2 border-dashed border-slate-600 rounded-lg p-6 text-center mb-4 hover:border-slate-500 transition cursor-pointer"
                                id="drop-zone"
                                onclick="document.getElementById('file-input').click()">
            
                                {{-- Icon --}}
                                <svg class="w-10 h-10 text-slate-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
            
                                {{-- Label default --}}
                                <p class="text-slate-400 text-sm" id="file-label">Klik untuk pilih file</p>
                                <p class="text-slate-600 text-xs mt-1">atau drag & drop di sini</p>
            
                                <input id="file-input"
                                    type="file"
                                    name="file"
                                    class="hidden"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                            </div>
            
                            {{-- Detail file yang dipilih (tersembunyi sampai ada file) --}}
                            <div id="file-detail" class="hidden mb-4 bg-slate-900 rounded-lg p-3 flex items-center gap-3">
                                <div class="bg-blue-500/20 p-2 rounded-lg flex-shrink-0">
                                    <svg id="file-icon" class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="detail-name" class="text-white text-sm font-medium truncate"></p>
                                    <p id="detail-meta" class="text-slate-400 text-xs mt-0.5"></p>
                                </div>
                                {{-- Tombol clear --}}
                                <button type="button"
                                        id="clear-file"
                                        class="text-slate-500 hover:text-red-400 transition flex-shrink-0"
                                        title="Hapus pilihan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
            
                            {{-- Pratinjau gambar sebelum upload --}}
                            <div id="image-preview-wrap" class="hidden mb-4">
                                <p class="text-slate-400 text-xs mb-1">Pratinjau:</p>
                                <img id="image-preview"
                                    src=""
                                    alt="preview"
                                    class="rounded-lg max-h-40 object-contain border border-slate-700 w-full">
                            </div>
            
                            {{-- Button upload — disabled sampai ada file --}}
                            <button type="submit"
                                    id="upload-btn"
                                    disabled
                                    class="w-full bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-medium py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span id="upload-btn-label">Pilih file terlebih dahulu</span>
                            </button>
                        </form>
                    </div>
            
                    {{-- Hasil Upload --}}
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Hasil Upload</h3>
            
                        @if(session('upload_success'))
                            <div class="space-y-3">
                                <div class="bg-green-500/10 border border-green-500/30 rounded-lg px-4 py-3 text-green-400 text-sm font-medium">
                                    ✓ Upload berhasil!
                                </div>
            
                                {{-- Info file --}}
                                <div class="bg-slate-900 rounded-lg p-4 space-y-2 text-sm">
                                    <div class="flex justify-between gap-2">
                                        <span class="text-slate-400 flex-shrink-0">Nama file</span>
                                        <span class="text-white font-medium truncate text-right">{{ session('upload_name') }}</span>
                                    </div>
                                    <div class="flex justify-between gap-2">
                                        <span class="text-slate-400 flex-shrink-0">Ukuran</span>
                                        <span class="text-white">{{ number_format(session('upload_size') / 1024, 1) }} KB</span>
                                    </div>
                                    <div class="pt-1 border-t border-slate-700">
                                        <p class="text-slate-400 mb-1">Path (disimpan ke DB):</p>
                                        <code class="block bg-slate-800 text-green-400 text-xs rounded px-2 py-1.5 break-all select-all">{{ session('upload_path') }}</code>
                                    </div>
                                    <div class="pt-1 border-t border-slate-700">
                                        <p class="text-slate-400 mb-1">Public URL:</p>
                                        <a href="{{ session('upload_url') }}" target="_blank"
                                        class="text-blue-400 hover:text-blue-300 text-xs break-all underline">
                                            {{ session('upload_url') }}
                                        </a>
                                    </div>
                                </div>
            
                                {{-- Pratinjau gambar hasil upload --}}
                                @php $ext = strtolower(pathinfo(session('upload_name'), PATHINFO_EXTENSION)); @endphp
                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                    <div>
                                        <p class="text-slate-400 text-xs mb-2">Pratinjau dari Supabase:</p>
                                        <img src="{{ session('upload_url') }}"
                                            alt="preview"
                                            class="rounded-lg max-h-48 object-contain border border-slate-700 w-full">
                                    </div>
                                @endif
            
                                {{-- Tombol hapus --}}
                                <form action="{{ route('superadmin.storage.delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ session('upload_path') }}">
                                    <button type="submit"
                                            class="w-full bg-red-600/20 hover:bg-red-600/40 border border-red-500/30 text-red-400 font-medium py-2 px-4 rounded-lg transition text-sm">
                                        Hapus file ini dari Storage
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-48 text-slate-600">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Belum ada file yang diupload</p>
                            </div>
                        @endif
                    </div>
            
                </div>
            </div>
        </div>
    </div>
<script>
(function () {
    const input      = document.getElementById('file-input');
    const dropZone   = document.getElementById('drop-zone');
    const fileDetail = document.getElementById('file-detail');
    const detailName = document.getElementById('detail-name');
    const detailMeta = document.getElementById('detail-meta');
    const clearBtn   = document.getElementById('clear-file');
    const uploadBtn  = document.getElementById('upload-btn');
    const btnLabel   = document.getElementById('upload-btn-label');
    const previewWrap= document.getElementById('image-preview-wrap');
    const previewImg = document.getElementById('image-preview');
 
    const imageTypes = ['image/jpeg', 'image/png', 'image/webp'];
 
    function formatSize(bytes) {
        if (bytes < 1024)       return bytes + ' B';
        if (bytes < 1024*1024)  return (bytes/1024).toFixed(1) + ' KB';
        return (bytes/1024/1024).toFixed(2) + ' MB';
    }
 
    function showFile(file) {
        detailName.textContent = file.name;
        detailMeta.textContent = formatSize(file.size) + ' · ' + (file.type || 'unknown type');
        fileDetail.classList.remove('hidden');
 
        // Pratinjau gambar
        if (imageTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewWrap.classList.add('hidden');
            previewImg.src = '';
        }
 
        // Aktifkan tombol upload
        uploadBtn.disabled = false;
        btnLabel.textContent = 'Upload ke Supabase';
    }
 
    function clearFile() {
        input.value = '';
        fileDetail.classList.add('hidden');
        previewWrap.classList.add('hidden');
        previewImg.src = '';
        uploadBtn.disabled = true;
        btnLabel.textContent = 'Pilih file terlebih dahulu';
    }
 
    // Event: pilih file via input
    input.addEventListener('change', () => {
        if (input.files.length > 0) showFile(input.files[0]);
    });
 
    // Event: clear file
    clearBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        clearFile();
    });
 
    // Event: drag & drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500');
        const file = e.dataTransfer.files[0];
        if (!file) return;
 
        // Inject ke input
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showFile(file);
    });
 
    // Loading state saat submit
    document.getElementById('upload-form').addEventListener('submit', () => {
        uploadBtn.disabled = true;
        btnLabel.textContent = 'Mengupload...';
    });
})();
</script>
</x-sidebar>
</x-app-layout>