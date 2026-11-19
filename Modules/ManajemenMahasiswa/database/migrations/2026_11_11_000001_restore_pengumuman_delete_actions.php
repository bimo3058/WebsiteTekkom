<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->restoreActions(true);
    }

    public function down(): void
    {
        $this->restoreActions(false);
    }

    private function restoreActions(bool $restore): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::transaction(function () use ($restore) {
            DB::statement("SET LOCAL lock_timeout = '5s'");
            $actions = [
                'mk_repo_mulmed' => 'SET NULL',
                'mk_pengumuman_attachments' => 'CASCADE',
                'mk_pengumuman_personal_pins' => 'CASCADE',
                'mk_pengumuman_approval_requests' => 'CASCADE',
            ];

            foreach ($actions as $table => $action) {
                $constraints = DB::select(<<<'SQL'
SELECT c.conname, c.convalidated, pg_get_constraintdef(c.oid) AS definition
FROM pg_constraint c
WHERE c.conrelid = ?::regclass AND c.confrelid = 'public.mk_pengumuman'::regclass
AND c.contype = 'f'
AND c.conkey = ARRAY[(SELECT attnum FROM pg_attribute WHERE attrelid = c.conrelid AND attname = 'pengumuman_id')]::smallint[]
SQL, ['public.'.$table]);

                foreach ($constraints as $constraint) {
                    $name = '"'.str_replace('"', '""', $constraint->conname).'"';
                    $action = $restore ? $action : 'NO ACTION';
                    $definition = preg_replace('/ ON DELETE (?:NO ACTION|RESTRICT|CASCADE|SET NULL|SET DEFAULT)/', '', $constraint->definition);
                    $definition = preg_replace('/ NOT VALID$/', '', $definition);
                    $definition .= ' ON DELETE '.$action.($constraint->convalidated ? '' : ' NOT VALID');

                    DB::statement('ALTER TABLE public."'.$table.'" DROP CONSTRAINT '.$name);
                    DB::statement('ALTER TABLE public."'.$table.'" ADD CONSTRAINT '.$name.' '.$definition);
                }
            }
        });
    }
};
