<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Silber\Bouncer\Bouncer as BouncerInstance;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class RBACService
{
    protected $bouncer;

    public function __construct(BouncerInstance $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /*
     * Roles related methods started
     */

    public function createRole(string $roleName, string $roleTitle)
    {
        return Role::create([
            'name' => $roleName,
            'title' => $roleTitle,
        ]);

    }

    public function createRoleWithAbilities($name, $permissions)
    {
        // Create or find the role
        $role = $this->bouncer->role()->firstOrCreate([
            'name' => $name,
        ], [
            'title' => userCaseWord($name),
        ]);

        $abilityIds = [];
        foreach ($permissions as $permission) {
            $ability = $this->bouncer->ability()->firstOrCreate([
                'name' => $permission,
            ], [
                'title' => userCaseWord($permission),
            ]);

            $abilityIds[] = $ability->id;
        }

        // Attach all abilities to the role
        $this->bouncer->allow($role)->to($abilityIds);

        return $role;
    }

    public function getAbilitiesByRole($roleName)
    {
        // Fetch the role
        $role = Bouncer::role()->where('name', $roleName)->first();

        if (!$role) {
            throw new \Exception("Role not found");
        }
        dd(Bouncer::role($role->id)->abilities());
        // Get abilities for the role
        return Bouncer::role($role->id)->abilities()->get();
    }

    public function assignAbilityToRole(Role $role, string $abilityName)
    {
        $this->bouncer->allow($role)->to($abilityName);
    }

    /*
     * Roles related methods ended
     */

    /*
     * Ability/Permission related methods started
     */
    public function createAbility(string $name)
    {
        $title = userCaseWord($name);
        return Ability::create([
            'name' => $name,
            'title' => $title,
        ]);
    }

    public function updateAbility(string $name)
    {
        $title = userCaseWord($name);
        return Ability::updateOrCreate(
            ['name' => $name],
            ['title' => $title]
        );
    }
    public function getAllAbility()
    {
        return Ability::orderBy('name')->get();

    }

    public function updateAbilityById($id, $newName)
    {
        $ability = Ability::find($id);

        if ($ability) {
            $ability->update([
                'name' => $newName,
                'title' => userCaseWord($newName),
            ]);

            return $ability;
        } else {
            return null;
        }
    }
    public function deleteAbilityById($id)
    {
        $ability = Ability::find($id);

        if ($ability) {
            $ability->delete();

            return $ability;
        } else {
            return null;
        }
    }

    /*
     * Ability/Permission related methods started
     */

    public function tagRoleToTeam(Role $role, Group $group)
    {
        // Assign the role to the team
        $this->bouncer->scope()->to($group->id);
        $this->bouncer->assign($role)->to($group);
        $this->bouncer->scope()->to(null);
    }

    public function assignUserToTeamWithRole(User $user, Group $group, Role $role)
    {
        // Assign the role to the user within the team context
        $this->bouncer->scope()->to($group->id);
        $this->bouncer->assign($role)->to($user);
        $this->bouncer->scope()->to(null);
    }

    public function checkUserAbilityInTeam(User $user, Group $group, string $abilityName)
    {
        // Apply the team scope
        $this->bouncer->scope()->to($group->id);

        // Check if the user has the ability
        $hasAbility = $user->can($abilityName);

        // Reset the scope
        $this->bouncer->scope()->to(null);

        return $hasAbility;
    }

    public function getUserPermissionsInTeam(User $user, Group $group)
    {

        $abilityIds = \DB::table('permissions')
            ->where('entity_id', $group->id)
            ->pluck('ability_id');

        $abilities = \DB::table('abilities')
            ->whereIn('id', $abilityIds)
            ->pluck('name');

        return [
            'permissions' => $abilities,
        ];
    }

}
