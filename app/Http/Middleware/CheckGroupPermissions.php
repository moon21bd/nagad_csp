<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// use Spatie\Permission\PermissionRegistrar;

class CheckGroupPermissions
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Set the current team context
        $teamId = $user->group_id;
        // app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        // if ($user->can($permission)) {
        //     return $next($request);
        // }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
