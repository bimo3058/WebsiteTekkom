<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\DigitalSignatureController;
use Modules\Capstone\Http\Requests\DigitalSignature\SignDocumentRequest;
use Modules\Capstone\Models\DigitalSignature;
use Tests\TestCase;

/**
 * Smoke: Chunk 4 signature Request is wired to the controller correctly.
 * Proves the 201 path and the rule rejection through the real FormRequest
 * (a rules() pin alone cannot catch a mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class SignatureRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.signature_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('signature_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_digital_signatures', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('document_reference');
            $t->string('document_type');
            $t->text('signature_data')->nullable();
            $t->string('hash');
            $t->timestamp('signed_at')->nullable();
            $t->timestamps();
        });
    }

    private function signRequest(User $user, array $data): SignDocumentRequest
    {
        $request = SignDocumentRequest::create('/x', 'POST', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    private function payload(): array
    {
        return [
            'document_reference' => 'ST-001',
            'document_type' => 'SURAT_TUGAS',
            'signature_data' => 'base64data',
        ];
    }

    public function test_sign_returns_201_and_persists(): void
    {
        $user = User::create(['name' => 'Dosen', 'email' => 'dosen@example.test']);

        $response = (new DigitalSignatureController)->sign($this->signRequest($user, $this->payload()));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('ST-001', DigitalSignature::first()->document_reference);
        $this->assertNotEmpty(DigitalSignature::first()->hash);
    }

    public function test_sign_rejects_unknown_document_type(): void
    {
        $user = User::create(['name' => 'Dosen', 'email' => 'dosen@example.test']);
        $payload = $this->payload();
        $payload['document_type'] = 'MEMO';

        $this->expectException(ValidationException::class);
        $this->signRequest($user, $payload);
    }
}
