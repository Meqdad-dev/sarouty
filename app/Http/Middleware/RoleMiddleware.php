<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de vérification des rôles utilisateur.
 * Usage : ->middleware('role:admin') ou ->middleware('role:admin,agent')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        if (!empty($roles) && !$user->hasRole($roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
