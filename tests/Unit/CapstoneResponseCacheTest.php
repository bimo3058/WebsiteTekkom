<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Capstone\Http\Middleware\CacheCapstoneResponse;
use Tests\TestCase;

class CapstoneResponseCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'capstone.http_cache.enabled' => true,
            'capstone.http_cache.ttl_seconds' => 30,
            'capstone.http_cache.max_bytes' => 1024,
        ]);

        Cache::clear();
    }

    public function test_it_reuses_a_private_get_response_for_the_same_user_and_role(): void
    {
        $calls = 0;
        $next = function () use (&$calls) {
            $calls++;

            return response()->json(['calls' => $calls]);
        };

        $middleware = new CacheCapstoneResponse;
        $first = $middleware->handle($this->request('GET'), $next);
        $second = $middleware->handle($this->request('GET'), $next);

        $this->assertSame(1, $calls);
        $this->assertSame('MISS', $first->headers->get('X-Capstone-Cache'));
        $this->assertSame('HIT', $second->headers->get('X-Capstone-Cache'));
        $this->assertSame($first->getContent(), $second->getContent());
    }

    public function test_a_successful_mutation_invalidates_previous_get_responses(): void
    {
        $calls = 0;
        $middleware = new CacheCapstoneResponse;
        $get = function () use (&$calls) {
            $calls++;

            return response()->json(['calls' => $calls]);
        };

        $middleware->handle($this->request('GET'), $get);
        $middleware->handle(
            $this->request('POST'),
            fn () => response()->json(['saved' => true])
        );
        $response = $middleware->handle($this->request('GET'), $get);

        $this->assertSame(2, $calls);
        $this->assertSame('MISS', $response->headers->get('X-Capstone-Cache'));
    }

    private function request(string $method): Request
    {
        $request = Request::create('/api/capstone/dashboard?period=1', $method);
        $user = new User;
        $user->id = 99;

        $request->setUserResolver(fn () => $user);
        $request->attributes->set('capstone_role', 'admin');

        return $request;
    }
}
