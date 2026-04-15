<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SupabaseStorage;
use Modules\BankSoal\Models\RpsTemplate;

class TemplateRpsController extends Controller
{
    /**
     * Upload template RPS baru (GPM only)
     */
    public function store(Request $request)
    {
        // Validasi input
        try {
            $validated = $request->validate([
                'dokumen'     => ['required', 'file', 'mimes:docx,doc', 'max:1024'], // Max 10MB
                'keterangan'  => ['nullable', 'string', 'max:500']
            ], [
                'dokumen.mimes'    => 'File harus dalam format Word (.doc atau .docx)',
                'dokumen.max'      => 'Ukuran file maksimal 1MB',
                'dokumen.required' => 'File template harus diunggah',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Template Upload Validation Failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
            ]);

            $errorMessage = implode(' | ', array_merge(...array_values($e->errors())));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();

        try {
            \Log::info('Template Upload Started', ['user_id' => Auth::id()]);
            
            // Ambil file
            $file = $request->file('dokumen');
            $originalFilename = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileMime = $file->getMimeType();
            
            \Log::info('File Info', [
                'filename' => $originalFilename, 
                'size' => $fileSize,
                'mime' => $fileMime,
            ]);

            try {
                $maxVersion = RpsTemplate::max('version') ?? 0;
                $newVersion = $maxVersion + 1;
                
                $fileName = "TemplateRPS_V{$newVersion}";
                
                \Log::info('Uploading to Supabase', ['fileName' => $fileName, 'newVersion' => $newVersion]);
                
                $supabaseStorage = new SupabaseStorage();
                $pathDokumen = $supabaseStorage->upload($file, 'templates/rps', null, $fileName);

                if (!$pathDokumen) {
                    throw new \Exception('Supabase tidak mengembalikan file path - cek konfigurasi credentials atau permissions folder');
                }
                
                \Log::info('File Uploaded to Supabase', ['path' => $pathDokumen, 'size' => $fileSize]);

            } catch (\Exception $uploadError) {
                $uploadMessage = $uploadError->getMessage();
                
                // Identifikasi jenis error upload
                if (strpos($uploadMessage, 'credentials') !== false) {
                    $specificError = 'Gagal upload: Kredensial Supabase tidak valid atau expired';
                } elseif (strpos($uploadMessage, 'permission') !== false || strpos($uploadMessage, 'Forbidden') !== false) {
                    $specificError = 'Gagal upload: Tidak memiliki izin untuk upload ke folder templates/rps';
                } elseif (strpos($uploadMessage, 'timeout') !== false) {
                    $specificError = 'Gagal upload: Koneksi ke Supabase timeout - coba lagi';
                } else {
                    $specificError = "Gagal upload ke Supabase: {$uploadMessage}";
                }
                
                \Log::error('Supabase Upload Failed', [
                    'user_id' => Auth::id(),
                    'filename' => $originalFilename,
                    'size' => $fileSize,
                    'error' => $uploadMessage,
                ]);
                
                throw new \Exception($specificError);
            }

            // Set semua template sebelumnya menjadi not latest
            try {
                \Log::info('Updating previous templates');
                RpsTemplate::where('is_latest', "true")->update(['is_latest' => "false"]);
            } catch (\Exception $updateError) {
                \Log::error('Failed to update previous templates', [
                    'error' => $updateError->getMessage(),
                ]);
                throw new \Exception("Gagal update versi lama template: {$updateError->getMessage()}");
            }

            // Simpan template baru ke database
            try {
                \Log::info('Creating new template record');
                $template = RpsTemplate::create([
                    'original_filename' => $originalFilename,
                    'filename'          => $fileName,
                    'file_path'         => $pathDokumen,
                    'version'           => $newVersion,
                    'created_by'        => Auth::id(),
                    'is_latest'         => "true",
                    'keterangan'        => $validated['keterangan'] ?? null
                ]);
                
                if (!$template) {
                    throw new \Exception('Template tidak berhasil dibuat di database');
                }

            } catch (\Illuminate\Database\QueryException $dbError) {
                $errorCode = $dbError->errorInfo[0] ?? 'UNKNOWN';
                
                // Identifikasi jenis error database
                if (strpos($dbError->getMessage(), 'Integrity constraint violation') !== false) {
                    if (strpos($dbError->getMessage(), 'Duplicate') !== false) {
                        $specificDbError = 'Gagal simpan: Versi template sudah ada di database';
                    } else {
                        $specificDbError = 'Gagal simpan: Terdapat constraint violation - cek data yang dikirim';
                    }
                } elseif ($errorCode === '22001') {
                    $specificDbError = 'Gagal simpan: Panjang data terlalu besar untuk beberapa field';
                } elseif ($errorCode === '23505') {
                    $specificDbError = 'Gagal simpan: Data duplikat di database';
                } elseif ($errorCode === '23503') {
                    $specificDbError = 'Gagal simpan: User yang login tidak valid atau sudah dihapus';
                } else {
                    $specificDbError = "Gagal simpan template ke database (Error: {$errorCode})";
                }
                
                \Log::error('Database Insert Failed', [
                    'user_id' => Auth::id(),
                    'error_code' => $errorCode,
                    'original_error' => $dbError->getMessage(),
                ]);
                
                throw new \Exception($specificDbError);

            } catch (\Exception $createError) {
                \Log::error('Template Creation Error', [
                    'user_id' => Auth::id(),
                    'error' => $createError->getMessage(),
                ]);
                throw $createError;
            }

            DB::commit();
            
            \Log::info('Template Upload Success', ['template_id' => $template->id, 'version' => $newVersion]);

            $successMessage = "Template RPS v{$newVersion} berhasil diupload.";

            // Return JSON jika AJAX request, redirect jika form submission biasa
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'template' => [
                        'id' => $template->id,
                        'version' => $template->version,
                        'original_filename' => $template->original_filename,
                        'created_at' => $template->created_at->format('d M Y H:i'),
                        'created_by' => $template->uploadedBy->name ?? 'Unknown',
                    ]
                ]);
            }

