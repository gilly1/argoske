<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\Config::get('constants.roles') as $key => $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web', 'deleteable' => in_array($role, [config('constants.roles.super_admin')]) ? 0 : 1]);
        }
    }
}
