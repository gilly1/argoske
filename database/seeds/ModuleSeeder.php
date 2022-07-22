<?php

use App\Modules;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = \Config::get('constants.modules');
        foreach ($modules as $module) {
            $module_check = Modules::where('name', $module)->first();
            if (!$module_check) {
                Modules::create(
                    [
                        'name' => $module['name'],
                        'slug' => $module['slug']
                    ]
                );
            }
        }
    }
}
