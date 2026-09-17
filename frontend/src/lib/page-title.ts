const EXACT_TITLES: Record<string, string> = {
  "/": "Beranda",
  "/login": "Masuk",
  "/auth/exchange": "Menghubungkan SSO",
  "/unauthorized": "Akses Ditolak",
  "/notifications": "Notifikasi",
  "/profile": "Profil Saya",

  "/admin/dashboard": "Dashboard Admin",
  "/admin/periods": "Periode",
  "/admin/periods/new": "Tambah Periode",
  "/admin/users": "Pengguna",
  "/admin/users/new": "Tambah Pengguna",
  "/admin/groups": "Kelompok",
  "/admin/locations": "Lokasi",
  "/admin/document-requirements": "Persyaratan Dokumen",
  "/admin/document-types": "Tipe Dokumen",
  "/admin/assessment-bank": "Bank Asesmen",
  "/admin/assessments": "Penilaian",
  "/admin/period-assessment-config": "Tipe Penilaian",
  "/admin/peer-review": "Peer Review",
  "/admin/evaluation-setup/grade-configuration": "Konfigurasi Nilai",
  "/admin/finalization": "Finalisasi Kelompok",
  "/admin/schedule": "Jadwal",
  "/admin/sempro": "Sidang Proposal",
  "/admin/expo": "Expo Capstone",
  "/admin/ta-defense": "Sidang Tugas Akhir",
  "/admin/analytics/progress": "Progres Capstone",
  "/admin/peer-review-dashboard": "Dashboard Peer Review",
  "/admin/reports": "Laporan",
  "/admin/reports/assessments": "Laporan Penilaian",
  "/admin/reports/final-grades": "Nilai Akhir",
  "/admin/reports/grade-consistency": "Konsistensi Nilai",
  "/admin/reports/groups": "Laporan Kelompok",
  "/admin/reports/pdc1": "Laporan PDC 1",
  "/admin/reports/pdc2": "Laporan PDC 2",
  "/admin/reports/peer-reviews": "Laporan Peer Review",
  "/admin/reports/ta": "Laporan Tugas Akhir",
  "/admin/document-uploads": "Unggahan Dokumen",
  "/admin/audit-logs": "Log Audit",
  "/admin/settings": "Pengaturan",
  "/admin/titles": "Judul Capstone",

  "/dosen/dashboard": "Dashboard Dosen",
  "/dosen/titles": "Judul Saya",
  "/dosen/title-approvals": "Persetujuan Judul",
  "/dosen/bids": "Review Penawaran Judul",
  "/dosen/requests": "Permintaan Mahasiswa",
  "/dosen/supervised-groups": "Kelompok Bimbingan",
  "/dosen/bimbingan": "Bimbingan",
  "/dosen/schedule": "Jadwal Saya",
  "/dosen/evaluation": "Penilaian Mahasiswa",
  "/dosen/supervisor-evaluation": "Penilaian Pembimbing",
  "/dosen/ta-review": "Review Tugas Akhir",

  "/mahasiswa/dashboard": "Dashboard Mahasiswa",
  "/mahasiswa/registration": "Registrasi Capstone",
  "/mahasiswa/group": "Kelompok Saya",
  "/mahasiswa/titles": "Pilihan Judul",
  "/mahasiswa/propose-title": "Ajukan Judul",
  "/mahasiswa/bidding": "Penawaran Judul",
  "/mahasiswa/documents": "Dokumen",
  "/mahasiswa/ta-submission": "Pengumpulan Tugas Akhir",
  "/mahasiswa/schedule": "Jadwal Saya",
  "/mahasiswa/expo": "Expo Capstone",
  "/mahasiswa/peer-review": "Peer Review",
  "/mahasiswa/grades": "Nilai Saya",
  "/mahasiswa/ta-defense": "Sidang Tugas Akhir",
};

const DYNAMIC_TITLES: Array<[RegExp, string]> = [
  [/^\/admin\/periods\/[^/]+\/edit$/, "Edit Periode"],
  [/^\/admin\/users\/[^/]+\/edit$/, "Edit Pengguna"],
  [/^\/admin\/users\/[^/]+$/, "Detail Pengguna"],
  [/^\/admin\/groups\/[^/]+$/, "Detail Kelompok"],
  [/^\/admin\/assessment-bank\/[^/]+\/edit$/, "Edit Template Asesmen"],
  [/^\/admin\/document-requirements\/[^/]+$/, "Persyaratan Dokumen"],
  [/^\/admin\/evaluation-summary\/[^/]+$/, "Ringkasan Penilaian"],
  [/^\/admin\/peer-review\/[^/]+\/edit$/, "Edit Peer Review"],
  [/^\/admin\/period-assessment-config\/[^/]+\/edit$/, "Edit Tipe Penilaian"],
  [
    /^\/admin\/reports\/assessments\/student\/[^/]+(?:\/.*)?$/,
    "Detail Penilaian Mahasiswa",
  ],
  [/^\/dosen\/titles\/[^/]+$/, "Detail Judul"],
  [/^\/dosen\/evaluation\/[^/]+$/, "Penilaian Mahasiswa"],
  [/^\/dosen\/supervisor-evaluation\/[^/]+$/, "Penilaian Kelompok Bimbingan"],
  [/^\/dosen\/ta-evaluation\/[^/]+$/, "Penilaian Tugas Akhir"],
  [/^\/mahasiswa\/titles\/[^/]+$/, "Detail Judul"],
  [/^\/mahasiswa\/expo\/[^/]+$/, "Detail Expo Capstone"],
];

const SEGMENT_LABELS: Record<string, string> = {
  admin: "Admin",
  analytics: "Analitik",
  dashboard: "Dashboard",
  dosen: "Dosen",
  mahasiswa: "Mahasiswa",
  reports: "Laporan",
  settings: "Pengaturan",
};

function normalizePathname(pathname: string): string {
  const withoutQuery = pathname.split(/[?#]/, 1)[0] || "/";
  const normalized = `/${withoutQuery.split("/").filter(Boolean).join("/")}`;

  return normalized === "/" ? normalized : normalized.replace(/\/$/, "");
}

export function getPageTitle(pathname: string): string {
  const normalized = normalizePathname(pathname);
  const exact = EXACT_TITLES[normalized];
  if (exact) return exact;

  const dynamic = DYNAMIC_TITLES.find(([pattern]) => pattern.test(normalized));
  if (dynamic) return dynamic[1];

  const segments = normalized.split("/").filter(Boolean);
  const last = segments.at(-1) ?? "dashboard";
  const decoded = decodeURIComponent(last).replace(/[-_]+/g, " ");

  return (
    SEGMENT_LABELS[last] ??
    decoded.replace(/\b\w/g, (letter) => letter.toUpperCase())
  );
}

export function getDocumentTitle(pathname: string): string {
  return `${getPageTitle(pathname)} | SICATA`;
}
