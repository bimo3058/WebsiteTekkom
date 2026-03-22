<x-banksoal::layouts.master>

<!-- [Dosen - RPS] View untuk manajemen RPS tingkat Dosen -->

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
            <strong>Departemen Teknik Komputer</strong>
            <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('banksoal.dashboard') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Home</a>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="nav-item active"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS</a>
        <a href="{{ route('banksoal.banksoal.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal</a>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal</a>
    </nav>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="topbar-btn"><i class="fas fa-cog"></i></button>
    <button class="topbar-btn notif-btn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
    <div class="user-chip">
        <div class="user-avatar-chip">A</div>
        <div class="user-info">
            <strong>Prof. Dr. Siti Rahayu</strong>
            <span>198503122010121001</span>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="main">

<div class="page-header">
    <h1>Manajemen RPS</h1>
    <p>Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung.</p>
</div>

{{-- STATUS BANNER --}}
<div class="status-bar">
    <div class="status-left">
        <i class="fas fa-exclamation-circle status-icon not-uploaded"></i>
        <div>
            <div class="status-label">Status: Belum Diunggah</div>
            <div class="status-desc">
                Batas akhir pengunggahan RPS untuk Semester Ganjil 2025/2026 adalah 30 Agustus 2025.
            </div>
        </div>
    </div>
    <a href="#" class="panduan-btn">
        <i class="fas fa-circle-question"></i> Panduan Pengisian
    </a>
</div>

