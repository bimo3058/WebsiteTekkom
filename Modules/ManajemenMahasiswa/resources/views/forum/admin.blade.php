<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .bg-gradient-purple { background: #4D4DFF; }

        .forum-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            margin-bottom: 20px;
        }

        .avatar-placeholder {
            width: 40px; height: 40px; border-radius: 50%;
            background-color: #e0e7ff; color: #4f46e5;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 16px;
        }

        .btn-post {
            background-color: #818cf8; color: white; border: none;
            border-radius: 8px; padding: 0 20px; height: 42px;
            font-weight: 600; display: inline-flex; align-items: center;
            gap: 8px; white-space: nowrap;
        }
        .btn-post:hover { background-color: #6366f1; }

        .post-actions button {
            background: #f3f4f6; border: none; padding: 6px 14px;
            border-radius: 20px; color: #4b5563; font-size: 13px;
            font-weight: 600; display: inline-flex; align-items: center;
            gap: 6px; margin-right: 8px;
        }
        .post-actions button:hover { background: #e5e7eb; }

        .search-input {
            background-color: #f3f4f6; border: none; border-radius: 8px;
            height: 42px; padding-left: 36px;
        }
        .search-input:focus { background-color: #fff; box-shadow: 0 0 0 2px #e0e7ff; }

        .search-wrapper { position: relative; flex-grow: 1; }
        .search-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: #9ca3af; font-size: 14px;
        }

        .leaderboard-table th {
            font-weight: 500; font-size: 13px; color: rgba(255,255,255,0.8);
            border: none; padding-bottom: 15px; background-color: #4D4DFF;
        }
        .leaderboard-table td {
            border: none; color: #fff; font-size: 14px;
            padding: 6px 0; background-color: #4D4DFF;
        }

        .tag-label { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; }
        .tag-green { background: #dcfce7; color: #16a34a; }
        .tag-red   { background: #fee2e2; color: #dc2626; }
        .tag-gray  { background: #f3f4f6; color: #6b7280; }
    </style>
    @endpush

    <div class="mb-5">
        <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
        <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
    </div>

    <!-- Header Cards -->
    <div class="row mb-4">
        <div class="col-md-7 mb-3 mb-md-0">
            <div class="bg-gradient-purple rounded-4 p-4 h-100 text-white shadow-sm">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">🏆 Leaderboard</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0 leaderboard-table">
                        <thead>
                            <tr>
                                <th style="width:10%">No.</th>
                                <th style="width:40%">User</th>
                                <th style="width:20%">Level</th>
                                <th style="width:30%">Badges</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1.</td><td>Lutfi Halimawan</td><td>Lvl ...</td><td></td></tr>
                            <tr><td>2.</td><td>Reza</td><td>Lvl ...</td><td></td></tr>
                            <tr><td>3.</td><td>Surya</td><td>Lvl ...</td><td></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="bg-gradient-purple rounded-4 p-4 h-100 text-white shadow-sm d-flex flex-column justify-content-center">
                <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">🔥 Streak Kamu Hari Ini : ...</h6>
                <div class="mb-3 ps-4"><span style="font-size:14px;font-weight:500;">Rank : ...</span></div>
                <div class="mb-3 ps-4 d-flex align-items-center gap-2"><span>🏵️</span> <span style="font-size:14px;font-weight:500;">Level Kamu : ...</span></div>
                <div class="ps-4 d-flex align-items-center gap-2"><span>📊</span> <span style="font-size:14px;font-weight:500;">Exp : .../...</span></div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-4">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon">🔍</span>
            <input type="text" class="form-control search-input w-100" placeholder="Search">
        </div>
        <div class="d-flex gap-3">
            <select class="form-select border-1" style="border-radius:8px;height:42px;min-width:130px;">
                <option>Filter</option>
            </select>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none">Post ⊕</a>
        </div>
    </div>

    <!-- Forum Posts -->
    @for ($i = 0; $i < 3; $i++)
        <a href="{{ route('manajemenmahasiswa.forum.show', $i + 1) }}" class="text-decoration-none">
            <div class="forum-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">👤</div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold text-dark mb-0">Username</h6>
                                <span class="text-primary fw-medium" style="font-size:12px;">• 2h ago</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-muted fw-bold" style="cursor:pointer;font-size:20px;line-height:1;" onclick="event.preventDefault();">...</span>
                </div>
                <p class="text-dark" style="font-size:14px;margin-bottom:12px;line-height:1.5;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.
                </p>
                <div class="d-flex gap-2 mb-3">
                    <span class="tag-label tag-green">Label</span>
                    <span class="tag-label tag-red">Label</span>
                    <span class="tag-label tag-gray">+4</span>
                </div>
                <div class="post-actions d-flex align-items-center">
                    <button onclick="event.preventDefault();"><span class="me-1" style="font-size:14px;">↑</span> ... <span class="ms-1" style="font-size:14px;">↓</span></button>
                    <button onclick="event.preventDefault();"><span class="me-1" style="font-size:14px;">💬</span> ...</button>
                    <button onclick="event.preventDefault();"><span style="font-size:14px;">🔗</span></button>
                </div>
            </div>
        </a>
    @endfor

</x-manajemenmahasiswa::layouts.admin>
