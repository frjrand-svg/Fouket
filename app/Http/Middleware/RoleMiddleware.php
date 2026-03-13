<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user || !$user->active) {
            return redirect()->route('login')->withErrors([
                'auth' => 'Session expirée, veuillez vous reconnecter.',
            ]);
        }

        if ($roles && $user->role && in_array($user->role->slug, $roles)) {
            return $next($request);
        }

        if (!$roles) {
            return $next($request);
        }

        abort(403, 'Accès refusé');
    }
}
