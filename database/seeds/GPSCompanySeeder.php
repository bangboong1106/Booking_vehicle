<?php

use Illuminate\Database\Seeder;

class GPSCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $data = [['Bình Anh',
            'GPS_BINH_ANH_WEB_SERVICE_WSDL=http://gps4.binhanh.com.vn/WebServices/BinhAnh.asmx?wsdl',
            '7213',
            'pUrgARkgRakh4ZBAJqRdHCPKBTGMtf3KZdjU2fUA',
            'GetVehicleInfoWithAddress',
            'GetRouteByXNCodeWithAddress'],
            ['VietMap',
                '',
                '',
                '',
                '',
                ''],
            ['ADA',
                '',
                '',
                '',
                '',
                ''],
            ['Bình Anh 2',
                '',
                '',
                '',
                '',
                ''],
            ['Eupfin',
                '',
                '',
                '',
                '',
                ''],
            ['Vcomsat',
                '',
                '',
                '',
                '',
                ''],
            ['Eposi',
                '',
                '',
                '',
                '',
                ''],
            ['Adsun',
                '',
                '',
                '',
                '',
                ''],
            ['Vinh Hiển',
                '',
                '',
                '',
                '',
                '']
            ];
        foreach ($data as $item) {
            \App\Model\Entities\GpsCompany::firstOrCreate(array(
                'name' => $item[0],
                'web_service_wsdl' => $item[1],
                'user' => $item[2],
                'key' => $item[3],
                'function_name' => $item[4],
                'vehicle_function_name' => $item[5]
            ));
        }
    }
}
