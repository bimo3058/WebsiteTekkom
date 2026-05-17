@php
    $user = auth()->user();
    $roles = $user ? $user->roles->pluck('name')->toArray() : [];
    $currentRoute = request()->route() ? request()->route()->getName() : '';

    // Build user initials
    $name = $user->name ?? 'User';
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));

    // Determine primary role display
    if (in_array('superadmin', $roles)) $roleDisplay = 'Super Admin';
    elseif (in_array('admin', $roles)) $roleDisplay = 'Admin';
    elseif (in_array('admin_kemahasiswaan', $roles)) $roleDisplay = 'Admin Kemahasiswaan';
    elseif (in_array('gpm', $roles)) $roleDisplay = 'GPM';
    elseif (in_array('dosen_koordinator', $roles)) $roleDisplay = 'Koordinator';
    elseif (in_array('dosen', $roles)) $roleDisplay = 'Dosen';
    elseif (in_array('ketua_himpunan', $roles)) $roleDisplay = 'Ketua Himpunan';
    elseif (in_array('ketua_bidang', $roles)) $roleDisplay = 'Ketua Bidang';
    elseif (in_array('ketua_unit', $roles)) $roleDisplay = 'Ketua Unit';
    elseif (in_array('staff_himpunan', $roles)) $roleDisplay = 'Staff Himpunan';
    elseif (in_array('mahasiswa', $roles)) $roleDisplay = 'Mahasiswa';
    elseif (in_array('alumni', $roles)) $roleDisplay = 'Alumni';
    else $roleDisplay = 'User';

    // Breadcrumb page title based on current route
    $topbarRoutes = [
        'manajemenmahasiswa.dashboard' => 'Analitik',
        'manajemenmahasiswa.pengumuman' => 'Pengumuman',
        'manajemenmahasiswa.direktori' => 'Mahasiswa & Alumni',
        'manajemenmahasiswa.pengguna' => 'Permission',
        'manajemenmahasiswa.proker' => 'Rencana Proker',
        'manajemenmahasiswa.pelaksanaan' => 'Pelaksanaan Kegiatan',
        'manajemenmahasiswa.kegiatan' => 'Laporan & Arsip',
        'manajemenmahasiswa.verifikasi' => 'Verifikasi Data',
        'manajemenmahasiswa.forum' => 'Forum Diskusi',
        'manajemenmahasiswa.pengaduan' => 'Layanan Pengaduan',
        'profile.edit' => 'Settings',
    ];

    $pageTitle = 'Dashboard';
    foreach ($topbarRoutes as $routePrefix => $title) {
        if (str_starts_with($currentRoute, $routePrefix)) {
            $pageTitle = $title;
            break;
        }
    }

    // If on main dashboard (not manajemenmahasiswa.dashboard), show "Dashboard"
    if ($currentRoute === 'dashboard' || $currentRoute === 'superadmin.dashboard') {
        $pageTitle = 'Dashboard';
    }

    // Get unread notification count
    $unreadNotifCount = \Modules\ManajemenMahasiswa\Models\ForumNotification::forUser($user->id)->unread()->count();
@endphp

<div class="simenma-topbar">
    <div class="simenma-crumb">
        <span>SIMENMA</span>
        <span class="simenma-crumb-sep">/</span>
        <b>{{ $pageTitle }}</b>
    </div>

    <div class="simenma-topbar-right">
        {{-- Notification Bell --}}
        <div class="simenma-notif-wrap" id="notifWrap">
            <button class="simenma-icon-btn" title="Notifikasi" onclick="toggleNotifDropdown()" id="notifBtn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 1112 0c0 7 3 9 3 9H3s3-2 3-9" />
                    <path d="M10 21a2 2 0 004 0" />
                </svg>
                @if($unreadNotifCount > 0)
                    <span class="simenma-notif-badge" id="notifBadge">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
                @else
                    <span class="simenma-notif-badge" id="notifBadge" style="display:none;"></span>
                @endif
            </button>

            {{-- Dropdown --}}
            <div class="simenma-notif-dropdown" id="notifDropdown">
                <div class="simenma-notif-header">
                    <span class="simenma-notif-title">Notifikasi</span>
                    <button class="simenma-notif-readall" id="notifReadAll" onclick="markAllNotifRead()">Tandai semua dibaca</button>
                </div>
                <div class="simenma-notif-list" id="notifList">
                    <div class="simenma-notif-empty">Memuat...</div>
                </div>
            </div>
        </div>

        <div class="simenma-topbar-user">
            <div class="simenma-topbar-avatar">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt=""
                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="simenma-topbar-meta">
                <div class="simenma-topbar-name">{{ $user->name }}</div>
                <div class="simenma-topbar-role">{{ $roleDisplay }}</div>
            </div>
        </div>
    </div>
