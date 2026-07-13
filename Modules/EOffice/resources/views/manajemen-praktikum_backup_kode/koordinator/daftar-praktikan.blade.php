<x-eoffice::manajemen-praktikum.layout pageTitle="Data Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Data Praktikan</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else

@if ($errors->any())
<div class="mp-flash mp-flash-error" style="border-radius:10px;border:1px solid #DF1C41;margin-bottom:16px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div style="flex:1;">
        <div style="font-weight:700;">Gagal:</div>
        <ul style="margin:4px 0 0;padding-left:20px;list-style-type:disc;">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

@if (session('success'))
<div class="mp-flash mp-flash-success" style="border-radius:10px;border:1px solid #10B981;margin-bottom:16px;padding:12px 16px;display:flex;gap:10px;background:#ECFDF5;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <div style="flex:1;">
        <div style="font-size:13px;color:#065F46;font-weight:500;">{{ session('success') }}</div>
    </div>
</div>
@endif

<div x-data="manajemenKelompok()">
    
    {{-- TOMBOL UTAMA KELOLA KELOMPOK --}}
    <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
        <button @click="openMainModal()" style="display:inline-flex; align-items:center; gap:8px; background:#0B266E; color:#FFF; padding:10px 18px; font-size:13px; font-weight:600; border-radius:6px; border:none; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#081d54'" onmouseout="this.style.background='#0B266E'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Kelola Kelompok & Shift
        </button>
    </div>

    {{-- ──────────────────────────────────────────────────── --}}
    {{-- SECTION: Tabel Praktikan Terdaftar (Read-Only)      --}}
    {{-- ──────────────────────────────────────────────────── --}}
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Praktikan Terdaftar</span>
        <span class="sec-rule"></span>
    </div>

    <div class="mp-card flex-shrink-0">
        <div class="mp-card-header">
            <span class="mp-card-title">
                Praktikan Terdaftar
                <span class="mp-badge neutral sm" style="margin-left:6px;">{{ $praktikans->total() }}</span>
            </span>
            <div class="right" style="gap:8px;display:flex;align-items:center;flex-wrap:wrap;">
                {{-- Export CSV --}}
                <a href="{{ route('eoffice.manprak.koor.praktikan.export') }}"
                   class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </a>
                {{-- Search --}}
                <form method="GET" style="display:flex;gap:4px;">
                    <input name="search" value="{{ $search }}" placeholder="Cari nama / NIM..." class="mp-input" style="width:180px;">
                    <button type="submit" class="mp-btn primary sm">Cari</button>
                </form>
            </div>
        </div>

        <div style="overflow-x:auto;">
            @php
                $shiftRowspan = [];
                $kelompokRowspan = [];
                
                $items = $praktikans->items();
                $totalItems = count($items);

                // Calculate rowspan for shift
                $i = 0;
                while ($i < $totalItems) {
                    $val = $items[$i]->shift;
                    if (empty($val)) {
                        $shiftRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                        $count++;
                    }
                    $shiftRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $shiftRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }

                // Calculate rowspan for kelompok
                $i = 0;
                while ($i < $totalItems) {
                    $valK = $items[$i]->kelompok;
                    $valS = $items[$i]->shift;
                    if (empty($valK)) {
                        $kelompokRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while (
                        $i + $count < $totalItems && 
                        $items[$i + $count]->kelompok === $valK && 
                        $items[$i + $count]->shift === $valS
                    ) {
                        $count++;
                    }
                    $kelompokRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $kelompokRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }
            @endphp
            <table class="mp-table" style="min-width:900px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 16px;width:40px;">#</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:150px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:150px;border-right:1px solid #DFE1E7;">Shift</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($praktikans as $idx => $p)
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7; transition: background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;">
                            {{ $praktikans->firstItem() + $idx }}
                        </td>
                        <td style="padding:12px 16px;">
                            <div class="flex items-center gap-[10px]">
                                <div class="mp-av yellow" style="width:28px;height:28px;font-size:11px;">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                                <div>
                                    <div style="font-weight:600;color:#0D0D12;font-size:13px;">{{ $p->user?->name ?? '-' }}</div>
                                    <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                            {{ $p->user?->student?->student_number ?? '-' }}
                        </td>
                        
                        {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                        @if(isset($kelompokRowspan[$idx]) && $kelompokRowspan[$idx] > 0)
                            <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                @if($p->kelompok)
                                    {{ $p->kelompok }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif
                        
                        {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                        @if(isset($shiftRowspan[$idx]) && $shiftRowspan[$idx] > 0)
                            <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                @if($p->shift)
                                    {{ $p->shift }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif

                        <td style="padding:12px 16px;">
                            <span class="mp-badge success sm"><span class="dot"></span>{{ $p->status ?? 'terdaftar' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div style="padding:48px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan yang terdaftar.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($praktikans->hasPages())
        <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $praktikans->links() }}</div>
        @endif
    </div>

    {{-- ──────────────────────────────────────────────────── --}}
    {{-- MODAL UTAMA: KELOLA KELOMPOK & SHIFT                  --}}
    {{-- ──────────────────────────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="showMainModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5);" x-transition>
            <div style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px;">
                <div @click.outside="closeMainModal()" style="background:#FFF; width:100%; max-width:850px; border-radius:12px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
                
                {{-- Modal Header (Sticky) --}}
                <div style="padding:20px 24px; border-bottom:1px solid #DFE1E7; background:#FFF; border-radius:12px 12px 0 0; position:sticky; top:0; z-index:10; box-shadow:0 2px 5px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div style="font-weight:700; font-size:16px;">Kelola Kelompok & Shift</div>
                        <button @click="closeMainModal()" style="background:none; border:none; cursor:pointer; color:#666D80;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('eoffice.manprak.koor.praktikan.settings') }}" method="POST" onsubmit="return confirm('PENTING: Menyimpan pengaturan baru akan MENGHAPUS manual plotting sebelumnya dan mendistribusikan ulang (reset) semua anggota. Yakin ingin melanjutkan?');">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                        <div style="display:flex; gap:16px; align-items:flex-end;">
                            <div style="flex:1;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jumlah Kelompok</label>
                                <input type="number" name="jumlah_kelompok" value="{{ $praktikum->jumlah_kelompok ?? 1 }}" min="1" class="mp-input" style="width:100%; height:38px;">
                            </div>
                            <div style="flex:1;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jumlah Shift</label>
                                <input type="number" name="jumlah_shift" value="{{ $praktikum->jumlah_shift ?? 1 }}" min="1" class="mp-input" style="width:100%; height:38px;">
                            </div>
                            <div style="flex:1.5;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Metode Pembagian</label>
                                <select name="method" required class="mp-input" style="width:100%; height:38px; appearance:auto; background:#FFF; cursor:pointer;">
                                    <option value="urutan_sistem">Berurutan</option>
                                    <option value="acak">Acak</option>
                                </select>
                            </div>
                            <div style="flex:1.5;">
                                <button type="submit" class="mp-btn primary md" style="width:100%; background:#0B266E; height:38px; display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div style="padding:24px; overflow-y:auto; flex:1;">
                    
                    {{-- Bottom Section: Daftar Kelompok (Manual Plot) --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:16px;">
                        <div>
                            <div style="font-weight:700; font-size:15px; color:#0D0D12;">Assign Manual Anggota Kelompok</div>
                            <div style="font-size:12px; color:#666D80; margin-top:2px;">Klik Edit (Pensil) pada salah satu kelompok untuk mengisi/merubah anggota.</div>
                        </div>
                        <div style="font-size:12px; font-weight:600; padding:6px 12px; background:#FEF2F2; color:#DC2626; border-radius:6px; border:1px solid #FECACA;" x-show="unassignedCount > 0">
                            <span x-text="unassignedCount"></span> Praktikan belum dapat kelompok!
                        </div>
                        <div style="font-size:12px; font-weight:600; padding:6px 12px; background:#ECFDF5; color:#059669; border-radius:6px; border:1px solid #A7F3D0;" x-show="unassignedCount === 0">
                            Semua praktikan sudah terdistribusi
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
                        @for($i=1; $i<=($praktikum->jumlah_kelompok ?? 0); $i++)
                            @php 
                                $groupsPerShift = max(1, ($praktikum->jumlah_kelompok ?? 1) / ($praktikum->jumlah_shift ?? 1));
                                $defShift = (int) ceil($i / $groupsPerShift);
                                if($defShift > ($praktikum->jumlah_shift ?? 1)) $defShift = $praktikum->jumlah_shift;
                                $jmlAnggota = $praktikansSemua->where('kelompok', $i)->count();
                            @endphp
                            <div style="border:1px solid #DFE1E7; border-radius:10px; padding:16px; background:#FFF; display:flex; justify-content:space-between; align-items:center; transition:border 0.2s;" onmouseover="this.style.borderColor='#A4ABB8'" onmouseout="this.style.borderColor='#DFE1E7'">
                                <div>
                                    <div style="font-weight:700; color:#0D0D12; font-size:14px; margin-bottom:4px;">Kelompok {{ $i }}</div>
                                    <div style="font-size:12px; color:#666D80;">Shift {{ $defShift }} <span style="margin:0 4px;">•</span> <strong>{{ $jmlAnggota }}</strong> Anggota</div>
                                </div>
                                <button @click="openEditModal('{{ $i }}', '{{ $defShift }}')" style="background:#F0F1F4; border:none; padding:8px; border-radius:6px; cursor:pointer; color:#0B266E;" title="Edit Anggota Kelompok {{ $i }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                            </div>
                        @endfor
                    </div>
                    
                    {{-- Tombol Kosongkan Plotting diletakkan di paling bawah --}}
                    <div style="margin-top:40px; padding-top:24px; border-top:1px dashed #DFE1E7;">
                        <form action="{{ route('eoffice.manprak.koor.praktikan.reset-plot') }}" method="POST" onsubmit="return confirm('YAKIN INGIN MENGOSONGKAN SELURUH ANGGOTA DARI SEMUA KELOMPOK? Data mahasiswa tidak akan hilang, hanya pembagian kelompoknya saja yang direset menjadi kosong.');">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                            <button type="submit" style="width:100%; background:#FFF0F0; color:#D32F2F; border:1px solid #FFCDD2; padding:12px 0; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background 0.2s;" onmouseover="this.style.background='#FFE4E4'" onmouseout="this.style.background='#FFF0F0'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                Hapus Kelompok & Shift
                            </button>
                        </form>
                    </div>

                </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ──────────────────────────────────────────────────── --}}
    {{-- MODAL SUB: EDIT ANGGOTA KELOMPOK                      --}}
    {{-- ──────────────────────────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="showEditModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6);" x-transition>
            <div style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px;">
                <div @click.outside="closeEditModal()" style="background:#FFF; width:100%; max-width:600px; border-radius:12px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                
                {{-- Header --}}
                <div style="padding:16px 24px; border-bottom:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:center; background:#F9FAFB; border-radius:12px 12px 0 0;">
                    <div>
                        <div style="font-weight:700; font-size:16px; color:#0D0D12;" x-text="'Pilih Anggota Kelompok ' + activeKelompok"></div>
                        <div style="font-size:12px; color:#666D80; margin-top:2px;" x-text="'Shift ' + activeShift"></div>
                    </div>
                    <button @click="closeEditModal()" style="background:none; border:none; cursor:pointer; color:#666D80;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                {{-- Filter --}}
                <div style="padding:16px 24px; border-bottom:1px solid #DFE1E7;">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Cari Anggota (Hanya yg kosong atau yg sdh masuk kelompok ini)</label>
                    <input type="text" x-model="searchQuery" placeholder="Ketik nama atau NIM..." class="mp-input" style="width:100%;">
                </div>
                
                {{-- Daftar Mahasiswa --}}
                <div style="flex:1; overflow-y:auto; padding:8px 24px;">
                    <div style="font-size:12px; color:#A4ABB8; margin:8px 0; font-weight:600;">DAFTAR MAHASISWA (<span x-text="filteredPraktikans.length"></span>)</div>
                    <template x-for="p in filteredPraktikans" :key="p.id">
                        <label style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#F0F1F4'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" :value="p.id" x-model="selectedMembers" style="width:18px; height:18px; cursor:pointer;">
                            <div class="mp-av yellow" style="width:32px;height:32px;font-size:12px;" x-text="p.inisial"></div>
                            <div style="flex:1;">
                                <div style="font-weight:600; color:#0D0D12; font-size:13px;" x-text="p.nama"></div>
                                <div style="font-size:12px; color:#666D80; font-family:monospace;" x-text="p.nim"></div>
                            </div>
                            <div x-show="p.kel === activeKelompok" style="font-size:11px; background:#ECFDF5; color:#059669; padding:2px 8px; border-radius:4px; font-weight:600;">Anggota</div>
                            <div x-show="p.kel === null || p.kel === ''" style="font-size:11px; background:#FEF2F2; color:#DC2626; padding:2px 8px; border-radius:4px; font-weight:600;">Kosong</div>
                        </label>
                    </template>
                    <div x-show="filteredPraktikans.length === 0" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Semua mahasiswa sudah dimasukkan ke kelompok lain, atau pencarian tidak ditemukan.
                    </div>
                </div>

                {{-- Footer --}}
                <div style="padding:16px 24px; border-top:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:center; background:#F9FAFB; border-radius:0 0 12px 12px;">
                    <div style="font-size:13px; font-weight:600; color:#0D0D12;">
                        Terpilih: <span style="color:#0B266E;" x-text="selectedMembers.length"></span> orang
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button @click="closeEditModal()" style="background:#F0F1F4; border:1px solid #DFE1E7; color:#353849; padding:8px 16px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#E4E6EB'" onmouseout="this.style.background='#F0F1F4'">Batal</button>
                        <button @click="saveMembers()" :disabled="isSaving" style="background:#0B266E; border:none; color:#FFF; padding:8px 16px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; min-width:90px; transition:background 0.2s;" onmouseover="this.style.background='#081d54'" onmouseout="this.style.background='#0B266E'">
                            <span x-show="!isSaving">Simpan</span>
                            <span x-show="isSaving">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('manajemenKelompok', () => ({
            showMainModal: false,
            showEditModal: false,
            activeKelompok: '',
            activeShift: '',
            searchQuery: '',
            isSaving: false,
            selectedMembers: [],
            allPraktikans: @json($praktikansSemuaJSON),
            
            get unassignedCount() {
                return this.allPraktikans.filter(p => p.kel === null || p.kel === '').length;
            },

            get filteredPraktikans() {
                let q = this.searchQuery.toLowerCase();
                // Tampilkan hanya yg kosong ATAU miliki kelompok ini
                return this.allPraktikans.filter(p => {
                    let isEligible = (p.kel === this.activeKelompok) || (p.kel === null || p.kel === '');
                    if(!isEligible) return false;
                    
                    if(q === '') return true;
                    return p.nama.toLowerCase().includes(q) || p.nim.toLowerCase().includes(q);
                });
            },

            openMainModal() {
                this.showMainModal = true;
            },
            closeMainModal() {
                this.showMainModal = false;
            },
            
            openEditModal(kel, shift) {
                this.activeKelompok = kel.toString();
                this.activeShift = shift.toString();
                this.searchQuery = '';
                
                // Pre-fill checkboxes based on current members of this group
                this.selectedMembers = this.allPraktikans
                    .filter(p => p.kel === this.activeKelompok)
                    .map(p => p.id);
                    
                this.showEditModal = true;
            },
            closeEditModal() {
                this.showEditModal = false;
            },
            
            saveMembers() {
                this.isSaving = true;
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('praktikum_id', '{{ $praktikum->id }}');
                formData.append('kelompok', this.activeKelompok);
                formData.append('shift', this.activeShift);
                
                this.selectedMembers.forEach(id => {
                    formData.append('members[]', id);
                });
                
                fetch('{{ route('eoffice.manprak.koor.praktikan.save-group-members') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan. Pastikan data valid.');
                        this.isSaving = false;
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan jaringan.');
                    this.isSaving = false;
                });
            }
        }))
    })
</script>

@endif

</x-eoffice::manajemen-praktikum.layout>
