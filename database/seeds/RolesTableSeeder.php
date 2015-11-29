<?php

use Illuminate\Database\Seeder;

use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Admin', 'code' => 'admin'],
            ['name' => 'User', 'code' => 'user', 'is_default' => true],
        ];
        
        array_map(function ($role) {
            (new Role)->forceFill($role)->save();
        }, $roles);
    }
}
