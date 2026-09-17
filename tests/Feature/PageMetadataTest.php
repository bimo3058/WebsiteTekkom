<?php

namespace Tests\Feature;

use App\Support\PageTitle;
use Tests\TestCase;

class PageMetadataTest extends TestCase
{
    public function test_login_uses_contextual_title_and_undip_icon(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<title>Masuk | SITKOM</title>', false)
            ->assertSee('rel="icon" type="image/png"', false)
            ->assertSee('/images/UNDIPOfficial.png', false)
            ->assertDontSee('<title>Laravel</title>', false);
    }

    public function test_page_title_resolver_understands_module_routes_and_actions(): void
    {
        $this->assertSame(
            'Edit Mata Kuliah',
            PageTitle::resolve('banksoal.admin.kontrol-umum.mata-kuliah.edit')
        );
        $this->assertSame(
            'Dashboard Dosen',
            PageTitle::resolve('eoffice.kp.dosen.dashboard')
        );
        $this->assertSame(
            'Tambah Program Kerja',
            PageTitle::resolve('manajemenmahasiswa.proker.create')
        );
    }
}
