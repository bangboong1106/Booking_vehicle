<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\AdminUserInfo;

class AdminUserInfoTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		//AdminUserInfo::truncate();

        if (!AdminUserInfo::where('username', '=', 'admin')->exists()) {
            AdminUserInfo::firstOrCreate(array(
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'role' => 'admin',
            ));
        }
	}
}
