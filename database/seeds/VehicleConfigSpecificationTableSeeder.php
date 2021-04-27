<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\VehicleConfigSpecification;

class VehicleConfigSpecificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        //  VehicleConfigSpecification::truncate();
        $datas = [
            ['Chú thích', '1', null, '2', '0']
        ];
        foreach ($datas as $data) {
            VehicleConfigSpecification::firstOrCreate(array(
                'name' => $data[0],
                'type' => $data[1],
                'group_unit' => $data[2],
                'tab_view' => $data[3],
                'is_required' => $data[4]
            ));
        }
    }
}
