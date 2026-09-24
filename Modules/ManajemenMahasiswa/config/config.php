<?php

return [
    'name' => 'ManajemenMahasiswa',

    /*
     * Bucket Supabase untuk bukti dukung Layanan Pengaduan. Sebaiknya bucket
     * PRIVATE (file hanya dibaca lewat route ber-otorisasi, bukan public URL).
     * Bila kosong, memakai bucket default (SUPABASE_BUCKET) dengan nama file acak.
     */
    'pengaduan_bukti_bucket' => env('SUPABASE_PENGADUAN_BUCKET'),
];
