{{--
    Perangkap bot (lihat Support/Honeypot). Field jebakan disembunyikan dari
    pengguna nyata & pembaca layar; cap waktu diteruskan apa adanya pada langkah
    konfirmasi supaya waktu-isi form dihitung sejak form asli dibuka.
--}}
@php
    $hpStamp = request()->input(\Modules\ManajemenMahasiswa\Support\Honeypot::STAMP_FIELD)
        ?: \Modules\ManajemenMahasiswa\Support\Honeypot::stamp();
@endphp
<div aria-hidden="true" style="position: absolute; left: -10000px; top: auto; width: 1px; height: 1px; overflow: hidden;">
    <label>Jangan diisi
        <input type="text" name="{{ \Modules\ManajemenMahasiswa\Support\Honeypot::FIELD }}" value="" tabindex="-1" autocomplete="off">
    </label>
</div>
<input type="hidden" name="{{ \Modules\ManajemenMahasiswa\Support\Honeypot::STAMP_FIELD }}" value="{{ $hpStamp }}">
