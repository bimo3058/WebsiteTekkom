<?php

namespace Modules\EOffice\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\PengajuanModul;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\PengajuanModulService;
use Tests\TestCase;

class PengajuanModulServiceTest extends TestCase
{
    private const PRAKTIKUM_ID = '0199f101-0000-7000-8000-000000000001';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.modul_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('modul_test');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('eo_prak_praktikum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->unsignedBigInteger('matkul_id')->nullable();
            $table->unsignedBigInteger('koor_id')->nullable();
            $table->string('tahun_ajaran');
            $table->string('semester');
            $table->string('status')->default('aktif');
            $table->boolean('is_active')->default(true);
            $table->string('status_pelaksanaan')->default('persiapan');
            $table->timestamp('started_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('eo_prak_pengajuan_modul', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->uuid('praktikum_sumber_id')->nullable();
            $table->unsignedInteger('versi');
            $table->string('status');
            $table->unsignedBigInteger('diajukan_oleh');
            $table->unsignedBigInteger('direview_oleh')->nullable();
            $table->text('catatan_pengajuan')->nullable();
            $table->text('catatan_reviewer')->nullable();
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('direview_at')->nullable();
            $table->timestamps();
        });
        Schema::create('eo_prak_modul_praktikum', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('modul_sumber_id')->nullable();
            $table->unsignedBigInteger('pengajuan_modul_id')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan');
            $table->string('kode_modul')->nullable();
            $table->string('jadwal_minggu')->nullable();
            $table->timestamps();
        });
        Schema::create('eo_prak_pengajuan_modul_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengajuan_modul_id');
            $table->unsignedBigInteger('modul_sumber_id')->nullable();
            $table->string('aksi');
            $table->string('nama_lama')->nullable();
            $table->unsignedInteger('urutan_lama')->nullable();
            $table->string('nama_baru')->nullable();
            $table->unsignedInteger('urutan_baru')->nullable();
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Koordinator'],
            ['id' => 2, 'name' => 'Dosen'],
        ]);
        DB::table('eo_prak_praktikum')->insert([
            'id' => self::PRAKTIKUM_ID,
            'nama' => 'Praktikum Test',
            'matkul_id' => 10,
            'koor_id' => 1,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
        ]);
    }

    private function unchangedPreviousDraft(): PengajuanModul
    {
        $old=Praktikum::create(['nama'=>'Periode Lama','matkul_id'=>10,'tahun_ajaran'=>'2025/2026','semester'=>'Genap']);
        $old->modul()->create(['nama'=>'Modul Lama','urutan'=>1]);
        return (new PengajuanModulService)->buatDraft(Praktikum::findOrFail(self::PRAKTIKUM_ID),1);
    }

    public function test_unchanged_previous_modules_can_be_used_without_fabricating_lecturer_review(): void
    {
        $draft=$this->unchangedPreviousDraft();$service=new PengajuanModulService;
        $service->pakaiModulTetap($draft,1);
        $this->assertSame(['Modul Lama'],Praktikum::findOrFail(self::PRAKTIKUM_ID)->modul()->pluck('nama')->all());
        $this->assertSame('disetujui',$draft->fresh()->status);
        $this->assertNull($draft->fresh()->direview_oleh);
        $this->assertNull($draft->fresh()->direview_at);
        $this->assertStringContainsString('di luar sistem',$draft->fresh()->catatan_pengajuan);
        try{$service->pakaiModulTetap($draft,1);$this->fail('Repeated activation accepted');}
        catch(ValidationException){$this->assertSame(1,Praktikum::findOrFail(self::PRAKTIKUM_ID)->modul()->count());}
    }

    public function test_previous_modules_cannot_bypass_review_after_modification(): void
    {
        $draft=$this->unchangedPreviousDraft();
        $draft->items()->first()->update(['nama_baru'=>'Diubah','aksi'=>'ubah']);
        try{(new PengajuanModulService)->pakaiModulTetap($draft,1);$this->fail('Modified draft accepted');}
        catch(ValidationException){$this->assertSame(0,Praktikum::findOrFail(self::PRAKTIKUM_ID)->modul()->count());}
    }

    public function test_previous_modules_are_locked_after_practicum_starts(): void
    {
        $draft=$this->unchangedPreviousDraft();
        Praktikum::whereKey(self::PRAKTIKUM_ID)->update(['started_at'=>now()]);
        $this->expectException(ValidationException::class);
        (new PengajuanModulService)->pakaiModulTetap($draft,1);
    }

    public function test_previous_modules_cannot_be_activated_by_another_coordinator(): void
    {
        $draft=$this->unchangedPreviousDraft();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new PengajuanModulService)->pakaiModulTetap($draft,2);
    }

    public function test_previous_modules_cannot_bypass_a_pending_lecturer_review(): void
    {
        $draft=$this->unchangedPreviousDraft();$draft->update(['status'=>'diajukan']);
        $this->expectException(ValidationException::class);
        (new PengajuanModulService)->pakaiModulTetap($draft,1);
    }

    public function test_previous_module_button_and_cerc_dashboard_templates_compile(): void
    {
        $source=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/modul.blade.php'));
        $this->assertStringContainsString('Gunakan Modul Lama',$source);
        $this->assertNotEmpty(token_get_all(app('blade.compiler')->compileString($source),TOKEN_PARSE));
        $dashboard=file_get_contents(base_path('Modules/EOffice/resources/views/manajemen-praktikum/cerc/dashboard.blade.php'));
        $this->assertStringNotContainsString('booking dibatalkan untuk jadwal hari ini',$dashboard);
    }

    public function test_memindahkan_modul_menggeser_urutan_lain_dan_merapikan_penghapusan(): void
    {
        $service = new PengajuanModulService;
        $pengajuan = $service->buatDraft(Praktikum::findOrFail(self::PRAKTIKUM_ID), 1);
        $a = $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'A', 'urutan_baru' => 1]);
        $b = $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'B', 'urutan_baru' => 2]);
        $c = $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'C', 'urutan_baru' => 3]);
        $service->susunUrutan($pengajuan, $c, 1);
        $this->assertSame(['C', 'A', 'B'], $pengajuan->items()->pluck('nama_baru')->all());
        $this->assertSame([1, 2, 3], $pengajuan->items()->pluck('urutan_baru')->map(fn ($n) => (int) $n)->all());
        $service->susunUrutan($pengajuan, $c, 3);
        $this->assertSame(['A', 'B', 'C'], $pengajuan->items()->pluck('nama_baru')->all());
        $b->delete();
        $service->susunUrutan($pengajuan);
        $this->assertSame([1, 2], $pengajuan->items()->pluck('urutan_baru')->map(fn ($n) => (int) $n)->all());
        $inserted = $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'Baru', 'urutan_baru' => 1]);
        $service->susunUrutan($pengajuan, $inserted, 1);
        $this->assertSame(['Baru', 'A', 'C'], $pengajuan->items()->pluck('nama_baru')->all());
    }

    public function test_penghapusan_modul_dengan_asisten_ditolak_dengan_petunjuk_dan_tanpa_perubahan(): void
    {
        Schema::create('eo_prak_modul_asprak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modul_id');
            $table->unsignedBigInteger('asprak_id');
        });
        $modul = \Modules\EOffice\Models\Modul::create(['praktikum_id' => self::PRAKTIKUM_ID, 'nama' => 'Modul lama', 'urutan' => 1]);
        DB::table('eo_prak_modul_asprak')->insert(['modul_id' => $modul->id, 'asprak_id' => 1]);
        $service = new PengajuanModulService;
        $pengajuan = $service->buatDraft(Praktikum::findOrFail(self::PRAKTIKUM_ID), 1);
        $pengajuan->items()->first()->update(['aksi' => 'hapus', 'nama_baru' => null, 'urutan_baru' => null]);
        $pengajuan->update(['status' => 'diajukan']);
        $this->assertStringContainsString('Hapus atau pindahkan penugasan', $service->hambatanPenghapusan($pengajuan)[0]);
        try {
            $service->setujui($pengajuan, 2, null);
            $this->fail('Persetujuan seharusnya ditolak.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('Pembagian Modul ke Asisten', $exception->errors()['pengajuan'][0]);
        }
        $this->assertSame('diajukan', $pengajuan->fresh()->status);
        $this->assertDatabaseHas('eo_prak_modul_praktikum', ['id' => $modul->id]);
        $this->assertDatabaseHas('eo_prak_modul_asprak', ['modul_id' => $modul->id, 'asprak_id' => 1]);
        $pengajuan->update(['status' => 'dibatalkan']);
        $this->assertSame([], $service->hambatanPenghapusan($pengajuan));
    }

    public function test_pengajuan_baru_tidak_mengubah_modul_sebelum_disetujui(): void
    {
        $service = new PengajuanModulService;
        $praktikum = Praktikum::findOrFail(self::PRAKTIKUM_ID);
        $pengajuan = $service->buatDraft($praktikum, 1);
        $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'Pengenalan', 'urutan_baru' => 1]);

        $service->ajukan($pengajuan->fresh(), 'Pengajuan awal');
        $this->assertDatabaseCount('eo_prak_modul_praktikum', 0);

        $service->setujui($pengajuan->fresh(), 2, null);

        $this->assertDatabaseHas('eo_prak_modul_praktikum', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'nama' => 'Pengenalan',
            'urutan' => 1,
        ]);
        $this->assertSame('siap_berjalan', Praktikum::findOrFail(self::PRAKTIKUM_ID)->status_pelaksanaan);
    }

    public function test_struktur_tidak_dapat_diajukan_setelah_praktikum_berjalan(): void
    {
        $service = new PengajuanModulService;
        $praktikum = Praktikum::findOrFail(self::PRAKTIKUM_ID);
        $pengajuan = $service->buatDraft($praktikum, 1);
        $pengajuan->items()->create(['aksi' => 'tambah', 'nama_baru' => 'Pengenalan', 'urutan_baru' => 1]);
        $praktikum->update(['status_pelaksanaan' => 'berjalan', 'started_at' => now()]);

        $this->expectException(ValidationException::class);
        $service->ajukan(PengajuanModul::findOrFail($pengajuan->id), null);
    }

    public function test_modul_periode_sebelumnya_menjadi_dasar_dan_satu_keputusan_bersifat_final(): void
    {
        $praktikumLamaId = '0199f101-0000-7000-8000-000000000099';
        DB::table('eo_prak_praktikum')->insert([
            'id' => $praktikumLamaId,
            'nama' => 'Praktikum Lama',
            'matkul_id' => 10,
            'koor_id' => 1,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Genap',
        ]);
        $modulLamaId = DB::table('eo_prak_modul_praktikum')->insertGetId([
            'praktikum_id' => $praktikumLamaId,
            'nama' => 'Routing Dasar',
            'urutan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = new PengajuanModulService;
        $praktikum = Praktikum::findOrFail(self::PRAKTIKUM_ID);
        $pengajuan = $service->buatDraft($praktikum, 1);
        $item = $pengajuan->items()->firstOrFail();

        $this->assertSame($modulLamaId, $item->modul_sumber_id);
        $item->update(['aksi' => 'ubah', 'nama_baru' => 'Routing Statis']);
        $service->ajukan($pengajuan->fresh(), null);
        $service->setujui($pengajuan->fresh(), 2, 'Disetujui');

        $this->assertDatabaseHas('eo_prak_modul_praktikum', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'modul_sumber_id' => $modulLamaId,
            'nama' => 'Routing Statis',
        ]);

        $this->expectException(ValidationException::class);
        $service->setujui($pengajuan->fresh(), 1, null);
    }
}
