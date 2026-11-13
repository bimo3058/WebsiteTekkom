<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;

/** Run unchanged historical migrations against isolated SQLite fixtures using today's table names. */
trait RunsLegacyPracticumMigrations
{
    protected function withLegacyPracticumTables(callable $callback): mixed
    {
        if(DB::connection()->getDriverName()!=='sqlite' || DB::connection()->getDatabaseName()!==':memory:')throw new \LogicException('Legacy fixture migrations must only run in isolated SQLite memory databases.');
        return DB::transaction(function()use($callback){
            $namespace=require base_path('Modules/EOffice/database/migrations/2026_11_05_000002_standardize_practicum_table_names.php');
            $namespace->down();
            try{return $callback();}finally{$namespace->up();}
        });
    }

    protected function runLegacyPracticumMigration(string $path): void
    {
        $this->withLegacyPracticumTables(fn()=>(require $path)->up());
    }
}
