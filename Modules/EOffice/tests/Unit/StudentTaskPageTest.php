<?php

namespace Modules\EOffice\Tests\Unit;

use App\Models\User;
use App\Services\SupabaseStorage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Blade, DB, Schema};
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\TugasController;
use Tests\TestCase;

class StudentTaskPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.student_task_test'=>['driver'=>'sqlite','database'=>':memory:']]);
        DB::setDefaultConnection('student_task_test');
        Schema::create('eo_prak_praktikum', function (Blueprint $t) {
            $t->string('id')->primary(); $t->string('nama'); $t->string('status'); $t->json('komponen_modul')->nullable(); $t->softDeletes();
        });
        Schema::create('eo_prak_daftar_praktikan', function (Blueprint $t) {
            $t->string('id')->primary(); $t->integer('user_id'); $t->string('praktikum_id');
        });
        Schema::create('eo_prak_modul_praktikum', function (Blueprint $t) {
            $t->id(); $t->string('praktikum_id'); $t->string('nama'); $t->integer('urutan');
        });
        Schema::create('eo_prak_tugas_praktikum', function (Blueprint $t) {
            $t->id(); $t->integer('modul_id'); $t->boolean('is_published'); $t->timestamp('deadline')->nullable();
        });
        Schema::create('eo_prak_modul_asprak', function (Blueprint $t) {$t->id(); $t->integer('modul_id');});
        Schema::create('eo_prak_pengumpulan_tugas', function (Blueprint $t) {$t->id(); $t->integer('tugas_id'); $t->string('daftar_praktikan_id');});
        $user = new User; $user->id = 1;
        $this->actingAs($user);
    }

    private function data(): array
    {
        $request = Request::create('/tugas');
        return (new TugasController($this->mock(SupabaseStorage::class)))->index($request)->getData();
    }

    public function test_empty_page_has_defined_practicum_and_module_list(): void
    {
        $data = $this->data();
        $this->assertArrayHasKey('praktikum', $data);
        $this->assertNull($data['praktikum']);
        $this->assertTrue($data['modulList']->isEmpty());
        $this->assertNull($data['daftarPraktikan']);
    }

    public function test_unpublished_grade_page_only_shows_publication_empty_state(): void
    {
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/mahasiswa/nilai.blade.php'));
        // Render the page body without the navigation layout, which is tested separately.
        $source=preg_replace('~<x-eoffice::manajemen-praktikum.layout\b[^>]*>|</x-eoffice::manajemen-praktikum.layout>|<x-eoffice::manajemen-praktikum.mhs-header\b[^>]*\/>~s','',$source);
        $html=Blade::render($source,['praktikumList'=>collect(['praktikum']),'praktikum'=>(object)['nama'=>'Praktikum Uji'],'isPublished'=>false]);
        $this->assertStringContainsString('Nilai Belum Dipublikasikan',$html);
        $this->assertStringNotContainsString('Menunggu persetujuan Koordinator',$html);
        $this->assertStringNotContainsString('Tugas Akhir',$html);
        $this->assertStringNotContainsString('Nilai Keseluruhan',$html);
    }

    public function test_task_page_receives_selected_practicum_and_dynamic_assessment_columns(): void
    {
        DB::table('eo_prak_praktikum')->insert(['id'=>'prak','nama'=>'Praktikum Multimedia','status'=>'aktif','komponen_modul'=>json_encode([['id'=>'tambahan_1','nama'=>'Form Data','bobot'=>10]])]);
        DB::table('eo_prak_daftar_praktikan')->insert(['id'=>'student','user_id'=>1,'praktikum_id'=>'prak']);
        DB::table('eo_prak_modul_praktikum')->insert(['praktikum_id'=>'prak','nama'=>'Modul 1','urutan'=>1]);
        $data = $this->data();
        $this->assertSame('prak', $data['praktikum']->id);
        $this->assertCount(1, $data['modulList']);
        $html = Blade::render('@foreach($praktikum->moduleTaskTypes() as $label){{ $label }};@endforeach', $data);
        $this->assertStringContainsString('Lembar Analisis', $html);
        $this->assertStringContainsString('Form Data', $html);
    }
}
