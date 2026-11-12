<?php

namespace Tests\Unit;

use App\Database\CachedPostgresConnection;
use PDO;
use Tests\TestCase;

class PgsqlFailFastTimeoutTest extends TestCase
{
    public function test_fail_open_on_non_pgsql_and_invalid_config(): void
    {
        $connection = new CachedPostgresConnection(
            new PDO('sqlite::memory:'),
            ':memory:',
            '',
            ['name' => 'fail-fast-test']
        );

        config([
            'database.connections.fail-fast-test.statement_timeout' => 'not-a-timeout',
            'database.connections.fail-fast-test.lock_timeout' => '5s',
            'database.connections.fail-fast-test.idle_in_transaction_session_timeout' => '10s',
        ]);

        $connection->statement('create table probe (id integer primary key)');
        $connection->insert('insert into probe (id) values (1)');
        $rows = $connection->select('select * from probe');

        $this->assertCount(1, $rows);
    }

    public function test_pgsql_timeouts_default_to_fail_fast_values(): void
    {
        $this->assertSame('5s', config('database.connections.pgsql.statement_timeout'));
        $this->assertSame('5s', config('database.connections.pgsql.lock_timeout'));
        $this->assertSame('10s', config('database.connections.pgsql.idle_in_transaction_session_timeout'));
    }
}
