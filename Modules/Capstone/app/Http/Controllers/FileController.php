<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Capstone\Services\DocumentStorageService;

class FileController extends Controller
{
    public function __construct(private readonly DocumentStorageService $storage)
    {
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = $this->storage->store(
            $validated['file'],
            'uploads/'.$request->user()->id
        );

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => $this->storage->signedUrl($path),
            'message' => 'File uploaded successfully',
        ], 201);
    }

    public function download(Request $request, string $path)
    {
        $this->authorizePath($request, $path);
        $file = $this->storage->get($path);
        abort_unless($file, 404, 'File not found');

        return response($file['content'], 200, [
            'Content-Type' => $file['mime_type'],
            'Content-Disposition' => 'attachment; filename="'.basename($path).'"',
        ]);
    }

    public function show(Request $request, string $path)
    {
        $this->authorizePath($request, $path);
        $file = $this->storage->get($path);
        abort_unless($file, 404, 'File not found');

        return response($file['content'], 200)->header('Content-Type', $file['mime_type']);
    }

    public function list(Request $request)
    {
        $prefix = 'uploads/'.$request->user()->id;
        $basePath = 'capstone/'.$prefix.'/';

        $files = collect($this->storage->list($prefix))
            ->filter(fn (array $file) => isset($file['name'], $file['id']))
            ->map(fn (array $file) => [
                'path' => $basePath.$file['name'],
                'name' => $file['name'],
                'size' => $file['metadata']['size'] ?? null,
                'url' => $this->storage->signedUrl($basePath.$file['name']),
                'last_modified' => $file['updated_at'] ?? $file['created_at'] ?? null,
            ])
            ->values();

        return response()->json($files);
    }

    public function delete(Request $request, string $path)
    {
        $this->authorizePath($request, $path);
        $this->storage->delete($path);

        return response()->json(['message' => 'File deleted']);
    }

    private function authorizePath(Request $request, string $path): void
    {
        $expectedPrefix = 'capstone/uploads/'.$request->user()->id.'/';
        abort_unless(str_starts_with($path, $expectedPrefix), 403, 'Unauthorized');
    }
}
