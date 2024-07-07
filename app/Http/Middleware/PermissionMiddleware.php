<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
