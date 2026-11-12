<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\AssessmentComponentTemplateController;
use Modules\Capstone\Http\Requests\Assessment\StoreComponentTemplateRequest;
use Modules\Capstone\Models\AssessmentComponentTemplate;
use Tests\TestCase;

/**
 * Smoke: Chunk 1 template Request is wired to the controller correctly.
 * Proves the 201 path and the rejection path through the real FormRequest
 * (a rules() pin alone cannot catch a mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class TemplateRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.template_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('template_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_assessment_component_templates', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->decimal('weight', 5, 2);
            $t->boolean('is_active')->default(true);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
    }

    private function storeRequest(User $user, array $data): StoreComponentTemplateRequest
    {
        $request = StoreComponentTemplateRequest::create('/x', 'POST', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    public function test_store_template_returns_201_and_persists(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);

        $response = (new AssessmentComponentTemplateController)->store(
            $this->storeRequest($user, ['code' => 'SMK', 'name' => 'Smoke', 'weight' => 25])
        );

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('SMK', AssessmentComponentTemplate::first()->code);
        $this->assertEquals($user->id, AssessmentComponentTemplate::first()->created_by);
    }

    public function test_store_template_rejects_invalid_weight(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);

        $this->expectException(ValidationException::class);
        $this->storeRequest($user, ['code' => 'SMK', 'name' => 'Smoke', 'weight' => 999]);
    }
}
