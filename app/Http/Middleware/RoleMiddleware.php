<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the authenticated user has one of the allowed roles.
     * Returns 403 Forbidden if the user's role is not permitted.
     *
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,owner')
     *
     * @param  \Closure(Request): (Response)  $next
     * @param  string  ...$roles  One or more allowed roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
