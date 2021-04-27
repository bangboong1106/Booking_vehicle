<?php

use App\Model\Entities\Currency;
use Illuminate\Database\Seeder;


class SystemCurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        //Currency::truncate();
        $datas = [
            ['VND', 'Việt Nam Đồng'],
            ['USD', 'Dollar Mỹ'],
            ['JPY', 'Yên Nhật'],
            ['CNY', 'Nhân dân tệ'],
            ['KRW', 'Won Hàn Quốc'],
            ['GBP', 'Bảng Anh'],
            ['EUR', 'Euro'],
            ['RUB', 'Ruble Nga']
        ];
        foreach ($datas as $data) {
            Currency::firstOrCreate(array(
                'currency_code' => $data[0],
                'currency_name' => $data[1]
            ));
        }
    }
}
