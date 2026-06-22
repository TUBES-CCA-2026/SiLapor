<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Daftarkan di app/Http/Kernel.php (Laravel 10) atau bootstrap/app.php (Laravel 11):
     * 'role' => \App\Http\Middleware\RoleMiddleware::class,
     *
     * Pemakaian di route: ->middleware('role:asisten,koordinator_lab')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Admin selalu lolos, apa pun role yang didaftarkan di route ini —
        // jadi admin otomatis bisa akses SEMUA fitur tanpa perlu ditambahkan
        // satu-satu ke tiap middleware('role:...').
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
