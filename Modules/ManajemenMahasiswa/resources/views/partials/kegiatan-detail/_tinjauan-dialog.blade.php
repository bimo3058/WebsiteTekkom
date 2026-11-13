{{--
    Dialog alur tinjau (S-82): "Tolak dengan catatan" (dibuka tombol header lewat event
    mk-buka-tolak) dan "Ganti ketua pelaksana" (dibuka tombol Ganti di kartu meta Ketua
    Pelaksana lewat event mk-buka-ketua). Dipasang _body di luar kartu.

    Variabel (dari $panelTinjauan halaman show):
      $kegiatan, $canTolak, $canGantiKetua, $calonKetua, $routeTolak, $routeKetua
--}}
@php
    $namaKetua = $kegiatan->ketuaPelaksana?->user?->name;
@endphp

{{-- Dialog Tolak dengan catatan --}}
@if($canTolak && $routeTolak)
<div x-data="{ open: {{ $errors->has('isi') ? 'true' : 'false' }} }" @mk-buka-tolak.window="open = true; $nextTick(() => $refs.isi.focus())" @keydown.escape.window="open = false">
    <div class="mk-tolak-latar" x-show="open" x-cloak style="display:none;" @click.self="open = false">
        <form method="POST" action="{{ $routeTolak }}" class="mk-tolak-kotak" role="dialog" aria-modal="true" aria-labelledby="judulTolak">
            @csrf
            <div class="mk-tolak-kotak__kepala">
                <h3 id="judulTolak">Kembalikan ke ketua pelaksana</h3>
                <p>Status berubah menjadi Perlu Perbaikan dan {{ $namaKetua ?? 'ketua pelaksana' }} mendapat notifikasi untuk memperbaikinya.</p>
            </div>
            <div class="mk-tolak-kotak__isi">
                <label class="form-label-custom" for="isiTolak">Yang perlu diperbaiki <span class="required">*</span></label>
                <textarea name="isi" id="isiTolak" x-ref="isi" rows="4" maxlength="1000" required minlength="5"
                          class="form-control form-control-custom" placeholder="Mis. RAB konsumsi 205 nasi kotak padahal target 140 peserta. Sesuaikan lalu unggah RAB baru.">{{ old('isi') }}</textarea>
            </div>
            <div class="mk-tolak-kotak__kaki">
                <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" @click="open = false">Batal</button>
                <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm" style="background:#B42318;border-color:#B42318;">Kirim catatan</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Dialog Ganti ketua pelaksana --}}
@if($canGantiKetua && $routeKetua)
<div x-data="{ open: {{ $errors->has('ketua_pelaksana_id') ? 'true' : 'false' }} }" @mk-buka-ketua.window="open = true" @keydown.escape.window="open = false">
    <div class="mk-tolak-latar" x-show="open" x-cloak style="display:none;" @click.self="open = false">
        <form method="POST" action="{{ $routeKetua }}" class="mk-tolak-kotak" role="dialog" aria-modal="true" aria-labelledby="judulKetua">
            @csrf
            @method('PATCH')
            <div class="mk-tolak-kotak__kepala">
                <h3 id="judulKetua">{{ $namaKetua ? 'Ganti ketua pelaksana' : 'Tunjuk ketua pelaksana' }}</h3>
                <p>Ketua baru meneruskan pekerjaan yang ada dan menerima notifikasinya. Status tinjauan tidak berubah.</p>
            </div>
            <div class="mk-tolak-kotak__isi">
                <label class="form-label-custom" for="ketuaBaru">Ketua pelaksana baru <span class="required">*</span></label>
                @if($calonKetua->isEmpty())
                    <p style="font-size:13px;color:var(--c-fg-muted);margin:0;">Belum ada staff lain di bidang/unit ini. Atur bidangnya di halaman Permission.</p>
                @else
                    <select name="ketua_pelaksana_id" id="ketuaBaru" class="form-select form-control-custom" required>
                        <option value="" disabled selected>Pilih staff himpunan sebidang</option>
                        @foreach($calonKetua as $calon)
                            <option value="{{ $calon['id'] }}" @selected((string) old('ketua_pelaksana_id') === (string) $calon['id'])>{{ $calon['nama'] }} — {{ $calon['nim'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="mk-tolak-kotak__kaki">
                <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" @click="open = false">Batal</button>
                <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm" @disabled($calonKetua->isEmpty())>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

@once
<style>
    .mk-tolak-latar { position: fixed; inset: 0; background: rgba(13,13,18,.45); z-index: 1060; display: flex; align-items: center; justify-content: center; padding: 16px; }
    .mk-tolak-kotak { background: var(--c-surface, #fff); border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 20px 48px rgba(0,0,0,.2); }
    .mk-tolak-kotak__kepala { padding: 18px 20px; border-bottom: 1px solid var(--c-border); }
    .mk-tolak-kotak__kepala h3 { margin: 0; font-size: 17px; font-weight: 700; }
    .mk-tolak-kotak__kepala p { margin: 4px 0 0; font-size: 13px; color: var(--c-fg-sec); }
    .mk-tolak-kotak__isi { padding: 16px 20px; }
    .mk-tolak-kotak__kaki { padding: 14px 20px; border-top: 1px solid var(--c-border); display: flex; justify-content: flex-end; gap: 8px; }
</style>
@endonce
