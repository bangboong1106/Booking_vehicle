<?php

use App\Model\Entities\TPApiConfig;
use Illuminate\Database\Seeder;

class TPApiConfigTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        if (!TPApiConfig::where('id', '=', '1')->exists()) {
            TPApiConfig::firstOrCreate(array(
                'name' => '1MG',
                'client_id' => 'partner_client',
                'client_secret' => 'secret',
                'grant_type' => 'password',
                'username' => 'GHN',
                'password' => '1234',
                'env' => '0',
                'request_header_authen' => 'authorization'
            ));
        }
	}
}
