{{-- resources/views/superadmin/users/online.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">
<div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
<div style="max-width:100%; padding:24px 24px 56px;">

    {{-- Breadcrumb --}}
    <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
        <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none;"
           onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <a href="{{ route('superadmin.users.index') }}" style="color:var(--c-fg-muted); text-decoration:none;"
           onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">User Management</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <span style="color:var(--c-fg); font-weight:500;">User Online</span>
    </nav>

    {{-- Header + Filter --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">User Online</h1>
            <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                <span style="color:var(--c-success); font-weight:600;">{{ $users->total() }}</span> user sedang aktif
            </p>
        </div>

        <form action="{{ url()->current() }}" method="GET" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

            {{-- Per Page --}}
            <div x-data="{ open:false, selected:'{{ request('per_page','10') }}', opts:['10','25','50','100'] }" style="position:relative; width:110px;">
                <input type="hidden" name="per_page" :value="selected">
                <button type="button" @click="open=!open" @click.outside="open=false"
                        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                        :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                    <span x-text="selected + ' baris'" style="font-weight:500;"></span>
                    <svg :class="open ? 'rotate-180' : ''" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; color:var(--c-fg-placeholder); flex-shrink:0;"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 8px 20px rgba(0,0,0,.08); z-index:50; padding:4px 0;">
                    <template x-for="opt in opts" :key="opt">
                        <button type="button" @click="selected=opt; open=false; $nextTick(()=>$el.closest('form').submit())"
                                class="w-full text-left hover:bg-[var(--c-bg)]"
                                :style="selected==opt ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600' : 'color:var(--c-fg-sec)'"
                                style="padding:6px 12px; font-size:12px; font-family:inherit; border:none; cursor:pointer; background:none; transition:background .12s;">
                            <span x-text="opt + ' baris'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Role --}}
            <div x-data="{
                open:false,
                selected:'{{ request('role','all') }}',
                roles:[{name:'all',label:'Semua Role'}, @foreach($roles as $r){name:'{{ $r->name }}',label:'{{ ucfirst($r->name) }}'}, @endforeach],
                get lbl(){ return this.roles.find(r=>r.name===this.selected)?.label||'Semua Role'; }
            }" style="position:relative; width:140px;">
                <input type="hidden" name="role" :value="selected">
                <button type="button" @click="open=!open" @click.outside="open=false"
                        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                        :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                    <span x-text="lbl" style="font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                    <svg :class="open ? 'rotate-180' : ''" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; color:var(--c-fg-placeholder); flex-shrink:0; margin-left:4px;"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 8px 20px rgba(0,0,0,.08); z-index:50; padding:4px 0; max-height:200px; overflow-y:auto;">
                    <template x-for="r in roles" :key="r.name">
                        <button type="button" @click="selected=r.name; open=false; $nextTick(()=>$el.closest('form').submit())"
                                :style="selected===r.name ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600' : 'color:var(--c-fg-sec)'"
                                style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; border:none; cursor:pointer; background:none; transition:background .12s;">
                            <span x-text="r.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Search --}}
            <div style="position:relative;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     style="position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none; color:var(--c-fg-placeholder);">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                       style="padding:7px 12px 7px 32px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; width:220px; transition:border-color .15s, box-shadow .15s;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                       onblur="this.style.borderColor='#D0D5DD'; this.style.boxShadow='none'">
            </div>

            <button type="submit"
                    style="padding:7px 14px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s;"
                    onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                Filter
            </button>
            <a href="{{ route('superadmin.users.index') }}"
               style="font-size:12px; font-weight:500; color:var(--c-fg-muted); text-decoration:none; padding:7px 4px; transition:color .15s;"
               onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">
                Kembali
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">User</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Role</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Login</th>
                    <th style="padding:12px 16px; text-align:center; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $isMe         = $user->id === auth()->id();
                    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                    $initials     = strtoupper(substr($user->name, 0, 1));
                    $sp = strpos($user->name, ' ');
                    if ($sp !== false) $initials .= strtoupper(substr($user->name, $sp+1, 1));
                    [$avBg, $avColor] = $isSuperadmin
                        ? ['rgba(11,38,110,0.08)', 'var(--c-primary)']
                        : ['var(--c-success-subtle)', '#287F6E'];
                @endphp
                <tr style="border-bottom:1px solid #F6F8FA; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">

                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="position:relative; flex-shrink:0;">
                                <div style="width:32px; height:32px; border-radius:50%; background:{{ $avBg }}; color:{{ $avColor }}; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; font-weight:700; border:1px solid rgba(0,0,0,0.06);">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @elseif($isSuperadmin)
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <span style="position:absolute; bottom:-1px; right:-1px; width:8px; height:8px; border-radius:50%; background:var(--c-success); border:1.5px solid #fff;"></span>
                            </div>
                            <div style="min-width:0;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <p style="font-size:13px; font-weight:600; color:var(--c-fg); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $user->name }}</p>
                                    @if($isMe)
                                        <span style="font-size:8px; font-weight:700; text-transform:uppercase; padding:2px 6px; border-radius:9999px; background:rgba(11,38,110,0.08); color:var(--c-primary);">YOU</span>
                                    @endif
                                </div>
                                <p style="font-size:11px; color:var(--c-fg-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    <td style="padding:12px 16px;">
                        @forelse($user->roles as $role)
                        @php
                            [$rBg, $rColor] = match(strtolower($role->name)) {
                                'superadmin' => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
                                'dosen'      => ['#DDF2EE', '#287F6E'],
                                'mahasiswa'  => ['#F9ECCB', '#956321'],
                                'gpm'        => ['#D1F0F9', '#0C4D6E'],
                                default      => ['var(--c-bg)', 'var(--c-fg-muted)'],
                            };
                        @endphp
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:3px 8px; border-radius:9999px; background:{{ $rBg }}; color:{{ $rColor }}; display:inline-block; margin-right:3px;">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                        @empty
                        <span style="font-size:10px; color:var(--c-fg-placeholder); font-style:italic;">—</span>
                        @endforelse
                    </td>

                    <td style="padding:12px 16px;">
                        <p style="font-size:12px; font-weight:600; color:var(--c-success);">Aktif sekarang</p>
                        <p style="font-size:10px; color:var(--c-fg-muted);">{{ $user->last_login?->diffForHumans() ?? 'Baru saja' }}</p>
                    </td>

                    <td style="padding:12px 16px; text-align:center;">
                        <div style="display:flex; align-items:center; justify-content:center; gap:4px;">
                            <button onclick="openForceLogoutModal({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}' })"
                                    title="Force Logout"
                                    style="width:28px; height:28px; border-radius:7px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-warning); transition:background .12s;"
                                    onmouseover="this.style.background='var(--c-warning-subtle)'" onmouseout="this.style.background='transparent'">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238"/>
                                </svg>
                            </button>
                            @if(!$isSuperadmin && !$isMe)
                            <button onclick="openSuspendModal({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}' })"
                                    title="Suspend"
                                    style="width:28px; height:28px; border-radius:7px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-error); transition:background .12s;"
                                    onmouseover="this.style.background='var(--c-error-subtle)'" onmouseout="this.style.background='transparent'">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:56px 24px; text-align:center;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                            <svg width="36" height="36" fill="none" stroke="var(--c-border)" viewBox="0 0 24 24" stroke-width="1.5">
                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round"/>
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round"/>
                            </svg>
                            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--c-fg-placeholder);">Tidak ada user online saat ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">{{ $users->appends(request()->query())->links() }}</div>

</div>
</div>

@include('superadmin.users._modal_force_logout')
@include('superadmin.users._modal_suspend')

<script>
function openModal(id) { const m=document.getElementById(id); if(m){m.style.display='flex'; document.body.style.overflow='hidden';} }
function closeModal(id) { const m=document.getElementById(id); if(m){m.style.display='none'; document.body.style.overflow='';} }
</script>
</x-sidebar>
</x-app-layout>