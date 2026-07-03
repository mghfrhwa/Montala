<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pakai di routes/web.php:
 *   Route::middleware(['auth', 'role:KPH'])->group(...)   // khusus Dashboard KPH
 *   Route::middleware(['auth', 'role:KRPH'])->group(...)  // khusus Input Data KRPH/Asper
 *
 * Daftarkan alias 'role' => EnsureRole::class di bootstrap/app.php (Laravel 11)
 * atau app/Http/Kernel.php $middlewareAliases (Laravel 10).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->status_aktif) {
            abort(403, 'Akun tidak aktif atau belum login.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
