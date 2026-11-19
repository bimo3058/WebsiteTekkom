<?php

namespace Tests\Feature;

use App\Livewire\Pulse\RequestMonitor;
use App\Pulse\Recorders\RequestMonitorRecorder;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Contracts\Storage;
use Laravel\Pulse\Entry;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Value;
use Livewire\Livewire;
use Tests\TestCase;

class PulseRecordingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.connections.pulse_test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'pulse.storage.database.connection' => 'pulse_test',
            'pulse.ingest.driver' => 'storage',
            'pulse.cache' => 'array',
        ]);
        DB::setDefaultConnection('pulse_test');
        (require database_path('migrations/2026_03_25_155552_create_pulse_tables.php'))->up();
        DB::setDefaultConnection('sqlite');
    }

    public function test_repair_restores_unique_indexes_and_storage_upserts_without_duplicates(): void
    {
        $schema = Schema::connection('pulse_test');
        $schema->table('pulse_values', fn ($table) => $table->dropUnique(['type', 'key_hash']));
        $schema->table('pulse_aggregates', fn ($table) => $table->dropUnique(['bucket', 'period', 'type', 'aggregate', 'key_hash']));
        $repair = require database_path('migrations/2026_11_13_000001_restore_pulse_indexes.php');
        $repair->up();
        $repair->up();

        $this->assertTrue($schema->hasIndex('pulse_values', ['type', 'key_hash'], 'unique'));
        $this->assertTrue($schema->hasIndex('pulse_aggregates', ['bucket', 'period', 'type', 'aggregate', 'key_hash'], 'unique'));

        $items = collect([
            (new Entry(now()->timestamp, 'user_request', '123'))->count(),
            new Value(now()->timestamp, 'system', 'server', '{"name":"server"}'),
        ]);
        app(Storage::class)->store($items);
        app(Storage::class)->store($items);

        $db = DB::connection('pulse_test');
        $this->assertSame(1, $db->table('pulse_values')->count());
        $this->assertSame(4, $db->table('pulse_aggregates')->count());
        $this->assertEquals(2, $db->table('pulse_aggregates')->min('value'));
    }

    public function test_request_recorder_persists_data_and_livewire_displays_the_request(): void
    {
        app(Pulse::class)->startRecording();
        $request = Request::create('/capstone/dashboard', 'GET', server: ['REMOTE_ADDR' => '127.0.0.1']);
        app(RequestMonitorRecorder::class)->record(new RequestHandled($request, new Response('OK', 200)));
        app(Pulse::class)->ingest();

        $entry = DB::connection('pulse_test')->table('pulse_entries')->where('type', 'request_monitor')->first();
        $this->assertNotNull($entry);
        $this->assertSame('/capstone/dashboard', json_decode($entry->key, true)['path']);
        Livewire::withoutLazyLoading()->test(RequestMonitor::class)
            ->assertSee('/capstone/dashboard')->assertSee('Guest')->assertSee('200');
    }

    public function test_monitor_uses_pulse_connection_and_selected_time_period(): void
    {
        $storage = app(Storage::class);
        $key = fn ($path) => json_encode(['method' => 'GET', 'path' => $path, 'status' => 200]);
        $storage->store(collect([
            new Entry(now()->timestamp, 'request_monitor', $key('/recent-request'), 20),
            new Entry(now()->subHours(2)->timestamp, 'request_monitor', $key('/older-request'), 30),
        ]));

        Livewire::withoutLazyLoading()->test(RequestMonitor::class)
            ->assertSee('/recent-request')->assertDontSee('/older-request')
            ->set('period', '6_hours')->assertSee('/older-request');
    }

    public function test_internal_requests_are_excluded_from_request_monitor(): void
    {
        app(Pulse::class)->startRecording();
        foreach (['/pulse', '/livewire/update', '/up', '/build/app.js'] as $path) {
            app(RequestMonitorRecorder::class)->record(new RequestHandled(Request::create($path), new Response('OK')));
        }
        app(Pulse::class)->ingest();
        $this->assertSame(0, DB::connection('pulse_test')->table('pulse_entries')->where('type', 'request_monitor')->count());
    }
}