{{-- MULTISELECT CSS --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/banksoal/css/dashboard.css') }}">
@endpush

{{-- FORM CARD --}}
<div class="form-card">
    <div class="form-card-title">Formulir Rencana Pembelajaran</div>

    <form action="{{ route('banksoal.rps.dosen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid-2">
            {{-- Mata Kuliah --}}
            <div class="form-group">
                <label class="form-label">Mata Kuliah</label>
                <select name="mata_kuliah_id" id="mkSelect" class="form-control">
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    <option value="1">Pemrograman Dasar (INF101)</option>
                    <option value="2">Struktur Data (INF201)</option>
                    <option value="3">Basis Data (INF301)</option>
                    <option value="4">Jaringan Komputer (INF401)</option>
                </select>
            </div>

            {{-- Dosen Pengampu Lain — Multi-select --}}
            <div class="form-group">
                <label class="form-label">Dosen Pengampu Lain</label>
                <div id="dosenMs" class="ms-wrapper"
                     data-name="dosen_lain[]"
                     data-placeholder="Pilih mata kuliah terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">Pilih satu atau lebih dosen pengampu.</small>
            </div>

            {{-- Semester --}}
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-control">
                    <option value="Ganjil" selected>Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="form-group">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-control">
                    <option value="2023/2024">2023/2024</option>
                    <option value="2024/2025">2024/2025</option>
                    <option value="2025/2026" selected>2025/2026</option>
                </select>
            </div>

            {{-- CPL — Multi-select --}}
            <div class="form-group full">
                <label class="form-label">Capaian Pembelajaran Lulusan (CPL) <span style="color: red;">*</span></label>
                <div id="cplMs" class="ms-wrapper"
                     data-name="cpl_ids[]"
                     data-placeholder="Pilih mata kuliah terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">Pilih satu atau lebih CPL.</small>
            </div>

            {{-- CPMK — Multi-select --}}
            <div class="form-group full">
                <label class="form-label">Capaian Pembelajaran Mata Kuliah (CPMK) <span style="color: red;">*</span></label>
                <div id="cpmkMs" class="ms-wrapper"
                     data-name="cpmk_ids[]"
                     data-placeholder="Pilih CPL terlebih dahulu"
                     data-disabled="true"></div>
                <small class="form-hint">CPMK akan tersedia setelah CPL dipilih.</small>
            </div>

            {{-- Upload --}}
            <div class="form-group full">
                <label class="form-label">Dokumen RPS <span style="color: red;">*</span></label>
                <label class="upload-zone" id="uploadZone">
                    <input type="file" name="dokumen_rps" accept=".pdf,.docx" id="fileInput" required>
                    <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
                    <strong id="uploadText">Klik untuk unggah atau seret file ke sini</strong>
                    <span id="uploadSub">PDF, DOCX (Maks. 10MB)</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">Batal</button>
            <button type="submit" class="btn-primary">
                <i class="fas fa-floppy-disk"></i> Simpan RPS
            </button>
        </div>
    </form>
</div>

{{-- HISTORY TABLE --}}
<div class="history-card">
    <div class="history-title">Riwayat Pengunggahan</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tahun/Semester</th>
                    <th>Mata Kuliah</th>
                    <th>Tanggal Unggah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2022/2023 - Genap</td>
                    <td>Pemrograman Dasar (INF101)</td>
                    <td>12 Feb 2023</td>
                    <td><span class="badge badge-verified">✓ TERVERIFIKASI</span></td>
                    <td><a href="#" class="action-link"><i class="fas fa-eye"></i> Lihat</a></td>
                </tr>
                <tr>
                    <td>2022/2023 - Ganjil</td>
                    <td>Struktur Data (INF201)</td>
                    <td>28 Aug 2022</td>
                    <td><span class="badge badge-verified">✓ TERVERIFIKASI</span></td>
                    <td><a href="#" class="action-link"><i class="fas fa-eye"></i> Lihat</a></td>
                </tr>
                <tr>
                    <td>2023/2024 - Ganjil</td>
                    <td>Basis Data (INF301)</td>
                    <td>05 Sep 2023</td>
                    <td><span class="badge badge-pending">📝 DRAFT</span></td>
                    <td><a href="#" class="action-link"><i class="fas fa-eye"></i> Lihat</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</main>

@push('scripts')
<script>
/* ══════════════════════════════════════════════════════════════
   DUMMY DATA — menggantikan AJAX ke server
   ══════════════════════════════════════════════════════════════ */
const DUMMY = {
    dosen: {
        1: [
            { id: 10, name: 'Dr. Ahmad Fauzi, M.Kom' },
            { id: 11, name: 'Dr. Budi Santoso, M.T' },
            { id: 12, name: 'Prof. Citra Dewi, Ph.D' },
            { id: 13, name: 'Ir. Dodi Prakoso, M.Sc' },
            { id: 14, name: 'Dr. Eka Putri, M.Kom' },
            { id: 15, name: 'Fajar Nugroho, M.T' },
        ],
        2: [
            { id: 11, name: 'Dr. Budi Santoso, M.T' },
            { id: 13, name: 'Ir. Dodi Prakoso, M.Sc' },
        ],
        3: [
            { id: 10, name: 'Dr. Ahmad Fauzi, M.Kom' },
            { id: 14, name: 'Dr. Eka Putri, M.Kom' },
            { id: 15, name: 'Fajar Nugroho, M.T' },
        ],
        4: [
            { id: 12, name: 'Prof. Citra Dewi, Ph.D' },
            { id: 13, name: 'Ir. Dodi Prakoso, M.Sc' },
            { id: 15, name: 'Fajar Nugroho, M.T' },
        ],
    },
    cpl: {
        1: [
            { id: 101, kode: 'CPL-01: Menguasai konsep dasar pemrograman' },
            { id: 102, kode: 'CPL-02: Mampu menganalisis algoritma sederhana' },
            { id: 103, kode: 'CPL-03: Mampu merancang solusi berbasis kode' },
        ],
        2: [
            { id: 101, kode: 'CPL-01: Menguasai konsep dasar pemrograman' },
            { id: 104, kode: 'CPL-04: Mampu mengimplementasikan struktur data' },
            { id: 105, kode: 'CPL-05: Mampu menganalisis kompleksitas algoritma' },
        ],
        3: [
            { id: 106, kode: 'CPL-06: Mampu merancang skema basis data' },
            { id: 107, kode: 'CPL-07: Mampu menggunakan SQL secara efektif' },
            { id: 108, kode: 'CPL-08: Memahami normalisasi dan optimasi query' },
        ],
        4: [
            { id: 109, kode: 'CPL-09: Memahami arsitektur jaringan komputer' },
            { id: 110, kode: 'CPL-10: Mampu mengkonfigurasi protokol jaringan' },
        ],
    },
    cpmk: {
        101: [
            { id: 201, kode: 'CPMK-1.1: Memahami tipe data dan variabel' },
            { id: 202, kode: 'CPMK-1.2: Menerapkan struktur kontrol (if, loop)' },
        ],
        102: [
            { id: 203, kode: 'CPMK-2.1: Menganalisis kompleksitas Big-O dasar' },
            { id: 204, kode: 'CPMK-2.2: Membandingkan algoritma sorting sederhana' },
        ],
        103: [
            { id: 205, kode: 'CPMK-3.1: Merancang fungsi modular' },
            { id: 206, kode: 'CPMK-3.2: Mengimplementasikan OOP dasar' },
        ],
        104: [
            { id: 207, kode: 'CPMK-4.1: Mengimplementasikan linked list' },
            { id: 208, kode: 'CPMK-4.2: Mengimplementasikan stack dan queue' },
            { id: 209, kode: 'CPMK-4.3: Mengimplementasikan tree dan graph' },
        ],
        105: [
            { id: 210, kode: 'CPMK-5.1: Menganalisis algoritma rekursif' },
            { id: 211, kode: 'CPMK-5.2: Menerapkan dynamic programming' },
        ],
        106: [
            { id: 212, kode: 'CPMK-6.1: Merancang ERD dan skema relasional' },
            { id: 213, kode: 'CPMK-6.2: Mengimplementasikan DDL dan DML' },
        ],
        107: [
            { id: 214, kode: 'CPMK-7.1: Menulis query SELECT kompleks' },
            { id: 215, kode: 'CPMK-7.2: Menggunakan JOIN dan subquery' },
        ],
        108: [
            { id: 216, kode: 'CPMK-8.1: Menerapkan normalisasi 1NF–3NF' },
            { id: 217, kode: 'CPMK-8.2: Mengoptimalkan query dengan index' },
        ],
        109: [
            { id: 218, kode: 'CPMK-9.1: Menjelaskan model OSI dan TCP/IP' },
            { id: 219, kode: 'CPMK-9.2: Menganalisis topologi jaringan' },
        ],
        110: [
            { id: 220, kode: 'CPMK-10.1: Mengkonfigurasi IP addressing dan subnetting' },
            { id: 221, kode: 'CPMK-10.2: Mensimulasikan routing statik dan dinamik' },
        ],
    },
};

// Script inisialisasi multiselect dan upload handler
// (Implementasi JavaScript untuk form interaktivity)
document.addEventListener('DOMContentLoaded', function() {
    // TODO: Implementasi JavaScript untuk MultiSelect dan file upload
});
</script>
@endpush

</x-banksoal::layouts.master>