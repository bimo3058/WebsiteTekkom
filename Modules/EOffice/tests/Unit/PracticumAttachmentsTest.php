<?php

namespace Modules\EOffice\Tests\Unit;

use App\Models\User;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\AttachmentController;
use Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PracticumAttachmentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs((new User)->forceFill(['id'=>7,'name'=>'Viewer']));
    }

    public function test_pdf_previews_inline_with_the_original_filename(): void
    {
        $url=app(PracticumAttachments::class)->publicUrl('uploads/random.pdf','eoffice','Print IRS.pdf');
        $storage=$this->mock(SupabaseStorage::class);
        $storage->shouldReceive('download')->once()->with('uploads/random.pdf','eoffice')->andReturn(['content'=>"%PDF-1.4\nTest",'mime_type'=>'application/octet-stream']);
        $response=(new AttachmentController)(Request::create($url),$storage);
        $this->assertSame('application/pdf',$response->headers->get('Content-Type'));
        $this->assertStringStartsWith('inline;',$response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Print IRS.pdf',$response->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString('?download=',$url);
    }

    public function test_other_formats_are_sent_directly_to_the_browser_with_original_names(): void
    {
        $url=app(PracticumAttachments::class)->publicUrl('uploads/random.bin','eoffice','Data.zip');
        $storage=$this->mock(SupabaseStorage::class);
        $storage->shouldReceive('download')->once()->andReturn(['content'=>"PK\x03\x04\0\0\0\0",'mime_type'=>'application/octet-stream']);
        $preview=(new AttachmentController)(Request::create($url),$storage);
        $this->assertSame("PK\x03\x04\0\0\0\0",$preview->getContent());
        $this->assertStringStartsWith('inline;',$preview->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Data.zip',$preview->headers->get('Content-Disposition'));
    }

    public function test_active_content_is_sandboxed_without_a_custom_preview_page(): void
    {
        $response=PracticumAttachments::response('<!doctype html><script>alert(1)</script>','Fake.pdf',false,'/download');
        $this->assertStringContainsString('sandbox;',$response->headers->get('Content-Security-Policy'));
        $this->assertStringNotContainsString('text/html',$response->headers->get('Content-Type'));
    }

    public function test_explicit_download_preserves_the_original_name(): void
    {
        $response=PracticumAttachments::response('Document','Laporan Mahasiswa.txt',true,'');
        $this->assertStringStartsWith('attachment;',$response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Laporan Mahasiswa.txt',$response->headers->get('Content-Disposition'));
    }

    public function test_modified_expired_and_other_account_links_cannot_read_storage(): void
    {
        $storage=$this->mock(SupabaseStorage::class);
        $storage->shouldNotReceive('download');
        $url=app(PracticumAttachments::class)->publicUrl('uploads/a.pdf','eoffice','A.pdf');
        $expired=URL::temporarySignedRoute('eoffice.manprak.attachment',now()->subMinute(),['path'=>'uploads/a.pdf','name'=>'A.pdf','viewer'=>'7']);
        foreach ([$url.'&download=1',$expired] as $invalid) {
            try {(new AttachmentController)(Request::create($invalid),$storage);$this->fail('Invalid link accepted');}
            catch(HttpException $error){$this->assertSame(403,$error->getStatusCode());}
        }
        $this->actingAs((new User)->forceFill(['id'=>8,'name'=>'Another viewer']));
        try {(new AttachmentController)(Request::create($url),$storage);$this->fail('Other account accepted');}
        catch(HttpException $error){$this->assertSame(403,$error->getStatusCode());}
    }
}
