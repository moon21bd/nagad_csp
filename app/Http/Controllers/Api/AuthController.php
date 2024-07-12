<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                /** @var User $user */
                $user = Auth::user();
                // $token = $user->createToken('API Token')->accessToken;
                $token = $user->createToken('authToken')->plainTextToken;

                if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
                    return response([
                        'message' => 'Email must be verified.'
                    ], 401);
                }

                $user['roles'] = $user->getAllPermissions();
                Log::info('USER: ' . json_encode($user));
                $permissions = [];
                foreach ($user['roles'] as $roles) {
                    $permissions[] = $roles['name'];
                }
                $user['cando'] = $permissions;

                return response([
                    'message' => 'Successfully loggedIn.',
                    'token' => $token,
                    'user' => $user
                ]);

            }
        } catch (\Exception $e) {
            return response([
                'message' => 'Internal error, please try again later.' //$e->getMessage()
            ], 400);
        }

        return response([
            'title' => 'Invalid login details',
            'message' => 'Invalid login details',
        ], 401);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user['roles'] = $user->getAllPermissions();
        Log::info('USER: ' . json_encode($user));
        $permissions = [];
        foreach ($user['roles'] as $roles) {
            $permissions[] = $roles['name'];
        }
        $user['cando'] = $permissions;
        Log::info('PERMISSIONS: ' . json_encode($permissions) . ' USER: ' . json_encode($user));

        return response()->json($user);
    }

    /*public function user()
    {
        Log::info('user method called', Auth::user());
        return response()->json(Auth::user());
    }*/
}
