<x-app-layout>
<x-sidebar :user="auth()->user()">
@php
    $sortBy  = request('sort_by', 'name');
    $sortDir = request('sort_dir', 'asc');
    $search  = request('search', '');
    $perPage = (int) request('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

    $rolesQuery = \App\Models\Role::withCount('users')->with('permissions');
    if ($search) $rolesQuery->where('name', 'like', "%{$search}%");

    $allowedSort = ['name', 'users_count'];
    $sortBy  = in_array($sortBy, $allowedSort) ? $sortBy : 'name';
    $sortDir = in_array($sortDir, ['asc','desc']) ? $sortDir : 'asc';

    $roles = $rolesQuery->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

    $dbModules = \App\Models\SystemModule::all();

    // Overview metrics
    $totalRoles = \App\Models\Role::count();
    $activeRoles = \App\Models\Role::has('users')->count();
    $totalPermissions = \App\Models\Permission::count();
    $usersWithRoles = \App\Models\User::has('roles')->count();
@endphp

@include('superadmin.permission._overview-style')
<div class="rp-wrap"><section class="rp-box">
    <header class="rp-header">
        <div><h1>Role &amp; Permission</h1><p>Kelola role dan izin akses modul untuk setiap pengguna.</p></div>
        <a class="rp-button" href="{{ route('superadmin.users.index') }}">User Management <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>
    </header>
    <div class="rp-body">
        <section class="rp-stats" aria-label="Ringkasan akses">
            @foreach([
                ['Total role', $totalRoles, 'Role yang tersedia dalam sistem'],
                ['Role digunakan', $activeRoles, 'Role dengan pengguna terdaftar'],
                ['Total permission', $totalPermissions, 'Izin akses yang tersedia'],
                ['User memiliki role', $usersWithRoles, 'Pengguna dengan role terpasang'],
            ] as [$label, $value, $description])
                <div class="rp-stat"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong><p>{{ $description }}</p></div>
            @endforeach
        </section>
        <section class="rp-table-card" aria-label="Daftar role">
            <div class="rp-toolbar">
                <h2>Daftar role <span class="rp-count">{{ $roles->total() }}</span></h2>
                <form method="GET" action="{{ route('superadmin.permissions') }}" class="rp-filters">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <label class="rp-search"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7"/><path d="m16 16 5 5"/></svg><input type="search" name="search" value="{{ $search }}" placeholder="Cari role..." aria-label="Cari role"></label>
                    <div x-data="{ open: false }" class="rp-sort" @click.outside="open = false" @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()">
                        <input type="hidden" name="sort_by" value="{{ $sortBy }}"><input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                        <button class="rp-button" type="button" x-ref="trigger" @click="open = !open" :aria-expanded="open" aria-controls="rp-sort-options">{{ $sortBy === 'users_count' ? ($sortDir === 'desc' ? 'User terbanyak' : 'User tersedikit') : ($sortDir === 'asc' ? 'Nama A-Z' : 'Nama Z-A') }} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></button>
                        <div class="rp-sort-menu" id="rp-sort-options" x-show="open" x-cloak>
                            @foreach([['name','asc','Nama A-Z'],['name','desc','Nama Z-A'],['users_count','desc','User terbanyak'],['users_count','asc','User tersedikit']] as [$column,$direction,$label])
                                <a href="{{ route('superadmin.permissions', array_merge(request()->except(['sort_by','sort_dir','page']), ['sort_by'=>$column,'sort_dir'=>$direction])) }}" @if($sortBy === $column && $sortDir === $direction) aria-current="true" @endif>{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                    <button class="rp-button rp-primary" type="submit">Cari</button>
                    @if($search)<a class="rp-button" href="{{ route('superadmin.permissions') }}">Reset</a>@endif
                </form>
            </div>
            <div class="rp-table-scroll" tabindex="0" role="region" aria-label="Daftar role, geser untuk melihat semua kolom">
                <table class="rp-table"><thead><tr><th scope="col">Role</th><th scope="col">Akses modul</th><th scope="col">Pengguna</th><th scope="col">Aksi</th></tr></thead><tbody>
                    @forelse($roles as $role)
                        @php $permsByModule = $role->permissions->groupBy(fn ($permission) => explode('.', $permission->name)[0]); @endphp
                        <tr>
                            <td><a class="rp-role-name" href="{{ route('superadmin.permissions.show', ['role'=>$role->name]) }}">{{ ucwords(str_replace('_',' ', $role->name)) }}</a><div class="rp-role-meta"><x-ui.role-badge :role="$role->name" size="xs"/><span class="rp-muted">{{ $role->users_count > 0 ? 'Digunakan' : 'Belum digunakan' }}</span></div></td>
                            <td><div class="rp-access-count">{{ $permsByModule->count() }} modul <span aria-hidden="true">&middot;</span> {{ $role->permissions->count() }} permission</div><div class="rp-tags">@foreach($permsByModule->take(3) as $moduleName => $permissions)<span>{{ ucfirst($moduleName) }} <b>{{ $permissions->count() }}</b></span>@endforeach @if($permsByModule->count() > 3)<span>+{{ $permsByModule->count() - 3 }} lainnya</span>@endif @if($role->permissions->isEmpty())<p class="rp-muted">Belum ada permission terpasang</p>@endif</div></td>
                            <td><a class="rp-user-count" href="{{ route('superadmin.users.index', ['role'=>$role->name]) }}">{{ number_format($role->users_count) }} <span>user</span></a></td>
                            <td>
                                <div x-data="roleOverviewMenu()" @resize.window="close()" @scroll.window.capture="if (!$refs.panel?.contains($event.target)) close()">
                                    <button type="button" class="rp-button rp-more" x-ref="trigger" @click="toggle()" :aria-expanded="open" aria-label="Aksi role {{ $role->name }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg></button>
                                    <template x-teleport="body"><div class="rp-action-menu" popover="manual" x-ref="panel" :style="position" @click.outside="close()" @keydown.escape.stop.prevent="close(true)"><a href="{{ route('superadmin.permissions.show', ['role'=>$role->name]) }}">Edit permissions</a><a href="{{ route('superadmin.users.index', ['role'=>$role->name]) }}">Lihat pengguna</a></div></template>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="rp-empty"><h3>{{ $search ? 'Tidak ada role yang cocok' : 'Belum ada role' }}</h3><p>{{ $search ? 'Coba kata kunci lain atau reset pencarian.' : 'Role yang tersedia akan ditampilkan di sini.' }}</p>@if($search)<a class="rp-button" href="{{ route('superadmin.permissions') }}">Reset pencarian</a>@endif</div></td></tr>
                    @endforelse
                </tbody></table>
            </div>
            <footer class="rp-pagination">@include('superadmin.users._pagination', ['users'=>$roles, 'paginationRoute'=>'superadmin.permissions'])</footer>
        </section>
    </div>
</section></div>
<script>
function roleOverviewMenu() {
    return {
        open: false, position: {},
        close(focus = false) { this.open = false; this.$refs.panel.hidePopover(); if (focus) this.$refs.trigger.focus(); },
        toggle() {
            if (this.open) { this.close(); return; }
            const rect = this.$refs.trigger.getBoundingClientRect();
            const upwards = window.innerHeight - rect.bottom < 110;
            this.position = { left: Math.max(8, Math.min(rect.right - 190, innerWidth - 198)) + 'px', top: upwards ? 'auto' : (rect.bottom + 6) + 'px', bottom: upwards ? (innerHeight - rect.top + 6) + 'px' : 'auto' };
            this.open = true;
            this.$nextTick(() => this.$refs.panel.showPopover());
        }
    };
}
</script>
</x-sidebar>
</x-app-layout>
