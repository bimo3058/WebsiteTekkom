{{--
    Modal Tinjau — dipakai bersama halaman Verifikasi admin (admin.blade.php) dan
    halaman pengajuan mahasiswa (mahasiswa.blade.php).

    Bukti (kiri) & data pengajuan (kanan) tampil dalam satu layar. Seluruh isinya
    datang dari payload per baris, jadi halaman pemanggil hanya menyusun daftar
    datanya — kerangka, gaya, dan perilakunya dijamin sama di kedua halaman.

    JANGAN menyalin modal ini ke halaman lain: ubah di sini supaya kedua halaman
    ikut berubah bersamaan.

    Halaman pemanggil wajib memuat partials.tinjau-modal-styles lebih dulu (di
    bagian atas halaman), karena kelas .tp-* & .btn-tinjau juga dipakai tabelnya.

    Variabel include:
      $tinjauAksi  bool — render panel keputusan (Setujui/Tolak). Default false.

    Payload per baris (argumen openTinjau):
      judul     judul modal
      sections  [{judul, items}] — tiap item: [label, nilai]
                nilai boleh array (dirender jadi chip) dan boleh diikuti nama
                kelas badge sebagai elemen ketiga, mis. ['Status', 'Disetujui',
                'status-verif approved']
      bukti     [{url, nama, is_image}, ...]
      pending   bool — baris masih menunggu keputusan
      readonly  teks yang tampil bila tidak ada aksi untuk baris ini
      aksi      opsional, tombol sekunder pada modal baca-saja:
                {label, gaya: 'tolak'|'utama', panggil: 'namaFungsiGlobal', args: [...]}
      jenis, id, contoh — hanya terpakai bila $tinjauAksi (untuk mengirim keputusan)
--}}
@php $tinjauAksi = $tinjauAksi ?? false; @endphp

<div class="modal fade tinjau-modal" id="tinjauModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <span id="tinjauTitle">Tinjau Pengajuan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="tp-grid">
                    @include('manajemenmahasiswa::verifikasi.partials.tinjau-bukti-pane')

                    {{-- Kanan: data pengajuan + aksi --}}
                    <div class="tp-pane-data">
                        <div id="tpFields"></div>

                        @if($tinjauAksi)
                            {{-- Form hanya membungkus panel keputusan: halaman yang tidak
                                 memutus apa pun (mahasiswa) tidak ikut memuat form ini. --}}
                            <form id="tinjauForm" method="POST" style="margin-top: auto;">
                                @csrf @method('PATCH')
                                <div id="tpActions" style="padding-top: 18px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold mb-0" style="font-size: 12.5px;">Catatan Verifikasi</label>
                                        <span class="text-muted" style="font-size: 11px;" id="charCount_tp">0 / 200 huruf</span>
                                    </div>
                                    <textarea name="verification_note" id="tpNote" class="form-control" rows="3" maxlength="200"
                                              style="border-radius: 10px; font-size: 13.5px;"
                                              oninput="document.getElementById('charCount_tp').innerText = this.value.length + ' / 200 huruf'; document.getElementById('tpError').style.display = 'none';"></textarea>
                                    <div id="tpError" style="display: none; font-size: 12px; font-weight: 600; color: var(--c-error); margin-top: 6px;"></div>
                                    <div class="tp-aksi">
                                        <button type="button" id="tpTolakBtn" class="tp-btn-tolak">Tolak</button>
                                        <button type="button" id="tpSetujuiBtn" class="tp-btn-setujui">Setujui</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <div id="tpReadonly" style="display: none; margin-top: auto; padding-top: 18px;">
                            <p id="tpReadonlyText" style="font-size: 12.5px; color: var(--c-fg-muted); margin: 0;"></p>
                            {{-- Aksi lanjutan (mis. batalkan pengajuan reward) diletakkan di
                                 bawah datanya, bukan di baris tabel — jadi baru bisa diambil
                                 setelah isinya terbaca. --}}
                            <div id="tpAksiLain" style="margin-top: 14px;"></div>
                            <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal"
                                    style="border-radius: 10px; margin-top: 10px; font-weight: 600; font-size: 13.5px;">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ── Modal Tinjau ───────────────────────────────────────────────────────
// Bukti & data pengajuan tampil berdampingan; keputusan (bila halaman ini
// memilikinya) hanya bisa diambil dari sini, jadi tidak pernah sebelum
// buktinya terlihat.
let tpData = null;

// Dipakai bersama modal lain di halaman yang sama (mis. Ajukan Reward), jadi
// panel & datanya dioper sebagai argumen — bukan diambil dari id global.
function tpRenderBukti(pane, bukti, idx) {
    if (!pane) return;
    const preview = pane.querySelector('[data-tp-preview]');
    const thumbs  = pane.querySelector('[data-tp-thumbs]');
    const openTab = pane.querySelector('[data-tp-opentab]');
    bukti = bukti || [];

    preview.innerHTML = '';
    thumbs.innerHTML  = '';

    if (!bukti.length) {
        const kosong = document.createElement('div');
        kosong.style.cssText = 'padding: 70px 0; text-align: center; font-size: 13px; color: var(--c-fg-muted);';
        kosong.textContent = 'Tidak ada bukti dilampirkan';
        preview.appendChild(kosong);
        openTab.style.display = 'none';
        return;
    }

    const b = bukti[idx] || bukti[0];
    if (b.is_image) {
        const img = document.createElement('img');
        img.src = b.url;
        img.alt = b.nama || 'Bukti';
        img.className = 'tp-viewer-img';
        preview.appendChild(img);
    } else {
        const frame = document.createElement('iframe');
        frame.src = b.url + '#view=FitH';
        frame.title = b.nama || 'Bukti';
        frame.className = 'tp-viewer';
        preview.appendChild(frame);
    }

    openTab.href = b.url;
    openTab.style.display = 'inline';

    // Selector hanya perlu bila pengajuan punya lebih dari satu berkas
    if (bukti.length > 1) {
        bukti.forEach(function (f, i) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tp-thumb' + (i === idx ? ' active' : '');
            btn.title = f.nama || 'Bukti';
            btn.onclick = function () { tpRenderBukti(pane, bukti, i); };
            if (f.is_image) {
                const th = document.createElement('img');
                th.src = f.url;
                th.alt = '';
                btn.appendChild(th);
            } else {
                btn.textContent = 'PDF';
            }
            thumbs.appendChild(btn);
        });
    }
}

