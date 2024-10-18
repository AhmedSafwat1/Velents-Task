<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Guard for role and permission
     */
    protected string $guard = 'api';

    /**
     * Permissions
     */
    protected array $permissions = [
        'orders' => [
            'show_order',
            'edit_order',
            'add_order',
            'delete_order',
            'change_status',
        ],
    ];

    /**
     * Roles
     */
    protected array $roles = [
        'admin',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = $this->createRoles();
        $permissionsId = [];
        foreach ($this->permissions as $category => $permissions) {
            foreach ($permissions as $permission) {
                $permission = Permission::updateOrCreate(
                    ['guard_name' => $this->guard, 'name' => $permission],
                    ['category' => $category]
                );
                $permissionsId[] = $permission->id;
            }
        }

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permissionsId);
        }
    }

    /**
     * Create Roles
     */
    public function createRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = Role::updateOrCreate(['name' => $role, 'guard_name' => $this->guard]);
        }

        return $roles;
    }
}
