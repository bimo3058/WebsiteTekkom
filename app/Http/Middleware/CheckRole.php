<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Gunakan RoleMiddleware ('role:...') sebagai gantinya.
 * CheckRole dipertahankan hanya untuk backward compatibility dengan route
 * yang masih pakai 'check.role:...' — migrasi ke 'role:...' saat ada kesempatan.
 */
class CheckRole extends RoleMiddleware
{
    // Mewarisi semua logika dari RoleMiddleware.
    // Tidak perlu kode tambahan — alias murni.
}