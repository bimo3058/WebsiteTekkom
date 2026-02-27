@extends('layouts.app')

@section('title', 'User Management - Superadmin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">User Management</h1>
                <p class="text-slate-400">Manage all users and their roles</p>
            </div>
            <a href="{{ route('superadmin.dashboard') }}" class="text-blue-400 hover:text-blue-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
        </div>

        <!-- Alerts -->
        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
            <h3 class="text-red-400 font-semibold mb-2">Errors</h3>
            <ul class="text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-500/10 border border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-300">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Search & Filter -->
        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 mb-8">
            <form method="GET" action="{{ route('superadmin.users.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-slate-300 text-sm font-medium mb-2">Search (Name or Email)</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Type name or email..." 
                               class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="block text-slate-300 text-sm font-medium mb-2">Filter by Role</label>
                        <select name="role" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="all">All Roles</option>
                            <option value="SUPERADMIN" {{ request('role') === 'SUPERADMIN' ? 'selected' : '' }}>Superadmin</option>
                            <option value="DOSEN" {{ request('role') === 'DOSEN' ? 'selected' : '' }}>Lecturer (DOSEN)</option>
                            <option value="MAHASISWA" {{ request('role') === 'MAHASISWA' ? 'selected' : '' }}>Student (MAHASISWA)</option>
                            <option value="ADMIN_BANKSOAL" {{ request('role') === 'ADMIN_BANKSOAL' ? 'selected' : '' }}>Admin Bank Soal</option>
                            <option value="ADMIN_CAPSTONE" {{ request('role') === 'ADMIN_CAPSTONE' ? 'selected' : '' }}>Admin Capstone</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                            Search
                        </button>
                        <a href="{{ route('superadmin.users.index') }}" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition text-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users Table -->
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
                                <div>
                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                    <p class="text-slate-400 text-sm">ID: {{ $user->id }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-300">{{ $user->email }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 flex-wrap">
                                    @forelse($user->roles as $role)
                                    <span class="inline-block bg-blue-500/20 text-blue-300 text-xs font-semibold px-2.5 py-1 rounded-full">
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
                                <span class="text-slate-400 text-sm">
                                    {{ $user->created_at->format('M d, Y H:i') }}
                                </span>
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
                            <td class="px-6 py-4 text-center">
                                <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}')" 
                                        class="text-blue-400 hover:text-blue-300 font-medium text-sm">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No users found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 border border-slate-700 rounded-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Edit User Roles</h2>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <div>
                <p class="text-slate-300 text-sm font-medium mb-3">Select roles for <span id="modalUserName" class="text-blue-400">User</span></p>
                
                <div class="space-y-3">
                    @foreach($roles as $role)
                    <label class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg hover:bg-slate-600 transition cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                               class="w-4 h-4 text-blue-600 bg-slate-600 border-slate-500 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <p class="text-white font-medium">{{ $role->name }}</p>
                            <p class="text-slate-400 text-xs">{{ $role->module }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" 
                        class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(userId, userName) {
    const modal = document.getElementById('editModal');
    const nameSpan = document.getElementById('modalUserName');
    const form = document.getElementById('editForm');
    
    nameSpan.textContent = userName;
    form.action = `/superadmin/users/${userId}/update-role`;
    
    // Clear all checkboxes first
    document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Set checked based on current roles
    const userRoles = {!! json_encode($users->mapWithKeys(function($u) { 
        return [$u->id => $u->roles->pluck('id')->toArray()]; 
    })) !!};
    
    if (userRoles[userId]) {
        userRoles[userId].forEach(roleId => {
            const checkbox = document.querySelector(`input[name="roles[]"][value="${roleId}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
    
    modal.classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection