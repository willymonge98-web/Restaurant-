<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  string  ...$roles  Role slugs allowed (admin always allowed via User::canAccess)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Compte inactif ou non authentifié.');
        }

        if ($roles !== [] && ! $user->canAccess(...$roles)) {
            abort(403, 'Accès refusé pour votre rôle.');
        }

        return $next($request);
    }
}
