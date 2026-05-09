<x-app-layout>
    <x-sidebar :user="auth()->user()">
        <div class="min-h-screen bg-slate-50 p-6">
            <div class="max-w-full mx-auto">
                
                {{-- Bagian Header & Filter Sebaris (Disamakan dengan Online/Suspended) --}}
                <div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                            Kategori: <span class="text-[#5E53F4]">{{ $category }}</span>
                        </h1>
                        <p class="text-slate-500 text-xs mt-0.5">Menampilkan semua pengguna dalam grup ini</p>
                    </div>

                    <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-2">
                        
                        {{-- Dropdown Per Page (Alpine.js - Style Seragam) --}}
                        <div class="relative w-32" x-data="{ 
                            open: false,
                            selected: '{{ request('per_page') ?: '' }}',
                            options: ['10', '25', '50', '100'],
                            init() {
                                if (!this.selected) {
                                    this.selected = localStorage.getItem('cat_per_page') || '10';
                                }
                            },
                            setSelected(val) {
                                this.selected = String(val);
                                localStorage.setItem('cat_per_page', String(val));
                            }
                        }">
                            <input type="hidden" name="per_page" :value="selected">
                            <button type="button" @click="open = !open" @click.away="open = false" 
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs flex items-center justify-between shadow-sm">
                                <span x-text="selected + ' Baris'" class="font-medium"></span>
                                <span class="material-symbols-outlined text-slate-400 ml-1" :class="{'rotate-180': open}" style="font-size:16px; transition: transform 0.2s;">expand_more</span>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl z-[50] overflow-hidden py-1">
                                <template x-for="opt in options" :key="opt">
                                    <button type="button" @click="setSelected(opt); open = false"
                                        class="w-full text-left px-3 py-1.5 text-xs transition-colors hover:bg-slate-50"
                                        :class="selected == opt ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                        <span x-text="opt + ' Baris'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        
                        {{-- Dropdown Filter Role — hanya untuk Admins --}}
                        @if($category === 'Admins')
                        <div class="relative w-40" x-data="{
                            open: false,
                            selected: '{{ request('role', 'all') }}',
                            roles: [
                                { name: 'all', label: 'Semua Role' },
                                { name: 'superadmin', label: 'Superadmin' },
                                { name: 'admin', label: 'Admin' },
                                { name: 'admin_banksoal', label: 'Admin Bank Soal' },
                                { name: 'admin_capstone', label: 'Admin Capstone' },
                                { name: 'admin_eoffice', label: 'Admin E-Office' },
                                { name: 'admin_kemahasiswaan', label: 'Admin Kemahasiswaan' },
                            ],
                            get currentLabel() {
                                return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role';
                            }
                        }">
                            <input type="hidden" name="role" :value="selected">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs flex items-center justify-between shadow-sm">
                                <span x-text="currentLabel" class="font-medium truncate"></span>
                                <span class="material-symbols-outlined text-slate-400 ml-1" :class="{'rotate-180': open}" style="font-size:16px; transition: transform 0.2s;">expand_more</span>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display:none;"
                                class="absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl z-[50] overflow-hidden py-1 max-h-52 overflow-y-auto">
                                <template x-for="r in roles" :key="r.name">
                                    <button type="button" @click="selected = r.name; open = false"
                                        class="w-full text-left px-3 py-1.5 text-xs transition-colors hover:bg-slate-50"
                                        :class="selected === r.name ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                        <span x-text="r.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @endif

                        {{-- Search Input (Style Seragam) --}}
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama atau email..." 
                                class="text-xs border-slate-200 rounded-lg pl-8 pr-4 py-2 focus:ring-[#5E53F4] focus:border-[#5E53F4] w-64 shadow-sm">
                            <span class="material-symbols-outlined text-slate-400 absolute left-2.5 top-2" style="font-size:18px">search</span>
                        </div>

                        {{-- Action Buttons --}}
                        <button type="submit" class="bg-[#5E53F4] hover:bg-[#4e44e0] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('superadmin.permissions') }}" class="text-xs font-bold text-[#5E53F4] ml-2 hover:underline transition-all">
                            Kembali
                        </a>
                    </form>
                </div>

                {{-- List User (Tetap Menggunakan Card Bawaan) --}}
                <div class="grid grid-cols-1 gap-3">

                    @forelse($users as $user)
                        @include('superadmin.permission._user_card', ['user' => $user])
                    @empty
                        <div class="bg-white p-10 text-center rounded-xl border border-dashed border-slate-300">
                            <p class="text-slate-500 text-sm">User tidak ditemukan dalam kategori ini.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination (Style Seragam) --}}
                <div class="mt-6">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @include('superadmin.permission._scripts')
    </x-sidebar>
</x-app-layout>