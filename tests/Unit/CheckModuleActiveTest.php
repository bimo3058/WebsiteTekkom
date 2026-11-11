<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckModuleActive;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CheckModuleActiveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $module = \Mockery::mock(SystemModule::class)->makePartial();
        $module->is_active = true;
        $module->shouldReceive('setting')->with('max_upload', 10)->andReturn(5);
        Cache::put('module_data_eoffice', $module, 3600);
    }

    private function handle(array $files): Response
    {
        $request = Request::create('/draft', 'POST', [], [], $files);
        return (new CheckModuleActive)->handle($request, fn () => new Response('', 204), 'eoffice');
    }

    public function test_nested_stage_attachments_are_checked_without_array_error(): void
    {
        $response = $this->handle([
            'tahap' => [
                ['lampiran' => [UploadedFile::fake()->create('proposal.pdf', 5120)]],
                ['lampiran' => [UploadedFile::fake()->create('proyek.zip', 100)]],
            ],
        ]);
        $this->assertSame(204, $response->getStatusCode());
    }

    public function test_oversized_nested_attachment_still_rejected(): void
    {
        $response = $this->handle(['tahap' => [['lampiran' => [UploadedFile::fake()->create('besar.zip', 5121)]]]]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('5 MB', session('error'));
    }

    public function test_empty_optional_nested_attachments_are_safe(): void
    {
        $this->assertSame(204, $this->handle(['tahap' => [['lampiran' => [null]], ['lampiran' => []]]])->getStatusCode());
        $this->assertSame(204, $this->handle([])->getStatusCode());
    }

    public function test_single_and_flat_multiple_uploads_remain_supported(): void
    {
        $this->assertSame(204, $this->handle(['file' => UploadedFile::fake()->create('file.pdf', 100)])->getStatusCode());
        $this->assertSame(204, $this->handle(['files' => [UploadedFile::fake()->create('a.pdf', 100), UploadedFile::fake()->create('b.zip', 200)]])->getStatusCode());
    }
}
