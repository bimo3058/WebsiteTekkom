<?php

namespace Tests\Unit;

use App\Services\UserPresence;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserPresenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::clear();

        Schema::dropIfExists('users');
        Schema::create('users', function ($table): void {
            $table->increments('id');
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
        });
        DB::table('users')->insert(['id' => 1, 'is_online' => false, 'last_seen_at' => null]);
    }

    public function test_touch_writes_presence_once_per_interval(): void
    {
        $presence = app(UserPresence::class);

        DB::enableQueryLog();

        $this->assertTrue($presence->touch(1));

        $queriesAfterFirst = count(DB::getQueryLog());

        $this->assertFalse($presence->touch(1));

        $queriesAfterSecond = count(DB::getQueryLog());

        $this->assertSame($queriesAfterFirst, $queriesAfterSecond);
        $this->assertTrue((bool) DB::table('users')->where('id', 1)->value('is_online'));
        $this->assertNotNull(DB::table('users')->where('id', 1)->value('last_seen_at'));

        DB::disableQueryLog();
    }

    public function test_is_timeout_classifies_postgres_cancellations(): void
    {
        $presence = app(UserPresence::class);
        $method = new \ReflectionMethod(UserPresence::class, 'isTimeout');

        $timeout = $this->queryException('57014', 'canceling statement due to statement timeout');
        $lockTimeout = $this->queryException('55P03', 'canceling statement due to lock timeout');
        $deadlock = $this->queryException('40P01', 'deadlock detected');

        $this->assertTrue($method->invoke($presence, $timeout));
        $this->assertTrue($method->invoke($presence, $lockTimeout));
        $this->assertTrue($method->invoke($presence, $deadlock));

        $generic = $this->queryException('08006', 'connection failure: unexpected EOF');
        $this->assertFalse($method->invoke($presence, $generic));
    }

    private function queryException(string $code, string $message): QueryException
    {
        $previous = new \PDOException($message, (int) $code);
        $previous->errorInfo = [$code, 0, $message];

        return new QueryException(
            'sqlite',
            'update "users" set "is_online" = ?',
            [true],
            $previous
        );
    }
}
