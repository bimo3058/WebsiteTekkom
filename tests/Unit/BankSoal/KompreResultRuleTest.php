<?php

namespace Tests\Unit\BankSoal;

use Modules\BankSoal\Services\Komprehensif\KompreResultService;
use Tests\TestCase;

class KompreResultRuleTest extends TestCase
{
    public function test_lulus_jika_semua_cpl_minimal_enam(): void
    {
        $service = new KompreResultService;

        $this->assertTrue($service->isPassed([
            ['nilai' => 6.0],
            ['nilai' => 7.5],
            ['nilai' => 10.0],
        ]));
    }

    public function test_tidak_lulus_jika_salah_satu_cpl_di_bawah_enam(): void
    {
        $service = new KompreResultService;

        $this->assertFalse($service->isPassed([
            ['nilai' => 10.0],
            ['nilai' => 10.0],
            ['nilai' => 5.99],
        ]));
    }

    public function test_tidak_lulus_jika_tidak_ada_cpl_dinilai(): void
    {
        $this->assertFalse((new KompreResultService)->isPassed([]));
    }

    public function test_batas_tepat_enam_dianggap_lulus(): void
    {
        $service = new KompreResultService;

        $this->assertTrue($service->isPassed([['nilai' => 6.0]]));
        $this->assertFalse($service->isPassed([['nilai' => 5.99]]));
    }
}
