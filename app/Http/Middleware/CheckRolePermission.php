<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermission
{
    /**
     * Protect a route by a named permission slug.
     *
     * Usage in routes:
     *   ->middleware('permission:releases')
     */
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        // Super Admin bypasses everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasPermission($slug)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
