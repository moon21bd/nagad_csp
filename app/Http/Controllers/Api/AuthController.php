<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AgentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserDetail;
use App\Models\UserLoginActivity;
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

    protected $requiresLocationGroups;

    protected $agentHelper;

    public function __construct(AgentHelper $agentHelper)
    {
        $this->agentHelper = $agentHelper;
        $this->requiresLocationGroups = config('nagad.requires_location_groups');

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

            if ($validatedData['level'] === config('nagad.USER') && $validatedData['parent_id'] === null) {
                $error = [
                    'title' => 'Failed to register.',
                    'message' => 'Please choose a valid parent_id. A user level with parent_id 0 is not acceptable.',
                ];
                return $this->sendError($error);

            }

            // Prevent duplicate group owners
            if ($validatedData['level'] == config('nagad.GROUP_OWNER')) {
                $existingOwner = User::where('group_id', $validatedData['group_id'])
                    ->where('level', config('nagad.GROUP_OWNER'))
                    ->first();

                if ($existingOwner) {
                    $error = [
                        'title' => 'Failed to register.',
                        'message' => 'Group already has an owner. Try Different owner.',
                    ];
                    return $this->sendError($error);
                }
            }

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
            'level' => 'required',
            'group_id' => 'required_unless:level,1', // Apply validation unless level is 1
            'parent_id' => 'nullable',
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
        // Create the user
        $user = User::create([
            'name' => $data['employee_name'],
            'parent_id' => $data['parent_id'] ?? 0,
            'level' => $data['level'],
            'group_id' => $data['group_id'],
            'mobile_no' => $data['mobile_no'],
            'email' => $data['email'],
            'status' => in_array($data['level'], [1, 2, 3]) ? 'Active' : 'Pending',
            'avatar' => uploadMediaGetPath($data['avatar']),
            'password' => Hash::make($data['password']),
            'created_by' => $authUserId,
            'updated_by' => $authUserId,
        ]);

        // Determine role name based on level
        $roleName = match ($user->level) {
            1 => 'superadmin',
            2 => 'admin',
            3 => 'owner',
            default => 'user',
        };

        // Attach the role to the user
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->attachRole($role);

            // Attach default role permissions
            $user->attachPermissions($role->permissions);
            Log::info('USER-ROLE-PERMISSIONS|' . json_encode($user->toArray()));
        } else {
            // Handle case where role is not found
            throw new \Exception("Role {$roleName} not found.");
        }

        // Attach group permissions
        $group = Group::find($data['group_id']);
        if ($group) {
            $groupPermissions = $group->permissions->pluck('id')->toArray();
            $user->permissions()->sync($groupPermissions);
            Log::info('USER-GROUP-ROLE-PERMISSIONS|' . json_encode($user->toArray()));

        } else {
            // Handle case where group is not found
            throw new \Exception("Group with ID {$data['group_id']} not found.");
        }

        return $user;
    }

    /* protected function createUser($data, $authUserId)
    {
    $user = User::create([
    'name' => $data['employee_name'],
    'parent_id' => $data['parent_id'] ?? 0,
    'level' => $data['level'],
    'group_id' => $data['group_id'],
    'mobile_no' => $data['mobile_no'],
    'email' => $data['email'],
    'status' => in_array($data['level'], [1, 2, 3]) ? 'Active' : 'Pending',
    'avatar' => uploadMediaGetPath($data['avatar']),
    'password' => Hash::make($data['password']),
    'created_by' => $authUserId,
    'updated_by' => $authUserId,
    ]);

    // Role assignment based on level ID
    $roleName = match ($user->level) {
    1 => 'superadmin',
    2 => 'admin',
    3 => 'owner',
    default => 'user',
    };

    $user->attachRole($roleName);

    $role = Role::where('name', $roleName)->first();
    $user->attachPermissions($role->permissions, $data['group_id']);

    return $user;
    } */

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
            'creator_ip' => getIPAddress(),
            'creator_device' => $this->agentHelper->getDeviceName(),
            'last_update_date' => Carbon::now(),
        ]);
    }

    protected function updateUserActivity(User $user)
    {
        return UserLoginActivity::create([
            'user_id' => $user->id,
            'group_id' => $user->group_id,
            'login_device_name' => $this->agentHelper->getDeviceName(),
            'browser' => $this->agentHelper->getBrowser(),
            'ip_address' => getIPAddress(),
            'last_online' => Carbon::now(),
            'last_login' => Carbon::now(),
        ]);

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
        // $user['roles'] = $user->getAllPermissions();
        Log::info('USER: ' . json_encode($user));

        // $permissions = $user['roles']->pluck('name');
        $user['cando'] = [];
        // Log::info('PERMISSIONS: ' . json_encode($permissions) . ' USER: ' . json_encode($user));

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

        $userActivity = UserLoginActivity::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
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

                // $permissions = $this->getUserPermissions($user);
                $cando = $user->allPermissions();
                // $roles = $this->getUserRoles($user);
                // Transforming the user and roles data
                $userData = $user->only(['id', 'uuid', 'group_id', 'level', 'parent_id', 'mobile_no', 'name', 'avatar', 'email', 'status']);
                $userData['roles'] = $user->roles->pluck('name');
                return response([
                    'message' => 'Successfully logged in.',
                    'token' => $token,
                    'user' => $userData,
                    // 'cando' => $cando,
                ]);
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
        // $roles = $this->getUserRoles($user);

        // Transforming the user and roles data
        $userData = $user->only(['id', 'uuid', 'group_id', 'level', 'parent_id', 'mobile_no', 'name', 'avatar', 'email', 'status']);
        $userData['roles'] = $user->roles->pluck('name');

        return response(['message' => 'Successfully logged in.', 'token' => $token, 'user' => $userData]);
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
        $userDetails = UserLoginActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($userDetails) {
            $userDetails->update(['latitude' => $location['latitude'], 'longitude' => $location['longitude']]);
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

    private function getUserRoles($user)
    {
        return $user->roles->map(function ($role) {
            return $role->name;
        });
    }

    /*
 * Login related methods end <<<---
 */

}
