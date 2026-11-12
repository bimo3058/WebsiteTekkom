<?php

namespace Tests\Unit;

use Modules\Capstone\Http\Controllers\Admin\DocumentUploadController as AdminController;
use Modules\Capstone\Http\Controllers\DocumentController;
use PHPUnit\Framework\TestCase;

class CapstoneDownloadFilenameTest extends TestCase
{
    public function test_phase_download_filename_uses_slug_type_and_version(): void
    {
        $this->assertSame(
            'Absen_Kehadiran_Pengunjung_v3.pdf',
            $this->phaseFilename('Absen Kehadiran Pengunjung', 'scan.pdf', 'capstone/documents/owner/absen.pdf', 3)
        );
    }

    public function test_extension_is_preserved_from_original_name(): void
    {
        $this->assertSame(
            'C400_v2.docx',
            $this->phaseFilename('C400', 'draft.docx', 'capstone/documents/group/c400.docx', 2)
        );
    }

    public function test_slash_in_document_type_is_sanitized(): void
    {
        $this->assertSame(
            'TOEFL_IELTS_v1.pdf',
            $this->phaseFilename('TOEFL/IELTS', 'sertifikat.pdf', 'capstone/documents/owner/toefl.pdf', 1)
        );
    }

    public function test_unknown_extension_falls_back_to_pdf(): void
    {
        $this->assertSame(
            'GENERAL_v1.pdf',
            $this->phaseFilename('GENERAL', 'notes.txt', 'capstone/documents/owner/notes.txt', 1)
        );
    }

    public function test_admin_filename_appends_group_code(): void
    {
        $controller = (new \ReflectionClass(AdminController::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod(AdminController::class, 'buildDownloadName');

        $this->assertSame(
            'C400_v1_KEL07.pdf',
            $method->invoke($controller, 'C400', 'draft.pdf', 'capstone/documents/group/c400.pdf', 1, 'KEL07')
        );
    }

    private function phaseFilename(?string $type, ?string $originalName, string $filePath, int $version): string
    {
        $controller = (new \ReflectionClass(DocumentController::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod(DocumentController::class, 'downloadFilename');

        return $method->invoke($controller, $type, $originalName, $filePath, $version);
    }
}
