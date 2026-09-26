{{--
    Modal "Buat Pengaduan": hanya pemilih jalur. Kerangka disamakan dengan modal
    "Ajukan Prestasi" di Verifikasi Data.

    - Reguler      → tautan biasa ke halaman form (tab yang sama), seperti sebelumnya.
    - Konfidensial → membuat magic link lewat AJAX (anon.generate), lalu isi modal
                     DIGANTI dengan tautannya; tombol "Buka Form" membuka halaman
                     track/form di tab baru, sama seperti halaman init lama. Bila
                     AJAX gagal, form disubmit biasa (halaman init sebagai cadangan).

    Terbuka otomatis bila URL memuat ?buat=1 (tautan lama /jalur dan tombol
    "Ganti jalur" di halaman form).
--}}
<style>
    #buatPengaduanModal .modal-content { border-radius: 16px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    #buatPengaduanModal .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    #buatPengaduanModal .modal-title { font-size: 16px; font-weight: 700; color: var(--c-fg); }
    #buatPengaduanModal .modal-body { padding: 22px; }

    .bp-jalur-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 600px) { .bp-jalur-grid { grid-template-columns: 1fr; } }
    .bp-jalur-form { display: flex; margin: 0; }
    .bp-jalur {
        display: flex; flex-direction: column; gap: 8px; width: 100%; text-align: left;
        background: #ffffff; border: 1px solid var(--c-border); border-radius: 12px;
        padding: 20px; cursor: pointer; font: inherit; color: inherit;
        text-decoration: none !important;
        transition: border-color .15s, box-shadow .15s;
        -webkit-appearance: none; appearance: none;
    }
    .bp-jalur:hover { border-color: var(--c-primary-border); box-shadow: 0 4px 14px rgba(11,38,110,.08); color: inherit; }
    .bp-jalur:disabled { opacity: .6; cursor: progress; }
    .bp-jalur-icon {
        width: 42px; height: 42px; border-radius: 10px; display: flex;
        align-items: center; justify-content: center; background: var(--c-primary-subtle); color: var(--c-primary);
    }
    .bp-jalur.is-konfidensial .bp-jalur-icon { background: var(--c-grey-50); color: var(--c-fg-sec); }
    .bp-jalur-pill {
        align-self: flex-start; font-size: 10px; font-weight: 700; letter-spacing: .5px;
        padding: 2px 9px; border-radius: 20px; background: var(--c-primary-subtle); color: var(--c-primary);
    }
    .bp-jalur.is-konfidensial .bp-jalur-pill { background: var(--c-grey-50); color: var(--c-fg-sec); }
    .bp-jalur-title { font-size: 16px; font-weight: 800; color: var(--c-fg); }
    .bp-jalur-desc { font-size: 12px; color: var(--c-fg-muted); line-height: 1.55; flex: 1; }

    /* Tampilan magic link (menggantikan pemilih jalur) */
    .bp-link-icon {
        width: 56px; height: 56px; border-radius: 50%; background: var(--c-primary-subtle);
        color: var(--c-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
    }
    .bp-link-box {
        background: var(--c-bg); border: 2px dashed var(--c-border); border-radius: 12px;
        padding: 16px; margin: 18px 0;
    }
    .bp-link-url {
        display: block; font-family: monospace; font-size: 13px; font-weight: 600;
        color: var(--c-primary); word-break: break-all; margin-bottom: 12px;
    }
    /* Tombol salin memakai .mk-btn--secondary; saat berhasil disalin, garisnya hijau sebentar. */
    .bp-btn-copy.is-done { border-color: var(--c-success); color: var(--c-success); }
</style>

<div class="modal fade" id="buatPengaduanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- ═══ Pilih jalur ═══ --}}
            <div id="bpPilihan">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 13px; color: var(--c-fg-muted); margin-bottom: 16px;">
                        Pilih jalur pelaporan yang sesuai dengan kebutuhan Anda.
                    </p>
                    <div class="bp-jalur-grid">
                        <a href="{{ route('manajemenmahasiswa.pengaduan.create', ['jalur' => 'reguler']) }}" class="bp-jalur">
                            <span class="bp-jalur-pill">STANDAR</span>
                            <span class="bp-jalur-icon"><x-manajemenmahasiswa::ui.icon name="user-circle" size="22" /></span>
                            <span class="bp-jalur-title">Reguler</span>
                            <span class="bp-jalur-desc">Nama dan identitas Anda terlihat oleh Admin untuk memudahkan tindak lanjut dan koordinasi langsung.</span>
                        </a>

                        {{-- POST, bukan link: aksi ini membuat tiket draft di database. --}}
                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.anon.generate') }}" class="bp-jalur-form" id="bpFormKonfidensial">
                            @csrf
                            <button type="submit" class="bp-jalur is-konfidensial">
                                <span class="bp-jalur-pill">DILINDUNGI</span>
                                <span class="bp-jalur-icon"><x-manajemenmahasiswa::ui.icon name="shield-02" size="22" /></span>
                                <span class="bp-jalur-title">Konfidensial</span>
                                <span class="bp-jalur-desc">Identitas Anda tidak ditampilkan di sistem. Data tetap tersimpan secara internal untuk memastikan masalah dapat diselesaikan dengan tepat.</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ═══ Magic link (menggantikan pilihan jalur) ═══ --}}
            <div id="bpLink" style="display: none;">
                <div class="modal-header">
                    <h5 class="modal-title">Magic Link Dibuat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="bp-link-icon"><x-manajemenmahasiswa::ui.icon name="link-01" size="26" /></div>
                    <p style="font-size: 13px; color: var(--c-fg-muted); line-height: 1.6; margin: 0;">
                        Sistem telah membuatkan Anda link khusus. Anda akan menggunakan link ini untuk
                        <strong>mengisi form pengaduan</strong> dan <strong>melacak balasan</strong> dari admin.
                    </p>

                    <div class="bp-link-box">
                        <div class="fw-bold mb-2" style="font-size: 12px; text-transform: uppercase; color: var(--c-error);">Simpan tautan ini!</div>
                        <a href="#" target="_blank" rel="noopener" class="bp-link-url" id="bpLinkUrl"></a>
                        <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm bp-btn-copy" id="bpSalin">
                            <x-manajemenmahasiswa::ui.icon name="files-01" size="15" /> <span>Salin Tautan</span>
                        </button>
                    </div>

                    <p style="font-size: 12px; color: var(--c-fg-muted); margin-bottom: 18px;">
                        <span style="color: var(--c-warning);"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="13" /></span>
                        Tautan ini bersifat sangat rahasia. Jika hilang, Anda tidak dapat memulihkannya.
                        Pastikan Anda menyalinnya sebelum membuka form.
                    </p>

                    <a href="#" target="_blank" rel="noopener" class="mk-btn mk-btn--primary" id="bpBukaForm">
                        Buka Form Pengaduan (Tab Baru)
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
        const modalEl = document.getElementById('buatPengaduanModal');
        const pilihan = document.getElementById('bpPilihan');
        const panelLink = document.getElementById('bpLink');
        const form = document.getElementById('bpFormKonfidensial');
        const tombolJalur = form.querySelector('button');
        const linkUrl = document.getElementById('bpLinkUrl');
        const salin = document.getElementById('bpSalin');

        // Selalu mulai dari pilihan jalur (mis. setelah modal ditutup saat tautan tampil).
        const resetTampilan = () => {
            pilihan.style.display = '';
            panelLink.style.display = 'none';
            tombolJalur.disabled = false;
        };
        modalEl.addEventListener('hidden.bs.modal', resetTampilan);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            tombolJalur.disabled = true;
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                });
                if (!res.ok) throw new Error(res.status);
                const { url } = await res.json();

                linkUrl.href = url;
                linkUrl.textContent = url;
                document.getElementById('bpBukaForm').href = url;
                pilihan.style.display = 'none';
                panelLink.style.display = '';
            } catch (err) {
                // Cadangan: kirim form biasa → halaman "Magic Link Dibuat" seperti dulu.
                form.submit();
            }
        });

        salin.addEventListener('click', async () => {
            const url = linkUrl.textContent;
            try {
                await navigator.clipboard.writeText(url);
            } catch (err) {
                // Konteks non-HTTPS: pilih teks lalu salin manual.
                const r = document.createRange();
                r.selectNodeContents(linkUrl);
                const s = window.getSelection();
                s.removeAllRanges();
                s.addRange(r);
                document.execCommand('copy');
            }
            const label = salin.querySelector('span');
            label.textContent = '✓ Tersalin!';
            salin.classList.add('is-done');
            setTimeout(() => {
                label.textContent = 'Salin Tautan';
                salin.classList.remove('is-done');
            }, 2000);
        });

        @if(request()->filled('buat'))
            window.addEventListener('load', () => {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        @endif
    })();
</script>