// Juga dipakai modal Ajukan Reward untuk menampilkan blok data prestasinya,
// supaya daftar data di kedua modal tersusun dengan cara yang sama persis.
function tpRenderSections(container, sections) {
    if (!container) return;
    container.innerHTML = '';

    (sections || []).forEach(function (sec) {
        const blok = document.createElement('div');
        blok.className = 'tp-section';

        if (sec.judul) {
            const judul = document.createElement('p');
            judul.className = 'tp-pane-heading';
            judul.textContent = sec.judul;
            blok.appendChild(judul);
        }

        (sec.items || []).forEach(function (item) {
            const row = document.createElement('div');
            row.className = 'tp-field';

            const lbl = document.createElement('span');
            lbl.className = 'tp-field-label';
            lbl.textContent = item[0];

            const val = document.createElement('span');
            val.className = 'tp-field-value';

            const isi = item[1];
            if (Array.isArray(isi)) {
                // Daftar pendek → chip, mis. mata kuliah yang diusulkan
                const chips = document.createElement('span');
                chips.className = 'tp-chips';
                isi.forEach(function (teks) {
                    const chip = document.createElement('span');
                    chip.className = 'tp-chip';
                    chip.textContent = teks;
                    chips.appendChild(chip);
                });
                val.appendChild(chips);
            } else if (item[2]) {
                // Badge — kelasnya datang dari halaman, isinya tetap textContent
                const badge = document.createElement('span');
                badge.className = item[2];
                badge.textContent = isi || '—';
                val.appendChild(badge);
            } else {
                // textContent, bukan innerHTML — isinya berasal dari input mahasiswa
                val.textContent = isi || '—';
            }

            row.appendChild(lbl);
            row.appendChild(val);
            blok.appendChild(row);
        });

        container.appendChild(blok);
    });
}

function openTinjau(data) {
    tpData = data;

    const modalEl = document.getElementById('tinjauModal');
    document.getElementById('tinjauTitle').textContent = data.judul || 'Tinjau Pengajuan';

    tpRenderSections(document.getElementById('tpFields'), data.sections);
    tpRenderBukti(modalEl.querySelector('[data-tp-bukti]'), data.bukti, 0);

    // Panel keputusan hanya untuk baris pending di halaman yang memang berwenang;
    // selain itu modal jadi baca-saja dengan keterangan dari payload.
    const aksiEl = document.getElementById('tpActions');
    const noteEl = document.getElementById('tpNote');
    const bolehAksi = !!aksiEl && !!data.pending;

    if (aksiEl) aksiEl.style.display = bolehAksi ? 'block' : 'none';
    document.getElementById('tpReadonly').style.display = bolehAksi ? 'none' : 'block';
    document.getElementById('tpReadonlyText').textContent = data.readonly || '';

    // Aksi lanjutan dipanggil lewat nama fungsi global — modal ini tidak perlu
    // tahu apa pun tentang alur milik halaman pemanggil.
    const lainEl = document.getElementById('tpAksiLain');
    lainEl.innerHTML = '';
    if (!bolehAksi && data.aksi && typeof window[data.aksi.panggil] === 'function') {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = data.aksi.gaya === 'tolak' ? 'tp-btn-batal' : 'tp-btn-utama';
        btn.textContent = data.aksi.label;
        btn.onclick = function () {
            const lanjut = function () { window[data.aksi.panggil].apply(null, data.aksi.args || []); };
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (!inst) return lanjut();
            // Tunggu modal ini benar-benar tertutup — kalau modal berikutnya dibuka
            // sebelum animasi selesai, backdrop Bootstrap tertinggal di layar.
            modalEl.addEventListener('hidden.bs.modal', lanjut, { once: true });
            inst.hide();
        };
        lainEl.appendChild(btn);
    }

    if (bolehAksi) {
        noteEl.placeholder = data.contoh || '';
        noteEl.value = '';
        document.getElementById('charCount_tp').innerText = '0 / 200 huruf';
        document.getElementById('tpError').style.display = 'none';
    }

    new bootstrap.Modal(modalEl).show();
}

@if($tinjauAksi)
(function () {
    const baseUrl = '{{ url("manajemen-mahasiswa/verifikasi") }}';
    const form    = document.getElementById('tinjauForm');
    if (!form) return;
    const errEl = document.getElementById('tpError');

    function kirim(aksi) {
        if (!tpData) return;
        // Alasan wajib saat menolak; menyetujui boleh tanpa catatan
        if (aksi === 'reject' && !document.getElementById('tpNote').value.trim()) {
            errEl.textContent = 'Alasan penolakan wajib diisi.';
            errEl.style.display = 'block';
            return;
        }
        form.action = baseUrl + '/' + tpData.jenis + '/' + tpData.id + '/' + aksi;
        form.submit();
    }

    document.getElementById('tpSetujuiBtn').addEventListener('click', function () { kirim('approve'); });
    document.getElementById('tpTolakBtn').addEventListener('click', function () { kirim('reject'); });
})();
@endif
</script>
