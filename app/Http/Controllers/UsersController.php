<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{

    public function __construct()
    {
        //
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

        $users = User::with(['group', 'user_activity', 'user_details'])
            ->orderByDesc('id')
            ->get();

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

        return response()->json([
            'title' => 'Success.',
            'message' => 'User Details.',
            'data' => $user,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the input
        $validatedData = $request->validate([
            'group_id' => 'required',
            'status' => 'required|in:Active,Inactive,Pending',
            'avatar' => 'nullable|string', // Make avatar nullable
        ]);

        // Log the received avatar data for debugging
        Log::info('Received avatar data: ', ['avatar' => $validatedData['avatar']]);

        // Handle the avatar update
        if (!empty($validatedData['avatar'])) {
            $avatarPath = uploadMediaGetPath($validatedData['avatar']);

            // Log the new avatar path
            Log::info('New avatar path: ', ['avatarPath' => $avatarPath]);

            // Only update the avatar if it's not empty
            if ($avatarPath) {
                $validatedData['avatar'] = $avatarPath;
            } else {
                // Remove the avatar field if saving failed
                unset($validatedData['avatar']);
            }
        } else {
            // If avatar is not provided or is empty, remove it from the update data
            unset($validatedData['avatar']);
        }

        // Update the user with validated data
        $user->update($validatedData);

        return response()->json($user);
    }

    /**
     * Save or update Users details with Role and Permission
     *
     * @param  array  $request
     * @return json array response
     */
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

    public function removeRole($id, $roleId)
    {
        $user = User::findOrFail($id);
        $role = Role::findOrFail($roleId);
        $user->detachRole($role);
        return response()->json(['message' => 'Role removed successfully.']);
    }

}
