<div class="bg-white border border-[#DEE2E6] rounded-2xl p-5 mb-6 shadow-sm">
    <form method="GET" action="{{ route('superadmin.audit-logs') }}" id="auditFilterForm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            
            {{-- Module Filter --}}
            <div class="md:col-span-3" x-data="{ 
                open: false, 
                selected: '{{ request('module', '') }}',
                modules: { '': 'Semua Modul', @foreach($modules as $mod) '{{ $mod }}': '{{ strtoupper(str_replace('_', ' ', $mod)) }}', @endforeach }
            }">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Modul System</label>
                <div class="relative">
                    <input type="hidden" name="module" :value="selected">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                        <span x-text="modules[selected]"></span>
                        <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                    </button>
                    <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                        <template x-for="(label, value) in modules" :key="value">
                            <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Action Filter --}}
            <div class="md:col-span-2" x-data="{ 
                open: false, 
                selected: '{{ request('action', '') }}',
                actions: { '': 'Semua Action', @foreach($actions as $act) '{{ $act }}': '{{ strtoupper($act) }}', @endforeach }
            }">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Tipe Aksi</label>
                <div class="relative">
                    <input type="hidden" name="action" :value="selected">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                        <span x-text="actions[selected]"></span>
                        <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                    </button>
                    <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                        <template x-for="(label, value) in actions" :key="value">
                            <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- User Filter --}}
            <div class="md:col-span-3" x-data="{ 
                open: false, 
                selected: '{{ request('user_id', '') }}',
                users: { '': 'Semua User', @foreach($users as $u) '{{ $u->id }}': '{{ $u->name }}', @endforeach }
            }">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Pelaku (User)</label>
                <div class="relative">
                    <input type="hidden" name="user_id" :value="selected">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                        <span x-text="users[selected]" class="truncate mr-2"></span>
                        <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                    </button>
                    <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1 max-h-60 overflow-y-auto scrollbar-thin">
                        <template x-for="(label, value) in users" :key="value">
                            <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Dates --}}
            <div class="md:col-span-2">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-[#1A1C1E] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs font-medium">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-[#1A1C1E] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs font-medium">
            </div>

            {{-- Search --}}
            <div class="md:col-span-9">
                <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Pencarian Deskripsi</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#ADB5BD] group-focus-within:text-[#5E53F4] transition-colors" style="font-size:18px">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kata kunci aktivitas..."
                        class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl pl-11 pr-4 py-2.5 text-[#1A1C1E] placeholder-[#ADB5BD] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs">
                </div>
            </div>

            <div class="md:col-span-3 flex items-end gap-2 pb-0.5">
                <button type="submit" class="flex-1 bg-[#5E53F4] hover:bg-[#4e44e0] active:scale-[0.98] text-white font-medium py-2.5 px-4 rounded-xl transition-all text-xs shadow-sm shadow-[#5E53F4]/20 uppercase tracking-widest">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>