<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\VehicleConfigFile;

class VehicleConfigFileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        //  VehicleConfigFile::truncate();
        $datas = [
            ['Đăng ký xe', '1', '1', '1', '1'],
        ];
        foreach ($datas as $data) {
            VehicleConfigFile::firstOrCreate(array(
                'file_name' => $data[0],
                'allow_extension' => $data[1],
                'is_show_register' => $data[2],
                'is_show_expired' => $data[3],
                'is_required' => $data[4]
            ));
        }
    }
}
