<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Check if user has one of the roles
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}