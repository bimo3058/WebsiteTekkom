<?php

namespace Modules\EOffice\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum,Tugas,NilaiJenisTugas};
use Modules\EOffice\Services\ManajemenPraktikum\ModuleAssessment;
use Tests\TestCase;

class ModuleAssessmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.component_test'=>['driver'=>'sqlite','database'=>':memory:']]);
        DB::setDefaultConnection('component_test');
        DB::purge('component_test');
        Schema::create('eo_praktikum',function(Blueprint $t){$t->uuid('id')->primary();$t->string('nama');foreach(['tp','praktikum','laporan','responsi'] as $key)$t->integer('bobot_'.$key)->default($key==='tp'?10:30);$t->timestamps();$t->softDeletes();});
        (require base_path('Modules/EOffice/database/migrations/2026_10_09_230000_add_module_assessment_components.php'))->up();
        Schema::create('modul_praktikum',function(Blueprint $t){$t->id();$t->uuid('praktikum_id');$t->string('nama');$t->timestamps();});
        Schema::create('tugas_praktikum',function(Blueprint $t){$t->id();$t->integer('modul_id');$t->string('jenis_tugas');$t->string('judul');$t->boolean('is_published')->default(false);$t->timestamps();});
        Schema::create('pengumpulan_tugas',function(Blueprint $t){$t->id();$t->integer('tugas_id');});
        Schema::create('nilai_jenis_tugas',function(Blueprint $t){$t->id();$t->integer('modul_id');$t->string('daftar_praktikan_id');$t->string('jenis_tugas');$t->float('nilai')->nullable();$t->timestamps();});
        Schema::create('daftar_praktikan',function(Blueprint $t){$t->string('id');$t->uuid('praktikum_id');});
        Schema::create('nilai_praktikum',function(Blueprint $t){$t->id();$t->string('daftar_praktikan_id');$t->boolean('disetujui_koor')->default(false);$t->boolean('disetujui_dosen')->default(false);$t->boolean('dipublikasikan')->default(false);});
        DB::table('eo_praktikum')->insert(['id'=>'prak','nama'=>'Praktikum Multimedia']);
        DB::table('modul_praktikum')->insert([['id'=>1,'praktikum_id'=>'prak','nama'=>'Modul 1'],['id'=>2,'praktikum_id'=>'prak','nama'=>'Modul 2']]);
    }

    private function data(array $extra=[]): array
    {
        return array_replace(['version'=>0,'bobot_tp'=>10,'bobot_praktikum'=>30,'bobot_laporan'=>30,'bobot_responsi'=>20,'tambahan'=>[1=>['nama'=>'Form Data','bobot'=>10]]],$extra);
    }

    private function save(array $data): void
    {
        app(ModuleAssessment::class)->save(Praktikum::firstOrFail(),$data);
    }

    public function test_components_apply_to_every_module_and_average_includes_missing_as_zero(): void
    {
        $this->save($this->data());$praktikum=Praktikum::firstOrFail();
        $this->assertSame('Form Data',$praktikum->moduleTaskTypes()['tambahan_1']);
        $this->assertSame('Lembar Analisis',$praktikum->moduleTaskTypes()['laporan']);
        $this->assertEquals(10,$praktikum->moduleAverage(['tambahan_1'=>100]));
        $this->assertEquals(0,$praktikum->moduleAverage([]));
        foreach([1,2] as $module) {
            $task=Tugas::create(['modul_id'=>$module,'jenis_tugas'=>'tambahan_1','judul'=>'Form','is_published'=>false]);
            $this->assertSame('Form Data',$task->jenisLabel());
        }
        $this->assertFalse($praktikum->moduleComponentsLocked());
    }

    public function test_publication_permanently_locks_structure_even_after_task_is_closed(): void
    {
        $this->save($this->data());
        $task=Tugas::create(['modul_id'=>1,'jenis_tugas'=>'tambahan_1','judul'=>'Form','is_published'=>true]);
        $task->update(['is_published'=>false]);
        $this->assertTrue(Praktikum::first()->moduleComponentsLocked());
        $this->expectException(ValidationException::class);
        $this->save($this->data(['version'=>1,'tambahan'=>[1=>['nama'=>'Komponen Baru','bobot'=>10]]]));
    }

    public function test_score_zero_also_permanently_locks_components(): void
    {
        NilaiJenisTugas::create(['modul_id'=>1,'daftar_praktikan_id'=>'student','jenis_tugas'=>'laporan','nilai'=>0]);
        NilaiJenisTugas::query()->delete();
        $this->assertTrue(Praktikum::first()->moduleComponentsLocked());
        $this->expectException(ValidationException::class);$this->save($this->data());
    }

    public function test_legacy_submission_locks_components(): void
    {
        DB::table('tugas_praktikum')->insert(['id'=>1,'modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>false]);
        DB::table('pengumpulan_tugas')->insert(['tugas_id'=>1]);
        $this->assertTrue(Praktikum::first()->moduleComponentsLocked());
        $this->expectException(ValidationException::class);$this->save($this->data());
    }

    public function test_invalid_configurations_and_stale_versions_do_not_change_saved_settings(): void
    {
        foreach([
            $this->data(['tambahan'=>[1=>['nama'=>'Laporan','bobot'=>10]]]),
            $this->data(['tambahan'=>[1=>['nama'=>'Form','bobot'=>null]]]),
            $this->data(['tambahan'=>[1=>['nama'=>'Form','bobot'=>10.5]]]),
            $this->data(['bobot_responsi'=>30]),
            $this->data(['version'=>99]),
            $this->data(['tambahan'=>[1=>['nama'=>'Form','bobot'=>5],2=>['nama'=>'Form','bobot'=>5]]]),
            $this->data(['tambahan'=>[3=>['nama'=>'Form','bobot'=>10]]]),
        ] as $data){
            try{$this->save($data);$this->fail('Invalid configuration accepted.');}catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
            $this->assertNull(Praktikum::first()->komponen_modul);
            $this->assertEquals(0,Praktikum::first()->komponen_modul_version);
        }
    }

    public function test_weights_can_change_after_opening_but_not_after_approval(): void
    {
        $this->save($this->data());
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
        $this->save($this->data(['version'=>1,'bobot_responsi'=>10,'tambahan'=>[1=>['nama'=>'Form Data','bobot'=>20]]]));
        $this->assertEquals(20,Praktikum::first()->moduleWeights()['tambahan_1']);
        DB::table('daftar_praktikan')->insert(['id'=>'student','praktikum_id'=>'prak']);
        DB::table('nilai_praktikum')->insert(['daftar_praktikan_id'=>'student','disetujui_koor'=>true]);
        $this->expectException(ValidationException::class);$this->save($this->data(['version'=>2]));
    }

    public function test_unknown_component_and_invalid_weights_cannot_publish_tasks(): void
    {
        try {Tugas::create(['modul_id'=>1,'jenis_tugas'=>'tambahan_1','judul'=>'Form','is_published'=>false]);$this->fail('Unknown component accepted.');} catch(ValidationException $e){$this->assertArrayHasKey('jenis_tugas',$e->errors());}
        DB::table('eo_praktikum')->update(['bobot_tp'=>9]);
        $this->expectException(ValidationException::class);
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
    }

    public function test_modified_views_compile(): void
    {
        foreach(['koordinator/modul','koordinator/nilai','dosen/nilai','mahasiswa/nilai','asprak/absensi','asprak/absensi-show','asprak/tugas','asprak/tugas-create','asprak/tugas-edit','dosen/tugas','mahasiswa/tugas','tugas-akhir/editor','partials/module-extra-headers','partials/module-extra-cells','partials/module-weight-confirmation'] as $view){
            $compiled=app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/'.$view.'.blade.php')));
            $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE),$view);
        }
        $compiled=app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/module-components.blade.php')));
        $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));
        $compiled=app('blade.compiler')->compileString(file_get_contents(base_path('Modules/EOffice/resources/views/components/manajemen-praktikum/ui/module-history.blade.php')));
        $this->assertNotEmpty(token_get_all($compiled,TOKEN_PARSE));
    }

    public function test_two_extra_components_can_be_saved_and_unused_components_removed(): void
    {
        $this->save($this->data(['tambahan'=>[1=>['nama'=>'Form Data','bobot'=>5],2=>['nama'=>'Evaluasi','bobot'=>5]]]));
        $this->assertCount(6,Praktikum::first()->moduleTaskTypes());
        $this->save($this->data(['version'=>1,'bobot_responsi'=>30,'tambahan'=>[]]));
        $this->assertCount(4,Praktikum::first()->moduleTaskTypes());
        $this->assertTrue(Praktikum::first()->moduleWeightsValid());
    }

    public function test_prepared_task_must_be_removed_before_its_component(): void
    {
        $this->save($this->data());
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'tambahan_1','judul'=>'Form','is_published'=>false]);
        $this->expectException(ValidationException::class);
        $this->save($this->data(['version'=>1,'bobot_responsi'=>30,'tambahan'=>[]]));
    }

    public function test_component_settings_render_with_add_button_and_formal_warning(): void
    {
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',['praktikum'=>Praktikum::first()])->render();
        $this->assertStringContainsString('Tambah Kolom',$html);
        $this->assertStringContainsString('Kolom Penilaian Modul',$html);
        $this->assertStringContainsString('NAMA KOLOM PENILAIAN',$html);
        $this->assertStringContainsString('Contoh: Form Data',$html);
        $this->assertStringContainsString('Total bobot:',$html);
        $this->assertStringNotContainsString('Susunan kolom dikunci setelah',$html);
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',['praktikum'=>Praktikum::first()])->render();
        $this->assertStringContainsString('Susunan kolom dikunci setelah tugas dibuka, dikumpulkan, atau dinilai.',$html);
        $this->assertStringNotContainsString('Tambah Kolom',$html);
    }

    public function test_fixed_names_are_plain_text_and_weight_attributes_are_unchanged(): void
    {
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',['praktikum'=>Praktikum::first()])->render();
        $dom=new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath=new \DOMXPath($dom);
        $this->assertSame(4,$xpath->query('//span[contains(@class,"mp-column-label")]')->length);
        foreach(['Tugas Pendahuluan','Praktikum (Tugas Pengganti)','Lembar Analisis','Responsi'] as $name) {
            $this->assertSame(1,$xpath->query('//span[contains(@class,"mp-column-label") and text()="'.$name.'"]')->length);
            $this->assertSame(0,$xpath->query('//input[@value="'.$name.'"]')->length);
        }
        foreach(['bobot_tp'=>10,'bobot_praktikum'=>30,'bobot_laporan'=>30,'bobot_responsi'=>30] as $key=>$weight){
            $input=$xpath->query('//input[@name="'.$key.'"]')->item(0);
            $this->assertSame((string)$weight,$input->getAttribute('value'));
            foreach(['type'=>'number','min'=>'0','max'=>'100','step'=>'1'] as $attribute=>$value) $this->assertSame($value,$input->getAttribute($attribute));
            $this->assertTrue($input->hasAttribute('required'));
        }
        $this->assertStringContainsString('var(--c-success-200)',$html);
        $this->assertStringContainsString('mp-column-footer-copy',$html);
        $this->assertStringContainsString('@media(max-width:640px)',$html);
        $this->assertStringNotContainsString('mp-alert warning',$html);
    }

    public function test_extra_name_editing_and_approval_disabled_state_remain_intact(): void
    {
        $this->save($this->data());
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',['praktikum'=>Praktikum::first()])->render();
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        $name=$xpath->query('//input[@name="tambahan[1][nama]"]')->item(0);
        $this->assertSame('Form Data',$name->getAttribute('value'));
        $this->assertFalse($name->hasAttribute('readonly'));
        $this->assertSame('60',$name->getAttribute('maxlength'));
        $this->assertStringContainsString("input.value=''",html_entity_decode($html));
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
        DB::table('daftar_praktikan')->insert(['id'=>'student','praktikum_id'=>'prak']);
        DB::table('nilai_praktikum')->insert(['daftar_praktikan_id'=>'student','disetujui_koor'=>true]);
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',['praktikum'=>Praktikum::first()])->render();
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        $this->assertTrue($xpath->query('//input[@name="tambahan[1][nama]"]')->item(0)->hasAttribute('readonly'));
        $this->assertSame(1,$xpath->query('//fieldset[@disabled]')->length);
        $this->assertSame(1,$xpath->query('//button[@type="submit" and @disabled]')->length);
        $this->assertStringContainsString('Batalkan pengajuan atau publikasi nilai',$html);
    }

    public function test_structure_lock_disables_this_section_before_grade_approval(): void
    {
        $this->save($this->data());
        Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
        $praktikum=Praktikum::first();
        $this->assertTrue($praktikum->moduleComponentsLocked());
        $this->assertFalse($praktikum->moduleWeightsLocked());
        $html=view('eoffice::components.manajemen-praktikum.ui.module-components',compact('praktikum'))->render();
        $dom=new \DOMDocument();@$dom->loadHTML($html);$xpath=new \DOMXPath($dom);
        $description=$xpath->query('//p[@class="mp-column-description"]')->item(0);
        $this->assertSame('Susunan kolom dikunci setelah tugas dibuka, dikumpulkan, atau dinilai.',$description->textContent);
        $this->assertStringNotContainsString('Berlaku untuk seluruh modul, kecuali tugas akhir.',$html);
        $this->assertSame(1,$xpath->query('//fieldset[@disabled]')->length);
        $this->assertSame(1,$xpath->query('//button[@type="submit" and @disabled]')->length);
        $this->assertSame(0,$xpath->query('//div[@class="mp-column-lock-notices"]')->length);
        $this->assertSame(6,$xpath->query('//td[@class="mp-column-no"]')->length);
        $this->assertStringContainsString('width:80px;text-align:center',$html);
        $this->assertSame(1,$xpath->query('//div[@class="mp-column-footer"]/div[@class="mp-column-footer-copy"]/p[@class="mp-column-help"]')->length);
        $this->assertSame(1,$xpath->query('//div[@class="mp-column-footer"]/button[@type="submit"]')->length);
        $this->assertStringContainsString('flex-direction:column;gap:7px',$html);
    }

    public function test_published_task_cannot_be_reassigned_to_another_component(): void
    {
        $task=Tugas::create(['modul_id'=>1,'jenis_tugas'=>'laporan','judul'=>'Laporan','is_published'=>true]);
        $this->expectException(ValidationException::class);
        $task->update(['jenis_tugas'=>'responsi']);
    }
}
