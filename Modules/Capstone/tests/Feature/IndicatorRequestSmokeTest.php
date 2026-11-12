<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\PeerReviewController;
use Modules\Capstone\Http\Requests\PeerReview\StoreIndicatorRequest;
use Modules\Capstone\Models\PeerReviewIndicator;
use Tests\TestCase;

/**
 * Smoke: Chunk 5b indicator Request is wired to the controller correctly.
 * Proves the create path and the rule rejection through the real FormRequest
 * (a rules() pin alone cannot catch a mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class IndicatorRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.indicator_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('indicator_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_peer_review_indicators', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('name');
            $t->text('description')->nullable();
            $t->decimal('weight', 5, 2)->default(0);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
    }

    private function storeRequest(User $user, array $data): StoreIndicatorRequest
    {
        $request = StoreIndicatorRequest::create('/x', 'POST', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    public function test_store_indicator_persists(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1']);

        $response = (new PeerReviewController)->storeIndicator(
            $this->storeRequest($user, ['period_id' => $periodId, 'name' => 'Kontribusi', 'weight' => 40])
        );

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Kontribusi', PeerReviewIndicator::first()->name);
    }

    public function test_store_indicator_rejects_missing_name(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);

        $this->expectException(ValidationException::class);
        $this->storeRequest($user, ['period_id' => 1, 'weight' => 40]);
    }
}
