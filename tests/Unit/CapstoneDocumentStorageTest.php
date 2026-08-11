<?php

namespace Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Modules\Capstone\Services\DocumentStorageService;
use Tests\TestCase;

class CapstoneDocumentStorageTest extends TestCase
{
    public function test_uploads_use_private_capstone_prefix_and_signed_urls(): void
    {
        config()->set('services.supabase', [
            'url' => 'https://supabase.test',
            'key' => 'test-service-role',
            'bucket' => 'storage_web',
        ]);

        Http::fake(function (Request $request) {
            if (str_contains($request->url(), '/storage/v1/object/sign/')) {
                return Http::response([
                    'signedURL' => '/object/sign/storage_web/capstone/document.pdf?token=signed',
                ]);
            }

            return Http::response(['Key' => 'stored'], 200);
        });

        $storage = app(DocumentStorageService::class);
        $path = $storage->store(
            UploadedFile::fake()->create('Proposal Final.pdf', 10, 'application/pdf'),
            'documents',
            '7/PDC1'
        );

        $this->assertStringStartsWith('capstone/documents/7/PDC1/', $path);
        $this->assertStringEndsWith('.pdf', $path);
        $this->assertStringContainsString('?token=signed', (string) $storage->signedUrl($path));

        Http::assertSent(fn (Request $request) =>
            $request->method() === 'POST'
            && str_contains($request->url(), '/storage/v1/object/storage_web/capstone/documents/7/PDC1/')
            && $request->hasHeader('Authorization', 'Bearer test-service-role')
        );
    }
}
