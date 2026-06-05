<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Manajemen Jadwal RPS" subtitle="Kelola periode unggah RPS untuk Dosen">
        <x-slot:actions>
            <a href="{{ route('banksoal.rps.gpm.periode-rps.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90">
                <i class="fas fa-plus"></i> Buat Periode & Template
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Info Periode</th>
                        <th class="px-6 py-4 text-left">Rentang Waktu</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($periodes as $index => $periode)
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $periode->judul }}</p>
                                <p class="text-xs text-slate-500">Semester {{ $periode->semester }} &bull; TA {{ $periode->tahun_ajaran }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y, H:i') }}
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-rose-500">
                                    <i class="fas fa-flag-checkered"></i>
                                    <span class="text-slate-600">{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($periode->is_active)
                                    <span class="inline-flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-primary/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10" data-modal-open="modalEdit{{ $periode->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" data-modal-open="modalHapus{{ $periode->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-calendar-times text-3xl text-slate-300"></i>
                                    <p class="text-sm font-semibold">Belum ada jadwal RPS</p>
                                    <p class="text-xs">Silakan tambah periode baru untuk mengaktifkan pengajuan RPS Dosen.</p>
                                    <a href="{{ route('banksoal.rps.gpm.periode-rps.create') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg border border-primary/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10">
                                        <i class="fas fa-plus"></i> Tambah Periode Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($periodes as $periode)
        <div id="modalEdit{{ $periode->id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalEdit{{ $periode->id }}"></div>
            <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
                <form action="{{ route('banksoal.rps.gpm.periode-rps.update', $periode->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-900">Edit Periode RPS</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalEdit{{ $periode->id }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Judul Periode <span class="text-rose-500">*</span></label>
                            <input type="text" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="judul" value="{{ $periode->judul }}" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Semester <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="semester" required>
                                    <option value="Ganjil" {{ $periode->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $periode->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tahun_ajaran" required>
                                    <option value="" disabled>Pilih Tahun Ajaran</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta }}" {{ $periode->tahun_ajaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Mulai <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_mulai" value="{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Selesai (Tenggat) <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_selesai" value="{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 text-xs text-slate-600">
                            <input class="mt-1" type="checkbox" name="is_active" value="1" {{ $periode->is_active ? 'checked' : '' }}>
                            <span>
                                <span class="font-semibold text-slate-700">Set sebagai periode aktif saat ini</span>
                                <span class="block text-[11px] text-slate-500">Hanya satu periode yang bisa aktif. Mengaktifkan ini akan menonaktifkan periode lainnya.</span>
                            </span>
                        </label>
                    </div>
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalEdit{{ $periode->id }}">Batal</button>
                        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalHapus{{ $periode->id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalHapus{{ $periode->id }}"></div>
            <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl">
                <div class="px-5 py-5 text-center">
                    <div class="text-rose-500 mb-3"><i class="fas fa-exclamation-triangle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Hapus Periode?</h3>
                    <p class="text-xs text-slate-500 mt-2">Anda yakin ingin menghapus jadwal <strong>{{ $periode->judul }}</strong>?</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalHapus{{ $periode->id }}">Batal</button>
                        <form action="{{ route('banksoal.rps.gpm.periode-rps.destroy', $periode->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach



</x-banksoal::layouts.gpm-master>
