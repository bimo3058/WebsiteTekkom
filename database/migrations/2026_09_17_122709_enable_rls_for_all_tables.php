<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all tables in the public schema
        $tables = DB::select("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
            AND tablename NOT LIKE 'pg_%'
            AND tablename NOT IN ('migrations', 'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring')
            ORDER BY tablename
        ");

        // Enable RLS for each table
        foreach ($tables as $table) {
            $tableName = $table->tablename;

            // Enable RLS
            DB::statement("ALTER TABLE \"{$tableName}\" ENABLE ROW LEVEL SECURITY;");

            // Create a permissive policy that allows all operations for authenticated users
            // This is a safe default - you can customize per table later
            DB::statement("
                CREATE POLICY \"Enable all access for authenticated users\"
                ON \"{$tableName}\"
                FOR ALL
                TO authenticated
                USING (true)
                WITH CHECK (true);
            ");

            // Create policy for service role (full access)
            DB::statement("
                CREATE POLICY \"Enable all access for service role\"
                ON \"{$tableName}\"
                FOR ALL
                TO service_role
                USING (true)
                WITH CHECK (true);
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all tables in the public schema
        $tables = DB::select("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
            AND tablename NOT LIKE 'pg_%'
            AND tablename NOT IN ('migrations', 'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring')
            ORDER BY tablename
        ");

        // Disable RLS and drop policies for each table
        foreach ($tables as $table) {
            $tableName = $table->tablename;

            // Drop all policies for this table
            $policies = DB::select("
                SELECT policyname
                FROM pg_policies
                WHERE tablename = '{$tableName}'
            ");

            foreach ($policies as $policy) {
                DB::statement("DROP POLICY IF EXISTS \"{$policy->policyname}\" ON \"{$tableName}\";");
            }

            // Disable RLS
            DB::statement("ALTER TABLE \"{$tableName}\" DISABLE ROW LEVEL SECURITY;");
        }
    }
};
