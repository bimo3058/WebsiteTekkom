<?php

namespace Tests\Unit;

use App\Database\CachedPostgresConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PDO;
use Tests\TestCase;

class CachedPostgresConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'database.query_cache.enabled' => true,
            'database.query_cache.store' => 'array',
            'database.query_cache.ttl_seconds' => 60,
            'database.query_cache.max_bytes' => 1024 * 1024,
            'database.query_cache.cache_console' => true,
        ]);

        Cache::clear();
        $this->app->instance('request', Request::create('/cached-query', 'GET'));
    }

    public function test_it_caches_identical_selects_and_invalidates_them_after_a_write(): void
    {
        $connection = $this->connection();
        $connection->statement('create table records (id integer primary key, name varchar(50))');
        $connection->insert('insert into records (id, name) values (?, ?)', [1, 'before']);

        $first = $connection->select('select name from records where id = ?', [1]);
        $second = $connection->select('select name from records where id = ?', [1]);

        $request = $this->app->make('request');
        $this->assertSame('before', $first[0]->name);
        $this->assertEquals($first, $second);
        $this->assertSame(1, $request->attributes->get('db_query_cache_misses'));
        $this->assertSame(1, $request->attributes->get('db_query_cache_hits'));

        $connection->update('update records set name = ? where id = ?', ['after', 1]);
        $fresh = $connection->select('select name from records where id = ?', [1]);

        $this->assertSame('after', $fresh[0]->name);
        $this->assertSame(2, $request->attributes->get('db_query_cache_misses'));
    }

    public function test_it_bypasses_volatile_and_locking_queries(): void
    {
        $connection = $this->connection();

        $connection->select('select current_timestamp as generated_at');
        $connection->select('select current_timestamp as generated_at');

        $request = $this->app->make('request');
        $this->assertNull($request->attributes->get('db_query_cache_hits'));
        $this->assertNull($request->attributes->get('db_query_cache_misses'));
    }

    private function connection(): CachedPostgresConnection
    {
        return new CachedPostgresConnection(
            new PDO('sqlite::memory:'),
            ':memory:',
            '',
            ['name' => 'query-cache-test']
        );
    }
}
