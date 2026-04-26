<x-manajemenmahasiswa::layouts.admin>
    <div class="min-h-screen" style="background:#F8F9FA; padding: 28px;">
        <div style="max-width:100%;">

            {{-- Header --}}
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 style="font-size:20px;font-weight:700;color:#1A1C1E;letter-spacing:-.02em;margin:0;">
                        Kategori: <span style="color:#5E53F4;">{{ $category }}</span>
                    </h1>
                    <p style="color:#6C757D;font-size:13px;margin:4px 0 0;">Menampilkan semua pengguna dalam grup ini</p>
                </div>
                <a href="{{ route('manajemenmahasiswa.pengguna.index') }}"
                    style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#5E53F4;text-decoration:none;padding:8px 16px;border:1px solid #D1BFFF;border-radius:10px;background:#fff;transition:all .2s;"
                    onmouseover="this.style.background='#F1E9FF'" onmouseout="this.style.background='#fff'">
                    <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Kembali
                </a>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div style="background:#E7F9F3;border:1px solid #B2EBD9;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#00C08D;">check_circle</span>
                    <span style="font-size:13px;color:#007A5A;font-weight:500;">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div style="background:#FEF2F2;border:1px solid #FEE2E2;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#EF4444;">error</span>
                    <span style="font-size:13px;color:#B91C1C;font-weight:500;">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter Bar --}}
            <form action="{{ url()->current() }}" method="GET"
                style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:20px;">

                {{-- Per Page --}}
                <div x-data="{
                        open: false,
                        selected: '{{ $perPage }}',
                        options: ['10','25','50','100'],
                    }" style="position:relative;width:120px;">
                    <input type="hidden" name="per_page" :value="selected">
                    <button type="button" @click="open=!open" @click.away="open=false"
                        style="width:100%;background:#fff;border:1px solid #DEE2E6;border-radius:10px;padding:7px 12px;font-size:12px;font-weight:600;color:#495057;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                        <span x-text="selected + ' Baris'"></span>
                        <span class="material-symbols-outlined" style="font-size:16px;color:#ADB5BD;" :style="open?'transform:rotate(180deg)':''">expand_more</span>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.150ms style="display:none;position:absolute;top:calc(100% + 4px);left:0;width:100%;background:#fff;border:1px solid #DEE2E6;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);z-index:50;overflow:hidden;padding:4px 0;">
                        <template x-for="opt in options" :key="opt">
                            <button type="button" @click="selected=opt;open=false"
                                style="width:100%;text-align:left;padding:6px 12px;font-size:12px;background:none;border:none;cursor:pointer;transition:background .1s;"
                                :style="selected===opt?'color:#5E53F4;font-weight:700;':'color:#495057;'"
                                onmouseover="this.style.background='#F8F9FA'" onmouseout="this.style.background='none'">
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
                            { name: 'wakil_ketua_himpunan',   label: 'Wakil Ketua Himpunan' },
                            { name: 'ketua_bidang',           label: 'Ketua Bidang' },
                            { name: 'ketua_unit',             label: 'Ketua Unit' },
                            { name: 'staff_himpunan',         label: 'Staff Himpunan' },
                        ],
                        get currentLabel() { return this.roles.find(r=>r.name===this.selected)?.label||'Semua Role'; }
                    }" style="position:relative;width:180px;">
                    <input type="hidden" name="role" :value="selected">
                    <button type="button" @click="open=!open" @click.away="open=false"
                        style="width:100%;background:#fff;border:1px solid #DEE2E6;border-radius:10px;padding:7px 12px;font-size:12px;font-weight:600;color:#495057;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                        <span x-text="currentLabel" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                        <span class="material-symbols-outlined" style="font-size:16px;color:#ADB5BD;flex-shrink:0;" :style="open?'transform:rotate(180deg)':''">expand_more</span>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.150ms style="display:none;position:absolute;top:calc(100%+4px);left:0;width:100%;background:#fff;border:1px solid #DEE2E6;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);z-index:50;overflow:hidden;padding:4px 0;">
                        <template x-for="r in roles" :key="r.name">
                            <button type="button" @click="selected=r.name;open=false"
                                style="width:100%;text-align:left;padding:6px 12px;font-size:12px;background:none;border:none;cursor:pointer;transition:background .1s;"
                                :style="selected===r.name?'color:#5E53F4;font-weight:700;':'color:#495057;'"
                                onmouseover="this.style.background='#F8F9FA'" onmouseout="this.style.background='none'">
                                <span x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
                @endif

                {{-- Search --}}
                <div style="position:relative;flex:1;min-width:200px;">
                    <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:#ADB5BD;">search</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, email..."
                        style="width:100%;background:#fff;border:1px solid #DEE2E6;border-radius:10px;padding:7px 14px 7px 34px;font-size:12px;color:#495057;outline:none;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#5E53F4'" onblur="this.style.borderColor='#DEE2E6'">
                </div>

                <button type="submit"
                    style="background:#5E53F4;border:none;border-radius:10px;padding:7px 18px;font-size:12px;font-weight:600;color:#fff;cursor:pointer;transition:background .2s;"
                    onmouseover="this.style.background='#4A42C1'" onmouseout="this.style.background='#5E53F4'">
                    Filter
                </button>
            </form>

            {{-- User Cards --}}
            <div style="display:flex;flex-direction:column;gap:8px;">
                @forelse($users as $user)
                    @include('manajemenmahasiswa::permissions._user_card', [
                        'user'            => $user,
                        'assignableRoles' => $assignableRoles,
                    ])
                @empty
                    <div style="background:#fff;border:2px dashed #DEE2E6;border-radius:12px;padding:48px;text-align:center;">
                        <p style="color:#ADB5BD;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin:0;">
                            Tidak ada pengguna dalam kategori ini
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div style="margin-top:20px;">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    @include('manajemenmahasiswa::permissions._scripts')
</x-manajemenmahasiswa::layouts.admin>
