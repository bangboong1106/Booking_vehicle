<?php

use Illuminate\Database\Seeder;

class DomainConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $data = [
            ['001', 'https://thanhdat.onelog.com.vn', 'Thành Đạt Express'],
            ['002', 'https://neco.onelog.com.vn', 'NECO'],
            ['003', 'https://tandat.onelog.com.vn', 'Tân Đạt']
        ];
        foreach ($data as $item) {
            \App\Model\Entities\DomainConfig::firstOrCreate(array(
                'code' => $item[0],
                'domain' => $item[1],
                'description' => $item[2]
            ));
        }
    }
}
