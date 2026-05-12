<?php

namespace Modules\EOffice\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\EOffice\Models\KerjaPraktik;

class KerjaPraktikRegistrationTest extends TestCase
{
    // WAJIB: Menggunakan DatabaseTransactions agar data test dihapus otomatis (di-rollback) 
    // dan tidak mengotori/merusak database Supabase aslimu.
    use DatabaseTransactions;

    /** @test */
    public function test_halaman_pendaftaran_kp_bisa_dibuka()
    {
        // Simulasi membuka halaman form
        $response = $this->get(route('kp.register'));

        // Pastikan sukses (200 OK) dan memuat teks yang benar
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Kerja Praktik');
        $response->assertSee('Nama Instansi');
    }

    /** @test */
    public function test_mahasiswa_bisa_menyimpan_data_pendaftaran_kp()
    {
        // 1. Siapkan data dummy seolah-olah diketik oleh mahasiswa
        $data = [
            'rencana_tempat' => 'PT. Teknologi Masa Depan',
            'rencana_judul' => 'Pengembangan Sistem Informasi Cerdas',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-08-01',
        ];

        // 2. Simulasi submit tombol form (POST request)
        $response = $this->post(route('kp.store'), $data);

        // 3. Pastikan dikembalikan (redirect) dengan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 4. Pastikan data tersebut BENAR-BENAR MASUK ke tabel Supabase
        $this->assertDatabaseHas('eo_kerja_praktik', [
            'rencana_tempat' => 'PT. Teknologi Masa Depan',
            'rencana_judul' => 'Pengembangan Sistem Informasi Cerdas',
        ]);
    }

    /** @test */
    public function test_validasi_form_pendaftaran_kp_wajib_diisi()
    {
        // Simulasi submit tanpa mengisi apapun (data kosong)
        $response = $this->post(route('kp.store'), []);

        // Harus ada error validasi di kolom-kolom ini karena wajib diisi
        $response->assertSessionHasErrors([
            'rencana_tempat', 
            'rencana_judul', 
            'tanggal_mulai', 
            'tanggal_selesai'
        ]);
    }
}
