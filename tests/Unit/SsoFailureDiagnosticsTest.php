<?php

namespace Tests\Unit;

use App\Services\SsoFailureDiagnostics;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Laravel\Socialite\Two\InvalidStateException;
use PHPUnit\Framework\TestCase;

class SsoFailureDiagnosticsTest extends TestCase
{
    public function test_only_safe_codes_are_retained_and_body_position_is_preserved(): void
    {
        $response = new Response(401, [], json_encode(['error'=>'invalid_client','error_codes'=>[7000222,'sensitive',-1], 'error_description'=>'secret credentials', 'access_token'=>'private-token']));
        $response->getBody()->read(2);
        $exception = new ClientException('sensitive message', new Request('POST','https://login.microsoftonline.com/tenant/token?code=private-code',['Authorization'=>'Bearer private-token']),$response);
        $context = (new SsoFailureDiagnostics)->context($exception);
        $this->assertSame(401,$context['http_status']);
        $this->assertSame('invalid_client',$context['provider_error']);
        $this->assertSame([7000222],$context['aad_codes']);
        $this->assertSame(2,$response->getBody()->tell());
        foreach(['private','sensitive','credentials','Authorization'] as $secret) $this->assertStringNotContainsString($secret,json_encode($context));
    }

    public function test_untrusted_provider_errors_and_generic_messages_are_not_logged(): void
    {
        $exception = new ClientException('secret',new Request('GET','https://example.com'),new Response(400,[],json_encode(['error'=>['code'=>'private-email@example.com']])));
        $context=(new SsoFailureDiagnostics)->context($exception);
        $this->assertArrayNotHasKey('provider_error',$context);
        $this->assertSame('other',$context['provider']);
        $this->assertSame(['exception'=>\RuntimeException::class],(new SsoFailureDiagnostics)->context(new \RuntimeException('secret')));
        $this->assertStringContainsString('Referensi: test-reference',(new SsoFailureDiagnostics)->message(new InvalidStateException,'test-reference'));
    }
}
