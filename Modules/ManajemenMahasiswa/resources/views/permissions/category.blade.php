<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
    <style>
        .user-card { transition: all 0.2s; }
        .card-chevron { transition: transform 0.3s; }
        .card-chevron.rotated { transform: rotate(180deg); }

        /* Page Header */
        .mp-page-header {
            margin-bottom: 28px;
        }
        .mp-page-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 2px;
            letter-spacing: -0.02em;
        }
        .mp-page-header p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        .mp-page-header .mp-highlight {
            color: #6366f1;
        }

        /* Back Button */
        .mp-btn-back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #6366f1;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #c7d2fe;
            border-radius: 10px;
            background: #fff;
            transition: all 0.2s;
        }
        .mp-btn-back:hover {
            background: #eef2ff;
            color: #4338ca;
        }

        /* Filter Bar */
        .mp-filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        .mp-filter-bar input[type="text"] {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 9px 14px 9px 38px;
            font-size: 13px;
            color: #374151;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .mp-filter-bar input[type="text"]:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .mp-filter-btn {
            background: #6366f1;
            border: none;
            border-radius: 10px;
            padding: 9px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s;
        }
        .mp-filter-btn:hover {
            background: #4338ca;
        }

        /* Flash Messages */
        .mp-flash {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
            border: none;
        }
        .mp-flash.success {
            background: #dcfce7;
            color: #16a34a;
        }
        .mp-flash.error {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Empty State */
        .mp-empty {
            background: #fff;
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 48px;
            text-align: center;
        }
        .mp-empty p {
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            margin: 0;
        }

        /* User Card List */
        .mp-card-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Pagination */
        .mp-pagination {
            margin-top: 24px;
        }
    </style>
    @endpush

    {{-- Header --}}
    <div class="mp-page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h1>Kategori: <span class="mp-highlight">{{ $category }}</span></h1>
            <p>Menampilkan semua pengguna dalam grup ini</p>
        </div>
        <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" class="mp-btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mp-flash success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mp-flash error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Bar --}}
    <form action="{{ url()->current() }}" method="GET" class="mp-filter-bar">

        {{-- Per Page --}}
        <div x-data="{
                open: false,
                selected: '{{ $perPage }}',
                options: ['10','25','50','100'],
            }" style="position:relative;width:120px;">
            <input type="hidden" name="per_page" :value="selected">
            <button type="button" @click="open=!open" @click.away="open=false"
                style="width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;justify-content:space-between;cursor:pointer;transition:border-color 0.2s;"
                @mouseover="$el.style.borderColor='#6366f1'" @mouseout="$el.style.borderColor='#e5e7eb'">
                <span x-text="selected + ' Baris'"></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open?'transform:rotate(180deg)':''"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div x-show="open" x-transition.opacity.duration.150ms style="display:none;position:absolute;top:calc(100% + 4px);left:0;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);z-index:50;overflow:hidden;padding:4px 0;">
                <template x-for="opt in options" :key="opt">
                    <button type="button" @click="selected=opt;open=false"
                        style="width:100%;text-align:left;padding:8px 12px;font-size:13px;background:none;border:none;cursor:pointer;transition:background .1s;"
                        :style="selected===opt?'color:#6366f1;font-weight:700;':'color:#374151;'"
                        @mouseover="$el.style.background='#f9fafb'" @mouseout="$el.style.background='none'">
                        <span x-text="opt + ' Baris'"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Role Filter — hanya Pengurus Himpunan --}}
        @if($category === 'Pengurus Himpunan')
        <div x-data="{
                open: false,
                selected: '{{ $roleFilter }}',
                roles: [
                    { name: 'all',                    label: 'Semua Role' },
                    { name: 'ketua_himpunan',         label: 'Ketua Himpunan' },
                    { name: 'ketua_bidang',           label: 'Ketua Bidang' },
                    { name: 'ketua_unit',             label: 'Ketua Unit' },
                    { name: 'staff_himpunan',         label: 'Staff Himpunan' },
                ],
                get currentLabel() { return this.roles.find(r=>r.name===this.selected)?.label||'Semua Role'; }
            }" style="position:relative;width:180px;">
            <input type="hidden" name="role" :value="selected">
            <button type="button" @click="open=!open" @click.away="open=false"
                style="width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;justify-content:space-between;cursor:pointer;transition:border-color 0.2s;"
                @mouseover="$el.style.borderColor='#6366f1'" @mouseout="$el.style.borderColor='#e5e7eb'">
                <span x-text="currentLabel" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open?'transform:rotate(180deg)':''"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div x-show="open" x-transition.opacity.duration.150ms style="display:none;position:absolute;top:calc(100%+4px);left:0;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);z-index:50;overflow:hidden;padding:4px 0;">
                <template x-for="r in roles" :key="r.name">
                    <button type="button" @click="selected=r.name;open=false"
                        style="width:100%;text-align:left;padding:8px 12px;font-size:13px;background:none;border:none;cursor:pointer;transition:background .1s;"
                        :style="selected===r.name?'color:#6366f1;font-weight:700;':'color:#374151;'"
                        @mouseover="$el.style.background='#f9fafb'" @mouseout="$el.style.background='none'">
                        <span x-text="r.label"></span>
                    </button>
                </template>
            </div>
        </div>
        @endif

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:200px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, email...">
        </div>

        <button type="submit" class="mp-filter-btn">Filter</button>
    </form>

    {{-- User Cards --}}
    <div class="mp-card-list">
        @forelse($users as $user)
            @include('manajemenmahasiswa::permissions._user_card', [
                'user'            => $user,
                'assignableRoles' => $assignableRoles,
            ])
        @empty
            <div class="mp-empty">
                <p>Tidak ada pengguna dalam kategori ini</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mp-pagination">
        {{ $users->appends(request()->query())->links() }}
    </div>

    @include('manajemenmahasiswa::permissions._scripts')
</x-manajemenmahasiswa::layouts.admin>
