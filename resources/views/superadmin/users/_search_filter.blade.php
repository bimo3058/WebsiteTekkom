@php
    $selectedRole  = request('role', 'all');
    $selectedLimit = request('per_page', '10');
    $roleItems = $roles->map(fn($r) => [
        'name'  => $r->name,
        'label' => ucfirst(str_replace('_', ' ', $r->name)),
    ])->prepend(['name' => 'all', 'label' => 'Semua Role'])->values()->all();
@endphp

<div class="bg-white border border-[#DFE1E6] rounded-xl px-4 py-3 mb-4">
    <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="flex flex-wrap items-end gap-2">

            {{-- Search --}}
            <div class="flex-1 min-w-40">
                <label class="block text-[10px] font-semibold text-[#808897] uppercase tracking-wider mb-1">Cari</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[#C1C7CF]" style="font-size:15px">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama atau email..."
                        class="w-full h-8 bg-[#F8F9FB] border border-[#DFE1E6] rounded-lg pl-8 pr-3 text-[12px] text-[#1A1B25] placeholder-[#C1C7CF] focus:border-[#6B39F4] focus:ring-1 focus:ring-[#6B39F4]/20 outline-none transition-all">
                </div>
            </div>

            {{-- Role Dropdown --}}
            <div class="w-36" x-data="{
                open: false,
                selected: '{{ $selectedRole }}',
                roles: @js($roleItems),
                get currentLabel() {
                    return this.roles.find(r => r.name === this.selected)?.label ?? 'Semua Role';
                }
            }">
                <label class="block text-[10px] font-semibold text-[#808897] uppercase tracking-wider mb-1">Role</label>
                <div class="relative">
                    <button type="button" @click="open = !open"
                        class="w-full h-8 flex items-center justify-between bg-[#F8F9FB] border border-[#DFE1E6] rounded-lg px-3 text-[12px] text-[#353849] outline-none transition-all">
                        <span x-text="currentLabel" class="truncate"></span>
                        <span class="material-symbols-outlined text-[#C1C7CF] shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" style="font-size:15px">expand_more</span>
                    </button>
                    <input type="hidden" name="role" :value="selected">
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute left-0 top-full mt-1 w-full bg-white border border-[#DFE1E6] rounded-lg shadow-lg z-50 py-1 max-h-48 overflow-y-auto">
                        <template x-for="r in roles" :key="r.name">
                            <button type="button" @click="selected = r.name; open = false"
                                class="w-full text-left px-3 py-1.5 text-[11px] hover:bg-[#F8F9FB] transition-colors"
                                :class="selected === r.name ? 'text-[#6B39F4] font-semibold' : 'text-[#666D80]'">
                                <span x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Limit Dropdown --}}
            <div class="w-28" x-data="{
                open: false,
                selected: '{{ $selectedLimit }}',
                options: [10, 25, 50, 100],
                select(val) {
                    this.selected = String(val);
                    localStorage.setItem('um_per_page', String(val));
                }
            }">
                <label class="block text-[10px] font-semibold text-[#808897] uppercase tracking-wider mb-1">Limit</label>
                <div class="relative">
                    <button type="button" @click="open = !open"
                        class="w-full h-8 flex items-center justify-between bg-[#F8F9FB] border border-[#DFE1E6] rounded-lg px-3 text-[12px] text-[#353849] outline-none transition-all">
                        <span x-text="selected + ' baris'"></span>
                        <span class="material-symbols-outlined text-[#C1C7CF] shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" style="font-size:15px">expand_more</span>
                    </button>
                    <input type="hidden" name="per_page" :value="selected">
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute left-0 top-full mt-1 w-full bg-white border border-[#DFE1E6] rounded-lg shadow-lg z-50 py-1">
                        <template x-for="opt in options" :key="opt">
                            <button type="button" @click="select(opt); open = false"
                                class="w-full text-left px-3 py-1.5 text-[11px] hover:bg-[#F8F9FB] transition-colors"
                                :class="selected == opt ? 'text-[#6B39F4] font-semibold' : 'text-[#666D80]'">
                                <span x-text="opt + ' baris'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Filter Button --}}
            <div>
                <button type="submit"
                    class="h-8 bg-[#6B39F4] hover:bg-[#5B2FD9] text-white text-[11px] font-medium px-4 rounded-lg transition-all">
                    Filter
                </button>
            </div>

        </div>
    </form>
</div>