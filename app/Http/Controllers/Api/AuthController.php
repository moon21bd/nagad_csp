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

                return response([
                    'title' => 'Successfully loggedIn.',
                    'message' => 'Successfully loggedIn.',
                    'token' => $token,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
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

    public function user()
    {
        Log::info('user method called', Auth::user());
        return response()->json(Auth::user());
    }
}
