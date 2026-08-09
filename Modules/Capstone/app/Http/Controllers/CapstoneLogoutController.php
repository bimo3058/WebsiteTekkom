<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\AuditLog as GlobalAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\AuditLog as CapstoneAuditLog;

class CapstoneLogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->where('name', 'capstone-fe')->delete();
            $user->clearUserCache();

            try {
                GlobalAuditLog::create([
                    'user_id' => $user->id,
                    'module' => 'capstone',
                    'action' => 'LOGOUT',
                    'description' => 'Logout dari CTMS',
                    'created_at' => now(),
                ]);

                CapstoneAuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'LOGOUT',
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'payload' => ['source' => 'capstone-fe'],
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Capstone logout audit gagal dicatat.', [
                    'user_id' => $user->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
