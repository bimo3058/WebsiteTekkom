{{--
    Shared Dosen Layout CSS (sidebar responsive, dropdown, backdrop)
    Usage: @include('banksoal::partials.dosen.layout-styles')
    
    Note: Only needed on pages that don't already have these styles
          via the main bsDosen.css (e.g. arsip, bank-soal, rps pages).
--}}

<style>
    /* ─────── MOBILE FIRST (Default) ─────── */
    .sidebar {
        position: fixed !important;
        left: -280px !important;
        top: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        background: white !important;
        box-shadow: 2px 0 8px rgba(0,0,0,0.1) !important;
        transition: left 0.3s ease !important;
        z-index: 1001 !important;
        overflow-y: auto !important;
    }

    .sidebar.active {
        left: 0 !important;
    }

    body.sidebar-open {
        overflow: hidden;
    }

    .sidebar-toggle {
        display: flex !important;
    }

    /* Backdrop (mobile only) */
    body.sidebar-open::before {
        content: '';
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 999;
        cursor: pointer;
    }

    /* User Dropdown */
    .user-dropdown-wrapper {
        position: relative;
    }

    .user-chip {
        background: #2563eb !important;
        color: white !important;
        border: none !important;
        padding: 8px 12px !important;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .user-chip:hover {
        background: #1d4ed8 !important;
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 280px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        z-index: 999;
        overflow: hidden;
    }

    .profile-dropdown.active {
        display: flex;
    }

    .dropdown-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
    }

    .dropdown-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
    }

    .dropdown-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .dropdown-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .dropdown-role {
        font-size: 12px;
        color: #64748b;
    }

    .dropdown-divider {
        height: 1px;
        background: #e2e8f0;
    }

    .dropdown-logout {
        background: none;
        border: none;
        color: #ef4444;
        padding: 12px 16px;
        text-align: left;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s ease;
    }

    .dropdown-logout:hover {
        background: #fef2f2;
    }

    /* ─────── DESKTOP LAYOUT (769px+) ─────── */
    @media (min-width: 769px) {
        .sidebar {
            left: 0 !important;
            top: 0;
            width: 280px !important;
            height: 100vh;
            position: fixed !important;
            z-index: 100;
        }

        .topbar {
            left: 280px !important;
            right: 0;
        }

        .main {
            margin-left: 280px !important;
            margin-top: 64px !important;
        }

        .sidebar-toggle {
            display: none !important;
        }

        body.sidebar-open {
            overflow: auto;
        }
    }
</style>
