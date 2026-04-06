<div class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            
            {{-- Search Input --}}
            <div class="md:col-span-4">
                <label class="block text-slate-700 text-[10px] font-semibold uppercase tracking-tight mb-1.5 ml-1">Pencarian</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#5E53F4] transition-colors" style="font-size:18px">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama atau email..."
                        class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 placeholder-slate-400 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs shadow-sm">
                </div>
            </div>

            {{-- Custom Dropdown Role (Alpine.js) --}}
            <div class="md:col-span-3" x-data="{ 
                open: false, 
                selected: '{{ request('role', 'all') }}',
                roles: [
                    { name: 'all', label: 'Semua Role' },
                    @foreach($roles as $role)
                        { name: '{{ $role->name }}', label: '{{ ucfirst($role->name) }}' },
                    @endforeach
                ],
                get currentLabel() {
                    return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role';
                }
            }">
                <label class="block text-slate-700 text-[10px] font-semibold uppercase tracking-tight mb-1.5 ml-1">Filter Role</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                        class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all shadow-sm outline-none">
                        <span x-text="currentLabel"></span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                    </button>
                    
                    <input type="hidden" name="role" :value="selected">

                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute left-0 top-full mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                        <template x-for="r in roles" :key="r.name">
                            <button type="button" @click="selected = r.name; open = false" 
                                class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                :class="selected === r.name ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                <span x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Custom Dropdown Limit (Alpine.js) --}}
            <div class="md:col-span-2" x-data="{ 
                open: false, 
                selected: '{{ request('per_page', 10) }}',
                options: [10, 25, 50, 100],
                get currentLabel() { return this.selected + ' Baris'; }
            }">
                <label class="block text-slate-700 text-[10px] font-semibold uppercase tracking-tight mb-1.5 ml-1">Limit</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                        class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all shadow-sm outline-none">
                        <span x-text="currentLabel"></span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                    </button>

                    <input type="hidden" name="per_page" :value="selected">

                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute left-0 top-full mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                        <template x-for="opt in options" :key="opt">
                            <button type="button" @click="selected = opt; open = false" 
                                class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                :class="selected == opt ? 'text-[#5E53F4] font-semibold bg-[#5E53F4]/5' : 'text-slate-600'">
                                <span x-text="opt + ' Baris'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="md:col-span-3 flex items-end gap-2 pb-0.5">
                <button type="submit" class="flex-1 bg-[#5E53F4] hover:bg-[#4e44e0] active:scale-[0.98] text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-xs shadow-sm shadow-[#5E53F4]/20 uppercase tracking-widest">
                    Filter
                </button>
                <a href="{{ route('superadmin.users.index') }}" 
                    class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl transition-all flex items-center justify-center">
                    <span class="material-symbols-outlined" style="font-size:20px">refresh</span>
                </a>
            </div>
        </div>
    </form>
</div>