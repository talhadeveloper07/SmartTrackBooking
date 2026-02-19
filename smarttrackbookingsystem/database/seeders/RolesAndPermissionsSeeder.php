<?php

// database/seeders/RolesAndPermissionsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::findOrCreate('org_admin');
        Role::findOrCreate('business_admin');
        Role::findOrCreate('employee');
        Role::findOrCreate('customer');

        // Later you can add permissions + assign them to roles
        // e.g. Permission::findOrCreate('manage businesses');
        // Role::findByName('organization_admin')->givePermissionTo('manage businesses');
    }
}
