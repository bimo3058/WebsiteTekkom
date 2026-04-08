<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Middleware ini tidak lagi diperlukan.
 *
 * Sebelumnya middleware ini memanggil loadMissing('roles') setiap request
 * untuk memastikan relasi roles tersedia di view/controller. Setelah refactor:
 *
 * - Roles di-cache saat login via AuthenticatedSessionController
 * - getCachedRoles() di User.php menangani lazy load dari cache secara otomatis
 *   tanpa query DB kalau cache sudah ada
 *
 * Middleware ini dibiarkan sebagai pass-through untuk menghindari error
 * kalau masih terdaftar di Kernel.php. Hapus registrasinya dari Kernel
 * saat ada kesempatan.
 */
class LoadUserRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}