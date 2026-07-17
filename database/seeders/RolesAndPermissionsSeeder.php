<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create standard permissions
        $permissions = [
            'manage-users',
            'manage-settings',
            'manage-templates',
            'manage-categories',
            'view-analytics',
            'use-ai-tools',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign created permissions
        $adminRole = Role::findOrCreate('Super Admin');
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::findOrCreate('User');
        $userRole->givePermissionTo(['use-ai-tools']);

        // Create default Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@quillnova.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);
        UserProfile::firstOrCreate([
            'user_id' => $admin->id,
            'company' => 'QuillNova Ltd.',
            'timezone' => 'UTC',
            'language' => 'en',
        ]);

        // Create default standard User
        $user = User::firstOrCreate(
            ['email' => 'user@quillnova.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('user123'),
                'status' => 'active',

                'email_verified_at' => now(),
            ]
        );
        $user->assignRole($userRole);
        UserProfile::firstOrCreate([
            'user_id' => $user->id,
            'company' => 'Freelancer',
            'timezone' => 'UTC',
            'language' => 'en',
        ]);
    }
}
