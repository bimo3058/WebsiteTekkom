<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Kelola Role & Permission</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="roleManagerApp()" x-cloak>
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-transition.opacity @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <div class="h-20 flex items-center px-8 border-b border-slate-100 shrink-0">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold mr-4 shadow-md shadow-indigo-200 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">Admin Center</h1>
                <p class="text-xs text-slate-500 font-medium">E-Office Admin</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4">
            <div class="mb-2 px-4">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</p>
            </div>

            <a href="{{ route('eoffice.admin.template_proposal') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Template Proposal
            </a>

            <!-- Active Menu: Kelola Role -->
            <a href="{{ route('eoffice.admin.kelola_role') }}"
                class="flex items-center px-4 py-3 mb-1 text-sm font-semibold text-indigo-700 bg-white shadow-sm border border-indigo-100 rounded-xl relative">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                </svg>
                Kelola Role
            </a>

            <a href="{{ route('eoffice.admin.validasi_timeline') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validasi Timeline
            </a>
        </div>

        <div class="p-4 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin SIKAPE' }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@sikape.undip.ac.id' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">

        <!-- Topbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0 shrink-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Sistem</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Kelola Role & Permission</span>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Undang Pengguna
                </button>
            </div>
        </header>

        <!-- Toast Notification -->
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed top-24 right-6 lg:right-10 z-50 bg-white border border-emerald-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5 bg-emerald-50 border border-emerald-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-900 mb-0.5" x-text="toast.title"></p>
                <p class="text-[13px] text-slate-500 leading-relaxed" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Dashboard Body -->
        <div class="flex-1 overflow-y-auto p-6 lg:p-10">
            <div class="max-w-6xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Akses</h2>
                    <p class="text-sm text-slate-500 mt-1">Atur role (peran) dan hak akses (permission) pengguna E-Office.</p>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full sm:max-w-xs">
                            <input type="text" x-model="searchQuery" placeholder="Cari nama atau email..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-sm">
                            <svg class="absolute left-3.5 top-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <select x-model="filterRole" class="flex-1 sm:flex-none bg-white border border-slate-200 rounded-lg text-sm font-medium px-4 py-2.5 focus:outline-none focus:border-indigo-500 text-slate-600 shadow-sm">
                                <option value="all">Semua Role</option>
                                <option value="Mahasiswa">Mahasiswa</option>
                                <option value="Dosen">Dosen</option>
                                <option value="Koordinator">Koordinator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">Pengguna</th>
                                    <th class="px-6 py-4">Role Aktif</th>
                                    <th class="px-6 py-4">Permissions Khusus</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <template x-for="user in filteredUsers" :key="user.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                                     :class="getAvatarStyle(user.name)">
                                                    <span x-text="user.name.charAt(0)"></span>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors" x-text="user.name"></p>
                                                    <p class="text-xs text-slate-500 font-medium" x-text="user.email"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="role in user.roles" :key="role">
                                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-md border"
                                                          :class="getRoleColor(role)" x-text="role"></span>
                                                </template>
                                                <span x-show="user.roles.length === 0" class="text-slate-400 italic text-xs">Belum ada role</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="perm in user.permissions" :key="perm">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span x-text="perm"></span>
                                                    </span>
                                                </template>
                                                <span x-show="user.permissions.length === 0" class="text-slate-400 italic text-xs">-</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="openAssignModal(user)" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 text-slate-600 hover:text-indigo-700 text-xs font-bold rounded-lg transition-all shadow-sm">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                Kelola
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredUsers.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                        <p class="font-bold text-slate-700">Tidak ada pengguna ditemukan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Role & Permission -->
    <div x-show="assignModalOpen" class="relative z-50" style="display: none;">
        <div x-show="assignModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
            <div x-show="assignModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 @click.away="assignModalOpen = false"
                 class="relative w-full max-w-2xl rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
                
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Kelola Role & Akses</h3>
                        <p class="text-sm text-slate-500 mt-1">Konfigurasi hak akses untuk pengguna terpilih.</p>
                    </div>
                    <button @click="assignModalOpen = false" class="text-slate-400 hover:text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 p-2 rounded-xl transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-8 overflow-y-auto custom-scrollbar">
                    <template x-if="editingUser">
                        <div>
                            <!-- User Info Header -->
                            <div class="flex items-center gap-4 mb-8 p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shrink-0 text-white shadow-sm"
                                     :class="getAvatarStyle(editingUser.name)">
                                    <span x-text="editingUser.name.charAt(0)"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-base" x-text="editingUser.name"></p>
                                    <p class="text-sm text-indigo-600 font-medium" x-text="editingUser.email"></p>
                                </div>
                            </div>
                            
                            <!-- Roles Section -->
                            <div class="mb-8">
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Roles (Peran Utama)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <template x-for="roleDef in availableRoles" :key="roleDef.name">
                                        <label class="relative flex items-start p-4 cursor-pointer rounded-xl border transition-all duration-200"
                                               :class="tempRoles.includes(roleDef.name) ? 'bg-indigo-50/50 border-indigo-600 shadow-sm ring-1 ring-indigo-600' : 'bg-white border-slate-200 hover:border-indigo-300'">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" :value="roleDef.name" x-model="tempRoles" 
                                                       class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2">
                                            </div>
                                            <div class="ml-3 flex flex-col">
                                                <span class="text-sm font-bold text-slate-900" x-text="roleDef.name"></span>
                                                <span class="text-xs text-slate-500 mt-0.5" x-text="roleDef.desc"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Permissions Section -->
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Permissions Tambahan</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <template x-for="permDef in availablePermissions" :key="permDef">
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="checkbox" :value="permDef" x-model="tempPermissions" 
                                                   class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2">
                                            <span class="ml-3 text-sm font-medium text-slate-700 group-hover:text-indigo-600 transition-colors" x-text="permDef"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="px-8 py-5 bg-white border-t border-slate-100 flex gap-3 justify-end rounded-b-3xl shrink-0">
                    <button @click="assignModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                    <button @click="saveRoleAssignment()" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function roleManagerApp() {
        return {
            sidebarOpen: false,
            searchQuery: '',
            filterRole: 'all',
            
            // Dummy Data Users
            users: [],
            
            // Define available roles & permissions
            availableRoles: [
                { name: 'Koordinator', desc: 'Akses penuh modul KP.' },
                { name: 'Dosen', desc: 'Akses bimbingan & nilai.' },
                { name: 'Mahasiswa', desc: 'Akses menu mahasiswa KP.' },
                { name: 'Admin', desc: 'Akses sistem E-Office.' }
            ],
            availablePermissions: [
                'approve_dokumen', 'edit_nilai', 'assign_dosen', 
                'publish_pengumuman', 'view_all_data', 'export_data'
            ],
            
            // Modal States
            assignModalOpen: false,
            editingUser: null,
            tempRoles: [],
            tempPermissions: [],
            
            // Toast state
            toast: { show: false, title: '', message: '' },
            
            init() {
                // Initialize default users if not in localStorage
                const stored = localStorage.getItem('eoffice_admin_roles_data');
                if(stored) {
                    this.users = JSON.parse(stored);
                } else {
                    this.users = [
                        { id: 1, name: 'Dr. Budi Santoso, M.Kom', email: 'budi.santoso@undip.ac.id', roles: ['Dosen'], permissions: ['approve_dokumen', 'edit_nilai'] },
                        { id: 2, name: 'Ike Pertiwi, M.T', email: 'ike.pertiwi@undip.ac.id', roles: ['Koordinator', 'Dosen'], permissions: ['assign_dosen', 'publish_pengumuman', 'view_all_data'] },
                        { id: 3, name: 'Ahmad Fathanah', email: 'ahmad@students.undip.ac.id', roles: ['Mahasiswa'], permissions: [] },
                        { id: 4, name: 'Bima Sakti', email: 'bima@students.undip.ac.id', roles: ['Mahasiswa'], permissions: [] },
                        { id: 5, name: 'Admin SIKAPE', email: 'admin@undip.ac.id', roles: ['Admin'], permissions: ['view_all_data', 'export_data'] }
                    ];
                    this.saveData();
                }
            },
            
            get filteredUsers() {
                return this.users.filter(u => {
                    const matchSearch = u.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                        u.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchRole = this.filterRole === 'all' || u.roles.includes(this.filterRole);
                    return matchSearch && matchRole;
                });
            },
            
            saveData() {
                localStorage.setItem('eoffice_admin_roles_data', JSON.stringify(this.users));
            },
            
            openAssignModal(user) {
                this.editingUser = user;
                // Clone arrays so we don't mutate instantly
                this.tempRoles = [...user.roles];
                this.tempPermissions = [...user.permissions];
                this.assignModalOpen = true;
            },
            
            saveRoleAssignment() {
                const userIndex = this.users.findIndex(u => u.id === this.editingUser.id);
                if (userIndex !== -1) {
                    this.users[userIndex].roles = [...this.tempRoles];
                    this.users[userIndex].permissions = [...this.tempPermissions];
                    this.saveData();
                    
                    this.toast.title = 'Akses Diperbarui';
                    this.toast.message = `Role & Permission untuk ${this.editingUser.name} berhasil disimpan.`;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 4000);
                }
                this.assignModalOpen = false;
            },
            
            getAvatarStyle(name) {
                const char = name.charAt(0).toUpperCase();
                if(['A','B','C','D','E'].includes(char)) return 'bg-blue-500 text-white';
                if(['F','G','H','I','J'].includes(char)) return 'bg-emerald-500 text-white';
                if(['K','L','M','N','O'].includes(char)) return 'bg-amber-500 text-white';
                if(['P','Q','R','S','T'].includes(char)) return 'bg-indigo-500 text-white';
                return 'bg-purple-500 text-white';
            },
            
            getRoleColor(role) {
                switch(role) {
                    case 'Koordinator': return 'bg-purple-50 text-purple-600 border-purple-200';
                    case 'Admin': return 'bg-red-50 text-red-600 border-red-200';
                    case 'Dosen': return 'bg-indigo-50 text-indigo-600 border-indigo-200';
                    case 'Mahasiswa': return 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    default: return 'bg-slate-100 text-slate-500 border-slate-200';
                }
            }
        }
    }
</script>
</body>
</html>
