<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Penjaga regresi CV Builder.
 *
 * Commit 1b3d91a mengganti CvProfile::firstOrCreate menjadi firstOrNew di
 * loadStep DAN saveStep. Perubahan di loadStep benar (GET tidak boleh menulis
 * DB), tapi saveStep ikut kehilangan satu-satunya tempat baris cv_profiles
 * pernah dibuat, lalu ditambahi penjaga 404 — sehingga setiap pengguna yang
 * belum punya baris macet di Step 1 dengan pesan "Profile not found".
 *
 * Tidak ada test yang menangkapnya, dan bug itu lolos ke produksi selama
 * lebih dari tiga bulan.
 */
class CvBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function mahasiswa(): User
    {
        Role::findOrCreate('mahasiswa', 'web');

        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        return $user;
    }

    public function test_save_step_membuat_baris_cv_untuk_pengguna_baru(): void
    {
        $user = $this->mahasiswa();

        $this->assertDatabaseMissing('cv_profiles', ['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson('/profile/cv/step/1', [
                'tentang_diri' => 'Mahasiswa Teknik Komputer.',
                'cv_domisili'  => 'Semarang',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('cv_profiles', [
            'user_id'      => $user->id,
            'tentang_diri' => 'Mahasiswa Teknik Komputer.',
            'cv_domisili'  => 'Semarang',
        ]);
    }

    public function test_simpan_berulang_tidak_menggandakan_baris(): void
    {
        $user = $this->mahasiswa();

        foreach (['Semarang', 'Jakarta'] as $kota) {
            $this->actingAs($user)
                ->postJson('/profile/cv/step/1', ['cv_domisili' => $kota])
                ->assertOk();
        }

        $this->assertSame(
            1,
            \App\Models\CvProfile::where('user_id', $user->id)->count(),
            'Indeks UNIQUE user_id harus tetap dihormati; simpan kedua memperbarui baris yang sama.'
        );
        $this->assertDatabaseHas('cv_profiles', [
            'user_id'     => $user->id,
            'cv_domisili' => 'Jakarta',
        ]);
    }

    /**
     * GET tidak boleh meninggalkan baris kosong — inilah alasan firstOrNew
     * dipakai di loadStep, dan penyebab 8 dari 12 baris cv_profiles di produksi
     * kosong melompong sebelum perbaikan ini.
     */
    public function test_load_step_tidak_membuat_baris(): void
    {
        $user = $this->mahasiswa();

        $this->actingAs($user)
            ->get('/profile/cv/step/1')
            ->assertOk();

        $this->assertDatabaseMissing('cv_profiles', ['user_id' => $user->id]);
    }
}
