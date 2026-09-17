<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Praktikan">
    <div class="mp-page-header">
        <div><h1 class="mp-page-title">Pendaftaran Praktikan</h1><p class="mp-page-sub">Pantau pendaftaran, verifikasi koordinator, dan dokumen IRS mahasiswa.</p></div>
        <a class="mp-btn secondary md" href="{{ route('eoffice.manprak.admin.daftar-praktikan.index') }}"><x-eoffice::manajemen-praktikum.ui.icon name="book" /> Praktikan terdaftar</a>
    </div>
    @include('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => 'praktikan'])
    <section class="mp-card">
        <form method="GET" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;padding:18px;">
            <div style="flex:1;min-width:180px;">
                <label for="registration-search" style="display:block;font-size:12px;margin-bottom:6px;">Nama mahasiswa</label>
                <input class="mp-input" id="registration-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari mahasiswa…" maxlength="100">
            </div>
            <div>
                <label for="registration-status" style="display:block;font-size:12px;margin-bottom:6px;">Status verifikasi</label>
                <select class="mp-input mp-select" name="status" id="registration-status">
                    @foreach(['' => 'Semua status', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $text)
                        <option value="{{ $value }}" @selected(request('status', '') === $value)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="mp-btn primary md">Terapkan</button>
            @if(request()->anyFilled(['search', 'status']))
                <a class="mp-btn secondary md" href="{{ route('eoffice.manprak.admin.pendaftaran-praktikan.index') }}">Reset</a>
            @endif
        </form>
        <div class="mp-table-wrap" tabindex="0" role="region" aria-label="Daftar pendaftaran praktikan">
            <table class="mp-table">
                <thead><tr><th scope="col">Mahasiswa</th><th scope="col">Praktikum</th><th scope="col">Tanggal daftar</th><th scope="col">Status</th><th scope="col">Dokumen IRS</th><th scope="col">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pendaftaran as $p)
                        <tr>
                            <td><strong>{{ $p->user?->name ?? 'Akun tidak tersedia' }}</strong><div style="font-size:12px;color:var(--c-fg-sec);">{{ $p->user?->student?->student_number ?? $p->user?->external_id ?? 'NIM belum tercatat' }}</div></td>
                            <td>{{ $p->praktikum?->nama ?? 'Tidak tersedia' }}</td>
                            <td>{{ $p->created_at?->format('d M Y') ?? 'Belum tercatat' }}</td>
                            <td><span class="mp-badge {{ ['approved' => 'success', 'rejected' => 'error'][$p->status] ?? 'warning' }} sm">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$p->status] ?? $p->status }}</span></td>
                            <td>
                                @if($p->irs_path)
                                    <a class="mp-btn secondary sm" target="_blank" rel="noopener" aria-label="Lihat IRS {{ $p->user?->name }} di tab baru" href="{{ route('eoffice.manprak.admin.pendaftaran.document', ['type' => 'praktikan', 'id' => $p->id, 'document' => 'irs_path']) }}"><x-eoffice::manajemen-praktikum.ui.icon name="file" /> Lihat IRS</a>
                                @else
                                    <span style="color:var(--c-fg-sec);">Tidak diunggah</span>
                                @endif
                            </td>
                            <td><a class="mp-btn secondary sm" href="{{ route('eoffice.manprak.admin.pendaftaran.show', ['type' => 'praktikan', 'id' => $p->id]) }}">Detail <x-eoffice::manajemen-praktikum.ui.icon name="arrow-right" /></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;">Tidak ada pendaftaran yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 18px;">{{ $pendaftaran->links() }}</div>
    </section>
</x-eoffice::manajemen-praktikum.layout>
