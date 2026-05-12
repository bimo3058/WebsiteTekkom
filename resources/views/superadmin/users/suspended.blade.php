{{-- resources/views/superadmin/users/suspended.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">
<div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
<div style="max-width:100%; padding:24px 24px 56px;">

    <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
        <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <a href="{{ route('superadmin.users.index') }}" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">User Management</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        <span style="color:var(--c-fg); font-weight:500;">User Suspended</span>
    </nav>

    {{-- Header + Filter --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">
                User <span style="color:var(--c-error);">Suspended</span>
            </h1>
            <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">
                Total <span style="color:var(--c-error); font-weight:600;">{{ $users->total() }}</span> user disuspend
            </p>
        </div>

        <form action="{{ url()->current() }}" method="GET" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

            {{-- Per Page --}}
            <div x-data="{ open:false, selected:'{{ request('per_page','10') }}', opts:['10','25','50','100'] }" style="position:relative; width:110px;">
                <input type="hidden" name="per_page" :value="selected">
                <button type="button" @click="open=!open" @click.outside="open=false"
                        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer;"
                        :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                    <span x-text="selected + ' baris'" style="font-weight:500;"></span>
                    <svg :class="open ? 'rotate-180' : ''" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; color:var(--c-fg-placeholder); flex-shrink:0;"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 8px 20px rgba(0,0,0,.08); z-index:50; padding:4px 0;">
                    <template x-for="opt in opts" :key="opt">
                        <button type="button" @click="selected=opt; open=false; $nextTick(()=>$el.closest('form').submit())"
                                :style="selected==opt ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600' : 'color:var(--c-fg-sec)'"
                                style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; border:none; cursor:pointer; background:none;">
                            <span x-text="opt + ' baris'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Role --}}
            <div x-data="{
                open:false, selected:'{{ request('role','all') }}',
                roles:[{name:'all',label:'Semua Role'}, @foreach($roles as $r){name:'{{ $r->name }}',label:'{{ ucfirst($r->name) }}'}, @endforeach],
                get lbl(){ return this.roles.find(r=>r.name===this.selected)?.label||'Semua Role'; }
            }" style="position:relative; width:140px;">
                <input type="hidden" name="role" :value="selected">
                <button type="button" @click="open=!open" @click.outside="open=false"
                        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid #D0D5DD; border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer;"
                        :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                    <span x-text="lbl" style="font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                    <svg :class="open ? 'rotate-180' : ''" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; color:var(--c-fg-placeholder); flex-shrink:0; margin-left:4px;"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 8px 20px rgba(0,0,0,.08); z-index:50; padding:4px 0; max-height:200px; overflow-y:auto;">
                    <template x-for="r in roles" :key="r.name">
                        <button type="button" @click="selected=r.name; open=false; $nextTick(()=>$el.closest('form').submit())"
                                :style="selected===r.name ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600' : 'color:var(--c-fg-sec)'"
                                style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; border:none; cursor:pointer; background:none;">
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
                    onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">Filter</button>
            <a href="{{ route('superadmin.users.index') }}"
               style="font-size:12px; font-weight:500; color:var(--c-fg-muted); text-decoration:none; padding:7px 4px;"
               onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Kembali</a>
        </form>
    </div>

    {{-- Table --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                        <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">User</th>
                        <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Role</th>
                        <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Alasan</th>
                        <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Waktu</th>
                        <th style="padding:12px 16px; text-align:center; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $isSA     = $user->roles->pluck('name')->contains('superadmin');
                        $initials = strtoupper(substr($user->name, 0, 1));
                        $sp = strpos($user->name, ' ');
                        if ($sp !== false) $initials .= strtoupper(substr($user->name, $sp+1, 1));
                    @endphp
                    <tr style="border-bottom:1px solid #F6F8FA; background:rgba(223,28,65,0.02); transition:background .12s;"
                        onmouseover="this.style.background='rgba(223,28,65,0.04)'" onmouseout="this.style.background='rgba(223,28,65,0.02)'">

                        <td style="padding:12px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--c-error-subtle); color:var(--c-error); display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; font-weight:700; border:1px solid #ED8296; opacity:0.7; flex-shrink:0;">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else {{ $initials }} @endif
                                </div>
                                <div style="min-width:0;">
                                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                                        <p style="font-size:13px; font-weight:600; color:var(--c-error); text-decoration:line-through; text-decoration-color:var(--c-error-subtle); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:160px;">{{ $user->name }}</p>
                                        <span style="font-size:8px; font-weight:700; text-transform:uppercase; padding:2px 6px; border-radius:9999px; background:var(--c-error-subtle); color:var(--c-error); white-space:nowrap;">Suspended</span>
                                    </div>
                                    <p style="font-size:11px; color:var(--c-fg-muted);">{{ $user->email }}</p>
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
                                    default      => ['var(--c-bg)', 'var(--c-fg-muted)'],
                                };
                            @endphp
                            <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:3px 8px; border-radius:9999px; background:{{ $rBg }}; color:{{ $rColor }}; display:inline-block; margin-right:3px;">
                                {{ str_replace('_', ' ', $role->name) }}
                            </span>
                            @empty
                            <span style="font-size:10px; color:var(--c-fg-placeholder); font-style:italic;">No Role</span>
                            @endforelse
                        </td>

                        <td style="padding:12px 16px;">
                            <p style="font-size:12px; font-weight:600; color:var(--c-error); max-width:200px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5;">
                                {{ $user->suspension_reason ?? 'No specific reason provided' }}
                            </p>
                        </td>

                        <td style="padding:12px 16px;">
                            <p style="font-size:11px; font-weight:600; color:var(--c-fg);">Banned</p>
                            <p style="font-size:10px; color:var(--c-fg-muted); font-style:italic;">{{ $user->suspended_at?->diffForHumans() ?? '-' }}</p>
                        </td>

                        <td style="padding:12px 16px; text-align:center;">
                            <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}" style="margin:0; display:inline;">
                                @csrf
                                <button type="submit" title="Unsuspend"
                                        style="width:30px; height:30px; border-radius:8px; border:1px solid #9DE0D3; background:#fff; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; color:var(--c-success); transition:background .12s;"
                                        onmouseover="this.style.background='var(--c-success-subtle)'" onmouseout="this.style.background='#fff'">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8V9C15 9.55228 15.4477 10 16 10C16.5523 10 17 9.55228 17 9V8C17 5.23858 14.7614 3 12 3C9.23858 3 7 5.23858 7 8V11C5.34315 11 4 12.3431 4 14V18C4 19.6569 5.34315 21 7 21H17C18.6569 21 20 19.6569 20 18V14C20 12.3431 18.6569 11 17 11H16H9V8ZM6 14C6 13.4477 6.44772 13 7 13H8H16H17C17.5523 13 18 13.4477 18 14V18C18 18.5523 17.5523 19 17 19H7C6.44772 19 6 18.5523 6 18V14ZM12 14C12.8284 14 13.5 14.6716 13.5 15.5C13.5 15.9443 13.3069 16.3434 13 16.6181V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V16.6181C10.6931 16.3434 10.5 15.9443 10.5 15.5C10.5 14.6716 11.1716 14 12 14Z"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:56px 24px; text-align:center;">
                            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--c-fg-placeholder);">Tidak ada user suspended</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:20px;">{{ $users->appends(request()->query())->links() }}</div>

</div>
</div>

@include('superadmin.users._modal_force_logout')

<script>
function openModal(id) { const m=document.getElementById(id); if(m){m.style.display='flex'; document.body.style.overflow='hidden';} }
function closeModal(id) { const m=document.getElementById(id); if(m){m.style.display='none'; document.body.style.overflow='';} }
</script>
</x-sidebar>
</x-app-layout>