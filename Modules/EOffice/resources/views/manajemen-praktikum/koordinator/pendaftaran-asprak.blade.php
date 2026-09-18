<x-eoffice::manajemen-praktikum.layout
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
                                            class="absolute right-1 top-1/2 -translate-y-1/2 text-[#DF1C41] hover:bg-[#FFF5F5] p-1.5 rounded-[6px] transition-colors"
                                            title="Hapus Berkas">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                                                <path d="M10 11v6M14 11v6" />
                                            </svg>
                                        </button>
                                    </div>
                                    <template x-if="berkasDesc">
                                        <p class="text-[11px] text-[#666D80] mt-1" x-text="berkasDesc"></p>
                                    </template>
                                </div>
                            </div>
                        </div>


                        <div class="mt-5">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-2">Jadwal <span
                                    class="text-red-500">*</span> <span class="text-[#808897] font-normal">(Pilih hari
                                    luang)</span></label>
                            <div class="flex gap-6 flex-wrap">
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Senin</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Selasa</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Rabu</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Kamis</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Jumat</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Sabtu</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Minggu</span>
                                </label>
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

                        <th class="mp-th text-left" style="padding:10px 16px;">KEANGGOTAAN CERC</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Jadwal</th>
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
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice') }}"
                                        target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>

                            <td style="padding:10px 16px;">
                                @if($p->berkas_cerc_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, 'eoffice') }}"
                                        target="_blank"
                                        style="display:inline-flex; align-items:center; gap:6px; color:#0B266E; font-size:12px; font-weight:600; text-decoration:none;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="color:#808897; font-size:12px; font-style:italic;">Tidak ada</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:#666D80;">
                                {{ collect($p->jadwal ?? [])->join(', ') ?: '—' }}
                            </td>
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
                                                @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) .closest('form').submit()"
                                                class="mp-btn destructive sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size:13px;color:#808897;">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:48px;text-align:center;">
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
        @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }"
                        class="relative" @click.away="open = false">
                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                            :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                            @click="open = !open">
                            <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per
                                halaman</span>
                            <div class="flex items-center gap-1 font-semibold text-[12px]">
                                <span x-text="selected"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div x-show="open" @click.away="open = false" style="display: none;"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                            <template x-for="option in options" :key="option">
                                <a :href="'?per_page=' + option + '&' + decodeURIComponent(new URLSearchParams(Object.fromEntries(Object.entries(Object.fromEntries(new URLSearchParams(window.location.search))).filter(([k,v])=>k!=='per_page'))).toString())"
                                    class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                    :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <span x-text="option"></span>
                                    <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>
                    <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $pendaftaran->firstItem() ?? 0 }}
                        sampai {{ $pendaftaran->lastItem() ?? 0 }} dari {{ $pendaftaran->total() }} data</div>
                </div>

                <div style="display:flex; gap:4px;">
                    @if ($pendaftaran->onFirstPage())
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $pendaftaran->previousPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $pendaftaran->currentPage();
                        $last = $pendaftaran->lastPage();
                        $start = max(1, $current - 1);
                        $end = min($start + 2, $last);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <span
                                style="width:32px; height:32px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $pendaftaran->url($i) }}"
                                style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($pendaftaran->hasMorePages())
                        <a href="{{ $pendaftaran->nextPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </a>
                    @else
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-eoffice::manajemen-praktikum.layout>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
    /* Styling Flatpickr untuk mengikuti tema desain */
    .flatpickr-calendar {
        width: 340px !important;
        padding-right: 12px !important;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12) !important;
        border: 1px solid #DFE1E7 !important;
        border-radius: 12px !important;
        font-family: inherit !important;
        padding-left: 8px !important;
        padding-bottom: 8px !important;
    }

    .flatpickr-days {
        width: 320px !important;
    }

    .dayContainer {
        width: 320px !important;
        min-width: 320px !important;
        max-width: 320px !important;
    }

    .flatpickr-calendar.hasTime .flatpickr-time {
        border-top: 1px solid #DFE1E7 !important;
        margin-top: 8px;
    }

    .flatpickr-months {
        margin-bottom: 8px !important;
    }

    .flatpickr-current-month {
        font-size: 14px !important;
        padding-top: 0px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        display: none !important;
    }

    .flatpickr-current-month .cur-month {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
        padding-top: 0px !important;
        cursor: pointer;
    }

    .flatpickr-current-month .cur-month:hover {
        color: #0B266E !important;
    }

    .custom-month-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
        z-index: 99999;
        width: 140px;
        max-height: 220px;
        overflow-y: auto;
        padding: 6px;
        font-family: inherit;
        visibility: hidden;
        opacity: 0;
        transform: translateY(-8px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .custom-month-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .custom-month-dropdown::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-month-dropdown::-webkit-scrollbar-thumb {
        background: #DFE1E7;
        border-radius: 4px;
    }

    .custom-month-dropdown.show {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .custom-month-arrow svg {
        transition: transform 0.2s ease;
    }

    .custom-month-arrow.open svg {
        transform: rotate(180deg);
    }

    .custom-month-item {
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #353849;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .custom-month-item:hover {
        background: #F6F8FA;
        color: #0B266E;
    }

    .custom-month-item.active {
        background: #0B266E !important;
        color: #fff !important;
    }


    .flatpickr-current-month .numInputWrapper {
        width: 65px !important;
        border-radius: 6px !important;
        margin-left: 4px;
    }

    .flatpickr-current-month input.cur-year {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
        padding: 6px 8px !important;
        border: 1px solid transparent !important;
        border-radius: 6px !important;
        height: 32px !important;
        box-sizing: border-box !important;
        background: transparent !important;
        transition: all 0.2s;
    }

    .flatpickr-current-month .numInputWrapper:hover input.cur-year {
        border: 1px solid #DFE1E7 !important;
        background: #F9FAFB !important;
    }

    /* Customizing the arrows */
    .flatpickr-current-month .numInputWrapper span.arrowUp,
    .flatpickr-current-month .numInputWrapper span.arrowDown {
        border: none !important;
        border-left: 1px solid transparent !important;
        right: 1px;
    }

    .flatpickr-current-month .numInputWrapper:hover span.arrowUp,
    .flatpickr-current-month .numInputWrapper:hover span.arrowDown {
        border-left: 1px solid #DFE1E7 !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp {
        border-top-right-radius: 5px;
        top: 1px;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown {
        border-bottom-right-radius: 5px;
        bottom: 1px;
    }

    .flatpickr-current-month .numInputWrapper span:hover {
        background: #F0F2F5 !important;
    }

    /* Color of the arrow triangles */
    .flatpickr-current-month .numInputWrapper span.arrowUp:after {
        border-bottom-color: #666D80 !important;
        top: 35% !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown:after {
        border-top-color: #666D80 !important;
        top: 40% !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp:hover:after {
        border-bottom-color: #0B266E !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown:hover:after {
        border-top-color: #0B266E !important;
    }


    .flatpickr-day {
        border-radius: 8px !important;
        font-size: 13px !important;
        color: #353849 !important;
        max-width: 42px !important;
        height: 40px !important;
        line-height: 40px !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:focus,
    .flatpickr-day.selected:hover {
        background: #0B266E !important;
        border-color: #0B266E !important;
        color: #fff !important;
        font-weight: 600;
    }

    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #C1C7D0 !important;
    }

    .flatpickr-day:hover,
    .flatpickr-day.prevMonthDay:hover,
    .flatpickr-day.nextMonthDay:hover {
        background: #F6F8FA !important;
        border-color: #DFE1E7 !important;
    }

    .flatpickr-time input {
        color: #353849 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .flatpickr-time .flatpickr-time-separator {
        color: #808897 !important;
    }

    .flatpickr-time .numInputWrapper:hover {
        background: #F6F8FA !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.flatpickr-input', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true,
            monthSelectorType: "static",
            onReady: function (selectedDates, dateStr, instance) {
                // Buat custom dropdown untuk ganti bulan secara estetik
                const monthDropdown = document.createElement('div');
                monthDropdown.className = 'custom-month-dropdown';

                const monthsStr = instance.l10n.months.longhand;
                monthsStr.forEach((month, index) => {
                    const div = document.createElement('div');
                    div.className = 'custom-month-item';
                    div.textContent = month;
                    div.addEventListener('click', (e) => {
                        e.stopPropagation();
                        instance.changeMonth(index, false); // set bulan
                        monthDropdown.classList.remove('show');
                        if (instance.monthNav.querySelector('.custom-month-arrow')) {
                            instance.monthNav.querySelector('.custom-month-arrow').classList.remove('open');
                        }
                    });
                    monthDropdown.appendChild(div);
                });

                instance.calendarContainer.appendChild(monthDropdown);

                const monthElement = instance.monthNav.querySelector('.cur-month');
                if (monthElement) {
                    // Add simple arrow to element parent to avoid reset issue
                    const arrow = document.createElement('span');
                    arrow.className = 'custom-month-arrow';
                    arrow.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;
                    arrow.style.marginLeft = '4px';
                    arrow.style.cursor = 'pointer';
                    arrow.style.display = 'inline-flex';
                    arrow.style.alignItems = 'center';
                    monthElement.parentNode.insertBefore(arrow, monthElement.nextSibling);

                    // Wrapper trigger
                    const toggleDropdown = (e) => {
                        e.stopPropagation();
                        monthDropdown.classList.toggle('show');

                        if (monthDropdown.classList.contains('show')) {
                            arrow.classList.add('open');
                        } else {
                            arrow.classList.remove('open');
                        }

                        const monthRect = monthElement.getBoundingClientRect();
                        const calRect = instance.calendarContainer.getBoundingClientRect();

                        monthDropdown.style.top = (monthRect.bottom - calRect.top + 3) + 'px';
                        monthDropdown.style.left = (monthRect.left - calRect.left - 6) + 'px';

                        const items = monthDropdown.querySelectorAll('.custom-month-item');
                        items.forEach((item, idx) => {
                            item.classList.toggle('active', idx === instance.currentMonth);
                        });

                        // Scroll into view safely
                        const activeItem = monthDropdown.querySelector('.custom-month-item.active');
                        if (activeItem && monthDropdown.classList.contains('show')) {
                            monthDropdown.scrollTop = activeItem.offsetTop - 10;
                        }
                    };

                    monthElement.addEventListener('click', toggleDropdown);
                    arrow.addEventListener('click', toggleDropdown);
                }

                // Hide if clicked out
                document.addEventListener('click', (e) => {
                    if (monthDropdown.classList.contains('show') && !instance.monthNav.contains(e.target) && !monthDropdown.contains(e.target)) {
                        monthDropdown.classList.remove('show');
                        if (monthElement.nextSibling && monthElement.nextSibling.classList) {
                            monthElement.nextSibling.classList.remove('open');
                        }
                    }
                });
            }
        });
    });
</script>

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
            class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 p-4">
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
                                        viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5"
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

                        <div class="flex gap-3 justify-end mt-4 pt-5 border-t border-[#DFE1E7]">
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