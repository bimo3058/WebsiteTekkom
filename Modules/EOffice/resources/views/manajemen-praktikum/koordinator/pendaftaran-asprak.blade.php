<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null"
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Seleksi Asisten">

    @if($praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif

    {{-- ── BUKA PERIODE BARU ────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <div class="mp-card flex-shrink-0 ">
        <div class="mp-card-header">
            <span class="mp-card-title">Form Pendaftaran Asisten</span>
        </div>

        <form action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">

            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Judul</label>
                    <input type="text" name="nama" class="mp-input w-full"
                        placeholder="Contoh: Seleksi Asisten Praktikum Genap 2025"
                        value="{{ old('nama', $periodeAktif->nama ?? '') }}">
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="mp-input w-full" rows="3" maxlength="500"
                        placeholder="Tuliskan deskripsi atau link soal tes..."
                        style="resize: vertical; min-height: 80px; max-height: 200px;">{{ old('deskripsi', $periodeAktif->deskripsi ?? '') }}</textarea>
                    <p class="text-[11px] text-[#666D80] mt-1">Maksimal 500 karakter.</p>
                </div>

                <hr class="border-t border-dashed border-[#DFE1E7] my-2">

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dibuka Pada</label>
                    <div class="relative">
                        <input type="text" name="dibuka_pada"
                            class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]"
                            placeholder="Pilih tanggal dan waktu..."
                            value="{{ old('dibuka_pada', isset($periodeAktif) && $periodeAktif->dibuka_pada ? \Carbon\Carbon::parse($periodeAktif->dibuka_pada)->format('Y-m-d H:i') : '') }}"
                            style="padding-right: 32px; cursor: pointer;">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <p class="text-[11px] text-[#666D80] mt-1">Kosongkan untuk langsung dibuka saat ini.</p>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Ditutup Pada <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="ditutup_pada"
                            class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]"
                            placeholder="Pilih tanggal dan waktu..."
                            value="{{ old('ditutup_pada', isset($periodeAktif) && $periodeAktif->ditutup_pada ? \Carbon\Carbon::parse($periodeAktif->ditutup_pada)->format('Y-m-d H:i') : '') }}"
                            required style="padding-right: 32px; cursor: pointer;">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <p class="text-[11px] text-[#666D80] mt-1">Wajib diisi sebagai batas penutupan.</p>
                </div>

                <hr class="border-t border-dashed border-[#DFE1E7] my-2">

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-3">Biodata & Persyaratan (Wajib
                        diisi mahasiswa)</label>

                    <div class="flex gap-4 mb-3">
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">Nama Mahasiswa</label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">NIM</label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                    </div>

                    <div class="flex gap-4 mb-3">
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">IPK <span
                                    class="text-red-500">*</span></label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Diisi oleh mahasiswa" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">Transkrip Nilai <span
                                    class="text-red-500">*</span></label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)" disabled
                                style="background: #F9FAFB; color: #808897; border-style: dashed;">
                        </div>
                    </div>

                    <div x-data="{
                        berkasName: '{{ $periodeAktif->nama_berkas_tambahan ?? '' }}',
                        berkasType: '{{ $periodeAktif->jenis_berkas_tambahan ?? '' }}',
                        berkasDesc: '{{ $periodeAktif->keterangan_berkas_tambahan ?? '' }}',
                        removeBerkas() {
                            this.berkasName = '';
                            this.berkasType = '';
                            this.berkasDesc = '';
                        }
                    }" @save-berkas.window="
                        if ($event.detail.name.trim() !== '' && $event.detail.type !== '') {
                            berkasName = $event.detail.name.trim();
                            berkasType = $event.detail.type;
                            berkasDesc = $event.detail.desc.trim();
                        } else {
                            alert('Nama Berkas dan Jenis Berkas wajib diisi!');
                        }
                    ">
                        <input type="hidden" name="nama_berkas_tambahan" :value="berkasName">
                        <input type="hidden" name="jenis_berkas_tambahan" :value="berkasType">
                        <input type="hidden" name="keterangan_berkas_tambahan" :value="berkasDesc">

                        <div class="flex gap-4 mb-3">
                            <div class="flex-1">
                                <label class="block text-[11px] font-semibold text-[#353849] mb-1">Keanggotaan CERC</label>
                                <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)" disabled
                                    style="background: #F9FAFB; color: #808897; border-style: dashed;">
                            </div>

                            <div class="flex-1">
                                <div x-show="!berkasName">
                                    <label
                                        class="block text-[11px] font-semibold text-transparent mb-1 select-none">&nbsp;</label>
                                    <button type="button"
                                        @click="$dispatch('open-berkas-modal', { name: berkasName, type: berkasType, desc: berkasDesc })"
                                        class="h-[38px] w-full text-[#0B266E] text-[12px] font-semibold flex justify-center items-center gap-1.5 hover:underline bg-[#F6F8FA] px-4 rounded-[8px] border border-dashed border-[#DFE1E7]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                        Tambah Pengumpulan Berkas
                                    </button>
                                </div>


                                {{-- If berkas exists --}}
                                <div x-show="berkasName" style="display: none;" x-transition>
                                    <label class="block text-[11px] font-semibold text-[#353849] mb-1">
                                        <span x-text="berkasName"></span> <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" class="mp-input w-full text-[12px]"
                                            :value="berkasType === 'pdf' ? 'Upload File (.pdf)' : 'Input Link'" disabled
                                            style="background: #F9FAFB; color: #808897; border-style: dashed; padding-right: 36px;">
                                        <button type="button" @click="removeBerkas()"
                                            class="mp-delete-button absolute right-1 top-1/2 -translate-y-1/2 text-[#DF1C41] hover:bg-[#FFF5F5] p-1.5 rounded-[6px] transition-colors"
                                            title="Hapus Berkas" aria-label="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                                    </div>
                                    <template x-if="berkasDesc">
                                        <p class="text-[11px] text-[#666D80] mt-1" x-text="berkasDesc"></p>
                                    </template>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                @if($periodeAktif && $periodeAktif->isSedangBuka())
                    <button type="submit" form="tutup-periode" class="mp-btn error sm"
                        onclick="return confirm('Tutup periode pendaftaran asisten praktikum sekarang?')"
                        style="height:36px; padding:0 16px;">Tutup Periode Sekarang</button>
                @endif
                <button type="submit" class="mp-btn primary sm"
                    style="height:36px; padding:0 16px;">{{ $periodeAktif ? 'Update Pendaftaran' : 'Buka Periode Pendaftaran' }}</button>
            </div>
        </form>
        @if($periodeAktif && $periodeAktif->isSedangBuka())
            <form id="tutup-periode" method="POST"
                action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $periodeAktif->id) }}"
                style="display:none;">
                @csrf
            </form>
        @endif
    </div>

    {{-- CARD 3: PENDAFTAR ASPRAK (TABEL) --}}
    <div class="mp-card flex-shrink-0">
        <div class="mp-card-header"
            style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
            <span class="mp-card-title">Daftar Calon Asisten</span>
            <span class="mp-badge navy sm" style="font-size: 11px;">{{ $pendaftaran->total() }} pendaftar</span>
        </div>
        <div style="padding: 16px 20px; border-bottom: 1px solid #DFE1E7; background: #fff;">
            <form method="GET" style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <input type="hidden" name="praktikum_id" value="{{ request('praktikum_id', $praktikum?->id) }}">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa..." class="mp-input w-full"
                        style="height:36px; font-size:13px; width:250px; padding-left: 36px; padding-right: 12px;">
                </div>

                <x-eoffice::manajemen-praktikum.ui.select name="sort" :options="[
        ['value' => 'terbaru', 'label' => 'Terbaru'],
        ['value' => 'terlama', 'label' => 'Terlama'],
        ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
        ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
    ]" :selected="request('sort', 'terbaru')"
                    placeholder="Urutkan..." onChange="$event.target.form.submit()" minWidth="140px" />

                <x-eoffice::manajemen-praktikum.ui.select name="status" :options="[
        ['value' => '', 'label' => 'Semua Status'],
        ['value' => 'pending', 'label' => 'Menunggu Review'],
        ['value' => 'approved', 'label' => 'Sudah Disetujui'],
        ['value' => 'rejected', 'label' => 'Ditolak']
    ]" :selected="request('status', '')"
                    placeholder="Semua Status" onChange="$event.target.form.submit()" minWidth="160px" />

                <button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">
                    Filter
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:13px;">
                <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                    <tr>
                        <th class="mp-th text-left" style="padding:10px 20px; width:40px;">No</th>
                        <th class="mp-th text-left" style="padding:10px 16px; padding-left: 0;">NAMA MAHASISWA</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">NIM</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">IPK</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">TRANSKRIP NILAI</th>
                        @if(filled($periodeTabel?->nama_berkas_tambahan))
                            <th class="mp-th text-left" style="padding:10px 16px;">
                                {{ strtoupper($periodeTabel->nama_berkas_tambahan) }}
                            </th>
                        @endif
                        <th class="mp-th text-left" style="padding:10px 16px;">KEANGGOTAAN CERC</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $p)
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:12px 20px;color:#808897;font-size:12px;">
                                {{ $loop->iteration }}
                            </td>
                            <td style="padding:12px 16px; padding-left: 0;">
                                <div class="flex items-center gap-[10px]">
                                    <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '--' }}</div>
                                        <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @php
                                $emailStr_c = $p->user?->email ?? '';
                                $nim_c = explode('@', $emailStr_c)[0];
                                if (empty($nim_c))
                                    $nim_c = '—';
                            @endphp
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{ $nim_c }}
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:400; font-size:13px; color:#666D80;">
                                    {{ number_format($p->ipk ?? 0, 2) }}
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->transkrip_path)
                                    <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($p->transkrip_path, 'eoffice') }}"
                                        target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>

                            <td style="padding:10px 16px;">
                                @if($p->berkas_cerc_path)
                                    <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($p->berkas_cerc_path, 'eoffice') }}"
                                        target="_blank"
                                        style="display:inline-flex; align-items:center; gap:6px; color:#0B266E; font-size:12px; font-weight:600; text-decoration:none;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="color:#808897; font-size:12px; font-style:italic;">Tidak ada</span>
                                @endif
                            </td>
                            @if(filled($periodeTabel?->nama_berkas_tambahan))
                                <td style="padding:10px 16px;">
                                    @if($p->berkas_tambahan_path)
                                        <a href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($p->berkas_tambahan_path, 'eoffice') }}"
                                            target="_blank" rel="noopener noreferrer"
                                            style="display:inline-flex; align-items:center; gap:6px; color:#0B266E; font-size:12px; font-weight:600; text-decoration:none;"
                                            class="hover:underline">Lihat Berkas</a>
                                    @else
                                        <span style="color:#808897; font-size:12px; font-style:italic;">Tidak ada</span>
                                    @endif
                                </td>
                            @endif
                            <td style="padding:12px 16px;">
                                @if($p->status_koor === 'disetujui')
                                    <div>
                                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                                        <div style="font-size:11px;color:#666D80;margin-top:4px;">Menunggu Admin</div>
                                    </div>
                                @elseif($p->status_koor === 'ditolak' || $p->status === 'rejected')
                                    <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                                @else
                                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->status_koor === 'menunggu')
                                    <div class="flex gap-2" x-data="{ catatan: '' }">
                                        <x-eoffice::manajemen-praktikum.ui.action-menu>
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.approve', $p->id) }}">
                                            @csrf
                                            <input type="hidden" name="catatan_koor" value="">
                                            <button type="submit" class="mp-btn ghost sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.reject', $p->id) }}"
                                            x-data="{ alasan: '' }">
                                            @csrf
                                            <input type="hidden" name="alasan_penolakan" :value="alasan">
                                            <button type="button"
                                                @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                                class="mp-btn destructive sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                        </x-eoffice::manajemen-praktikum.ui.action-menu>
                                    </div>
                                @else
                                    <span style="font-size:13px;color:#808897;">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ filled($periodeTabel?->nama_berkas_tambahan) ? 9 : 8 }}" style="padding:48px;text-align:center;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    style="margin:0 auto 12px;display:block;">
                                    <path
                                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5" />
                                </svg>
                                <div style="font-size:13px;color:#666D80;">Belum ada pendaftar asprak.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Custom Fungsional --}}
        @if(isset($pendaftaran) && $pendaftaran instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$pendaftaran" :options="[5, 10, 20]" />
        @endif
    </div>

</x-eoffice::manajemen-praktikum.layout>

<x-eoffice::manajemen-praktikum.ui.datetime-picker-assets />

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quizBuilder', () => ({
            showQuizModal: false,
            questions: [],
            init() {
                try {
                    // For live interaction preview
                    let existing = `{!! isset($periodeAktif) && $periodeAktif?->konfigurasi_kuis ? json_encode($periodeAktif->konfigurasi_kuis) : '[]' !!}`;
                    this.questions = JSON.parse(existing);
                } catch (e) {
                    this.questions = [];
                }
            },
            addQuestion(type) {
                this.questions.push({
                    id: Date.now(),
                    tipe: type,
                    pertanyaan: '',
                    poin: 10,
                    opsi: type === 'pilihan_ganda' ? [
                        { text: 'Opsi Jawaban A', is_correct: true },
                        { text: 'Opsi Jawaban B', is_correct: false }
                    ] : []
                });
                setTimeout(() => {
                    const modalBody = document.querySelector('.overflow-y-auto');
                    if (modalBody) modalBody.scrollTop = modalBody.scrollHeight;
                }, 50);
            },
            removeQuestion(index) {
                if (confirm('Yakin ingin menghapus form soal ini?')) {
                    this.questions.splice(index, 1);
                }
            },
            addOption(qIndex) {
                this.questions[qIndex].opsi.push({ text: '', is_correct: false });
            },
        },
            removeOption(qIndex, optIndex) {
            this.questions[qIndex].opsi.splice(optIndex, 1);
        },
            setCorrectOption(qIndex, optIndex) {
            this.questions[qIndex].opsi.forEach((o, i) => o.is_correct = (i === optIndex));
        }
        }));
    });
