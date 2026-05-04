{{-- resources/views/superadmin/users/_search_filter.blade.php --}}
@php
    $selectedRole  = request('role', 'all');
    $selectedLimit = request('per_page', '10');
    
    // Mapping Array ke format Javascript yang aman
    $roleItems = $roles->map(fn($r) => [
        'name'  => $r->name,
        'label' => ucfirst(str_replace('_', ' ', $r->name)),
    ])->prepend(['name' => 'all', 'label' => 'Semua Role'])->values()->all();
@endphp

{{-- 
  Perbaikan 1: Pindahkan x-data yang menyimpan parameter ke root div paling atas. 
  Ini memastikan 'selectedRole' dan 'selectedLimit' bisa diakses oleh semua child elements.
--}}
<div class="bg-white border rounded-xl px-4 py-3 mb-4 relative z-40" 
     style="border-color:var(--c-border); overflow: visible;"
     x-data="{ 
         filterRole: '{{ $selectedRole }}',
         filterLimit: '{{ $selectedLimit }}',
         rolesList: @js($roleItems),
         getRoleLabel(val) {
             return this.rolesList.find(r => r.name === val)?.label || 'Semua Role';
         }
     }">
     
    <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="flex flex-wrap items-end gap-2">

            {{-- 1. Search Input --}}
            <div class="flex-1 min-w-40 relative z-30">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Cari</label>
                <div class="relative">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                         class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:var(--c-fg-placeholder);">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama atau email..."
                           class="w-full h-8 pl-8 pr-3 rounded-lg text-xs outline-none transition-all"
                           style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit;"
                           onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                           onblur="this.style.borderColor='#D0D5DD'; this.style.boxShadow='none'">
                </div>
            </div>

            {{-- 2. Role Dropdown (Diperbaiki) --}}
            {{-- Perbaikan 2: x-data ini hanya fokus mengatur buka/tutup (open) dan membaca referensi filterRole dari root --}}
            <div class="w-36 relative z-50" x-data="{ open: false }">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Role</label>
                
                <div class="relative">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="w-full h-8 flex items-center justify-between gap-2 px-3 rounded-lg text-xs font-medium transition-colors"
                            style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit; cursor:pointer;"
                            :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                        
                        {{-- Menggunakan referensi root function 'getRoleLabel' --}}
                        <span class="truncate" x-text="getRoleLabel(filterRole)"></span>
                        
                        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:var(--c-fg-placeholder);">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    
                    {{-- Input hidden yang akan dikirim saat submit form --}}
                    <input type="hidden" name="role" :value="filterRole">
                    
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1 w-full rounded-lg overflow-hidden py-1 max-h-48 overflow-y-auto z-[100]"
                         style="display:none; background:#fff; border:1px solid var(--c-border); box-shadow:0 10px 25px rgba(0,0,0,.1);">
                        
                        <template x-for="r in rolesList" :key="r.name">
                            {{-- Saat di-klik, perbarui filterRole yang ada di ROOT container --}}
                            <button type="button" @click="filterRole = r.name; open = false"
                                    class="w-full text-left px-3 py-1.5 text-xs hover:bg-[var(--c-bg)] transition-colors"
                                    :class="filterRole === r.name ? 'font-semibold' : 'font-normal'"
                                    :style="filterRole === r.name ? 'color:var(--c-primary); background:rgba(11,38,110,0.04)' : 'color:var(--c-fg-sec)'"
                                    style="font-family:inherit; border:none; cursor:pointer; background:none;">
                                <span x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 3. Limit Dropdown --}}
            <div class="w-28 relative z-50" x-data="{
                open: false,
                options: [10, 25, 50, 100],
            }">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Limit</label>
                
                <div class="relative">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="w-full h-8 flex items-center justify-between gap-2 px-3 rounded-lg text-xs font-medium transition-colors"
                            style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit; cursor:pointer;"
                            :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                        
                        {{-- Baca nilai dari ROOT container --}}
                        <span x-text="filterLimit + ' baris'"></span>
                        
                        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:var(--c-fg-placeholder);">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    
                    {{-- Input hidden yang akan dikirim saat submit form --}}
                    <input type="hidden" name="per_page" :value="filterLimit">
                    
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1 w-full rounded-lg overflow-hidden py-1 z-[100]"
                         style="display:none; background:#fff; border:1px solid var(--c-border); box-shadow:0 10px 25px rgba(0,0,0,.1);">
                        
                        <template x-for="opt in options" :key="opt">
                            {{-- Saat di-klik, perbarui filterLimit yang ada di ROOT container --}}
                            <button type="button" @click="filterLimit = String(opt); open = false; localStorage.setItem('um_per_page', String(opt));"
                                    class="w-full text-left px-3 py-1.5 text-xs hover:bg-[var(--c-bg)] transition-colors"
                                    :class="filterLimit == opt ? 'font-semibold' : 'font-normal'"
                                    :style="filterLimit == opt ? 'color:var(--c-primary); background:rgba(11,38,110,0.04)' : 'color:var(--c-fg-sec)'"
                                    style="font-family:inherit; border:none; cursor:pointer; background:none;">
                                <span x-text="opt + ' baris'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 4. Filter Button --}}
            <div class="relative z-30">
                <button type="submit"
                        class="h-8 px-4 rounded-lg text-xs font-bold text-white transition-colors"
                        style="background:var(--c-primary); border:none; font-family:inherit; cursor:pointer; letter-spacing:.04em;"
                        onmouseover="this.style.background='var(--c-primary-hover)'"
                        onmouseout="this.style.background='var(--c-primary)'">
                    Filter
                </button>
            </div>

        </div>
    </form>
</div>