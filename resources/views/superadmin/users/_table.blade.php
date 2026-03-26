<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Roles</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Last Login</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-slate-800 font-medium text-sm">{{ $user->name }}</p>
                                <p class="text-slate-400 text-xs">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-600 text-sm">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-1.5 flex-wrap">
                            @forelse($user->roles as $role)
                            @php
                                $roleColors = match(strtolower($role->name)) {
                                    'superadmin' => 'bg-purple-50 text-purple-600 border-purple-100',
                                    'dosen' => 'bg-green-50 text-green-600 border-green-100',
                                    'mahasiswa' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'admin_banksoal' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'admin_capstone' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                    default => 'bg-blue-50 text-blue-600 border-blue-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold border {{ $roleColors }}">
                                {{ $role->name }}
                            </span>
                            @empty
                            <span class="inline-block bg-slate-50 text-slate-400 text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-200">
                                No role
                            </span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-500 text-sm">{{ $user->created_at->format('M d, Y H:i') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-500 text-sm">
                            @if($user->last_login)
                                {{ $user->last_login->diffForHumans() }}
                            @else
                                <em class="text-slate-400">Never</em>
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
                                class="text-blue-600 hover:text-blue-700 font-medium text-sm transition">
                                Edit Role
                            </button>

                            @if($user->id !== auth()->id())
                            <span class="text-slate-300">|</span>
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}"
                                onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600 font-medium text-sm transition">
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
                        <span class="material-symbols-outlined text-5xl mb-3 text-slate-300">person_off</span>
                        <p class="font-medium text-slate-500">Tidak ada user ditemukan</p>
                        <p class="text-sm mt-1 text-slate-400">Coba ubah filter pencarian</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>