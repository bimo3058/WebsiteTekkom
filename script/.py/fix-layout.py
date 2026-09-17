import json

target_file = r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen\pendaftaran-koor.blade.php"

target_content = """{{-- Section: Filter --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter Pendaftaran</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Pencarian & Filter</span>
    </div>
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..."
                   class="mp-input" style="width:200px;">
            <x-eoffice::manajemen-praktikum.ui.select 
                name="sort"
                :options="[
                    ['value' => 'terbaru', 'label' => 'Terbaru'],
                    ['value' => 'terlama', 'label' => 'Terlama'],
                    ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
                    ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
                ]"
                :selected="request('sort', 'terbaru')"
                placeholder="Urutkan..."
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />
            @php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        $label .= " · {$p->semester} {$p->tahun_ajaran}";
                        $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $label];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="240px"
            />
            <select name="status_dosen" class="mp-input mp-select">
                <option value="">Semua Status</option>
                <option value="menunggu"  {{ request('status_dosen')=='menunggu'  ? 'selected' : '' }}>Menunggu Review</option>
                <option value="disetujui" {{ request('status_dosen')=='disetujui' ? 'selected' : '' }}>Sudah Disetujui</option>
                <option value="ditolak"   {{ request('status_dosen')=='ditolak'   ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="mp-btn primary sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
        </form>
    </div>
</div>

{{-- Section: Daftar Pendaftar --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm">{{ $pendaftaran->total() }} pendaftar</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Berkas</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)"""

replacement_content = """    <div class="mp-card" style="display:flex; flex-direction:column; overflow:hidden;">
        <div class="mp-card-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
            <span class="mp-card-title">Pendaftar Koordinator</span>
            <span class="mp-badge navy sm" style="font-size: 11px;">{{ $pendaftaran->total() }} pendaftar</span>
        </div>
        <div style="padding: 16px 20px; border-bottom: 1px solid #DFE1E7; background: #fff;">
            <form method="GET" style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..." class="mp-input" style="padding:0 12px; height:36px; font-size:13px; width:250px;">
                
                <x-eoffice::manajemen-praktikum.ui.select 
                    name="sort"
                    :options="[
                        ['value' => 'terbaru', 'label' => 'Terbaru'],
                        ['value' => 'terlama', 'label' => 'Terlama'],
                        ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
                        ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
                    ]"
                    :selected="request('sort', 'terbaru')"
                    placeholder="Urutkan..."
                    onChange="$event.target.form.submit()"
                    minWidth="140px"
                />

                @php
                    $praktikumOptions = [];
                    if(isset($praktikumList)) {
                        foreach($praktikumList as $pr) {
                            $label = $pr->nama;
                            $label .= " · {$pr->semester} {$pr->tahun_ajaran}";
                            $praktikumOptions[] = ['value' => (string)$pr->id, 'label' => $label];
                        }
                    }
                @endphp
                <x-eoffice::manajemen-praktikum.ui.select 
                    name="praktikum_id"
                    :options="$praktikumOptions"
                    :selected="(string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))"
                    placeholder="Pilih Praktikum..."
                    onChange="$event.target.form.submit()"
                    minWidth="240px"
                />
                
                <x-eoffice::manajemen-praktikum.ui.select 
                    name="status_dosen"
                    :options="[
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'menunggu', 'label' => 'Menunggu Review'],
                        ['value' => 'disetujui', 'label' => 'Sudah Disetujui'],
                        ['value' => 'ditolak', 'label' => 'Ditolak']
                    ]"
                    :selected="request('status_dosen', '')"
                    placeholder="Semua Status"
                    onChange="$event.target.form.submit()"
                    minWidth="160px"
                />
                
                <button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px; width:40px;">No</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Nama Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Berkas</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)"""

import sys

with open(target_file, "r", encoding="utf-8") as f:
    content = f.read()

new_content = content.replace(target_content, replacement_content)

# We also need to add the No column to the row loop
row_target = """                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">"""
row_replacement = """                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;color:#353849;">{{ $loop->iteration + ($pendaftaran->currentPage() - 1) * $pendaftaran->perPage() }}</td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-3">"""
new_content = new_content.replace(row_target, row_replacement)

with open(target_file, "w", encoding="utf-8") as f:
    f.write(new_content)

print("Replaced!")