</div>

<style>
    .simenma-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        height: 60px;
        padding: 0 28px;
        background: #fff;
        border-bottom: 1px solid #DFE1E7;
        position: sticky;
        top: 0;
        z-index: 20;
        flex-shrink: 0;
    }

    .simenma-crumb {
        font-size: 12px;
        color: #666D80;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .simenma-crumb b {
        color: #0D0D12;
        font-weight: 600;
    }

    .simenma-crumb-sep {
        color: #C1C7CF;
    }

    .simenma-topbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .simenma-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid #DFE1E7;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666D80;
        cursor: pointer;
        transition: background .15s, border-color .15s;
        position: relative;
    }

    .simenma-icon-btn:hover {
        background: #F6F8FA;
        border-color: #C1C7CF;
        color: #0D0D12;
    }

    /* ─── Notification Badge ──────────────────────────────── */
    .simenma-notif-wrap {
        position: relative;
    }

    .simenma-notif-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 50px;
        background: #EF4444;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        border: 2px solid #fff;
        pointer-events: none;
    }

    /* ─── Notification Dropdown ──────────────────────────── */
    .simenma-notif-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 380px;
        max-height: 440px;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 14px;
        box-shadow: 0 12px 36px rgba(0,0,0,.12);
        display: none;
        flex-direction: column;
        z-index: 100;
        overflow: hidden;
    }

    .simenma-notif-dropdown.open {
        display: flex;
    }

    .simenma-notif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px 10px;
        border-bottom: 1px solid #F3F4F6;
    }

    .simenma-notif-title {
        font-size: .85rem;
        font-weight: 700;
        color: #0D0D12;
    }

    .simenma-notif-readall {
        font-size: .72rem;
        font-weight: 600;
        color: #293C79;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .simenma-notif-readall:hover {
        text-decoration: underline;
    }

    .simenma-notif-list {
        flex: 1;
        overflow-y: auto;
        max-height: 370px;
    }

    .simenma-notif-item {
        display: flex;
        gap: 10px;
        padding: 12px 18px;
        border-bottom: 1px solid #F9FAFB;
        cursor: pointer;
        transition: background .12s;
        text-decoration: none;
    }

    .simenma-notif-item:hover {
        background: #F6F8FA;
    }

    .simenma-notif-item.unread {
        background: #F0F2F8;
    }

    .simenma-notif-item.unread:hover {
        background: #E7E8F0;
    }

    .simenma-notif-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5C78B8, #0B266E);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .simenma-notif-body {
        flex: 1;
        min-width: 0;
    }

    .simenma-notif-msg {
        font-size: .8rem;
        color: #374151;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .simenma-notif-time {
        font-size: .7rem;
        color: #9CA3AF;
        margin-top: 3px;
    }

    .simenma-notif-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #293C79;
        flex-shrink: 0;
        align-self: center;
    }

    .simenma-notif-empty {
        text-align: center;
        padding: 32px 16px;
        color: #9CA3AF;
        font-size: .84rem;
    }

    /* ─── User Area ──────────────────────────────────────── */
    .simenma-topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-left: 12px;
        border-left: 1px solid #DFE1E7;
        margin-left: 4px;
    }

    .simenma-topbar-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5C78B8, #0B266E);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }

    .simenma-topbar-meta {
        line-height: 1.2;
    }

    .simenma-topbar-name {
        font-size: 13px;
        font-weight: 600;
        color: #0D0D12;
        white-space: nowrap;
    }

    .simenma-topbar-role {
        font-size: 11px;
        color: #666D80;
        white-space: nowrap;
    }

    /* Mobile adjustments */
    @media (max-width: 767px) {
        .simenma-topbar {
            padding: 0 14px;
            height: 52px;
            gap: 10px;
        }

        .simenma-topbar-meta {
            display: none;
        }

        .simenma-topbar-user {
            padding-left: 8px;
            gap: 6px;
        }

        .simenma-topbar-avatar {
            width: 30px;
            height: 30px;
            font-size: 10px;
        }

        .simenma-icon-btn {
            width: 30px;
            height: 30px;
        }

        .simenma-notif-dropdown {
            width: 300px;
            right: -40px;
        }
    }
