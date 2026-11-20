<!-- RPS History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan RPS</h2>
        <div class="flex items-center gap-2">
            @if(!($isUploadOpen ?? false))
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                    disabled title="Periode upload RPS saat ini tidak aktif">
                    Ajukan RPS
                </button>
            @else
                <a href="{{ route('banksoal.rps.dosen.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40"
                    title="Ajukan RPS baru">
                    Ajukan RPS
                </a>
            @endif
        </div>
    </div>

    <div class="controls-section mx-4 mt-4">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="riwayatSearchInput"
                type="text"
                placeholder="Cari nama mata kuliah atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleRiwayatSearch()"
            >
        </div>

        <div class="filter-group">
            <label for="riwayatStatusSelect">Status:</label>
            <select id="riwayatStatusSelect" onchange="handleRiwayatFilterChange()">
                <option value="">Semua</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="riwayatMkSelect">Mata Kuliah:</label>
            <select id="riwayatMkSelect" onchange="handleRiwayatFilterChange()">
                <option value="">Semua</option>
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell">Tahun/Semester</th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Tanggal</th>
                    <th class="table-header-cell">Diunggah Oleh</th>
                    <th class="table-header-cell">Status</th>
                    <th class="table-header-cell w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayat as $item)
                    @php
                        $isDraft = $item->is_draft ?? false;
                        $statusValue = $isDraft ? 'draft' : ($item->status?->value ?? '');

                        // Badge class
                        $statusClass = match($statusValue) {
                            'draft'     => 'bg-slate-100 text-slate-600 border-slate-200',
                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'revisi'    => 'bg-red-50 text-red-700 border-red-200',
                            'diajukan'  => 'bg-amber-50 text-amber-700 border-amber-200',
                            default     => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                        $statusLabel = $isDraft ? 'Draft' : ($item->status?->label() ?? 'Unknown');

                        // Uploader info (hanya untuk non-draft)
                        $fallbackUser = null;
                        if (!$isDraft && !$item->uploader_id) {
                            $fallbackUser = DB::table('bs_rps_dosen')
                                ->where('rps_id', $item->id)
                                ->join('users', 'users.id', '=', 'bs_rps_dosen.dosen_id')
                                ->orderBy('bs_rps_dosen.id', 'asc')
                                ->select('users.id', 'users.name')
                                ->first();
                        }
                        $uploaderId   = $isDraft ? ($item->uploader_id ?? null) : ($item->uploader_id ?? $fallbackUser?->id);
                        $uploaderName = $isDraft ? (Auth::user()->name) : ($item->uploader_name ?? $fallbackUser?->name ?? 'Tidak diketahui');

                        // Tanggal tampil
                        $tanggal = $isDraft
                            ? ($item->updated_at ? $item->updated_at->format('d M Y') : '-')
                            : ($item->created_at ? $item->created_at->format('d M Y') : '-');

                        // Step progress untuk draft generator
                        $stepLabels = ['Belum dimulai', 'Langkah 1/3', 'Langkah 2/3', 'Langkah 3/3'];
                        $stepReached = $isDraft ? ($item->step_reached ?? 0) : null;
                    @endphp

                    <tr class="table-row"
                        data-mk="{{ $item->mataKuliah?->nama ?? '' }} {{ $item->mataKuliah?->kode ?? '' }}"
                        data-status="{{ $statusValue }}"
                        data-year="{{ $item->tahun_ajaran }}">

                        <td class="table-cell-strong">{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>

                        <td class="table-cell">
                            {{ $item->mataKuliah?->nama ?? 'N/A' }}
                            <span class="text-xs text-slate-500">({{ $item->mataKuliah?->kode ?? 'N/A' }})</span>
                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                @if($isDraft)
                                    @if(($item->creation_method ?? '') === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200">Form</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">Upload</span>
                                    @endif
                                    @if($stepReached !== null && ($item->creation_method ?? '') === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200">
                                            {{ $stepLabels[$stepReached] ?? '' }}
                                        </span>
                                    @endif
                                @else
                                    @if($item->creation_method === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200">Form</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">Upload</span>
                                    @endif
                                @endif
                            </div>
                        </td>

                        <td class="table-cell">{{ $tanggal }}</td>

                        <td class="table-cell">
                            @php
                                $pengunggah = collect($item->dosens ?? [])
                                    ->pluck('name')
                                    ->filter()
                                    ->values();

                                if ($pengunggah->isEmpty() && ! empty($uploaderName)) {
                                    $pengunggah = collect([$uploaderName]);
                                }

                                // Pembuat RPS tampil lebih dulu, anggota lain menyusul.
                                if (! empty($uploaderName)) {
                                    $pengunggah = $pengunggah
                                        ->sortBy(fn ($nama) => $nama === $uploaderName ? 0 : 1)
                                        ->values();
                                }

                                $pengunggahUtama = $pengunggah->first() ?? 'Tidak diketahui';
                                $pengunggahLain = max($pengunggah->count() - 1, 0);
                                $pengunggahLabel = \Illuminate\Support\Str::limit($pengunggahUtama, 15);
                            @endphp

                            <div class="flex items-center gap-2"
                                x-data="{
                                    open: false,
                                    top: 0,
                                    left: 0,
                                    above: false,
                                    show(event) {
                                        const rect = event.currentTarget.getBoundingClientRect();
                                        const lebar = 260;
                                        this.left = Math.max(12, Math.min(rect.left, window.innerWidth - lebar - 12));
                                        this.above = (window.innerHeight - rect.bottom) < 220 && rect.top > 220;
                                        this.top = this.above ? rect.top - 6 : rect.bottom + 6;
                                        this.open = true;
                                    },
                                }">
                                <div class="rps-uploader">
                                    <button type="button" class="rps-uploader-trigger"
                                        @click.stop="open ? open = false : show($event)"
                                        :aria-expanded="open.toString()"
                                        title="{{ $pengunggah->implode(', ') }}">
                                        <span>{{ $pengunggahLabel }}</span>@if($pengunggahLain > 0)<span class="rps-uploader-more">, +{{ $pengunggahLain }}</span>@endif
                                    </button>

                                    {{-- Teleport ke body supaya tooltip tidak terpotong overflow tabel --}}
                                    <template x-teleport="body">
                                        <div class="rps-uploader-popover is-floating" x-show="open" x-cloak
                                            x-transition.opacity.duration.120ms @click.outside="open = false"
                                            @keydown.escape.window="open = false" role="tooltip"
                                            :style="`top: ${top}px; left: ${left}px; transform: ${above ? 'translateY(-100%)' : 'none'};`">
                                            <p class="rps-uploader-popover-title">Dosen pengunggah</p>
                                            <ul>
                                                @foreach($pengunggah as $nama)
                                                    <li>{{ $nama }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </template>
                                </div>

                                @if($uploaderId == Auth::id())
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Anda</span>
                                @endif
                            </div>
                        </td>

                        <td class="table-cell">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>

                        <td class="table-cell">
                            <div class="dots-wrap" id="dots-{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}">
                                <button type="button" class="btn-dots" onclick="toggleMenu('{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}', event)">⋮</button>
                                <div class="dots-menu" id="menu-{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}">

                                    @if($isDraft)
                                        {{-- Action untuk Draft --}}
                                        <a href="{{ route('banksoal.rps.dosen.create') }}?resume_draft={{ $item->draft_id }}&mk={{ $item->mk_id }}&method={{ $item->creation_method ?? 'generator' }}"
                                           class="inline-flex items-center gap-2">
                                            <span>Lanjutkan</span>
                                        </a>
                                        <button type="button"
                                                class="delete-draft-btn menu-delete"
                                                data-draft-id="{{ $item->draft_id }}"
                                                data-mk="{{ e($item->mataKuliah?->nama ?? 'Draft RPS') }}"
                                                data-destroy-url="{{ route('banksoal.rps.dosen.delete-draft', $item->draft_id) }}">
                                            Hapus Draft
                                        </button>

                                    @else
                                        {{-- Action untuk RPS yang sudah diajukan --}}
                                        @if ($item->dokumen)
                                            <button type="button"
                                                    class="preview-dokumen-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}">
                                                Preview
                                            </button>
                                        @endif

                                        @php
                                            $isUploader = $uploaderId == Auth::id();
                                            // Tombol Edit terkunci kecuali RPS berstatus Revisi
                                            $isRevisi   = $item->status->value === 'revisi';
                                            $canEdit    = $isUploader && $isRevisi;
                                        @endphp

                                        @if($canEdit)
                                            <a href="{{ route('banksoal.rps.dosen.edit', $item->id) }}" class="edit-rps-btn">
                                                Edit
                                            </a>
                                        @else
                                            <button type="button" class="cursor-not-allowed text-slate-400" disabled
                                                    title="{{ !$isUploader ? 'Hanya pengunggah yang dapat mengubah RPS ini' : 'Tombol edit terkunci. RPS hanya dapat diedit saat berstatus Revisi.' }}">
                                                <i class="fas fa-lock" aria-hidden="true"></i>
                                                Edit
                                            </button>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty-state="1">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat pengunggahan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($riwayat, 'links'))
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $riwayat->onEachSide(1)->links('banksoal::components.ui.laravel-pagination') }}
        </div>
    @endif
</div>

<style>
    /* ── Kolom pengunggah: nama dipotong 15 karakter + tooltip ── */
    .rps-uploader {
        position: relative;
        min-width: 0;
    }

    .rps-uploader-trigger {
        display: inline-flex;
        align-items: baseline;
        gap: 2px;
        max-width: 190px;
        padding: 0;
        border: 0;
        background: transparent;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        text-align: left;
        white-space: nowrap;
        cursor: pointer;
    }

    .rps-uploader-trigger:hover {
        color: #0b266e;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .rps-uploader-more {
        color: #64748b;
        font-weight: 600;
    }

    .rps-uploader-trigger:hover .rps-uploader-more {
        color: inherit;
    }

    .rps-uploader-popover {
        min-width: 180px;
        max-width: 260px;
        padding: 10px 12px;
        border: 1px solid #E4E7EC;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 24px -12px rgba(15, 23, 42, 0.35);
    }

    /* Teleport ke body: posisi fixed supaya tidak terpotong overflow tabel */
    .rps-uploader-popover.is-floating {
        position: fixed;
        z-index: 9999;
    }

    .rps-uploader-popover[x-cloak] {
        display: none !important;
    }

    .rps-uploader-popover-title {
        margin: 0 0 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .rps-uploader-popover ul {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin: 0;
        padding: 0;
        list-style: none;
        font-size: 12px;
        color: #334155;
    }

    .rps-uploader-popover li {
        overflow-wrap: anywhere;
    }

    .rps-uploader-popover li + li {
        padding-top: 4px;
        border-top: 1px solid #f1f5f9;
    }
</style>

