<?php

use App\Model\Entities\SystemCodeConfig;
use Illuminate\Database\Seeder;

class SystemCodeConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        //   SystemCodeConfig::truncate();
        $datas = [
            ['DHVT', '6', config('constant.sc_order'),1],
            ['KH', '6', config('constant.sc_customer'),0],
            ['TX', '6', config('constant.sc_driver'),0],
            ['GT', '6', config('constant.sc_good_type'),0],
            ['GU', '6', config('constant.sc_good_unit'),0],
            ['LC', '6', config('constant.sc_location'),0],
            ['DTX', '6', config('constant.sc_vehicle_team'),0],
            ['DX', '6', config('constant.sc_vehicle_group'),0],
            ['CX', '6', config('constant.sc_route'),0],
            ['CP', '6', config('constant.sc_quota'),0],
            ['DH', '6', config('constant.sc_order_customer'),1],
            ['CG', '6', config('constant.sc_customer_group'),0],
            ['BG', '6', config('constant.sc_price_quote'),0],
            ['TL', '6', config('constant.sc_payroll'),0],
            ['LG', '6', config('constant.sc_location_group'),0],
            ['PSC', '6', config('constant.sc_repair_ticket'),0],
            ['NHH', '6', config('constant.sc_goods_group'),0],
            ['DT', '6', config('constant.sc_partner'),0],
            ['DLKH', '6', config('constant.sc_customer_default'),0],
        ];
        foreach ($datas as $data) {
            SystemCodeConfig::firstOrCreate(array(
                'prefix' => $data[0],
                'suffix_length' => $data[1],
                'type' => $data[2],
                'is_generate_time' => $data[3],
            ));
        }
    }
}