</script>

<!-- Modal Tambah Berkas (Global) -->
<div x-data="{
    show: false,
    tempName: '',
    tempType: '',
    tempDesc: '',
    dropdownOpen: false,
    options: [
        { value: 'pdf', label: 'Berkas PDF (Maks. 5MB)' },
        { value: 'link', label: 'Link Tautan' }
    ],
    get selectedLabel() {
        let opt = this.options.find(o => o.value === this.tempType);
        return opt ? opt.label : 'Pilih Jenis Berkas';
    },
    init() {
        this.$watch('show', value => {
            if (value) { $store.modal.open(); }
            else { $store.modal.close(); }
        });
    }
}" @open-berkas-modal.window="
    tempName = $event.detail.name;
    tempType = $event.detail.type || '';
    tempDesc = $event.detail.desc;
    show = true;
    dropdownOpen = false;
">
    <template x-teleport="body">
        <div x-show="show" style="display:none;" x-cloak
            class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/50 transition-all duration-300 p-4">
            <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]"
                @click.away="show = false" x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                    <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Pengumpulan Berkas</div>
                </div>
                <div class="overflow-y-visible flex-1">
                    <div class="p-6 flex flex-col gap-4">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama
                                Berkas Pengumpulan <span class="text-red-500">*</span></label>
                            <input type="text" x-model="tempName"
                                class="mp-input w-full text-[13px] bg-white border-[#DFE1E7]"
                                placeholder="Contoh: Portofolio">
                        </div>

                        <!-- Custom Dropdown for Jenis Berkas -->
                        <div>
                            <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jenis
                                Berkas <span class="text-red-500">*</span></label>
                            <div class="relative w-full">
                                <button type="button" @click="dropdownOpen = !dropdownOpen"
                                    @click.away="dropdownOpen = false"
                                    class="mp-input w-full bg-white flex justify-between items-center text-left"
                                    :class="dropdownOpen ? 'border-[#0B266E] ring-2 ring-[#0B266E]/10' : 'border-[#DFE1E7]'">
                                    <span x-text="selectedLabel"
                                        :class="tempType === '' ? 'text-[#808897]' : 'text-[#0D0D12]'"></span>
                                    <svg class="transition-transform duration-200"
                                        :class="dropdownOpen ? 'rotate-180' : ''" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>

                                <div x-show="dropdownOpen" x-transition.opacity.duration.200ms
                                    class="absolute z-20 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[12px] shadow-lg py-1.5"
                                    style="display:none; top: 100%;">
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" @click="tempType = option.value; dropdownOpen = false"
                                            class="w-full text-left px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-[#F8FAFC] flex justify-between items-center"
                                            :class="tempType === option.value ? 'text-[#0B266E]' : 'text-[#353849]'">
                                            <span x-text="option.label"></span>
                                            <svg x-show="tempType === option.value" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#353849] mb-1">Keterangan
                                (Opsional)</label>
                            <input type="text" x-model="tempDesc"
                                class="mp-input w-full text-[13px] bg-white border-[#DFE1E7]"
                                placeholder="Tambahkan petunjuk untuk asisten...">
                        </div>

                        <div class="mp-modal-footer mp-modal-footer-bleed flex gap-3 justify-end mt-4 pt-5 border-t border-[#DFE1E7]" style="--mp-modal-footer-radius:16px;">
                            <button type="button" @click="show = false" class="mp-btn secondary md px-5">Batal</button>
                            <button type="button"
                                @click="$dispatch('save-berkas', {name: tempName, type: tempType, desc: tempDesc}); show = false"
                                class="mp-btn primary md px-5">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
    </template>
</div>
