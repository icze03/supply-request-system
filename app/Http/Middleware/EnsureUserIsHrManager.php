<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHrManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || (!$user->isHrManager() && !$user->isSuperAdmin())) {
            abort(403, 'Unauthorized access. HR Manager privileges required.');
        }

        return $next($request);
    }
}