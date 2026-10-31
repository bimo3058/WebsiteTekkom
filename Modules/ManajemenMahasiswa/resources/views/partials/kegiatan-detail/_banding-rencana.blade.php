{{--
    Isi tab "Rencana vs Pelaksanaan" di detail Laporan & Arsip (S-70), mengikuti mockup
    Opsi A pilihan Surya (31 Okt 2026).

    Kolom Rencana = baris Rencana Proker yang terkunci sejak Ajukan (S-49). Kolom
    Pelaksanaan = salinannya, yang isinya diganti angka nyata selama Pelaksanaan & Arsip.
    Data disiapkan RencanaProkerService::bandingkan().

    Variabel wajib: $banding, $kegiatan.
--}}
@php $isianBeda = collect($banding['baris'])->where('sesuai', false)->pluck('label'); @endphp
<div class="detail-card banding-card">
    <div class="banding-ringkas">
        <strong>{{ $kegiatan->judul }}</strong>
        @if($isianBeda->isNotEmpty())
            <span>· {{ $isianBeda->count() }} dari {{ count($banding['baris']) }} isian berbeda dari rencana:</span>
            @foreach($isianBeda as $label)
                <span class="banding-pill banding-pill--warning">{{ $label }}</span>
            @endforeach
        @else
            <span>· Semua isian sesuai rencana</span>
        @endif
    </div>

    <div class="banding-scroll">
        <table class="banding-table">
            <thead>
                <tr>
                    <th scope="col">Isian</th>
                    <th scope="col">Rencana (saat diajukan)</th>
                    <th scope="col">Pelaksanaan (diarsipkan)</th>
                    <th scope="col">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banding['baris'] as $baris)
                    <tr @class(['is-beda' => !$baris['sesuai']])>
                        <td>{{ $baris['label'] }}</td>
                        <td>{{ $baris['rencana'] }}</td>
                        <td>
                            {{ $baris['pelaksanaan'] }}
                            @if($baris['catatan'])
                                <div class="banding-catatan">{{ $baris['catatan'] }}</div>
                            @endif
                        </td>
                        <td><span class="banding-pill banding-pill--{{ $baris['nada'] }}">{{ $baris['keterangan'] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="banding-dokumen">
        <div>
            <div class="banding-dokumen-judul">Dokumen dari rencana</div>
            <div class="banding-chips">
                @forelse($banding['dokumenRencana'] as $dok)
                    <span class="banding-chip" title="{{ \Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::label($dok->jenis_dokumen) }}">{{ $dok->judul_file ?: $dok->nama_file }}</span>
                @empty
                    <span class="banding-kosong">Tidak ada</span>
                @endforelse
            </div>
        </div>
        <div>
            <div class="banding-dokumen-judul">Ditambah saat pelaksanaan</div>
            <div class="banding-chips">
                @foreach($banding['dokumenTambahan'] as $dok)
                    <span class="banding-chip banding-chip--baru" title="{{ \Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::label($dok->jenis_dokumen) }}">{{ $dok->judul_file ?: $dok->nama_file }}</span>
                @endforeach
                @if($banding['jumlahFoto'] > 0)
                    <span class="banding-chip banding-chip--baru">{{ $banding['jumlahFoto'] }} foto kegiatan</span>
                @endif
                @if($banding['dokumenTambahan']->isEmpty() && $banding['jumlahFoto'] === 0)
                    <span class="banding-kosong">Tidak ada</span>
                @endif
            </div>
        </div>
    </div>

    <p class="banding-kaki">
        Tab ini hanya tampil untuk pengurus, admin, DPM, GPM, dan Kadep; mahasiswa biasa hanya melihat Ringkasan.
        <a href="{{ route('manajemenmahasiswa.proker.show', $banding['rencana']->id) }}">Buka Rencana Proker</a>
    </p>
</div>
