<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Model\Entities\File;
use App\Model\Entities\GoodsType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoodsTypeTableSeeder extends Seeder
{
    //ten xe - hinh anh - gia -id - type - file_type - file path
    const NAME = 0;
    const IMG_NAME = 1;
    const AMOUNT = 2;
    const GOODS_GROUP_ID = 3;
    const TYPE = 4;
    const IMG_TYPE = 5;
    const IMG_PATH = 6;

    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // GoodsType::truncate();
        // File::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $row = 1;
        if (($handle = fopen(public_path()."/file/vehicle.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $row++;
                if ($row == 2) continue;
                $file = new File();
                $goodsType = new GoodsType();
                for ($c=0; $c < $num; $c++) {
                    $fileId = Str::uuid();
                    $file->file_id = $fileId;
                    $file->file_name = $data[self::IMG_NAME];
                    $file->file_type = $data[self::IMG_TYPE];

                    $newPath = 'media/goods/';
                    $code = '';

                    if ($data[self::TYPE] == 1) {
                        $newPath .= 'car/';
                        $code = 'OTO';
                    } else if ($data[self::TYPE] == 2) {
                        $newPath .= 'motor_bike/';
                        $code = 'XM';
                    }

                    $file->path = $newPath.$data[1];

                    $file->save();

                    $goodsType->title = $data[self::NAME];
                    $goodsType->amount = $data[self::AMOUNT];
                    $goodsType->type = $data[self::TYPE];
                    $goodsType->file_id = $fileId;
                    $goodsType->code = $code.$row;
                    $goodsType->goods_group_id = $data[self::GOODS_GROUP_ID];
                    $goodsType->customer_id = 65;

                    $goodsType->save();
                }
            }
            fclose($handle);
        }
    }
}
