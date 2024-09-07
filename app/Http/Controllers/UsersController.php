<?php
namespace App\Http\Controllers;

use App\Helpers\AgentHelper;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\NCTicket;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserMigrationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        /* $group = Group::find(1);
        $user = User::find(12);
        $userIsAble = $user->isAbleTo('user-edit', $group);
        dd($userIsAble);
        $this->assignPermissionToGroup(1);
        dd(""); */

        /* $user = User::find(14);
        $groupId = $user->group_id;
        $hasPermission = $user->hasPermission("user-create", $groupId);
        dd($hasPermission); */
        // $user->hasRole('owner|admin|default_role');
        // logged in user permission check
        // Auth::user()->hasPermission('role-create');

        /*
        // checking user hasPermission
        $user = User::find(14);
        $groupId = $user->group_id;
        $hasPermission = $user->hasPermission("dashboard", $groupId);
        dd($hasPermission); */

        /*
        // check user has permission to the user within group
        $user = User::find(14);
        $groupId = $user->group_id;
        $userIsAbleTo = $user->isAbleTo('dashboard', $groupId);
        dd($userIsAbleTo); */

        /* $users = User::with(['group', 'user_activity', 'user_login_activity', 'user_details'])
        ->orderByDesc('id')
        ->get(); */

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
        $user = User::with(['group', 'user_activity', 'user_details'])->find($id);
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

        // Validate the input
        $validatedData = $request->validate([
            'group_id' => 'required',
            'parent_id' => 'nullable|integer',
            'level' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive,Pending',
            'avatar' => 'nullable|string',
            'user_type' => 'required|string',
            'user_details.gender' => 'required|string',
        ]);

        // Handle avatar upload
        if (!empty($validatedData['avatar'])) {
            $validatedData['avatar'] = uploadMediaGetPath($validatedData['avatar']) ?: null;
        }

        // Capture previous roles and permissions
        $previousPermissions = $user->permissions->pluck('name')->toArray();
        $previousRoles = $user->roles->pluck('name')->toArray();
        $groupPermissions = $user->group ? $user->group->permissions->pluck('name')->toArray() : [];
        $previousAllPermissions = array_unique(array_merge($previousPermissions, $groupPermissions));

        // Check for changes in group, parent, or level
        $isUserMigrated = $user->group_id != $validatedData['group_id'];
        $isParentChanged = $user->parent_id != $validatedData['parent_id'];
        $isLevelChanged = $user->level != $validatedData['level'];

        if ($isUserMigrated || $isParentChanged || $isLevelChanged) {
            // Handle group migration if needed
            if ($isUserMigrated) {
                $user->permissions()->detach();
                $newGroupPermissions = Group::find($validatedData['group_id'])->permissions->pluck('id')->toArray();
                $user->permissions()->attach($newGroupPermissions);
            }

            // Log migration changes
            $totalTicket = NCTicket::where('ticket_created_by', $id)->count();

            $this->createUserMigrationLogs([
                'user_id' => $userId,
                'previous_group_id' => $user->group_id,
                'current_group_id' => $validatedData['group_id'],
                'total_ticket_created_till' => $totalTicket,
                'previous_roles_permissions' => json_encode([
                    'permissions' => $previousAllPermissions,
                    'roles' => $previousRoles,
                ]),
                'previous_level' => $user->level,
                'previous_parent_id' => $user->parent_id,
                'current_level' => $validatedData['level'],
                'current_parent_id' => $validatedData['parent_id'],
                'updator_group_id' => Auth::user()->group_id,
                'updated_by' => Auth::id(),
                'updator_ip' => getIPAddress(),
                'updator_device_name' => $this->agentHelper->getDeviceName(),
            ]);
        }

        // Update the user with validated data
        $user->update($validatedData);

        // Update user details
        UserDetail::where('user_id', $user->id)->update([
            'gender' => $validatedData['user_details']['gender'],
        ]);

        return response()->json([
            'message' => 'User data updated.',
            'type' => 'success',
        ], 200);
    }

    /* public function edit(Request $request, $id)
    {
    $user = User::findOrFail($id);
    $userId = $user->id;
    // dd($request->all());
    // Validate the input
    $validatedData = $request->validate([
    'group_id' => 'required',
    'parent_id' => 'nullable|integer',
    'level' => 'nullable|integer',
    'status' => 'required|in:Active,Inactive,Pending',
    'avatar' => 'nullable|string',
    'user_details.gender' => 'required|string',
    ]);
    // dd($validatedData);
    if (!empty($validatedData['avatar'])) {
    $avatarPath = uploadMediaGetPath($validatedData['avatar']);
    if ($avatarPath) {
    $validatedData['avatar'] = $avatarPath;
    } else {
    unset($validatedData['avatar']);
    }
    } else {
    unset($validatedData['avatar']);
    }

    // Capture previous roles and permissions
    $previousPermissions = $user->permissions->pluck('name')->toArray();
    $previousRoles = $user->roles->pluck('name')->toArray();

    $groupPermissions = $user->group ? $user->group->permissions->pluck('name')->toArray() : [];
    $previousAllPermissions = array_unique(array_merge($previousPermissions, $groupPermissions));

    // Determine if there are changes
    $isUserMigrated = $user->group_id != $validatedData['group_id'];
    $isParentChanged = $user->parent_id != $validatedData['parent_id'];
    $isLevelChanged = $user->level != $validatedData['level'];

    if ($isUserMigrated || $isParentChanged || $isLevelChanged) {
    // Handle group migration
    if ($isUserMigrated) {
    $user->permissions()->detach(); // Detach current permissions

    $newGroupPermissions = Group::find($validatedData['group_id'])->permissions->pluck('id')->toArray();
    $user->permissions()->attach($newGroupPermissions);
    }

    // Save migrated user logs
    $totalTicket = NCTicket::where('ticket_created_by', $id)->count();

    $data = [
    'user_id' => $userId,
    'previous_group_id' => $user->group_id,
    'current_group_id' => $validatedData['group_id'],
    'total_ticket_created_till' => $totalTicket,
    'previous_roles_permissions' => json_encode([
    'permissions' => $previousAllPermissions,
    'roles' => $previousRoles,
    ]),
    'previous_level' => $user->level,
    'previous_parent_id' => $user->parent_id,
    'current_level' => $validatedData['level'],
    'current_parent_id' => $validatedData['parent_id'],
    'updator_group_id' => Auth::user()->group_id,
    'updated_by' => Auth::id(),
    'updator_ip' => getIPAddress(),
    'updator_device_name' => $this->agentHelper->getDeviceName(),
    ];

    $this->createUserMigrationLogs($data);
    }

    // Update the user with validated data
    // $validatedData['gender'] = $user->user_details->gender;
    $user->update($validatedData);

    $userDetails = UserDetail::where('user_id', $user->id)->first();
    // dd('user-detail', $userDetails->toArray(), $validatedData['user_details']['gender']);
    if ($userDetails) {
    $userDetails->update(['gender' => $validatedData['user_details']['gender']]);
    }

    return response()->json([
    'message' => 'User data updated.',
    'type' => 'success',
    ], 200);
    } */

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
                'password' => 'required|min:8',
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
        $user = User::find($id);
        $user->syncRoles([]);
        $user->syncPermissions([]);

        $user->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'User Deleted.',
            'data' => '',
        ], 200);
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

                    $location = getLocationName($log->latitude, $log->longitude);

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
                'empId' => $user->user_details->employee_id ?? "",
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

}
