<?php

namespace Database\Seeders;

use App\Models\Manage\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            '超级管理员'
        ];

        foreach ($roles as $index => $role_name) {
            if (!Role::query()->where('name', $role_name)->exists()) {
                $role = new Role();
                $role->id = $index + 1;
                $role->name = $role_name;
                $role->save();
            }
        }
    }
}
