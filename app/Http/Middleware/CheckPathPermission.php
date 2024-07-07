<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPathPermission
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $path = $request->path();

        if ($user && $user->hasPermissionForPath($path)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