</style>

<script>
const NOTIF_URL = '{{ route("manajemenmahasiswa.notifications.index") }}';
const NOTIF_READ_URL = '{{ route("manajemenmahasiswa.notifications.read", "__ID__") }}';
const NOTIF_READ_ALL_URL = '{{ route("manajemenmahasiswa.notifications.read_all") }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

let _notifOpen = false;
let _notifLoaded = false;

function toggleNotifDropdown() {
    const dd = document.getElementById('notifDropdown');
    _notifOpen = !_notifOpen;
    dd.classList.toggle('open', _notifOpen);

    if (_notifOpen && !_notifLoaded) {
        loadNotifications();
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target) && _notifOpen) {
        _notifOpen = false;
        document.getElementById('notifDropdown').classList.remove('open');
    }
});

function loadNotifications() {
    fetch(NOTIF_URL, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        _notifLoaded = true;
        renderNotifications(data.notifications, data.unread_count);
    })
    .catch(() => {
        document.getElementById('notifList').innerHTML =
            '<div class="simenma-notif-empty">Gagal memuat notifikasi</div>';
    });
}

function renderNotifications(items, unreadCount) {
    const list = document.getElementById('notifList');
    const badge = document.getElementById('notifBadge');

    // Update badge
    if (unreadCount > 0) {
        badge.style.display = 'flex';
        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
    } else {
        badge.style.display = 'none';
    }

    if (!items.length) {
        list.innerHTML = '<div class="simenma-notif-empty">Belum ada notifikasi</div>';
        return;
    }

    list.innerHTML = items.map(n => `
        <a href="${n.thread_url}" class="simenma-notif-item ${n.is_read ? '' : 'unread'}"
           onclick="markNotifRead(event, ${n.id})" data-id="${n.id}">
            <div class="simenma-notif-avatar">${escN(n.actor_initials)}</div>
            <div class="simenma-notif-body">
                <div class="simenma-notif-msg">${escN(n.message)}</div>
                <div class="simenma-notif-time">${escN(n.time_ago)}</div>
            </div>
            ${n.is_read ? '' : '<div class="simenma-notif-dot"></div>'}
        </a>
    `).join('');
}

function escN(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

function markNotifRead(e, id) {
    // Don't prevent navigation, but fire the read request
    const item = e.currentTarget;
    if (item.classList.contains('unread')) {
        item.classList.remove('unread');
        const dot = item.querySelector('.simenma-notif-dot');
        if (dot) dot.remove();

        // Decrement badge
        const badge = document.getElementById('notifBadge');
        let count = parseInt(badge.textContent) || 0;
        count = Math.max(0, count - 1);
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
        } else {
            badge.style.display = 'none';
        }

        fetch(NOTIF_READ_URL.replace('__ID__', id), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
        });
    }
}

function markAllNotifRead() {
    fetch(NOTIF_READ_ALL_URL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
    })
    .then(() => {
        // Remove all unread styling
        document.querySelectorAll('.simenma-notif-item.unread').forEach(el => {
            el.classList.remove('unread');
            const dot = el.querySelector('.simenma-notif-dot');
            if (dot) dot.remove();
        });
        const badge = document.getElementById('notifBadge');
        badge.style.display = 'none';
    });
}

// Auto-refresh notification count every 60 seconds
setInterval(() => {
    fetch(NOTIF_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notifBadge');
            if (data.unread_count > 0) {
                badge.style.display = 'flex';
                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
            } else {
                badge.style.display = 'none';
            }
            // Refresh list if dropdown is open
            if (_notifOpen) {
                renderNotifications(data.notifications, data.unread_count);
            }
        })
        .catch(() => {});
}, 60000);
</script>
