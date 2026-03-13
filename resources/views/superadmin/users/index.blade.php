<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">User Management</h1>
                    <p class="text-slate-400">Total {{ $users->total() }} user terdaftar</p>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="openModal('modalAddUser')"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah User
                    </button>
                </div>
            </div>

            {{-- Alerts --}}
            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
                <h3 class="text-red-400 font-semibold mb-2">Errors</h3>
                <ul class="text-red-300 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="bg-green-500/10 border border-green-500 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-green-300">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            {{-- Search & Filter --}}
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-8">
                <form method="GET" action="{{ route('superadmin.users.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Cari (Nama atau Email)</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Ketik nama atau email..."
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Filter Role</label>
                            <select name="role"
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                                <option value="all">Semua Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-sm font-medium mb-2">Tampilkan</label>
                            <select name="per_page"
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                                @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                    {{ $size }} data
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2 md:col-span-2">
                            <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                Search
                            </button>
                            <a href="{{ route('superadmin.users.index') }}"
                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition text-center">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-700 bg-slate-700/50">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">User</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Roles</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Joined</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Last Login</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="border-b border-slate-700 hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center flex-shrink-0">
                                            <span class="text-blue-300 font-semibold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $user->name }}</p>
                                            <p class="text-slate-400 text-xs">ID: {{ $user->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-300 text-sm">{{ $user->email }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-1.5 flex-wrap">
                                        @forelse($user->roles as $role)
                                        @php
                                            $c = match(strtolower($role->name)) {
                                                'superadmin' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
                                                'dosen' => 'bg-green-500/20 text-green-300 border border-green-500/30',
                                                'mahasiswa' => 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
                                                'admin_banksoal' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                                                'admin_capstone' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
                                                default => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $c }}">
                                            {{ $role->name }}
                                        </span>
                                        @empty
                                        <span class="inline-block bg-slate-700 text-slate-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            No role
                                        </span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-400 text-sm">{{ $user->created_at->format('M d, Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-400 text-sm">
                                        @if($user->last_login)
                                            {{ $user->last_login->diffForHumans() }}
                                        @else
                                            <em>Never</em>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button onclick="openEditRoles({{ json_encode([
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'role_ids' => $user->roles->pluck('id')->values(),
                                            'has_lecturer' => (bool) $user->lecturer,
                                            'has_student' => (bool) $user->student,
                                            'employee_number' => optional($user->lecturer)->employee_number,
                                            'student_number' => optional($user->student)->student_number,
                                            'cohort_year' => optional($user->student)->cohort_year,
                                        ]) }})"
                                            class="text-blue-400 hover:text-blue-300 font-medium text-sm transition">
                                            Edit Role
                                        </button>

                                        @if($user->id !== auth()->id())
                                        <span class="text-slate-600">|</span>
                                        <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-medium text-sm transition">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12 14a8 8 0 00-8 8v2h16v-2a8 8 0 00-8-8z"/>
                                    </svg>
                                    <p class="font-medium">Tidak ada user ditemukan</p>
                                    <p class="text-sm mt-1 text-slate-500">Coba ubah filter pencarian</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8" id="paginationWrapper">{{ $users->links() }}</div>

            {{-- MODAL: Tambah User --}}
            <div id="modalAddUser" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" role="dialog" aria-modal="true">
                <div class="bg-slate-800 border border-slate-700 rounded-xl w-full max-w-lg shadow-2xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                        <h2 class="text-xl font-bold text-white">Tambah User Baru</h2>
                        <button onclick="closeModal('modalAddUser')" class="text-slate-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('superadmin.users.store') }}">
                        @csrf
                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="Nama lengkap">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-1.5">Email <span class="text-red-400">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="email@example.com">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-1.5">External ID <span class="text-red-400">*</span></label>
                                <input type="text" name="external_id" value="{{ old('external_id') }}" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="ID dari SSO">
                                <p class="text-slate-500 text-xs mt-1">ID unik dari sistem SSO.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-300 text-sm font-medium mb-1.5">Password <span class="text-red-400">*</span></label>
                                    <input type="password" name="password" required minlength="8" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="Min. 8 karakter">
                                </div>
                                <div>
                                    <label class="block text-slate-300 text-sm font-medium mb-1.5">Konfirmasi <span class="text-red-400">*</span></label>
                                    <input type="password" name="password_confirmation" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="Ulangi password">
                                </div>
                            </div>
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-2">Assign Role</label>
                                <div class="space-y-2">
                                    @foreach($roles as $role)
                                    @php $dot = match(strtolower($role->name)) { 'superadmin' => 'bg-purple-400', 'dosen' => 'bg-green-400', 'mahasiswa' => 'bg-orange-400', 'admin_banksoal' => 'bg-yellow-400', 'admin_capstone' => 'bg-cyan-400', default => 'bg-blue-400' }; @endphp
                                    <label class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg hover:bg-slate-600 transition cursor-pointer">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="w-4 h-4 text-blue-600 bg-slate-600 border-slate-500 rounded focus:ring-2 focus:ring-blue-500 add-role-cb" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                        <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                        <div>
                                            <p class="text-white font-medium text-sm">{{ $role->name }}</p>
                                            @if($role->module !== 'global')<p class="text-slate-400 text-xs">{{ $role->module }}</p>@endif
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div id="addFieldDosen" class="hidden bg-green-500/10 border border-green-500/30 rounded-lg p-4 space-y-3">
                                <p class="text-green-400 text-xs font-semibold uppercase tracking-wide">Data Dosen</p>
                                <div>
                                    <label class="block text-slate-300 text-xs font-medium mb-1">Nomor Pegawai</label>
                                    <input type="text" name="employee_number" value="{{ old('employee_number') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-green-500 text-sm" placeholder="Contoh: EMP-001">
                                </div>
                            </div>
                            <div id="addFieldMahasiswa" class="hidden bg-orange-500/10 border border-orange-500/30 rounded-lg p-4 space-y-3">
                                <p class="text-orange-400 text-xs font-semibold uppercase tracking-wide">Data Mahasiswa</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-300 text-xs font-medium mb-1">NIM</label>
                                        <input type="text" name="student_number" value="{{ old('student_number') }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 text-sm" placeholder="Contoh: 2021001">
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 text-xs font-medium mb-1">Angkatan</label>
                                        <input type="number" name="cohort_year" value="{{ old('cohort_year', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 px-6 py-4 border-t border-slate-700">
                            <button type="button" onclick="closeModal('modalAddUser')" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition">Batal</button>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">Simpan User</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Edit Role --}}
            <div id="modalEditRoles" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" role="dialog" aria-modal="true">
                <div class="bg-slate-800 border border-slate-700 rounded-xl w-full max-w-md shadow-2xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                        <div>
                            <h2 class="text-xl font-bold text-white">Edit User Roles</h2>
                            <p class="text-slate-400 text-sm mt-0.5">Roles untuk <span id="editRolesUserName" class="text-blue-400 font-medium"></span></p>
                        </div>
                        <button onclick="closeModal('modalEditRoles')" class="text-slate-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <form id="formEditRoles" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div>
                                <label class="block text-slate-300 text-sm font-medium mb-2">Assign Role</label>
                                <div class="space-y-2">
                                    @foreach($roles as $role)
                                    @php $dot = match(strtolower($role->name)) { 'superadmin' => 'bg-purple-400', 'dosen' => 'bg-green-400', 'mahasiswa' => 'bg-orange-400', 'admin_banksoal' => 'bg-yellow-400', 'admin_capstone' => 'bg-cyan-400', default => 'bg-blue-400' }; @endphp
                                    <label class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg hover:bg-slate-600 transition cursor-pointer">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="w-4 h-4 text-blue-600 bg-slate-600 border-slate-500 rounded focus:ring-2 focus:ring-blue-500 edit-role-cb" onchange="onEditRoleChange(this)">
                                        <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                        <div>
                                            <p class="text-white font-medium text-sm">{{ $role->name }}</p>
                                            @if($role->module !== 'global')<p class="text-slate-400 text-xs">{{ $role->module }}</p>@endif
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div id="editFieldDosen" class="hidden bg-green-500/10 border border-green-500/30 rounded-lg p-4 space-y-3">
                                <p class="text-green-400 text-xs font-semibold uppercase tracking-wide">Data Dosen</p>
                                <div>
                                    <label class="block text-slate-300 text-xs font-medium mb-1">Nomor Pegawai</label>
                                    <input type="text" name="employee_number" id="editEmpNumber" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-green-500 text-sm" placeholder="Contoh: EMP-001">
                                </div>
                            </div>
                            <div id="editFieldMahasiswa" class="hidden bg-orange-500/10 border border-orange-500/30 rounded-lg p-4 space-y-3">
                                <p class="text-orange-400 text-xs font-semibold uppercase tracking-wide">Data Mahasiswa</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-300 text-xs font-medium mb-1">NIM</label>
                                        <input type="text" name="student_number" id="editStudentNumber" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 text-sm" placeholder="Contoh: 2021001">
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 text-xs font-medium mb-1">Angkatan</label>
                                        <input type="number" name="cohort_year" id="editCohortYear" min="2000" max="{{ date('Y') + 1 }}" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 px-6 py-4 border-t border-slate-700">
                            <button type="button" onclick="closeModal('modalEditRoles')" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition">Cancel</button>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    const DOSEN_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'dosen')?->id ?? 'null' }};
    const MAHASISWA_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'mahasiswa')?->id ?? 'null' }};

    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        document.body.style.overflow = ''; 
    }
    
    document.addEventListener('keydown', e => { 
        if (e.key === 'Escape') {
            ['modalAddUser','modalEditRoles'].forEach(closeModal); 
        }
    });

    // Modal click outside
    ['modalAddUser','modalEditRoles'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) { 
            if (e.target === this) closeModal(id); 
        });
    });

    // Add user role checkboxes
    document.querySelectorAll('.add-role-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            const id = parseInt(this.value);
            if (id === DOSEN_ID) {
                document.getElementById('addFieldDosen').classList.toggle('hidden', !this.checked);
            }
            if (id === MAHASISWA_ID) {
                document.getElementById('addFieldMahasiswa').classList.toggle('hidden', !this.checked);
            }
        });
        cb.dispatchEvent(new Event('change'));
    });

    let _ctx = {};
    function openEditRoles(data) {
        _ctx = data;
        document.getElementById('formEditRoles').action = '/superadmin/users/' + data.id + '/roles';
        document.getElementById('editRolesUserName').textContent = data.name;
        
        document.querySelectorAll('.edit-role-cb').forEach(cb => {
            cb.checked = data.role_ids.includes(parseInt(cb.value));
            onEditRoleChange(cb);
        });
        
        openModal('modalEditRoles');
    }

    function onEditRoleChange(cb) {
        const id = parseInt(cb.value);
        
        if (id === DOSEN_ID) {
            document.getElementById('editFieldDosen').classList.toggle('hidden', !cb.checked);
            const input = document.getElementById('editEmpNumber');
            if (cb.checked && _ctx.has_lecturer) {
                input.value = _ctx.employee_number || '';
                input.disabled = true;
            } else {
                input.value = '';
                input.disabled = false;
            }
        }
        
        if (id === MAHASISWA_ID) {
            document.getElementById('editFieldMahasiswa').classList.toggle('hidden', !cb.checked);
            const nimIn = document.getElementById('editStudentNumber');
            const thnIn = document.getElementById('editCohortYear');
            
            if (cb.checked && _ctx.has_student) {
                nimIn.value = _ctx.student_number || '';
                thnIn.value = _ctx.cohort_year || '';
                nimIn.disabled = true;
                thnIn.disabled = true;
            } else {
                nimIn.value = '';
                thnIn.value = {{ date('Y') }};
                nimIn.disabled = false;
                thnIn.disabled = false;
            }
        }
    }

    // Auto-open modal if there are errors
    @if($errors->any())
        openModal('modalAddUser');
    @endif

    // Pagination handling
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationWrapper a');
        if (!link) return;

        e.preventDefault();

        const url = new URL(link.href);
        const page = url.searchParams.get('page') ?? 1;
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

    // Auto-submit on filter change
    ['select[name="per_page"]', 'select[name="role"]'].forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('change', function () {
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
        }
    });
    </script>
</x-sidebar>
</x-app-layout>