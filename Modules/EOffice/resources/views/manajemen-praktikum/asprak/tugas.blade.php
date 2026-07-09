<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Tugas Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">Buat tugas dan nilai pengumpulan mahasiswa · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn primary md" style="text-decoration:none;display:flex;align-items:center;gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Tugas</span>
    <span class="sec-rule"></span>
</div>

@forelse($tugasList ?? [] as $tugas)
@php
    $dl    = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
    $lewat = $dl && now()->gt($dl);
    $totalKumpul = $tugas->pengumpulan_count ?? 0;
    $acc         = $tugas->pengumpulan_acc_count ?? 0;
    $revisi      = $tugas->pengumpulan_revisi_count ?? 0;
    $belumDicek  = $totalKumpul - $acc - $revisi;
@endphp
<div class="mp-card" style="padding:24px;margin-bottom:4px;width:100%;min-width:0;overflow:hidden;"
     onmouseover="this.style.boxShadow='0 4px 16px rgba(11,38,110,.08)';this.style.borderColor='#B7C2DE';"
     onmouseout="this.style.boxShadow='';this.style.borderColor='#DFE1E7';"
     x-data="{ open: false }">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;">
        {{-- Left Content --}}
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
                <div style="font-size:15px;font-weight:700;color:#0D0D12;">{{ $tugas->judul }}</div>
                @if($lewat)
                <span class="mp-badge neutral sm">Berakhir</span>
                @else
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                @endif
                @if(!$tugas->is_published)
                <span class="mp-badge warning sm">Draft</span>
                @endif
            </div>

            <div style="font-size:13px;color:#666D80;margin-bottom:10px;">
                📚 Modul: <span style="font-weight:600;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</span>
                @if($dl)
                <br>⏰ Deadline AC: <span style="font-weight:600;color:{{ $lewat ? '#999' : '#353849' }};">{{ $dl->locale('id')->format('d M Y, H:i') }}</span>
                @endif
                @if($tugas->deadline_acc)
                <br>⏰ Deadline ACC: <span style="font-weight:600;color:{{ now()->gt($tugas->deadline_acc) ? '#DF1C41' : '#353849' }};">{{ \Carbon\Carbon::parse($tugas->deadline_acc)->locale('id')->format('d M Y, H:i') }}</span>
                @endif
            </div>

            @if($tugas->deskripsi)
            <div style="font-size:12px;color:#666D80;line-height:1.5;margin-bottom:10px;">{{ Str::limit($tugas->deskripsi, 150) }}</div>
            @endif

            {{-- File attachment badge --}}
            @if($tugas->file_path)
            <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($tugas->file_path, 'eoffice') }}"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:7px;background:#FADAE1;text-decoration:none;font-size:11px;font-weight:600;color:#95122B;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Lampiran PDF
            </a>
            @endif
        </div>

        {{-- Right: Stats + Actions --}}
        <div style="display:flex;flex-direction:column;gap:12px;align-items:flex-end;flex-shrink:0;">
            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:10px;">
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#F9FAFB;border:1px solid #E4E6EB;">
                    <div style="font-size:18px;font-weight:700;color:#0D0D12;">{{ $totalKumpul }}</div>
                    <div style="font-size:10px;font-weight:600;color:#666D80;margin-top:4px;">Dikumpul</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#FFFBF0;border:1px solid #FFE8C8;">
                    <div style="font-size:18px;font-weight:700;color:#D39C3D;">{{ max(0, $belumDicek) }}</div>
                    <div style="font-size:10px;font-weight:600;color:#854F0B;margin-top:4px;">Menunggu</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#FFF3F3;border:1px solid #FFD9E1;">
                    <div style="font-size:18px;font-weight:700;color:#DF1C41;">{{ $revisi }}</div>
                    <div style="font-size:10px;font-weight:600;color:#A51330;margin-top:4px;">Revisi</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#E8EEFF;border:1px solid #D4DBFF;">
                    <div style="font-size:18px;font-weight:700;color:#0B266E;">{{ $acc }}</div>
                    <div style="font-size:10px;font-weight:600;color:#185FA5;margin-top:4px;">ACC</div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="{{ route('eoffice.manprak.asprak.tugas.edit', $tugas->id) }}"
                   class="mp-btn secondary sm"
                   style="text-decoration:none;display:flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.destroy', $tugas->id) }}"
                      onsubmit="return confirm('Hapus tugas ini? Semua pengumpulan ikut terhapus.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="mp-btn destructive sm"
                            style="display:flex;align-items:center;gap:5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Pengumpulan Section --}}
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid #DFE1E7;">
        <button @click="open = !open" type="button"
                style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6366F1;cursor:pointer;padding:8px 0;border:none;background:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 :class="open ? 'rotate-90' : ''" style="transition:transform .2s;">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
            <span x-text="open ? 'Sembunyikan Pengumpulan' : 'Lihat Pengumpulan (' + {{ $totalKumpul }} + ')'"></span>
        </button>

        <div x-show="open" x-transition style="margin-top:16px;">
            @php
                $praktikans = $tugas->praktikans ?? collect();
                $pengumpulanMapped = $tugas->pengumpulan_mapped ?? collect();
                
                $shiftRowspan = [];
                $kelompokRowspan = [];
                
                $items = $praktikans->all();
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

                // Calculate rowspan for kelompok (must match same kelompok AND same shift)
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
            @if($praktikans->isEmpty())
            <div style="padding:40px;text-align:center;background:#F9FAFB;border-radius:8px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada praktikan terdaftar di praktikum ini.</div>
            </div>
            @else
            <div style="border:1px solid #DFE1E7;border-radius:10px;overflow:hidden;">
                <div style="overflow-x:auto;overflow-y:auto;width:100%;max-height:450px;">
                    <table class="mp-table" style="width:100%;border-collapse:collapse;min-width:1250px;font-size:13px;text-align:left;">
                        <thead>
                            <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;position:sticky;top:0;z-index:10;">
                                <th class="mp-th text-left" style="padding:12px 16px;width:40px;background:#F9FAFB;">#</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:200px;background:#F9FAFB;">Mahasiswa</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:120px;background:#F9FAFB;">NIM</th>
                                <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#F9FAFB;">Kelompok</th>
                                <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;background:#F9FAFB;">Shift</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:150px;background:#F9FAFB;">Dokumen</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:140px;background:#F9FAFB;">Waktu Submit</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:110px;background:#F9FAFB;">Nilai</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:90px;background:#F9FAFB;">Status</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:90px;background:#F9FAFB;">Kirim Revisi</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:150px;background:#F9FAFB;">Dokumen Revisi</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:140px;background:#F9FAFB;">Waktu Submit Revisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($praktikans as $idx => $pr)
                            @php
                                $peng = $pengumpulanMapped[$pr->id] ?? null;
                                $st = $peng ? ($peng->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                                $firstSub = $peng ? ($peng->riwayat->where('is_revision', false)->sortBy('created_at')->first() ?? $peng->riwayat->sortBy('created_at')->first()) : null;
                                $waktuSubmit = $firstSub ? $firstSub->created_at : ($peng ? $peng->created_at : null);
                                $latestRevision = $peng ? $peng->riwayat->where('is_revision', true)->sortByDesc('created_at')->first() : null;
                            @endphp
                            <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                                <td style="padding:12px 16px;vertical-align:middle;color:#808897;font-size:12px;">{{ $idx + 1 }}</td>
                                
                                {{-- Mahasiswa --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                        <div class="mp-av yellow" style="width:34px;height:34px;flex-shrink:0;">{{ strtoupper(substr($pr->user?->name ?? 'M', 0, 2)) }}</div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $pr->user?->name ?? '—' }}</div>
                                            <div style="font-size:11px;color:#666D80;">{{ $pr->user?->email }}</div>
                                            @if($peng && $peng->catatan)
                                            <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="{{ $peng->catatan }}">💬 Mhs: {{ Str::limit($peng->catatan, 20) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- NIM --}}
                                <td style="padding:12px 16px;vertical-align:middle;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                                    {{ $pr->user?->student?->student_number ?? '-' }}
                                </td>

                                {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                                @if($kelompokRowspan[$idx] > 0)
                                    <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        @if($pr->kelompok)
                                            {{ $pr->kelompok }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        @endif
                                    </td>
                                @endif
                                
                                {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                                @if($shiftRowspan[$idx] > 0)
                                    <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        @if($pr->shift)
                                            {{ $pr->shift }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Dokumen --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($peng && $firstSub && $firstSub->file_path)
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($firstSub->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="{{ basename($firstSub->file_path) }}">
                                            {{ Str::limit(basename($firstSub->file_path), 15) }}
                                        </a>
                                        @if($firstSub->catatan)
                                        <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="{{ $firstSub->catatan }}">💬 Mhs: {{ Str::limit($firstSub->catatan, 20) }}</div>
                                        @endif
                                        @if($peng->riwayat->isNotEmpty())
                                        <div x-data="{ openRiwayat: false }" style="position:relative;">
                                            <button type="button" @click="openRiwayat = !openRiwayat" style="background:none;border:none;padding:0;font-size:10px;color:#6366F1;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:1px;">
                                                Riwayat ({{ $peng->riwayat->count() }})
                                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                            </button>
                                            <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;left:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:220px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                                                @foreach($peng->riwayat as $index => $r)
                                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice') }}" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                                        <span style="font-weight:700;color:#0B266E;">#{{ $peng->riwayat->count() - $index }} {{ $r->is_revision ? 'Revisi' : 'Pertama' }}</span>
                                                        <span style="font-size:9px;color:#888;">{{ $r->created_at->format('H:i') }}</span>
                                                    </div>
                                                    <span style="font-size:9px;color:#A4ABB8;margin-top:2px;">{{ $r->created_at->locale('id')->format('d M Y') }}</span>
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @elseif($peng && $peng->file_path)
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($peng->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="{{ basename($peng->file_path) }}">
                                            {{ Str::limit(basename($peng->file_path), 15) }}
                                        </a>
                                    </div>
                                    @else
                                    <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                                    @endif
                                </td>

                                {{-- Waktu Submit --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($waktuSubmit)
                                    <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                        {{ \Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                    </span>
                                    @else
                                    <span style="font-size:12px;color:#999;">—</span>
                                    @endif
                                </td>

                                {{-- Nilai --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($peng)
                                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $peng->id) }}" style="display:flex;gap:4px;align-items:center;margin:0;">
                                        @csrf
                                        <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0-100"
                                               value="{{ $peng->nilai ?? '' }}" class="mp-input" style="width:60px;font-size:12px;padding:4px 6px;">
                                        <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;padding:4px 6px;font-size:11px;">{{ $st === 'acc' ? 'Edit' : 'ACC' }}</button>
                                    </form>
                                    @else
                                    <span style="font-size:12px;color:#999;">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($st === 'acc')
                                    <span class="mp-badge success sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>ACC</span>
                                    @elseif($st === 'revisi')
                                    <span class="mp-badge error sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Revisi</span>
                                    @elseif($st === 'belum_kumpul')
                                    <span class="mp-badge neutral sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Belum Kumpul</span>
                                    @else
                                    <span class="mp-badge warning sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Menunggu</span>
                                    @endif
                                </td>

                                {{-- Kirim Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($st === 'acc' || $st === 'belum_kumpul')
                                    <span style="font-size:12px;color:#999;">—</span>
                                    @else
                                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                                        <div x-data="{ showRevisiModal: false }">
                                            <button type="button" @click="showRevisiModal = true" class="mp-btn secondary sm" style="white-space:nowrap;padding:4px 8px;font-size:11px;">Revisi</button>
                
                                            <!-- Modal Overlay -->
                                            <div x-show="showRevisiModal" 
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15, 23, 42, 0.45);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px;"
                                                 @click.self="showRevisiModal = false">
                                                
                                                <!-- Modal Content -->
                                                <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);border:1px solid #DFE1E7;display:flex;flex-direction:column;overflow:hidden;">
                                                    
                                                    <!-- Modal Header -->
                                                    <div style="padding:16px 20px;border-bottom:1px solid #EEF0F5;display:flex;align-items:center;justify-content:between;">
                                                        <div style="font-size:15px;font-weight:700;color:#0D0D12;">Beri Catatan & Lampiran Revisi</div>
                                                        <button type="button" @click="showRevisiModal = false" style="background:none;border:none;cursor:pointer;color:#666D80;padding:4px;margin-left:auto;">
                                                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                
                                                    <!-- Form -->
                                                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $peng->id) }}" enctype="multipart/form-data" style="margin:0;">
                                                        @csrf
                                                        <div style="padding:20px;display:flex;flex-direction:column;gap:16px;text-align:left;">
                                                            <div>
                                                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Catatan Revisi <span style="color:#DF1C41;">*</span></label>
                                                                <textarea name="catatan_revisi" required rows="4" placeholder="Tuliskan catatan perbaikan untuk mahasiswa..." class="mp-input" style="width:100%;resize:none;font-size:13px;"></textarea>
                                                            </div>
                                                            <div>
                                                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Lampiran File Pendukung (Opsional)</label>
                                                                <input type="file" name="file_revisi" class="mp-input" style="width:100%;font-size:12px;">
                                                                <span style="font-size:11px;color:#666D80;display:block;margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</span>
                                                            </div>
                                                        </div>
                
                                                        <!-- Modal Footer -->
                                                        <div style="padding:16px 20px;background:#F9FAFB;border-top:1px solid #EEF0F5;display:flex;justify-content:end;gap:8px;">
                                                            <button type="button" @click="showRevisiModal = false" class="mp-btn secondary md">Batal</button>
                                                            <button type="submit" class="mp-btn primary md">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        @if($peng && ($peng->file_revisi_asprak || $peng->catatan_revisi))
                                        <div style="padding:6px 8px;background:#FEF2F2;border:1px solid #FEE2E2;border-radius:6px;font-size:11px;width:100%;box-sizing:border-box;">
                                            @if($peng->file_revisi_asprak)
                                            <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($peng->file_revisi_asprak, 'eoffice') }}" target="_blank" style="font-weight:600;color:#95122B;text-decoration:none;display:block;word-break:break-all;" title="{{ basename($peng->file_revisi_asprak) }}">
                                                📄 {{ Str::limit(basename($peng->file_revisi_asprak), 12) }}
                                            </a>
                                            @endif
                                            @if($peng->catatan_revisi)
                                            <div style="color:#7C1028;font-style:italic;margin-top:2px;word-break:break-word;" title="{{ $peng->catatan_revisi }}">💬: {{ Str::limit($peng->catatan_revisi, 25) }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </td>

                                {{-- Dokumen Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($latestRevision && $latestRevision->file_path)
                                    <div style="display:flex;flex-direction:column;gap:2px;">
                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($latestRevision->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0F6E56;text-decoration:none;" title="{{ basename($latestRevision->file_path) }}">
                                            {{ Str::limit(basename($latestRevision->file_path), 15) }}
                                        </a>
                                        @if($latestRevision->catatan)
                                        <div style="font-size:10px;color:#353849;font-style:italic;" title="{{ $latestRevision->catatan }}">💬 Mhs: {{ Str::limit($latestRevision->catatan, 25) }}</div>
                                        @endif
                                    </div>
                                    @else
                                    <span style="font-size:12px;color:#999;">—</span>
                                    @endif
                                </td>

                                {{-- Waktu Submit Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($latestRevision)
                                    <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                        {{ \Carbon\Carbon::parse($latestRevision->created_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }} WIB
                                    </span>
                                    @else
                                    <span style="font-size:12px;color:#999;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
    <div style="text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada tugas. Buat tugas baru sekarang!</div>
        <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn primary md" style="text-decoration:none;margin-top:16px;display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
</div>
@endforelse

</x-eoffice::manajemen-praktikum.layout>