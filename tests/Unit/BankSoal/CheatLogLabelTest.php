<?php

namespace Tests\Unit\BankSoal;

use Carbon\Carbon;
use Modules\BankSoal\Models\Komprehensif\CheatLog;
use Tests\TestCase;

class CheatLogLabelTest extends TestCase
{
    public function test_label_for_setiap_jenis_pelanggaran(): void
    {
        $this->assertSame('Pindah tab / minimize', CheatLog::labelFor('tab_switch'));
        $this->assertSame('Berpindah aplikasi/jendela', CheatLog::labelFor('window_blur'));
        $this->assertSame('Keluar dari layar penuh', CheatLog::labelFor('fullscreen_exit'));
        $this->assertSame('Layar penuh ditolak / diblokir', CheatLog::labelFor('fullscreen_denied'));
        $this->assertSame('Membuka tab ganda', CheatLog::labelFor('duplicate_tab'));
    }

    public function test_label_untuk_jenis_tak_dikenal_memakai_headline(): void
    {
        $this->assertSame('Unknown Event', CheatLog::labelFor('unknown_event'));
    }

    public function test_details_from_mengurutkan_log_dan_memetakan_label(): void
    {
        $logs = collect([
            $this->makeLog('tab_switch', 'Peserta berpindah tab.', '2026-11-09 22:08:24'),
            $this->makeLog('window_blur', 'Jendela kehilangan fokus.', '2026-11-09 22:08:23'),
        ]);

        $details = CheatLog::detailsFrom($logs);

        $this->assertSame([
            [
                'time' => '22:08:23',
                'label' => 'Berpindah aplikasi/jendela',
                'description' => 'Jendela kehilangan fokus.',
            ],
            [
                'time' => '22:08:24',
                'label' => 'Pindah tab / minimize',
                'description' => 'Peserta berpindah tab.',
            ],
        ], $details);
    }

    public function test_details_from_kosong_menghasilkan_array_kosong(): void
    {
        $this->assertSame([], CheatLog::detailsFrom(collect()));
    }

    private function makeLog(string $eventType, string $description, string $createdAt): CheatLog
    {
        $log = new CheatLog([
            'event_type' => $eventType,
            'description' => $description,
        ]);
        $log->created_at = Carbon::parse($createdAt);

        return $log;
    }
}
