<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Role — Manajemen Praktikum">

{{-- ════════════════════════
     PAGE HEADER
════════════════════════ --}}
<div class="mp-page-header" style="flex-shrink:0;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Role Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Assign atau cabut role asprak & koordinator per praktikum · {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select" style="min-width:240px;">
                @foreach($praktikumList as $prak)
                <option value="{{ $prak->id }}" {{ $prak->id == $praktikumId ? 'selected' : '' }}>
                    {{ $prak->nama }}
                    @if($prak->kode) [{{ $prak->kode }}] @endif
                    · {{ $prak->semester }} {{ $prak->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mp-alert success flex-shrink-0">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mp-alert warning flex-shrink-0">{{ session('error') }}</div>
@endif

@if($praktikumList->isEmpty())
{{-- Empty state --}}
<div class="mp-card flex-shrink-0">
    <div style="padding:56px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada praktikum yang tersedia.</div>
    </div>
</div>

@else

{{-- Info strip praktikum terpilih --}}
@if($praktikum)
<div style="background:#fff;border:1px solid #DFE1E7;border-radius:11px;padding:12px 16px;display:flex;align-items:center;gap:16px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);">
    <div style="flex:1;min-width:0;">
        <div style="font-size:13px;font-weight:700;color:#0D0D12;">{{ $praktikum->nama }}</div>
        <div style="font-size:11px;color:#A4ABB8;margin-top:1px;">{{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</div>
    </div>
    <div style="display:flex;gap:16px;flex-shrink:0;">
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Dosen</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;">{{ $praktikum->dosen?->name ?? '—' }}</div>
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Koordinator</div>
            @if($praktikum->koordinator)
            <div style="font-size:12px;font-weight:600;color:#10B981;">{{ $praktikum->koordinator->name }}</div>
            @else
            <div style="font-size:12px;font-weight:600;color:#EF4444;">Belum ditunjuk</div>
            @endif
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Anggota</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;">{{ $anggota->count() }} orang</div>
        </div>
    </div>
</div>
@endif

{{-- Main layout: tabel kiri + form assign kanan --}}
<div style="display:flex;gap:14px;flex:1;min-height:0;">

    {{-- ══ TABEL ANGGOTA ══════════════════════════════════════════════════ --}}
    <div style="flex:1;min-width:0;display:flex;flex-direction:column;overflow:hidden;">

        {{-- Toolbar --}}
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:12px 16px;margin-bottom:10px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:13px;font-weight:700;color:#0D0D12;">Anggota Terdaftar</div>
                <div style="font-size:11px;color:#A4ABB8;margin-top:1px;">
                    @php
                        $asprakCount = $anggota->where('role','asprak')->count();
                        $koorCount   = $anggota->where('role','koor')->count();
                    @endphp
                    {{ $asprakCount }} asprak · {{ $koorCount }} koordinator
                </div>
            </div>
            <div style="display:flex;gap:6px;">
                <span class="mp-badge success sm"><span class="dot"></span>{{ $asprakCount }} Asprak</span>
                <span class="mp-badge navy sm"><span class="dot"></span>{{ $koorCount }} Koor</span>
            </div>
        </div>

        {{-- Tabel --}}
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;flex-direction:column;flex:1;min-height:0;">
            <div style="overflow-y:auto;flex:1;">
                <table style="width:100%;border-collapse:collapse;min-width:420px;">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr style="border-bottom:1px solid #DFE1E7;background:#FAFAFA;">
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">Pengguna</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:110px;">Role</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Bergabung</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $i => $a)
                        @php
                            $np  = explode(' ', $a->user?->name ?? 'U');
                            $ini = strtoupper(substr($np[0]??'U',0,1).substr($np[1]??$np[0]??'U',0,1));
                            $avc = $a->role === 'koor' ? 'violet' : 'green';
                        @endphp
                        <tr style="border-bottom:1px solid #F8F9FB;transition:background .1s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:13px 16px;font-size:11px;color:#C8CAD4;text-align:center;">{{ $i + 1 }}</td>
                            <td style="padding:13px 16px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="mp-av {{ $avc }}" style="width:30px;height:30px;font-size:11px;flex-shrink:0;">{{ $ini }}</div>
                                    <div style="min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;">{{ $a->user?->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:#A4ABB8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;">{{ $a->user?->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                @if($a->role === 'koor')
                                <span class="mp-badge navy sm"><span class="dot"></span>Koor</span>
                                @else
                                <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
                                @endif
                            </td>
                            <td style="padding:13px 16px;text-align:center;font-size:11px;color:#A4ABB8;white-space:nowrap;">
                                {{ $a->created_at?->locale('id')->isoFormat('D MMM YY') ?? '—' }}
                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                <form method="POST"
                                      action="{{ route('eoffice.manprak.admin.kelola-role.revoke', $a->id) }}"
                                      onsubmit="return confirm('Cabut role {{ $a->role }} dari {{ addslashes($a->user?->name ?? '') }}?\n\nJika user tidak terdaftar di praktikum lain, role Spatie akan ikut dicabut.')"
                                      style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="font-size:11px;font-weight:600;padding:5px 10px;border-radius:6px;border:1px solid #FADAE1;background:#FFF5F6;color:#DF1C41;cursor:pointer;transition:all .12s;white-space:nowrap;"
                                            onmouseover="this.style.background='#FADAE1'" onmouseout="this.style.background='#FFF5F6'">
                                        Cabut
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:56px;text-align:center;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada asprak atau koordinator di praktikum ini.</div>
                                <div style="font-size:12px;color:#A4ABB8;margin-top:4px;">Gunakan form Assign Role di sebelah kanan untuk menambahkan.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- /Tabel --}}

    {{-- ══ SIDEBAR: ASSIGN ROLE ═══════════════════════════════════════════ --}}
    <div style="width:272px;flex-shrink:0;display:flex;flex-direction:column;gap:12px;">

        {{-- Form Assign --}}
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="padding:12px 16px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;">
                <div style="font-size:12px;font-weight:700;color:#0D0D12;text-transform:uppercase;letter-spacing:.06em;">Assign Role Baru</div>
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.admin.kelola-role.assign') }}"
                  style="padding:16px;display:flex;flex-direction:column;gap:14px;">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

                {{-- Pilih User --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Pilih User</label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: '{{ old('user_id') }}',
                        options: {{ json_encode($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])) }},
                        get filteredOptions() {
                            if (this.search === '') return this.options.slice(0, 50);
                            const lowerSearch = this.search.toLowerCase();
                            return this.options.filter(o => o.name.toLowerCase().includes(lowerSearch)).slice(0, 50);
                        },
                        selectOption(opt) {
                            this.selected = opt.id;
                            this.search = opt.name;
                            this.open = false;
                        },
                        init() {
                            if (this.selected) {
                                const opt = this.options.find(o => o.id == this.selected);
                                if (opt) this.search = opt.name;
                            }
                        }
                    }" class="relative">
                        <input type="hidden" name="user_id" :value="selected">
                        <div class="relative">
                            <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                   placeholder="Ketik nama mahasiswa..." class="mp-input" autocomplete="off"
                                   @input="selected = ''" style="width:100%; padding-right: 30px;">
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#A4ABB8] pointer-events-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="margin-top:-7px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </div>
                        
                        <div x-show="open" style="display:none; position:absolute; z-index:50; width:100%; background:#fff; border:1px solid #DFE1E7; border-radius:6px; margin-top:4px; max-height:192px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                            <template x-for="opt in filteredOptions" :key="opt.id">
                                <div @click="selectOption(opt)"
                                     style="padding:8px 12px; cursor:pointer; font-size:12px; color:#0D0D12; border-bottom:1px solid #F3F4F6;"
                                     onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='transparent'"
                                     x-text="opt.name">
                                </div>
                            </template>
                            <div x-show="filteredOptions.length === 0" style="padding:12px; text-align:center; font-size:11px; color:#A4ABB8;">
                                Tidak ada nama yang ditampilkan
                            </div>
                        </div>
                    </div>
                    @error('user_id')
                    <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Pilih Role --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Role</label>
                    <div style="display:flex;gap:8px;">
                        <label style="flex:1;display:flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #DFE1E7;border-radius:8px;cursor:pointer;transition:all .15s;"
                               id="lbl-asprak"
                               onmouseover="this.style.borderColor='#0B266E'" onmouseout="if(!document.getElementById('r-asprak').checked)this.style.borderColor='#DFE1E7'">
                            <input type="radio" name="role" value="asprak" id="r-asprak"
                                   {{ old('role','asprak') === 'asprak' ? 'checked' : '' }}
                                   style="accent-color:#0B266E;"
                                   onchange="syncRoleLabels()">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:#0D0D12;">Asprak</div>
                                <div style="font-size:10px;color:#A4ABB8;">Asisten Praktikum</div>
                            </div>
                        </label>
                        <label style="flex:1;display:flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #DFE1E7;border-radius:8px;cursor:pointer;transition:all .15s;"
                               id="lbl-koor"
                               onmouseover="this.style.borderColor='#0B266E'" onmouseout="if(!document.getElementById('r-koor').checked)this.style.borderColor='#DFE1E7'">
                            <input type="radio" name="role" value="koor" id="r-koor"
                                   {{ old('role') === 'koor' ? 'checked' : '' }}
                                   style="accent-color:#0B266E;"
                                   onchange="syncRoleLabels()">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:#0D0D12;">Koor</div>
                                <div style="font-size:10px;color:#A4ABB8;">Koordinator</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                    <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="mp-btn primary md" style="width:100%;justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Assign Role
                </button>
            </form>
        </div>

        {{-- Penjelasan alur cabut role --}}
        <div style="background:#FFFBF0;border:1px solid #FDE68A;border-radius:13px;padding:14px 16px;">
            <div style="font-size:11px;font-weight:700;color:#92400E;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
                ⚠️ Catatan Penting
            </div>
            <div style="font-size:12px;color:#78350F;line-height:1.6;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span><strong>Cabut</strong> akan menghapus user dari daftar praktikum ini.</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span>Jika user tidak terdaftar di praktikum lain, <strong>role Spatie</strong> (<code style="background:#FEF3C7;padding:0 3px;border-radius:3px;font-size:10px;">asprak</code> / <code style="background:#FEF3C7;padding:0 3px;border-radius:3px;font-size:10px;">koor_prak</code>) ikut dicabut dari sistem.</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span>Jika masih terdaftar di praktikum lain, role <strong>tidak</strong> dicabut.</span>
                </div>
            </div>
        </div>

    </div>
    {{-- /Sidebar --}}

</div>

@endif

<script>
function syncRoleLabels() {
    const asprak = document.getElementById('r-asprak');
    const koor   = document.getElementById('r-koor');
    const lblA   = document.getElementById('lbl-asprak');
    const lblK   = document.getElementById('lbl-koor');
    lblA.style.borderColor = asprak.checked ? '#0B266E' : '#DFE1E7';
    lblA.style.background  = asprak.checked ? 'rgba(11,38,110,0.04)' : '';
    lblK.style.borderColor = koor.checked   ? '#0B266E' : '#DFE1E7';
    lblK.style.background  = koor.checked   ? 'rgba(11,38,110,0.04)' : '';
}
document.addEventListener('DOMContentLoaded', syncRoleLabels);
</script>

</x-eoffice::manajemen-praktikum.layout>