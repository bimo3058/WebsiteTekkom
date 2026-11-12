<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\GradeConsistencyController;
use Modules\Capstone\Http\Requests\Grade\UpdateConsistencyCheckRequest;
use Modules\Capstone\Models\GradeConsistencyCheck;
use Tests\TestCase;

/**
 * Smoke: Chunk 5a consistency Request is wired to the controller correctly.
 * Proves the update path and the rule rejection through the real FormRequest
 * (a rules() pin alone cannot catch a mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class ConsistencyRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.consistency_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('consistency_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_grade_consistency_checks', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id')->nullable();
            $t->unsignedBigInteger('student_id')->nullable();
            $t->decimal('pdc1_score', 5, 2)->nullable();
            $t->decimal('pdc2_score', 5, 2)->nullable();
            $t->decimal('deviation', 5, 2)->nullable();
            $t->string('status')->default('UNCHECKED');
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('checked_by')->nullable();
            $t->timestamps();
        });
    }

    private function updateRequest(User $user, array $data): UpdateConsistencyCheckRequest
    {
        $request = UpdateConsistencyCheckRequest::create('/x', 'PUT', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    public function test_update_check_persists_status_and_checker(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $check = GradeConsistencyCheck::create(['status' => 'UNCHECKED']);

        $response = (new GradeConsistencyController)->update(
            $this->updateRequest($user, ['status' => 'CONSISTENT', 'notes' => 'Looks good']),
            $check->id
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('CONSISTENT', $check->fresh()->status);
        $this->assertEquals($user->id, $check->fresh()->checked_by);
    }

    public function test_update_check_rejects_unknown_status(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);

        $this->expectException(ValidationException::class);
        $this->updateRequest($user, ['status' => 'MAYBE']);
    }
}
