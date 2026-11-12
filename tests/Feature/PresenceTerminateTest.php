<?php

namespace Tests\Feature;

use App\Http\Middleware\UpdateUserOnlineStatus;
use App\Models\User;
use App\Services\UserPresence;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PresenceTerminateTest extends TestCase
{
    public function test_terminate_never_throws_when_presence_fails(): void
    {
        $user = new User(['name' => 'Heartbeat Probe']);
        $user->id = 1;

        $this->mock(UserPresence::class, function ($mock): void {
            $mock->shouldReceive('touch')->once()->andThrow(
                new QueryException(
                    'sqlite',
                    'update "users" set "is_online" = ?',
                    [true],
                    new \PDOException('canceling statement due to statement timeout')
                )
            );
        });

        $middleware = app(UpdateUserOnlineStatus::class);
        $request = Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware->terminate($request, new Response('ok'));

        $this->assertTrue(true);
    }

    public function test_terminate_skips_health_and_telescope_paths(): void
    {
        $user = new User(['name' => 'Heartbeat Probe']);
        $user->id = 1;

        $this->mock(UserPresence::class, function ($mock): void {
            $mock->shouldNotReceive('touch');
        });

        $middleware = app(UpdateUserOnlineStatus::class);

        foreach (['/up', '/telescope/requests', '/pulse', '/livewire/update'] as $path) {
            $request = Request::create($path, 'GET');
            $request->setUserResolver(fn () => $user);

            $middleware->terminate($request, new Response('ok'));
        }

        $this->assertTrue(true);
    }

    public function test_terminate_touches_once_on_dashboard_path(): void
    {
        $user = new User(['name' => 'Heartbeat Probe']);
        $user->id = 1;

        $this->mock(UserPresence::class, function ($mock): void {
            $mock->shouldReceive('touch')->once()->with(1)->andReturn(true);
        });

        $middleware = app(UpdateUserOnlineStatus::class);
        $request = Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware->terminate($request, new Response('ok'));

        $this->assertTrue(true);
    }
}
