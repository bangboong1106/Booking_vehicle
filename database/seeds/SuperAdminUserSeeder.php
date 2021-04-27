<?php

use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\Model\Entities\AdminUserInfo',
            'model_id' => env('SUPER_ADMIN_ID', 1)
        ]);
    }
}
