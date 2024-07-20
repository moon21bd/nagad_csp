<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            if (config('auth.must_verify_email')) {
                event(new Registered($user));
            }

            // $user->assignRole($request->input('roles'));

            return response()->json($user);

            /*return response()->json([
        'token_type' => 'Bearer',
        'title' => 'Successfully registered.',
        'message' => 'Successfully registered, you can do login now.'
        ], 200);*/

        } catch (\Exception $e) {
            return response([
                'message' => 'Internal error, please try again later.', //$e->getMessage()
            ], 400);
        }
    }
}
