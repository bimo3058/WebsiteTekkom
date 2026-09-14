<x-eoffice::manajemen-ruangan.layout pageTitle="Hak Akses Menu">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pengaturan Hak Akses Menu</h1>
            <p class="mp-page-sub">Kontrol matriks visibilitas akses menu Sidebar E-Office Manajemen Ruangan secara
                hierarkis.</p>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 24px;">

        {{-- Panel Superadmin Controls Admin --}}
        @if(auth()->user()->hasRole('superadmin'))
            <div class="mp-card" style="border-radius: 12px; width: 100%; border: 1px solid #E2E8F0;">
                <div class="mp-card-header" style="background: #FAFBFC;">
                    <h3 class="mp-card-title" style="font-size: 14px; display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2">
                            <path
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Akses Khusus Admin E-Office & Operator
                    </h3>
                </div>
                <form method="POST" action="{{ route('eoffice.peminjaman.admin.hak-akses.update') }}">
                    @csrf
                    <input type="hidden" name="scope_admin" value="1">
                    <div class="mp-card-body" style="padding: 24px;">
                        <p style="font-size: 12px; color: #64748B; margin-bottom: 20px;">
                            Tentukan menu administrasi apa saja yang dapat dilihat oleh akun dengan role <strong
                                style="color:#0F172A;">admin_eoffice</strong>.
                        </p>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                            @php
                                $menus = [
                                    ['name' => 'adm_klg', 'title' => 'Kalender Global', 'val' => $adminKlg, 'desc' => 'Tampilan kalender master.'],
                                    ['name' => 'adm_jad', 'title' => 'Jadwal Akademik', 'val' => $adminJad, 'desc' => 'Modul integrasi SIAP.'],
                                    ['name' => 'adm_evt', 'title' => 'Event & Maintenance', 'val' => $adminEvt, 'desc' => 'Pemblokiran manual internal.'],
                                    ['name' => 'adm_set', 'title' => 'Persetujuan Peminjaman', 'val' => $adminSet, 'desc' => 'Menerima/Tolak request.'],
                                    ['name' => 'adm_ars', 'title' => 'Arsip & Rekap', 'val' => $adminArs, 'desc' => 'Melihat history historis global.'],
                                    ['name' => 'adm_rua', 'title' => 'Manajemen Ruangan', 'val' => $adminRua, 'desc' => 'Atur ruangan dan inventaris.'],
                                    ['name' => 'adm_fas', 'title' => 'Manajemen Fasilitas', 'val' => $adminFas, 'desc' => 'Atur daftar fasilitas master.'],
                                    ['name' => 'adm_pgt', 'title' => 'Pengaturan Peminjaman', 'val' => $adminPgt, 'desc' => 'Jam operasional & libur.'],
                                ];
                            @endphp

                            @foreach($menus as $menu)
                                <div
                                    style="background: #F8FAFC; padding: 14px 16px; border-radius: 10px; border: 1px solid #E2E8F0;">
                                    <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer;">
                                        <input type="checkbox" name="{{ $menu['name'] }}" value="1" {{ $menu['val'] ? 'checked' : '' }} style="margin-top: 2px; width: 16px; height: 16px; accent-color: #2563EB;">
                                        <div>
                                            <span
                                                style="display:block; font-size:13px; font-weight:600; color:#0F172A;">{{ $menu['title'] }}</span>
                                            <span
                                                style="display:block; font-size:11.5px; color:#64748B; margin-top:2px;">{{ $menu['desc'] }}</span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="padding: 16px 24px; border-top:1px solid #E2E8F0; background:#fff; text-align:right;">
                        <button type="submit" class="mp-btn primary"
                            style="padding: 10px 24px; font-size: 13px; border-radius: 8px;">Simpan Akses Admin</button>
                    </div>
                </form>
            </div>
        @endif


        {{-- Panel Tampilan Mahasiswa / Umum --}}
        <div class="mp-card" style="border-radius: 12px; width: 100%; border: 1px solid #E2E8F0;">
            <div class="mp-card-header" style="background: #FAFBFC;">
                <h3 class="mp-card-title" style="font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2">
                        <path d="M17 20h5V4H2v16h5M7 15h10M7 11h10M7 7h10" />
                    </svg>
                    Akses Mahasiswa & Publik
                </h3>
            </div>
            <form method="POST" action="{{ route('eoffice.peminjaman.admin.hak-akses.update') }}">
                @csrf
                <input type="hidden" name="scope_user" value="1">
                <div class="mp-card-body" style="padding: 24px;">
                    <p style="font-size: 12px; color: #64748B; margin-bottom: 20px;">
                        Pilih menu apa saja yang dapat dilihat dan diakses oleh pengguna biasa di modul Peminjaman
                        Ruangan. (Sebagai Admin, menu Mahasiswa ini akan *selalu* terlihat di akun Anda).
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                        @php
                            $menusUsr = [
                                ['name' => 'usr_kat', 'title' => 'Katalog Ruangan', 'val' => $usrKat, 'desc' => 'Daftar ruangan tersedia.'],
                                ['name' => 'usr_kal', 'title' => 'Kalender Ruangan', 'val' => $usrKal, 'desc' => 'Tampilan jadwal per bulan.'],
                                ['name' => 'usr_pem', 'title' => 'Peminjaman Saya', 'val' => $usrPem, 'desc' => 'Menu booking aktif (menunggu/disetujui).'],
                                ['name' => 'usr_riw', 'title' => 'Riwayat', 'val' => $usrRiw, 'desc' => 'Rekapitulasi riwayat selesai/ditolak.'],
                            ];
                        @endphp

                        @foreach($menusUsr as $mu)
                            <div
                                style="background: #F8FAFC; padding: 14px 16px; border-radius: 10px; border: 1px solid #E2E8F0;">
                                <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer;">
                                    <input type="checkbox" name="{{ $mu['name'] }}" value="1" {{ $mu['val'] ? 'checked' : '' }} style="margin-top: 2px; width: 16px; height: 16px; accent-color: #059669;">
                                    <div>
                                        <span
                                            style="display:block; font-size:13px; font-weight:600; color:#0F172A;">{{ $mu['title'] }}</span>
                                        <span
                                            style="display:block; font-size:11.5px; color:#64748B; margin-top:2px;">{{ $mu['desc'] }}</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top:1px solid #E2E8F0; background:#fff; text-align:right;">
                    <button type="submit" class="mp-btn primary"
                        style="background:#059669; box-shadow:0 2px 6px rgba(5, 150, 105, .22); padding: 10px 24px; font-size: 13px; border-radius: 8px;">Simpan
                        Akses Mahasiswa</button>
                </div>
            </form>
        </div>

    </div>

</x-eoffice::manajemen-ruangan.layout>