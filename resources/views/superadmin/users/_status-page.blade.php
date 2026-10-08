@php
    $isOnlinePage = $statusPage === 'online';
    $statusRoute = 'superadmin.users.'.$statusPage;
    $hasFilters = request()->filled('search') || request('role', 'all') !== 'all';
    $roleOptions = $roles->map(fn ($role) => ['value' => $role->name, 'label' => ucfirst(str_replace('_', ' ', $role->name))])
        ->prepend(['value' => 'all', 'label' => 'Semua role'])->values()->all();
@endphp
<link rel="stylesheet" href="{{ asset('css/superadmin-user-status.css') }}?v=2">

<div class="su-status-wrap">
    <div class="su-status-box">
        <header class="su-status-header">
            <div class="su-heading-row">
                <div><h1>{{ $isOnlinePage ? 'User Online' : 'User Suspended' }}</h1><p>{{ $isOnlinePage ? 'Pantau pengguna aktif dan kelola sesi login.' : 'Tinjau akun yang ditangguhkan dan kelola pemulihan akses.' }}</p></div>
                <a class="su-btn" href="{{ route('superadmin.users.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 5-7 7 7 7M5 12h14" /></svg>User Management</a>
            </div>
            <nav class="su-status-tabs" aria-label="Status pengguna">
                <a href="{{ route('superadmin.users.index') }}">Semua user</a>
                <a href="{{ route('superadmin.users.online') }}" @if($isOnlinePage) aria-current="page" @endif>Online</a>
                <a href="{{ route('superadmin.users.suspended') }}" @if(!$isOnlinePage) aria-current="page" @endif>Suspended</a>
            </nav>
        </header>
        <div class="su-status-body">
            @if(session('success'))<div class="su-alert" role="status">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="su-alert su-alert-error" role="alert">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="su-alert su-alert-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <section class="su-table-card" aria-label="Daftar user {{ $statusPage }}">
                <div class="su-toolbar">
                    <h2>{{ $isOnlinePage ? 'User aktif' : 'Akun ditangguhkan' }} <span class="su-count">{{ number_format($users->total()) }}</span></h2>
                    <form method="GET" action="{{ route($statusRoute) }}" class="su-filter-form" x-ref="filterForm"
                        x-data="{ open: false, selected: @js((string) request('role', 'all')), roles: @js($roleOptions), get selectedLabel() { return this.roles.find(role => role.value === this.selected)?.label || 'Semua role'; }, focusOption(step) { const options = [...this.$refs.options.querySelectorAll('button')]; if (options.length) options[(options.indexOf(document.activeElement) + step + options.length) % options.length].focus(); } }">
                        <input type="hidden" name="per_page" value="{{ $users->perPage() }}">
                        <div class="su-search"><label for="status-user-search" class="su-sr-only">Cari nama atau email</label><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7" /><path d="m16 16 5 5" /></svg><input id="status-user-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."></div>
                        <div class="su-role-filter" @click.outside="open = false" @keydown.escape.stop.prevent="open = false; $refs.roleTrigger.focus()">
                            <input type="hidden" name="role" :value="selected">
                            <button type="button" class="su-btn su-role-trigger" x-ref="roleTrigger" @click="open = !open" :aria-expanded="open" aria-haspopup="listbox" aria-controls="status-role-options" aria-label="Filter role"
                                @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.options.querySelector('button')?.focus())"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h18l-7 8v7l-4 2v-9z" /></svg><span x-text="selectedLabel"></span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg></button>
                            <div id="status-role-options" role="listbox" aria-label="Role pengguna" class="su-role-options" x-ref="options" x-show="open" x-cloak x-transition.opacity.duration.100ms @keydown.arrow-down.prevent="focusOption(1)" @keydown.arrow-up.prevent="focusOption(-1)">
                                <template x-for="role in roles" :key="role.value"><button type="button" role="option" :aria-selected="selected === role.value" @click="selected = role.value; open = false; $nextTick(() => $refs.filterForm.requestSubmit())"><span x-text="role.label"></span><svg x-show="selected === role.value" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg></button></template>
                            </div>
                        </div>
                        <button type="submit" class="su-btn su-btn-primary">Cari</button>
                        @if($hasFilters)<a class="su-btn" href="{{ route($statusRoute, ['per_page' => $users->perPage()]) }}">Reset</a>@endif
                    </form>
                </div>
                @if($users->count())
                    <div class="su-table-scroll" tabindex="0" role="region" aria-label="Tabel pengguna, geser untuk melihat semua kolom">
                        <table class="su-status-table">
                            <thead><tr><th scope="col">User</th><th scope="col">Role</th>@if(!$isOnlinePage)<th scope="col">Alasan suspend</th>@endif<th scope="col">{{ $isOnlinePage ? 'Status / Login terakhir' : 'Ditangguhkan pada' }}</th><th scope="col">Aksi</th></tr></thead>
                            <tbody>
                                @foreach($users as $user)
                                    @php
                                        $isMe = $user->id === auth()->id();
                                        $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                                    @endphp
                                    <tr>
                                        <td><div class="su-user"><x-ui.user-avatar :user="$user" size="sm" :online-dot="$isOnlinePage" :suspended="!$isOnlinePage" /><div><div class="su-user-name">{{ $user->name }} @if($isMe)<span class="su-you">Anda</span>@endif</div><span class="su-meta">{{ $user->email }}</span></div></div></td>
                                        <td><div class="su-roles">@forelse($user->roles as $role)<x-ui.role-badge :role="$role->name" size="xs" />@empty<span class="su-meta">Belum ada role</span>@endforelse</div></td>
                                        @if(!$isOnlinePage)<td><p class="su-reason">{{ $user->suspension_reason ?: 'Tidak ada alasan yang dicatat.' }}</p></td>@endif
                                        <td>
                                            @if($isOnlinePage)<span class="su-status su-status-online">Online</span><span class="su-meta">{{ $user->last_login?->diffForHumans() ?? 'Baru saja' }}</span>
                                            @else<span class="su-status su-status-suspended">Suspended</span><span class="su-meta" @if($user->suspended_at) title="{{ $user->suspended_at->format('d M Y, H:i') }}" @endif>{{ $user->suspended_at?->diffForHumans() ?? 'Waktu tidak tersedia' }}</span>@endif
                                        </td>
                                        <td><div class="su-actions">
                                            @if($isOnlinePage)
                                                <div x-data="{ open: false, position: '', toggle() { if (this.open) { this.open = false; return; } const rect = this.$refs.trigger.getBoundingClientRect(); this.position = 'left:' + Math.max(8, Math.min(rect.right - 180, window.innerWidth - 188)) + 'px;' + (window.innerHeight - rect.bottom < 120 ? 'bottom:' + (window.innerHeight - rect.top + 6) : 'top:' + (rect.bottom + 6)) + 'px'; this.open = true; } }" @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()" @resize.window="open = false" @scroll.window.capture="open = false">
                                                    <button type="button" class="su-btn su-action-trigger" x-ref="trigger" @click="toggle()" :aria-expanded="open" aria-haspopup="menu" aria-controls="user-actions-{{ $user->id }}" aria-label="Aksi untuk {{ $user->name }}">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                                    </button>
                                                    <template x-teleport="body">
                                                        <div id="user-actions-{{ $user->id }}" class="su-action-menu" role="menu" aria-label="Aksi pengguna" x-show="open" x-cloak :style="position" @click.outside="open = false" @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()">
                                                <button type="button" role="menuitem" class="su-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" @click="open = false; $refs.trigger.focus(); openForceLogoutModal({id:$el.dataset.userId,name:$el.dataset.userName})" aria-label="Force logout {{ $user->name }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9 3H4v18h5m5-14 5 5-5 5M8 12h11" /></svg>Force logout</button>
                                                @if(!$isSuperadmin && !$isMe)<button type="button" role="menuitem" class="su-btn su-btn-danger" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" @click="open = false; $refs.trigger.focus(); openSuspendModal({id:$el.dataset.userId,name:$el.dataset.userName})" aria-label="Suspend {{ $user->name }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="m6 6 12 12" /></svg>Suspend</button>@endif
                                                        </div>
                                                    </template>
                                                </div>
                                            @else
                                                <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}">@csrf<button type="submit" class="su-btn" aria-label="Aktifkan kembali {{ $user->name }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 11V7a4 4 0 0 1 8 0M5 11h14v10H5zM12 15v3" /></svg>Aktifkan kembali</button></form>
                                            @endif
                                        </div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="su-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 21v-2a8 8 0 0 1 16 0v2" /></svg><h3>{{ $hasFilters ? 'Tidak ada user yang cocok' : ($isOnlinePage ? 'Belum ada user online' : 'Tidak ada user suspended') }}</h3><p>{{ $hasFilters ? 'Coba kata kunci lain atau reset filter yang digunakan.' : ($isOnlinePage ? 'Pengguna yang sedang aktif akan ditampilkan di sini.' : 'Akun yang ditangguhkan akan ditampilkan di sini.') }}</p>@if($hasFilters)<a class="su-btn" href="{{ route($statusRoute) }}">Reset filter</a>@endif</div>
                @endif
                <footer class="su-pagination">@include('superadmin.users._pagination', ['paginationRoute' => $statusRoute])</footer>
            </section>
        </div>
    </div>
</div>
@if($isOnlinePage)
    @include('superadmin.users._modal_force_logout')
    @include('superadmin.users._modal_suspend')
    <script src="{{ asset('js/superadmin-user-status.js') }}?v=1" defer></script>
@endif
