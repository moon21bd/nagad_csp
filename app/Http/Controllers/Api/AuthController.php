<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AgentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $registrationChannel = "WEB";

    protected $requiresLocationGroups = [3, 4];

    protected $agentHelper;

    public function __construct(AgentHelper $agentHelper)
    {
        $this->agentHelper = $agentHelper;
    }

    /**
     * Register the new user.
     *
     * @param array $request
     * @return json array response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->registrationRules(), $this->phoneValidationErrorMessages());

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $validatedData = $request->all();
            $authUserId = Auth::id();

            $user = $this->createUser($validatedData, $authUserId);
            $this->createUserDetail($validatedData, $user->id);
            $this->createUserActivity($request, $user->id);

        } catch (\Exception $e) {
            Log::error('USER-REGISTRATION-ERROR: ' . $e->getMessage());

            return response()->json([
                'title' => 'Failed to register.',
                'message' => 'User registration failed.',
            ], Response::HTTP_PRECONDITION_FAILED);
        }

        return response()->json([
            'title' => 'Successfully registered.',
            'message' => 'User successfully registered.',
        ], Response::HTTP_OK);
    }

    public function changePassword(Request $request)
    {

        try {

            $validatedData = $request->validate([
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|max:25|confirmed',
                'password_confirmation' => 'required|string|min:8|max:25',
            ]);

            // Get the authenticated user
            $user = auth()->user();

            // Check if the old password is correct
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return response()->json([
                    'message' => 'Old password is incorrect!',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update the user's password
            $user->password = Hash::make($request->input('password'));
            $user->save();

            // Update the user's activity log
            UserActivity::where('user_id', $user->id)->update([
                'last_password_change_time' => Carbon::now(),
                'last_update_date' => Carbon::now(),
            ]);

            // Return a success response
            return response()->json([
                'message' => 'Password updated successfully!',
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Return a detailed validation error response
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            // Return a general error response
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function registrationRules()
    {
        return [
            'group_id' => 'required',
            'employee_name' => 'required',
            'employee_id' => 'required',
            'employee_user_id' => 'required',
            'nid_card_no' => 'required',
            'birth_date' => 'required|date',
            'mobile_no' => $this->phoneValidationRules() . "|unique:users",
            'address' => 'required',
            'gender' => 'required',
            'status' => 'nullable',
            'avatar' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8|max:25',
        ];
    }

    protected function sendValidationError($validator)
    {
        $message = "";
        foreach ($validator->errors()->getMessages() ?? [] as $value) {
            $message .= ' ' . $value[0];
        }

        $error = [
            'title' => 'Validation Error',
            'message' => $message,
        ];

        return $this->sendError($error);
    }

    protected function createUser($data, $authUserId)
    {
        return User::create([
            'name' => $data['employee_name'],
            'parent_id' => $authUserId,
            'group_id' => $data['group_id'],
            'mobile_no' => $data['mobile_no'],
            'email' => $data['email'],
            'avatar' => uploadMediaGetPath($data['avatar']),
            'password' => Hash::make($data['password']),
            'created_by' => $authUserId,
            'updated_by' => $authUserId,
        ]);
    }

    protected function createUserDetail($data, $userId)
    {
        UserDetail::create([
            'user_id' => $userId,
            'employee_id' => $data['employee_id'],
            'employee_name' => $data['employee_name'],
            'employee_user_id' => $data['employee_user_id'],
            'nid_card_no' => $data['nid_card_no'],
            'registered_channel' => $this->registrationChannel,
            'gender' => $data['gender'],
            'birth_date' => date('Y-m-d', strtotime($data['birth_date'])),
            'address' => $data['address'],
            'last_update_date' => Carbon::now(),
        ]);
    }

    protected function createUserActivity($request, $userId)
    {
        UserActivity::create([
            'user_id' => $userId,
            'login_device_name' => $this->agentHelper->getDeviceName(),
            'browser' => $this->agentHelper->getBrowser(),
            'creator_ip' => getIPAddress(),
            'creator_device' => $this->agentHelper->getDeviceName(),
            'last_update_date' => Carbon::now(),
        ]);
    }

    protected function updateUserActivity(User $user)
    {
        $userActivity = UserActivity::where('user_id', $user->id)->first();
        if ($userActivity) {
            $userActivity->update([
                'last_login' => Carbon::now(),
                'browser' => $this->agentHelper->getBrowser(),
                'login_device_name' => $this->agentHelper->getDeviceName(),
                'last_online' => Carbon::now(),
            ]);
        }

    }

    /**
     * Logged-in user's details with roles and permissions
     *
     * @param array $request
     * @return json array response
     */
    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user['roles'] = $user->getAllPermissions();
        Log::info('USER: ' . json_encode($user));

        $permissions = $user['roles']->pluck('name');
        $user['cando'] = $permissions;
        Log::info('PERMISSIONS: ' . json_encode($permissions) . ' USER: ' . json_encode($user));

        return response()->json($user);
    }

    /**
     * Logout
     *
     * @param array $request
     * @return json array response
     */
    public function logout(Request $request)
    {
        $userId = $request->id;
        $userActivity = UserActivity::findOrFail($userId);
        $userActivity->update([
            'last_logout' => Carbon::now(),
            'last_update_date' => Carbon::now(),
        ]);

        if ($user = Auth::user()) {
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            return response()->json('Successfully logged out');
        }

        return response()->json('User not authenticated', Response::HTTP_UNAUTHORIZED);
    }

    /*
     * Login related methods start --->>>
     */

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
                    return response(['message' => 'Your account status is Pending. Please contact your system administrator.'], Response::HTTP_UNAUTHORIZED);
                }

                // Check if the user belongs to a group that requires location prompting
                if (in_array($user->group_id, $this->requiresLocationGroups)) {
                    return response(['message' => 'Location is required.', 'requiresLocation' => true, 'user' => $user]);
                }

                $token = $user->createToken('authToken')->plainTextToken;
                if ($this->mustVerifyEmail($user)) {
                    return response(['message' => 'Email must be verified.'], Response::HTTP_UNAUTHORIZED);
                }

                $this->updateUserActivity($user);

                $permissions = $this->getUserPermissions($user);

                return response(['message' => 'Successfully logged in.', 'token' => $token, 'user' => $user, 'cando' => $permissions]);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response(['message' => 'Internal error, please try again later.'], Response::HTTP_BAD_REQUEST);
        }

        return response(['title' => 'Invalid login details', 'message' => 'Invalid login details'], Response::HTTP_UNAUTHORIZED);
    }

    public function completeLogin(Request $request)
    {
        $userId = $request->input('userId');
        $location = $request->input('location');

        /** @var User $user */
        $user = User::findOrFail($userId);
        $token = $user->createToken('authToken')->plainTextToken;
        if ($this->mustVerifyEmail($user)) {
            return response(['message' => 'Email must be verified.'], Response::HTTP_UNAUTHORIZED);
        }

        $this->updateUserActivity($user);
        $this->updateUserLocation($user, $location);

        $permissions = $this->getUserPermissions($user);

        return response(['message' => 'Successfully logged in.', 'token' => $token, 'user' => $user, 'cando' => $permissions]);
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

    protected function updateUserLocation(User $user, $location)
    {
        $userDetails = UserDetail::where('user_id', $user->id)->first();
        if ($userDetails) {

            $userDetails->update(['lat' => $location['latitude'], 'lon' => $location['longitude']]);
        }
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

    /*
 * Login related methods end <<<---
 */

}
