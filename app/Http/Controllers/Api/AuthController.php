<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $requiresLocationGroups = [3, 4];

    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                /** @var User $user */
                $user = Auth::user();

                // Check if user is active
                if ($user->status !== 'Active') {
                    Auth::logout();
                    $this->incrementFailedLogins($user);
                    return response(['message' => 'Your account status is Pending. Please contact your system administrator.'], 401);
                }

                // Check if the user belongs to a group that requires location prompting
                if (in_array($user->group_id, $this->requiresLocationGroups)) {
                    return response(['message' => 'Location is required.', 'requiresLocation' => true, 'user' => $user]);
                }

                $token = $user->createToken('authToken')->plainTextToken;
                if ($this->mustVerifyEmail($user)) {
                    return response(['message' => 'Email must be verified.'], 401);
                }

                $this->updateUserActivity($user);

                $permissions = $this->getUserPermissions($user);

                return response(['message' => 'Successfully logged in.', 'token' => $token, 'user' => $user, 'cando' => $permissions]);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response(['message' => 'Internal error, please try again later.'], 400);
        }

        return response(['title' => 'Invalid login details', 'message' => 'Invalid login details'], 401);
    }

    public function completeLogin(Request $request)
    {
        $userId = $request->input('userId');
        $location = $request->input('location');

        /** @var User $user */
        $user = User::findOrFail($userId);
        $token = $user->createToken('authToken')->plainTextToken;
        if ($this->mustVerifyEmail($user)) {
            return response(['message' => 'Email must be verified.'], 401);
        }

        $this->updateUserActivity($user);
        $this->updateUserLocation($user, $location);

        $permissions = $this->getUserPermissions($user);

        return response(['message' => 'Successfully logged in.', 'token' => $token, 'user' => $user, 'cando' => $permissions]);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $permissions = $this->getUserPermissions($user);

        Log::info('USER: ' . json_encode($user));
        Log::info('PERMISSIONS: ' . json_encode($permissions));

        return response()->json($user);
    }

    protected function incrementFailedLogins(User $user)
    {
        $userActivity = UserActivity::where('user_id', $user->id)->firstOrFail();
        $userActivity->increment('failed_logins');
        $userActivity->update(['last_failed_login' => now()]);
    }

    protected function mustVerifyEmail(User $user)
    {
        return config('auth.must_verify_email') && !$user->hasVerifiedEmail();
    }

    protected function updateUserActivity(User $user)
    {
        $userActivity = UserActivity::findOrFail($user->id);
        $userActivity->update(['last_login' => Carbon::now(), 'last_online' => Carbon::now()]);
    }

    protected function updateUserLocation(User $user, $location)
    {
        $userDetails = UserDetail::findOrFail($user->id);
        $userDetails->update(['lat' => $location['latitude'], 'lon' => $location['longitude']]);
    }

    protected function getUserPermissions(User $user)
    {
        $user['roles'] = $user->getAllPermissions();
        $permissions = [];
        foreach ($user['roles'] as $roles) {
            $permissions[] = $roles['name'];
        }
        $user['cando'] = $permissions;
        return $permissions;
    }
}

// namespace App\Http\Controllers\Api;

// class AuthController extends Controller
// {
// protected $requiresLocationGroups = [3, 4];
// public function login(LoginRequest $request)
// {
//     try {
//         if (Auth::attempt($request->only('email', 'password'))) {
//             /** @var User $user */
//             $user = Auth::user();

//             // Check if user is active
//             if ($user->status !== 'Active') {
//                 Auth::logout();

//                 $userActivity = UserActivity::where('user_id', $user->id)->firstOrFail();

//                 $userActivity->failed_logins++;
//                 $userActivity->last_failed_login = now();
//                 $userActivity->save();

//                 return response([
//                     'message' => 'Your account status is Pending. Please contact with your system administrator.',
//                 ], 401);
//             }

//             // Check if the user belongs to a specific group that requires location prompting
//             if (in_array($user->group_id, $this->requiresLocationGroups)) {
//                 return response([
//                     'message' => 'Location is required.',
//                     'requiresLocation' => true,
//                     'user' => $user,
//                 ]);
//             }

//             $token = $user->createToken('authToken')->plainTextToken;

//             if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
//                 return response([
//                     'message' => 'Email must be verified.',
//                 ], 401);
//             }

//             // log the last successfull login into user_activities table for corresponding relationship.
//             // make relationship for user_activities file -- not done
//             $updateData['last_login'] = $updateData['last_online'] = Carbon::now();
//             $userActivity = UserActivity::findOrFail($user->id);
//             $userActivity->update($updateData);

//             $user['roles'] = $user->getAllPermissions();
//             Log::info('USER: ' . json_encode($user));
//             $permissions = [];
//             foreach ($user['roles'] as $roles) {
//                 $permissions[] = $roles['name'];
//             }
//             $user['cando'] = $permissions;

//             return response([
//                 'message' => 'Successfully loggedIn.',
//                 'token' => $token,
//                 'user' => $user,
//             ]);

//         }
//     } catch (\Exception $e) {
//         dd($e->getMessage());
//         return response([
//             'message' => 'Internal error, please try again later.', //$e->getMessage()
//         ], 400);
//     }

//     return response([
//         'title' => 'Invalid login details',
//         'message' => 'Invalid login details',
//     ], 401);
// }

// public function completeLogin(Request $request)
// {
//     $userId = $request->input('userId');
//     $location = $request->input('location');

//     /** @var User $user */
//     $user = User::findOrFail($userId);

//     // Proceed with the usual login process
//     $token = $user->createToken('authToken')->plainTextToken;

//     if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
//         return response([
//             'message' => 'Email must be verified.',
//         ], 401);
//     }

//     // Log the last successful login into user_activities table for corresponding relationship
//     $updateData['last_login'] = $updateData['last_online'] = Carbon::now();
//     $userActivity = UserActivity::findOrFail($user->id);
//     $userActivity->update($updateData);

//     // Update the user's location if necessary
//     // $user->location = $location;
//     // $user->save();

//     $userDetailsArr['lat'] = $location['latitude'];
//     $userDetailsArr['lon'] = $location['longitude'];
//     $userDetails = UserDetail::findOrFail($user->id);
//     $userDetails->update($userDetailsArr);

//     $user['roles'] = $user->getAllPermissions();
//     Log::info('USER: ' . json_encode($user));
//     $permissions = [];
//     foreach ($user['roles'] as $roles) {
//         $permissions[] = $roles['name'];
//     }
//     $user['cando'] = $permissions;

//     return response([
//         'message' => 'Successfully logged in.',
//         'token' => $token,
//         'user' => $user,
//     ]);
// }

// public function user(Request $request)
// {
//     $user = User::find(Auth::user()->id);
//     $user['roles'] = $user->getAllPermissions();
//     Log::info('USER: ' . json_encode($user));
//     $permissions = [];
//     foreach ($user['roles'] as $roles) {
//         $permissions[] = $roles['name'];
//     }
//     $user['cando'] = $permissions;
//     Log::info('PERMISSIONS: ' . json_encode($permissions) . ' USER: ' . json_encode($user));

//     return response()->json($user);
// }

// }