            return redirect()->route('banksoal.rps.gpm.validasi-rps')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Jika file sudah terupload ke Supabase, hapus file tersebut
            if (isset($pathDokumen) && $pathDokumen) {
                try {
                    \Log::info('Rolling back Supabase upload', ['file_path' => $pathDokumen]);
                    $supabaseStorage = new SupabaseStorage();
                    $supabaseStorage->delete($pathDokumen);
                } catch (\Exception $deleteError) {
                    \Log::error('Failed to rollback Supabase upload', [
                        'file_path' => $pathDokumen,
                        'error' => $deleteError->getMessage(),
                    ]);
                }
            }
            
            \Log::error('RPS Template Upload Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = $e->getMessage();

            // Return JSON jika AJAX request, redirect jika form submission biasa
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Delete all inactive templates (GPM only)
     * Menghapus semua versi template yang bukan versi terbaru
     */
    public function deleteInactive(Request $request)
    {
        DB::beginTransaction();

        try {
            // Ambil semua template yang tidak aktif (is_latest != true)
            $inactiveTemplates = RpsTemplate::where('is_latest', '!=', "true")->get();

            if ($inactiveTemplates->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada versi template yang tidak aktif untuk dihapus.',
                    'deleted_count' => 0,
                ]);
            }

            $supabaseStorage = new SupabaseStorage();
            $deletedCount = 0;
            $failedFiles = [];

            // Hapus file dari Supabase terlebih dahulu (sebelum DB deletion)
            foreach ($inactiveTemplates as $template) {
                if ($template->file_path) {
                    try {
                        \Log::info('Deleting file from Supabase', [
                            'template_id' => $template->id,
                            'file_path' => $template->file_path,
                            'version' => $template->version,
                        ]);
                        $supabaseStorage->delete($template->file_path);
                        $deletedCount++;
                    } catch (\Exception $storageError) {
                        $errorMsg = $storageError->getMessage();
                        
                        // Identifikasi jenis error Supabase
                        $specificError = "Template v{$template->version}";
                        
                        if (strpos($errorMsg, 'Not Found') !== false || strpos($errorMsg, '404') !== false) {
                            $specificError .= ' (File tidak ditemukan di Supabase - mungkin sudah dihapus)';
                        } elseif (strpos($errorMsg, 'Unauthorized') !== false || strpos($errorMsg, 'Forbidden') !== false) {
                            $specificError .= ' (Tidak memiliki izin hapus file di Supabase)';
                        } elseif (strpos($errorMsg, 'credentials') !== false) {
                            $specificError .= ' (Kredensial Supabase tidak valid)';
                        } else {
                            $specificError .= " (Error: {$errorMsg})";
                        }
                        
                        $failedFiles[] = $specificError;
                        
                        \Log::error('Failed to delete file from Supabase', [
                            'template_id' => $template->id,
                            'file_path' => $template->file_path,
                            'error' => $errorMsg,
                        ]);
                    }
                }
            }

            // Jika ada file yang gagal dihapus dari storage, jangan lanjut ke DB deletion
            if (!empty($failedFiles)) {
                DB::rollBack();
                
                $errorMessage = 'Gagal menghapus beberapa file dari Supabase: ' . implode(' | ', $failedFiles);
                
                \Log::error('Delete Inactive Templates - Storage Failed', [
                    'user_id' => Auth::id(),
                    'failed_count' => count($failedFiles),
                    'failed_files' => $failedFiles,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            // Setelah semua file berhasil dihapus dari Supabase, hapus dari database
            try {
                RpsTemplate::where('is_latest', '!=', "true")->delete();
            } catch (\Illuminate\Database\QueryException $dbError) {
                DB::rollBack();
                
                $errorCode = $dbError->errorInfo[0] ?? 'UNKNOWN';
                
                if (strpos($dbError->getMessage(), 'Integrity constraint violation') !== false) {
                    $specificDbError = 'Gagal hapus dari database: Ada constraint violation (template mungkin masih direferensi)';
                } else {
                    $specificDbError = "Gagal hapus dari database (Error: {$errorCode})";
                }
                
                \Log::error('Delete Inactive Templates - DB Delete Failed', [
                    'user_id' => Auth::id(),
                    'error_code' => $errorCode,
                    'original_error' => $dbError->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $specificDbError,
                ], 422);
            }

            DB::commit();

            \Log::info('Inactive Templates Deleted Successfully', [
                'user_id' => Auth::id(),
                'deleted_count' => $deletedCount,
            ]);

            $message = "$deletedCount versi template tidak aktif dan filenya berhasil dihapus dari sistem.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Delete Inactive Templates Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error tidak terduga saat menghapus template tidak aktif: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Download template (untuk Dosen - hanya versi terbaru/tertinggi)
     */
    public function download()
    {
        try {
            // Ambil template dengan versi tertinggi (bukan hanya is_latest=true)
            $template = RpsTemplate::orderBy('version', 'desc')->first();

            if (!$template) {
                abort(404, 'Template RPS tidak tersedia');
            }

            // Generate Supabase public URL
            $supabaseStorage = new SupabaseStorage();
            $publicUrl = $supabaseStorage->getPublicUrl($template->file_path);
            
            // Force download
            return redirect($publicUrl);

        } catch (\Exception $e) {
            \Log::error('RPS Template Download Error', [
                'error' => $e->getMessage(),
            ]);
            
            abort(404, 'Template tidak dapat diakses');
        }
    }
}
