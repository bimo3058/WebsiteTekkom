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
             * Pilih satu dengan pencarian (Ketua Pelaksana).
             * Kotak teks menampilkan nama terpilih; saat diketik ia menjadi kolom cari.
             * Begitu panel ditutup tanpa memilih, teksnya dikembalikan ke nama yang
             * benar-benar tersimpan — atau pilihan dilepas bila teksnya sengaja dikosongkan —
             * supaya yang terlihat selalu sama dengan yang akan dikirim.
             */
            Alpine.data('mkPilihSatu', (opsi, terpilihAwal) => ({
                opsi: opsi,
                terpilih: terpilihAwal ? String(terpilihAwal) : '',
                query: '',
                open: false,

                init() {
                    this.query = this.namaTerpilih;
                },

                get namaTerpilih() {
                    const o = this.opsi.find(o => String(o.id) === this.terpilih);

                    return o ? o.nama : '';
                },

                get hasil() {
                    // Selama teksnya masih nama terpilih, tampilkan seluruh daftar.
                    return this.query === this.namaTerpilih ? this.opsi : saring(this.opsi, this.query);
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

                pilihPertama() {
                    if (this.open && this.hasil.length) this.pilih(this.hasil[0]);
                },
            }));

            /**
             * Pilih banyak dengan pencarian + chip (Dosen Pendamping, Panitia, Akses Kelola).
             * `terpilih` menyimpan id sesuai urutan dipilih.
             */
            Alpine.data('mkPilihBanyak', (opsi, terpilihAwal) => ({
                opsi: opsi,
                terpilih: (terpilihAwal || []).map(String).filter(id => opsi.some(o => String(o.id) === id)),
                query: '',
                open: false,

                get hasil() {
                    return saring(this.opsi, this.query);
                },

                get daftarTerpilih() {
                    return this.terpilih
                        .map(id => this.opsi.find(o => String(o.id) === id))
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
