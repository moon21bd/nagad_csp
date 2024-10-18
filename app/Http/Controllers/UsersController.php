<?php
namespace App\Http\Controllers;

use App\Helpers\AgentHelper;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\NCTicket;
use App\Models\PasswordHistory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserDetail;
use App\Models\UserMigrationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laratrust\Models\LaratrustPermission;

class UsersController extends Controller
{
    protected $requiresLocationGroups;

    protected $userLevels;

    protected $agentHelper;

    public function __construct(AgentHelper $agentHelper)
    {
        $this->requiresLocationGroups = config('nagad.requires_location_groups');
        $this->userLevels = [
            ['value' => config('nagad.SUPER_ADMIN'), 'label' => 'SUPER_ADMIN'],
            ['value' => config('nagad.ADMIN'), 'label' => 'ADMIN'],
            ['value' => config('nagad.GROUP_OWNER'), 'label' => 'GROUP_OWNER'],
            ['value' => config('nagad.USER'), 'label' => 'USER'],
        ];
        $this->agentHelper = $agentHelper;
    }

    /**
     * Users List w/o pagination.
     *
     * @param  array  $request
     * @return json array response
     */
    public function index()
    {

        $users = User::with(['group', 'user_activity', 'user_login_activity', 'user_details'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($user) {
                $user->level_label = $this->getLevelLabel($user->level);
                return $user;
            });

        return response()->json([
            'title' => 'Success.',
            'message' => 'Users List.',
            'data' => $users,
        ], 200);

    }

    /**
     * Users List w/o pagination.
     *
     * @param  array  $request
     * @return json array response
     */
    public function getParentUsers()
    {

        $users = User::where('parent_id', 0)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Users List.',
            'data' => $users,
        ], 200);

    }

    public function userIndex()
    {
        $currentUser = auth()->user();

        $query = User::with(['group', 'latestLoginActivity', 'user_details']);

        if ($currentUser->hasRole('superadmin')) {
            // Super Admin can see all users, no need for additional filters
        } elseif ($currentUser->hasRole('admin')) {
            // Admin should not see Super Admin users
            $query->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'superadmin');
            });
        } elseif ($currentUser->hasRole('owner')) {
            // Owner can only see users based on their parent_id and group_id
            $query->where('parent_id', $currentUser->id)
                ->orWhere('group_id', $currentUser->group_id);
        } elseif ($currentUser->hasRole('user')) {
            // Regular User can only see their own data
            $query->where('id', $currentUser->id);
        }

        // Get the users and apply additional processing
        $users = $query->orderByDesc('id')
            ->get()
            ->map(function ($user) {
                $user->level_label = $this->getLevelLabel($user->level);
                return $user;
            });

        return response()->json([
            'title' => 'Success.',
            'message' => 'Users List.',
            'data' => $users,
        ], 200);
    }
    public function assignPermissionToGroup($groupId)
    {
        $team = Group::find($groupId);
        $users = $team->users;

        $allPermissions = Permission::all();
        foreach ($users as $key => $user) {
            $user->attachPermissions($allPermissions, $team);
        }

    }

    /**
     * Users List with pagination.
     *
     * @param  array  $request
     * @return json array response
     */
    public function users(Request $request)
    {
        if ($request->input('page')) {
            $users = User::paginate(5);
            return response()->json($users);
        } else {
            $users = User::all();
            return response()->json([
                'title' => 'Success.',
                'message' => 'Users List.',
                'data' => $users,
            ], 200);
        }
    }

    /**
     * Users details with roles and Permissions by ID
     *
     * @param  Int  $id
     * @return json array response
     */
    public function getUserById($id)
    {
        $user = User::with(['group', 'user_activity', 'user_details', 'roles.permissions'])->find($id);
        $user->allPermissions();

        $user->levels = $this->getUserLevels();

        // $userData = $user->only(['id', 'uuid', 'group_id', 'level', 'parent_id', 'mobile_no', 'name', 'avatar', 'email', 'status']);

        return response()->json([
            'title' => 'Success.',
            'message' => 'User Details.',
            'data' => $user,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userId = $user->id;
        $phoneRules = explode('|', $this->phoneValidationRules());

        $validator = Validator::make($request->all(), [
            'group_id' => 'sometimes|required',
            'parent_id' => 'sometimes|required|integer|exists:users,id',
            'level' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:Active,Inactive,Pending',
            'avatar' => 'sometimes|required|string',
            'user_type' => 'sometimes|required|string',
            'mobile_no' => array_merge(["sometimes"], $phoneRules, [
                Rule::unique('users')->ignore($userId),
            ]),
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:64',
                Rule::unique('users')->ignore($userId),
            ],
            'employee_user_id' => [
                'sometimes',
                'required',
                'string',
                'max:64',
                Rule::unique('users')->ignore($userId),
            ],
            'employee_id' => 'sometimes|required|string',
            'gender' => 'sometimes|required|string',
            'employee_name' => 'sometimes|required|string',
            'nid_card_no' => 'sometimes|nullable|string',
            'birth_date' => 'sometimes|required|date',
            'address' => 'sometimes|required|string',
        ], $this->phoneValidationErrorMessages());

        if ($validator->fails()) {
            return sendValidationErrorResponse($validator);
        }

        $validatedData = $validator->validated();

        $validatedUserDetails = [
            'user_details' => [
                'employee_id' => $validatedData['employee_id'] ?? null,
                'employee_user_id' => $validatedData['employee_user_id'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'employee_name' => $validatedData['employee_name'] ?? null,
                'nid_card_no' => $validatedData['nid_card_no'] ?? null,
                'birth_date' => isset($validatedData['birth_date']) ? date('Y-m-d', strtotime($validatedData['birth_date'])) : null,
                'address' => $validatedData['address'] ?? null,
            ],
        ];

        // Handle avatar upload
        if (!empty($validatedData['avatar'])) {
            $validatedData['avatar'] = uploadMediaGetPath($validatedData['avatar']) ?: null;
        }

        if (isset($validatedData['level']) && $validatedData['level'] === config('nagad.USER') && $validatedData['parent_id'] === null) {
            return $this->sendError([
                'title' => 'Failed to register.',
                'message' => 'Please choose a valid parent_id. A user level with parent_id 0 is not acceptable.',
            ]);

        }

        if (isset($validatedData['level']) && $validatedData['level'] == config('nagad.GROUP_OWNER')) {
            $existingOwner = User::where('group_id', $validatedData['group_id'])
                ->where('level', config('nagad.GROUP_OWNER'))
                ->first();

            if ($existingOwner) {
                return $this->sendError([
                    'title' => 'Failed to register.',
                    'message' => 'Group already has an owner. Try Different owner.',
                ]);
            }
        }

        // Check for changes in group, parent, or level

        // dd($validatedData['group_id'], (isset($validatedData['group_id']) && $user->group_id != $validatedData['group_id']), (isset($validatedData['parent_id']) && $user->parent_id != $validatedData['parent_id']), (isset($validatedData['level']) && $user->level != $validatedData['level']));
        if ($isUserMigrated = (isset($validatedData['group_id']) && $user->group_id != $validatedData['group_id'])
            || (isset($validatedData['parent_id']) && $user->parent_id != $validatedData['parent_id'])
            || (isset($validatedData['level']) && $user->level != $validatedData['level'])) {

            // $isUserMigrated = $user->group_id != $validatedData['group_id'];
            // $isParentChanged = $user->parent_id != $validatedData['parent_id'];
            // $isLevelChanged = $user->level != $validatedData['level'];

            // Capture previous roles and permissions
            $previousPermissions = $user->permissions->pluck('name')->toArray();
            $previousRoles = $user->roles->pluck('name')->toArray();
            $groupPermissions = $user->group ? $user->group->permissions->pluck('name')->toArray() : [];
            $previousAllPermissions = array_unique(array_merge($previousPermissions, $groupPermissions));

            // Handle group migration if needed
            if ($isUserMigrated) {
                if (!empty($validatedData['group_id'])) {
                    $user->permissions()->detach();
                    $newGroupPermissions = Group::find($validatedData['group_id'])->permissions->pluck('id')->toArray();
                    $user->permissions()->attach($newGroupPermissions);
                }
            }

            // Log migration changes
            $totalTicket = NCTicket::where('ticket_created_by', $userId)->count();

            $this->createUserMigrationLogs([
                'user_id' => $userId,
                'previous_group_id' => $user->group_id,
                'current_group_id' => $validatedData['group_id'] ?? $user->group_id,
                'total_ticket_created_till' => $totalTicket,
                'previous_roles_permissions' => json_encode([
                    'permissions' => $previousAllPermissions,
                    'roles' => $previousRoles,
                ]),
                'previous_level' => $user->level,
                'previous_parent_id' => $user->parent_id,
                'current_level' => $validatedData['level'] ?? $user->level,
                'current_parent_id' => $validatedData['parent_id'] ?? $user->parent_id,
                'updator_group_id' => Auth::user()->group_id,
                'updated_by' => Auth::id(),
                'updator_ip' => getIPAddress(),
                'updator_device_name' => $this->agentHelper->getDeviceName(),
            ]);
        }

        // Update user with only changed data
        $userData = [];
        foreach ($validatedData as $key => $value) {
            if ($user->{$key} !== $value) {
                $userData[$key] = $value;
            }
        }

        $userData = array_filter($userData, function ($value) {
            return !is_null($value);
        });

        if (!empty($userData)) {
            $user->update($userData);
        }

        $userDetailsData = [];
        foreach ($validatedUserDetails['user_details'] ?? [] as $key => $value) {
            if ($user->user_details->{$key} !== $value) {
                $userDetailsData[$key] = $value;
            }
        }

        $userDetailsData = array_filter($userDetailsData, function ($value) {
            return !is_null($value);
        });

        if (!empty($userDetailsData)) {
            UserDetail::where('user_id', $user->id)->update($userDetailsData);
        }

        return response()->json([
            'message' => 'User data updated.',
            'type' => 'success',
        ], 200);
    }

    public function resetPassword(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8', // Minimum length
                    'regex:/[a-z]/', // At least one lowercase letter
                    'regex:/[A-Z]/', // At least one uppercase letter
                    'regex:/[0-9]/', // At least one digit
                    'regex:/[@$!%*?&#]/', // At least one special character
                    'confirmed', // Match with password_confirmation
                ],
                'password_confirmation' => 'required|string|min:8|max:25',
            ]);

            // Get the user by ID
            $user = User::find($id);
            if ($user) {
                $now = Carbon::now();

                // Check for password reuse
                $oldPasswords = PasswordHistory::where('user_id', $user->id)->get();
                $newHashedPassword = Hash::make($request->input('password'));

                foreach ($oldPasswords as $oldPassword) {
                    // Check if the new hashed password matches any old hashed passwords
                    if (Hash::check($request->input('password'), $oldPassword->password)) {
                        return response()->json([
                            'message' => 'You cannot reuse your old password.',
                        ], Response::HTTP_PRECONDITION_FAILED);
                    }
                }

                // Save the old password to the password history table
                PasswordHistory::create([
                    'user_id' => $user->id,
                    'password' => $newHashedPassword, // Store current password (hashed)
                ]);

                // Update the user's password
                $user->password = $newHashedPassword;
                $user->password_changed_at = $now;
                $user->save();

                // Update the user's activity log
                UserActivity::where('user_id', $user->id)->update([
                    'last_password_change_time' => $now,
                    'last_update_date' => $now,
                    'updated_at' => $now,
                    'updator_device' => $this->agentHelper->getDeviceName(),
                ]);

                // Return a success response
                return response()->json([
                    'message' => 'Password updated successfully!',
                ], Response::HTTP_OK);
            }

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

    public function store(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $post_data = $request->validate([
                'name' => 'required|string',
            ]);

            $user = User::find($id);
            $user->name = $post_data['name'];
            $user->save();

            $user->syncRoles($request->input('currentroles'));
            $user->syncPermissions($request->input('currentpermissions'));
        } else {
            $post_data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                // 'password' => 'required|min:8',
                'password' => [
                    'required',
                    'string',
                    'min:8', // Minimum length
                    'regex:/[a-z]/', // At least one lowercase letter
                    'regex:/[A-Z]/', // At least one uppercase letter
                    'regex:/[0-9]/', // At least one digit
                    'regex:/[@$!%*?&#]/', // At least one special character
                    // 'confirmed', // Match with password_confirmation
                ],
            ]);

            $user = User::create([
                'name' => $post_data['name'],
                'email' => $post_data['email'],
                'password' => Hash::make($post_data['password']),
            ]);

            $user->assignRole($request->input('currentroles'));
            $user->givePermissionTo($request->input('currentpermissions'));
        }
        return response()->json([
            'title' => 'Success.',
            'message' => 'please update code in getUserById in userscontroller.',
            'data' => $user,
        ], 200);
    }

    /**
     * Delete user
     *
     * @param  Int  $id
     * @return json array response
     */
    public function destroy($id)
    {
        // $user = User::find($id);
        $user = User::withTrashed()->find($id); // Include soft-deleted users

        $user->syncRoles([]);
        $user->syncPermissions([]);

        $user->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'User Deleted.',
            'data' => '',
        ], 200);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); // Restores the user

        return response()->json(['message' => 'User restored successfully.']);
    }

    public function destroyPermanently($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete(); // Permanently deletes the user

        return response()->json(['message' => 'User permanently deleted.']);
    }

    public function assignRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = $request->input('roles', []);
        $user->syncRoles($roles);

        return response()->json(['message' => 'Roles assigned successfully.']);
    }

    public function getUserRoles($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->roles);
    }

    public function getUserLevels()
    {
        return $this->userLevels;
    }

    public function removeRole($id, $roleId)
    {
        $user = User::findOrFail($id);
        $role = Role::findOrFail($roleId);
        $user->detachRole($role);
        return response()->json(['message' => 'Role removed successfully.']);
    }

    public function assignPermission(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'type' => 'error',
            ], 404);
        }

        // Get the permission IDs based on the provided permission names
        $permissionIds = LaratrustPermission::whereIn('name', $request->permissions)
            ->pluck('id')
            ->toArray();

        // Log permission IDs for debugging
        \Log::info('Permission IDs:', $permissionIds);

        // Sync permissions
        $user->syncPermissions($permissionIds);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'type' => 'success',
            'permissions' => $user->permissions->pluck('name'), // Return current permissions
        ]);
    }

    public function getUserLocation()
    {
        $users = User::with(['group', 'user_login_activity', 'user_details'])
            ->whereIn('group_id', $this->requiresLocationGroups)
            ->orderByDesc('created_at')
            ->get();

        $userData = [];
        foreach ($users as $user) {
            // Group logs by date
            $logsGroupedByDate = $user->user_login_activity
                ->groupBy(function ($log) {
                    return Carbon::parse($log->last_login)->format('Y-m-d');
                });

            // Sort dates in descending order
            $logsGroupedByDate = $logsGroupedByDate->sortByDesc(function ($logs, $date) {
                return Carbon::parse($date);
            });

            $userLogs = [];

            foreach ($logsGroupedByDate as $date => $logs) {
                // Sort logs by login time within each date
                $logs = $logs->sortByDesc(function ($log) {
                    return Carbon::parse($log->last_login);
                });
                foreach ($logs as $log) {
                    $loginTime = $log->last_login ? formatTime($log->last_login) : ['formattedTime' => 'N/A', 'suffix' => ''];
                    $logoutTime = $log->last_logout ? formatTime($log->last_logout) : ['formattedTime' => 'N/A', 'suffix' => ''];

                    $lat = $log->latitude ?? 0;
                    $lon = $log->longitude ?? 0;
                    Log::info('Fetching location for lat: ' . $lat . ', lon: ' . $lon);

                    $location = getLocationName($lat, $lon);

                    $userLogs[] = [
                        'cdate' => $date,
                        'date' => Carbon::parse($log->last_login)->format('l, M d, Y'),
                        'loginTime' => $loginTime['formattedTime'],
                        'loginSuffix' => $loginTime['suffix'],
                        'logoutTime' => $logoutTime['formattedTime'],
                        'logoutSuffix' => $logoutTime['suffix'],
                        'device_name' => $log->login_device_name,
                        'device_icon' => getDeviceIcon($log->login_device_name),
                        'location' => $location['location'],
                        'latitude' => $log->latitude,
                        'longitude' => $log->longitude,
                        'cityCountry' => $location['city_country'],
                    ];
                }
            }

            $userData[] = [
                'name' => $user->name,
                'avatar' => $user->avatar_url,
                'empId' => $user->employee_user_id ?? "",
                'position' => $user->group->name ?? "",
                'userLogs' => $userLogs,
            ];
        }

        return response()->json([
            'title' => 'Success.',
            'message' => 'Users List.',
            'data' => $userData,
        ], 200);
    }

    public function getUserList()
    {
        $users = User::with('group')
            ->whereIn('group_id', $this->requiresLocationGroups)
            ->orderByDesc('created_at')
            ->get();

        $userData = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'avatar' => $user->avatar_url,
                'empId' => $user->employee_user_id ?? "",
                'userId' => $user->id ?? null,
                'position' => $user->group->name ?? "",
            ];
        });

        return response()->json([
            'title' => 'Success.',
            'message' => 'User List.',
            'data' => $userData,
        ], 200);
    }

    public function getUserLocationLogs($userId)
    {

        $startDate = request('start_date');
        $endDate = request('end_date');

        if (!$startDate && !$endDate) {

            $defaultDays = 30;
            $startDate = Carbon::now()->subDays($defaultDays)->startOfDay(); // 7 days ago
            $endDate = Carbon::now()->endOfDay(); // Today
        } elseif ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $user = User::with(['user_login_activity'])
            ->where('id', $userId)
            ->firstOrFail();

        $logs = $user->user_login_activity;

        // Apply date range filter
        if ($startDate && $endDate) {
            $logs = $logs->whereBetween('last_login', [$startDate, $endDate]);
        } elseif ($startDate) {
            $logs = $logs->where('last_login', '>=', $startDate);
        } elseif ($endDate) {
            $logs = $logs->where('last_login', '<=', $endDate);
        }

        // Group logs by date
        $logsGroupedByDate = $logs
            ->groupBy(function ($log) {
                return Carbon::parse($log->last_login)->format('Y-m-d');
            })
            ->sortByDesc(function ($logs, $date) {
                return Carbon::parse($date);
            });

        $userLogs = [];
        foreach ($logsGroupedByDate as $date => $logs) {
            foreach ($logs as $log) {
                $loginTime = $log->last_login ? formatTime($log->last_login) : ['formattedTime' => 'N/A', 'suffix' => ''];
                $logoutTime = $log->last_logout ? formatTime($log->last_logout) : ['formattedTime' => 'N/A', 'suffix' => ''];
                $location = getLocationName($log->latitude ?? 0, $log->longitude ?? 0);

                $userLogs[] = [
                    'cdate' => $date,
                    'date' => Carbon::parse($log->last_login)->format('l, M d, Y'),
                    'loginTime' => $loginTime['formattedTime'],
                    'loginSuffix' => $loginTime['suffix'],
                    'logoutTime' => $logoutTime['formattedTime'],
                    'logoutSuffix' => $logoutTime['suffix'],
                    'device_name' => $log->login_device_name,
                    'device_icon' => getDeviceIcon($log->login_device_name),
                    'location' => $location['location'],
                    'latitude' => $log->latitude,
                    'longitude' => $log->longitude,
                    'cityCountry' => $location['city_country'],
                ];
            }
        }

        // Check if userLogs array is empty
        if (empty($userLogs)) {
            return response()->json([
                'title' => 'No Data',
                'message' => 'No data available for the selected date range.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'title' => 'Success.',
            'message' => 'User Logs.',
            'data' => $userLogs,
        ], 200);
    }

    public function getActiveUsers()
    {
        $currentUserId = auth()->id();
        $users = User::where('status', 'Active')
            ->where('id', '<>', $currentUserId)
            ->orderByDesc('id')
            ->get();

        return response()->json($users);
    }

    public function createUserMigrationLogs($data)
    {
        return UserMigrationLog::create($data);
    }
    private function getLevelLabel($level)
    {
        $level = array_search($level, array_column($this->userLevels, 'value'));
        return $level !== false ? $this->userLevels[$level]['label'] : 'UNKNOWN';
    }

    // UserController.php
    public function getUserPermissions($id)
    {
        $user = User::find($id);

        // Ensure user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Retrieve permissions
        $permissions = $user->permissions->pluck('name'); // Adjust according to your actual permissions relationship
        // dd($permissions);
        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    public function getUserPermissionsCount()
    {
        $user = auth()->user();

        // Get the count of direct permissions
        $directPermissionsCount = $user->permissions()->count();

        // Get the count of permissions via roles
        $rolePermissionsCount = $user->roles->map(function ($role) {
            return $role->permissions()->count();
        })->sum();

        // Total permissions count (direct + via roles)
        $totalPermissionsCount = $directPermissionsCount + $rolePermissionsCount;

        return response()->json([
            'user' => $user->name,
            'direct_permissions_count' => $directPermissionsCount,
            'role_permissions_count' => $rolePermissionsCount,
            'total_permissions_count' => $totalPermissionsCount,
        ]);
    }

}
