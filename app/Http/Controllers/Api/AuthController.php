<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                /** @var User $user */
                $user = Auth::user();

                // Check if user is active
                if ($user->status !== 'Active') {
                    Auth::logout();

                    $userActivity = UserActivity::where('user_id', $user->id)->firstOrFail();

                    $userActivity->failed_logins++;
                    $userActivity->last_failed_login = now();
                    $userActivity->save();

                    return response([
                        'message' => 'Your account status is Pending. Please contact with your system administrator.',
                    ], 401);
                }

                $token = $user->createToken('authToken')->plainTextToken;

                if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
                    return response([
                        'message' => 'Email must be verified.',
                    ], 401);
                }

                // log the last successfull login into user_activities table for corresponding relationship.
                // make relationship for user_activities file -- not done
                $updateData['last_login'] = $updateData['last_online'] = Carbon::now();
                $userActivity = UserActivity::findOrFail($user->id);
                $userActivity->update($updateData);

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
                    'user' => $user,
                ]);

            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response([
                'message' => 'Internal error, please try again later.', //$e->getMessage()
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
