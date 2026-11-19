<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Support\PulseMigration;

return new class extends PulseMigration
{
    public function up(): void
    {
        if (! $this->shouldRun()) {
            return;
        }

        $schema = Schema::connection($this->getConnection());
        $indexes = [
            'pulse_values' => [
                'primary' => [['id']],
                'unique' => [['type', 'key_hash']],
                'index' => [['timestamp'], ['type']],
            ],
            'pulse_entries' => [
                'primary' => [['id']],
                'index' => [['timestamp'], ['type'], ['key_hash'], ['timestamp', 'type', 'key_hash', 'value']],
            ],
            'pulse_aggregates' => [
                'primary' => [['id']],
                'unique' => [['bucket', 'period', 'type', 'aggregate', 'key_hash']],
                'index' => [['period', 'bucket'], ['type'], ['period', 'type', 'aggregate', 'bucket']],
            ],
        ];

        foreach ($indexes as $table => $types) {
            foreach ($types as $type => $definitions) {
                foreach ($definitions as $columns) {
                    if (! $schema->hasIndex($table, $columns, $type === 'index' ? null : $type)) {
                        $schema->table($table, fn (Blueprint $blueprint) => $blueprint->{$type}($columns));
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // These indexes belong to Pulse's original schema and are required for ingestion.
    }
};
