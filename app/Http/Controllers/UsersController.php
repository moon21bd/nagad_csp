<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{

    public function __construct()
    {
        /* $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'store']]);
    $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:user-edit', ['only' => ['edit', 'store']]);
    $this->middleware('permission:user-delete', ['only' => ['destroy']]); */
    }

    /**
     * Users List w/o pagination.
     *
     * @param  array  $request
     * @return json array response
     */
    public function index()
    {

        $users = User::with(['group', 'user_activity', 'user_details'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Users List.',
            'data' => $users,
        ], 200);

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
        //$user['currentroles'] = $user->roles->pluck('id');
        //$user['currentpermissions'] = $user->getDirectPermissions()->pluck('id');

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
        $groupId = $user->group_id;

        foreach ($roles as $roleName) {
            try {
                $this->assignRoleWithGroup($user, $roleName, $groupId);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        }

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
        $user->removeRole($role);
        return response()->json(['message' => 'Role removed successfully.']);
    }

    public function assignRoleWithGroup(User $user, $roleName, $groupId)
    {
        // Retrieve the role
        $role = Role::where('name', $roleName)
            ->first();

        if (!$role) {
            throw new \Exception("Role with name '{$roleName}' and group_id '{$groupId}' not found.");
        }

        // Check if the role is already assigned
        $exists = \DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('role_id', $role->id)
            ->where('group_id', $groupId)
            ->exists();

        if (!$exists) {
            // Assign the role if it does not exist
            \DB::table('model_has_roles')->insert([
                'model_id' => $user->id,
                'model_type' => get_class($user),
                'role_id' => $role->id,
                'group_id' => $groupId,
            ]);
        }
    }
}
