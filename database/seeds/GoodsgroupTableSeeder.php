<?php

namespace Database\Seeders;

use App\Model\Entities\GoodsGroup;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Seeder;

class GoodsgroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            ['Toyota'],
            ['Kia'],
            ['Mazda'],
            ['Ford'],
            ['Hyundai'],
            ['Honda'],
            ['Mercedes Benz'],
            ['Bmw'],
            ['Mitsubishi'],
            ['Vinfast'],
            ['Chevrolet'],
            ['Honda'],
            ['Yamaha'],
            ['Suzuki'],
            ['Piaggio'],
            ['Kawasaki'],
            ['Sym'],
            ['Ducati'],
            ['Benelli'],
            ['Brixton'],
            ['Kymco'],
            ['Ktm'],
            ['Khác']

        ];
        foreach ($data as $goods_group){
            GoodsGroup::firstOrCreate(array(
                'name'=>$goods_group[0],
                'code'=>$goods_group[0]
            ));
        }

    }
}
