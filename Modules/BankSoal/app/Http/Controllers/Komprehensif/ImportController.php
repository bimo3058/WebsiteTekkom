<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Exports\KompreNilaiTemplateExport;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\Komprehensif\KompreImportService;

class ImportController extends Controller
{
    public function __construct(private KompreImportService $importService) {}

    public function template(PeriodeUjian $periode)
    {
        $this->authorize('viewAny', KompreSession::class);

        return (new KompreNilaiTemplateExport)->download($periode);
    }

    public function preview(Request $request)
    {
        $this->authorize('viewAny', KompreSession::class);

        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $paths = collect($request->file('files'))
            ->map(fn ($file) => $file->getRealPath())
            ->filter()
            ->values()
            ->all();

        try {
            return response()->json($this->importService->preview($paths, (int) auth()->id()));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function execute(Request $request, string $token)
    {
        $this->authorize('viewAny', KompreSession::class);

        try {
            return response()->json($this->importService->execute($token, (int) auth()->id()));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
