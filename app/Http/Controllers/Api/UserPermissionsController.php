<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserPermissionsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Assuming User has many groups and each group has many permissions
        $permissions = $user->groups()
            ->with('permissions') // Ensure 'permissions' is defined in the Group model
            ->get()
            ->pluck('permissions') // Get the 'permissions' collection from each group
            ->flatten() // Flatten the collection to remove nested arrays
            ->unique('id'); // Ensure unique permissions by 'id'
        return response()->json($permissions);
    }
}
