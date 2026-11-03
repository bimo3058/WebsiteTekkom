{{--
    Kotak pilih bercari (Alpine.js) untuk form kegiatan: Ketua Pelaksana (pilih satu),
    Dosen Pendamping, Panitia, dan Akses Kelola (pilih banyak).

    Polanya disamakan dengan pemilih Role di halaman edit user superadmin SITKOM
    (resources/views/superadmin/users/edit.blade.php): state di x-data, chip & daftar
    dirender x-for, panel x-show + x-transition, tutup lewat @click.outside, dan nilai
    form dikirim lewat <input type="hidden"> yang ikut dirender x-for.

    Tampilan tetap memakai kelas .search-select-* / .panitia-* yang sudah ada di
    _styles.blade.php maupun CSS form Laporan & Arsip. Satu-satunya tambahan: panel
    dibiarkan display:block oleh CSS di bawah, karena buka/tutupnya kini diatur x-show
    (dulu diatur kelas .show).

    Data opsi: [{ id, nama, sub, cari }] — `cari` teks huruf kecil gabungan nama + NIM/NIP.
--}}
@once
    <style>
        .mk-pilih .search-select-dropdown,
        .mk-pilih .panitia-dropdown { display: block; }

        .mk-pilih-caret {
            flex-shrink: 0;
            color: var(--c-fg-muted);
            transition: transform 0.2s;
            pointer-events: none;
        }
        .mk-pilih-caret.is-open { transform: rotate(180deg); }

        /* Ketua Pelaksana: panah di dalam kotak teks */
        .mk-pilih .search-select-wrapper input[type="text"] { padding-right: 36px; }
        .mk-pilih .search-select-wrapper .mk-pilih-caret {
            position: absolute;
            right: 14px;
            top: 50%;
            margin-top: -6px;
        }
        .mk-pilih .search-select-wrapper.ada-pilihan input[type="text"] { padding-right: 62px; }
        .mk-pilih-kosongkan {
            position: absolute;
            right: 34px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: var(--c-fg-muted);
            cursor: pointer;
        }
        .mk-pilih-kosongkan:hover { background: var(--c-primary-subtle); color: var(--c-primary); }
        .mk-pilih .search-select-option.is-active { background: var(--c-primary-subtle); color: var(--c-primary); }
        .mk-pilih .search-select-option.is-selected { font-weight: 700; color: var(--c-primary); }
    </style>

    <script>
        document.addEventListener('alpine:init', function () {
            function saring(opsi, query) {
                const q = (query || '').toLowerCase().trim();

                return q ? opsi.filter(o => o.cari.includes(q)) : opsi;
            }

            /**
             * Ketua Pelaksana yang sedang dipilih di bagian Personel, dibaca Akses Kelola
             * untuk baris terkunci "Ketua Pelaksana". Diisi lewat x-effect di _personel,
             * jadi urutan inisialisasi kedua kartu tidak berpengaruh.
             *   userId — id akun (Akses Kelola memakai id akun, bukan id mahasiswa)
             *   akses  — lolos KegiatanPolicy::bisaJadiPengelola, yakni benar-benar ikut mengelola
             */
            Alpine.store('mkKetua', { userId: '', nama: '', akses: false });

            /**
             * Pilih satu dengan pencarian (Ketua Pelaksana).
             * Kotak teks menampilkan nama terpilih; saat diketik ia menjadi kolom cari.
             * Begitu panel ditutup tanpa memilih, teksnya dikembalikan ke nama yang
             * benar-benar tersimpan — atau pilihan dilepas bila teksnya sengaja dikosongkan —
             * supaya yang terlihat selalu sama dengan yang akan dikirim.
             *
             * `sebidang` (opsional, S-72): { bidangAsal } membuat daftar mengikuti radio di
             * #bidangGroup. Pilihan yang bukan dari bidang terpilih dilepas, karena server
             * akan menolaknya (KegiatanPolicy::aturanKetuaSebidang).
             */
            Alpine.data('mkPilihSatu', (opsi, terpilihAwal, sebidang = null) => ({
                opsi: opsi,
                terpilih: terpilihAwal ? String(terpilihAwal) : '',
                query: '',
                open: false,
                bidangId: '',

                init() {
                    if (sebidang) {
                        const grup = document.getElementById('bidangGroup');
                        const bacaBidang = () => {
                            this.bidangId = grup.querySelector('input:checked')?.value || '';
                            if (this.terpilih && !this.opsiAktif.some(o => String(o.id) === this.terpilih)) {
                                this.kosongkan();
                            }
                        };

                        bacaBidang();
                        grup.addEventListener('change', bacaBidang);
                    }

                    this.query = this.namaTerpilih;
                },

                get opsiAktif() {
                    if (!sebidang) return this.opsi;

                    // Ketua tersimpan tetap ada selama bidang kegiatannya tidak diganti, termasuk
                    // draft Prodi yang memang belum berbidang (aturanKetuaSebidang).
                    return this.opsi.filter(o => this.bidangId === ''
                        ? o.tersimpan && sebidang.bidangAsal.length === 0
                        : o.bidangId === this.bidangId || (o.tersimpan && sebidang.bidangAsal.includes(this.bidangId)));
                },

                get namaTerpilih() {
                    const o = this.opsi.find(o => String(o.id) === this.terpilih);

                    return o ? o.nama : '';
                },

                get hasil() {
                    // Selama teksnya masih nama terpilih, tampilkan seluruh daftar.
                    return this.query === this.namaTerpilih ? this.opsiAktif : saring(this.opsiAktif, this.query);
                },

                buka() {
                    this.open = true;
                },

                pilih(o) {
                    this.terpilih = String(o.id);
                    this.query = o.nama;
                    this.open = false;
                },

                tutup() {
                    if (!this.open) return;

                    this.open = false;
                    if (this.query.trim() === '') this.terpilih = '';
                    this.query = this.namaTerpilih;
                },

                // Tombol × : kosongkan pilihan.
                kosongkan() {
                    this.terpilih = '';
                    this.query = '';
                    this.open = false;
                },

                // Nilai yang dikirim: teks dihapus habis = kosong, walau dropdown belum ditutup.
                get nilaiKirim() {
                    return this.query.trim() === '' ? '' : this.terpilih;
                },

                pilihPertama() {
                    if (this.query.trim() === '') return this.kosongkan();
                    if (this.open && this.hasil.length) this.pilih(this.hasil[0]);
                },
            }));

            /**
             * Pilih banyak dengan pencarian + chip (Dosen Pendamping, Panitia, Akses Kelola).
             * `terpilih` menyimpan id sesuai urutan dipilih.
             *
             * `kunci` (opsional): fungsi yang mengembalikan satu id yang sedang dipegang
             * kotak lain (Akses Kelola: akun Ketua Pelaksana). Id itu disembunyikan dari
             * daftar dan dari chip, sehingga tidak ikut terkirim dua kali. Ia tetap ada di
             * `terpilih`, jadi chipnya kembali bila ketuanya diganti.
             *
             * `sebidang` (opsional, S-73, Akses Kelola): daftar & chip mengikuti Bidang/Unit
             * yang terpilih di form. Chip bidang lain disembunyikan dan tidak ikut terkirim.
             */
            Alpine.data('mkPilihBanyak', (opsi, terpilihAwal, kunci = null, sebidang = false) => ({
                opsi: opsi,
                terpilih: (terpilihAwal || []).map(String).filter(id => opsi.some(o => String(o.id) === id)),
                query: '',
                open: false,
                bidangId: '',

                init() {
                    if (!sebidang) return;

                    const grup = document.getElementById('bidangGroup');
                    const baca = () => { this.bidangId = grup?.querySelector('input:checked')?.value || ''; };

                    baca();
                    grup?.addEventListener('change', baca);
                    // Form Arsip melepas bidang lewat skrip saat kategorinya hanya Prodi.
                    document.getElementById('kategoriGroup')?.addEventListener('change', baca);
                },

                get opsiAktif() {
                    return sebidang ? this.opsi.filter(o => this.bidangId !== '' && o.bidangId === this.bidangId) : this.opsi;
                },

                get idKunci() {
                    return kunci ? String(kunci() || '') : '';
                },

                get hasil() {
                    return saring(this.opsiAktif, this.query).filter(o => String(o.id) !== this.idKunci);
                },

                get daftarTerpilih() {
                    return this.terpilih
                        .filter(id => id !== this.idKunci)
                        .map(id => this.opsiAktif.find(o => String(o.id) === id))
                        .filter(Boolean);
                },

                dipilih(id) {
                    return this.terpilih.includes(String(id));
                },

                pilih(o) {
                    if (!this.dipilih(o.id)) this.terpilih.push(String(o.id));

                    this.query = '';
                    this.open = true;
                    this.$nextTick(() => this.$refs.cari.focus());
                },

                hapus(id) {
                    this.terpilih = this.terpilih.filter(x => x !== String(id));
                },

                pilihPertama() {
                    const o = this.hasil.find(o => !this.dipilih(o.id));

                    if (this.open && o) this.pilih(o);
                },
            }));
        });
    </script>
@endonce
