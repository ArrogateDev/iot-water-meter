<?php

namespace Database\Seeders;

use App\Models\Manage\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!AdminRole::query()->where('admin_id', 1)->where('role_id', 1)->exists()) {
            $admin_role = new AdminRole();
            $admin_role->admin_id = 1;
            $admin_role->role_id = 1;
            $admin_role->save();
        }
    }
}
