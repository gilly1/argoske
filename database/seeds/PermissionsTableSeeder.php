<?php

use App\SystemPermission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $systemPermissions = SystemPermission::defaultPermissions();
        foreach ($systemPermissions as $key => $modulePermissions) {
            foreach ($modulePermissions as $permission_key => $permissionsArray) {
                foreach ($permissionsArray as $permission) {
                    $perm_check = SystemPermission::where('name', $permission)->first();
                    if (!$perm_check) {
                        $new_perm = new SystemPermission();
                        $new_perm->name = $permission;
                        $new_perm->module_id = $key;
                        $new_perm->permission_key = $permission_key;
                        $new_perm->save();
                    }
                }
            }
        }
        $this->command->info('System Permissions added.');
    }
}
