<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\EoAuditLog;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\PendaftaranPraktikan;
use Modules\EOffice\Models\PeriodePendaftaran;

class RegistrationReviewController extends Controller
{
    private const TYPES = [
        'koor' => [PendaftaranKoordinator::class, 'Koordinator', ['transkrip_path' => 'Transkrip Nilai', 'berkas_cerc_path' => 'Keanggotaan CERC', 'berkas_tambahan_path' => 'Berkas Tambahan']],
        'asprak' => [PendaftaranAsprak::class, 'Asisten', ['transkrip_path' => 'Transkrip Nilai', 'berkas_cerc_path' => 'Keanggotaan CERC', 'cv_path' => 'CV', 'berkas_tambahan_path' => 'Berkas Tambahan']],
        'praktikan' => [PendaftaranPraktikan::class, 'Praktikan', ['irs_path' => 'Cetak IRS']],
    ];

    private function definition(Request $request, string $type): array
    {
        abort_unless($request->user()?->hasAnyRole(['superadmin', 'admin_eoffice']), 403);
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type];
    }

    public function index(Request $request)
    {
        $this->definition($request, 'praktikan');
        $data = $request->validate(['search' => 'nullable|string|max:100', 'status' => 'nullable|in:pending,approved,rejected']);
        $pendaftaran = PendaftaranPraktikan::with(['user.student', 'praktikum'])
            ->when($data['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($data['search'] ?? null, fn ($q, $search) => $q->whereHas('user', fn ($u) => $u->whereRaw('LOWER(name) like ?', ['%'.mb_strtolower($search).'%'])))
            ->latest()->paginate(20)->withQueryString();

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-praktikan', compact('pendaftaran'));
    }

    public function show(Request $request, string $type, int $id)
    {
        [$model, $label, $documents] = $this->definition($request, $type);
        $registration = $model::with(['user.student', 'praktikum', 'direviewOleh'])->findOrFail($id);
        if ($type === 'asprak') {
            unset($documents['cv_path']);
        }
        $additionalDocumentRequired = false;
        if (in_array($type, ['koor', 'asprak'], true) && Schema::hasTable('manprak_periode_pendaftaran')) {
            $additionalDocumentName = PeriodePendaftaran::where('praktikum_id', $registration->praktikum_id)
                ->where('jenis', $type)
                ->latest('created_at')
                ->value('nama_berkas_tambahan');
            $additionalDocumentRequired = filled($additionalDocumentName);
            $documents['berkas_tambahan_path'] = $additionalDocumentName ?: 'Berkas Tambahan';
        }
        $overrideAudit = $type === 'koor' ? EoAuditLog::with('user')
            ->where('subject_type', 'PendaftaranKoordinator')->where('subject_id', (string) $id)
            ->where('action', 'OVERRIDE_APPROVAL')->latest('created_at')->first() : null;

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-detail', compact(
            'registration',
            'type',
            'label',
            'documents',
            'overrideAudit',
            'additionalDocumentRequired'
        ));
    }

    public function document(Request $request, string $type, int $id, string $document, SupabaseStorage $storage)
    {
        [$model, , $documents] = $this->definition($request, $type);
        abort_unless(isset($documents[$document]), 404);
        $registration = $model::findOrFail($id);
        $path = $registration->getAttribute($document);
        abort_unless(is_string($path) && $path !== '' && ! preg_match('~(^/|\\\\|://|(?:^|/)\.\.(?:/|$))~', $path), 404);
        $file = $storage->download($path, 'eoffice');
        abort_unless($file, 404, 'Dokumen tidak tersedia.');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($file['content']);
        $inline = in_array($mime, ['application/pdf', 'image/png', 'image/jpeg'], true);
        $extension = match ($mime) {
            'application/pdf' => 'pdf', 'image/png' => 'png', 'image/jpeg' => 'jpg', default => pathinfo($path, PATHINFO_EXTENSION)
        };
        $filename = $type.'-'.$id.'-'.str_replace('_path', '', $document).'.'.preg_replace('/[^a-z0-9]/i', '', $extension);

        return response($file['content'])->withHeaders([
            'Content-Type' => $inline ? $mime : 'application/octet-stream',
            'Content-Disposition' => ($inline ? 'inline' : 'attachment').'; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'self'",
        ]);
    }
}
