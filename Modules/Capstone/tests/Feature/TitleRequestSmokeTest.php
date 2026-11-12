<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\TitleController;
use Modules\Capstone\Http\Requests\Title\StoreTitleRequest;
use Modules\Capstone\Models\Title;
use Tests\TestCase;

/**
 * Smoke: Chunk 3 title Request is wired to the controller correctly.
 * Proves the 201 path and the rule rejection through the real FormRequest
 * (a rules() pin alone cannot catch a mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class TitleRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.title_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('title_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });
        Schema::create('lecturers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_titles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('lecturer_id')->nullable();
            $t->string('title');
            $t->text('description')->nullable();
            $t->text('problem_statement')->nullable();
            $t->text('scope')->nullable();
            $t->text('specializations')->nullable();
            $t->integer('quota')->default(1);
            $t->string('status')->default('open');
            $t->string('title_source')->default('LECTURER');
            $t->unsignedBigInteger('period_id')->nullable();
            $t->timestamps();
        });
    }

    private function lecturer(): User
    {
        $user = User::create(['name' => 'Dosen', 'email' => 'dosen@example.test']);
        DB::table('lecturers')->insert(['user_id' => $user->id, 'name' => 'Dosen']);

        return $user;
    }

    private function storeRequest(User $user, array $data): StoreTitleRequest
    {
        $request = StoreTitleRequest::create('/x', 'POST', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    private function payload(): array
    {
        return [
            'title' => 'Smoke Title',
            'description' => 'Description',
            'problem_statement' => 'Problem',
            'scope' => 'Scope',
            'specializations' => ['AI'],
            'quota' => 2,
        ];
    }

    public function test_store_title_returns_201_and_persists(): void
    {
        $user = $this->lecturer();

        $response = (new TitleController)->store($this->storeRequest($user, $this->payload()));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Smoke Title', Title::first()->title);
        $this->assertSame('LECTURER', Title::first()->title_source);
    }

    public function test_store_title_rejects_missing_title(): void
    {
        $user = $this->lecturer();
        $payload = $this->payload();
        unset($payload['title']);

        $this->expectException(ValidationException::class);
        $this->storeRequest($user, $payload);
    }
}
