<?php

namespace Database\Seeders;

use App\Models\Manage\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Admin::query()->where('id', 1)->exists()) {
            $admin = new Admin();
            $admin->id = 1;
            $admin->name = env('APP_NAME');
            $admin->account = 'admin';
            $admin->password = md5(md5(111111));
            $admin->save();
        }
    }
}
