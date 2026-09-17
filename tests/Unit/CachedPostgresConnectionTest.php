<?php

namespace Tests\Unit;

use App\Database\CachedPostgresConnection;
use Illuminate\Cache\Repository;
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

    public function test_transaction_writes_invalidate_once_after_commit(): void
    {
        $connection = $this->connection();
        $connection->statement('create table records (id integer primary key, name varchar(50))');
        $connection->insert('insert into records values (1, ?)', ['before']);
        $connection->select('select name from records');
        $version = Cache::get('database:query-cache:version');

        $connection->transaction(function () use ($connection, $version) {
            $connection->update('update records set name = ?', ['middle']);
            $connection->transaction(function () use ($connection) {
                $connection->update('update records set name = ?', ['after']);
            });
            $this->assertSame($version, Cache::get('database:query-cache:version'));
            $this->assertSame('after', $connection->select('select name from records')[0]->name);
        });

        $this->assertSame($version + 1, Cache::get('database:query-cache:version'));
        $this->assertSame('after', $connection->select('select name from records')[0]->name);
    }

    public function test_rollback_keeps_cached_committed_data_and_manual_commit_invalidates(): void
    {
        $connection = $this->connection();
        $connection->statement('create table records (id integer primary key, name varchar(50))');
        $connection->insert('insert into records values (1, ?)', ['before']);
        $connection->select('select name from records');
        $version = Cache::get('database:query-cache:version');

        $connection->beginTransaction();
        $connection->update('update records set name = ?', ['discard']);
        $connection->rollBack();
        $this->assertSame($version, Cache::get('database:query-cache:version'));
        $this->assertSame('before', $connection->select('select name from records')[0]->name);

        $connection->beginTransaction();
        $connection->commit();
        $this->assertSame($version, Cache::get('database:query-cache:version'));

        $connection->beginTransaction();
        $connection->update('update records set name = ?', ['after']);
        $connection->commit();
        $this->assertSame($version + 1, Cache::get('database:query-cache:version'));
        $this->assertSame('after', $connection->select('select name from records')[0]->name);
    }

    public function test_disabled_cache_does_not_contact_redis_even_for_writes(): void
    {
        config(['database.query_cache.enabled' => false]);
        Cache::shouldReceive('store')->never();
        $connection = $this->connection();
        $connection->statement('create table records (id integer primary key)');
        $connection->insert('insert into records values (1)');
        $this->assertCount(1, $connection->select('select * from records'));
    }

    public function test_cache_outage_is_retried_on_the_next_request_only(): void
    {
        $store = \Mockery::mock(Repository::class);
        $store->shouldReceive('rememberForever')->once()->andThrow(new \RuntimeException('Redis unavailable'));
        Cache::shouldReceive('store')->with('array')->once()->andReturn($store);
        $connection = $this->connection();
        $this->assertSame(1, $connection->select('select 1 as value')[0]->value);
        $this->assertSame(2, $connection->select('select 2 as value')[0]->value);

        $this->app->instance('request', Request::create('/next-request', 'GET'));
        $healthyStore = new Repository(new \Illuminate\Cache\ArrayStore);
        Cache::shouldReceive('store')->with('array')->once()->andReturn($healthyStore);
        $this->assertSame(3, $connection->select('select 3 as value')[0]->value);
        $this->assertSame(1, request()->attributes->get('db_query_cache_misses'));
    }
}
