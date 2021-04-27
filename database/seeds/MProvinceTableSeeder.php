<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Province;

class MProvinceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
     //   Province::truncate();

        $data  = [
            ['01', 'Hà Nội', '2', 1],
            ['02', 'Hà Giang', '1', 1],
            ['04', 'Cao Bằng', '1', 1],
            ['06', 'Bắc Kạn', '1', 1],
            ['08', 'Tuyên Quang', '1', 1],
            ['10', 'Lào Cai', '1', 1],
            ['11', 'Điện Biên', '1', 1],
            ['12', 'Lai Châu', '1', 1],
            ['14', 'Sơn La', '1', 1],
            ['15', 'Yên Bái', '1', 1],
            ['17', 'Hòa Bình', '1', 1],
            ['19', 'Thái Nguyên', '1', 1],
            ['20', 'Lạng Sơn', '1', 1],
            ['22', 'Quảng Ninh', '1', 1],
            ['24', 'Bắc Giang', '1', 1],
            ['25', 'Phú Thọ', '1', 1],
            ['26', 'Vĩnh Phúc', '1', 1],
            ['27', 'Bắc Ninh', '1', 1],
            ['30', 'Hải Dương', '1', 1],
            ['31', 'Hải Phòng', '2', 1],
            ['33', 'Hưng Yên', '1', 1],
            ['34', 'Thái Bình', '1', 1],
            ['35', 'Hà Nam', '1', 1],
            ['36', 'Nam Định', '1', 1],
            ['37', 'Ninh Bình', '1', 1],
            ['38', 'Thanh Hóa', '1', 1],
            ['40', 'Nghệ An', '1', 1],
            ['42', 'Hà Tĩnh', '1', 1],
            ['44', 'Quảng Bình', '1', 1],
            ['45', 'Quảng Trị', '1', 1],
            ['46', 'Thừa Thiên Huế', '1', 1],
            ['48', 'Đà Nẵng', '2', 1],
            ['49', 'Quảng Nam', '1', 1],
            ['51', 'Quảng Ngãi', '1', 1],
            ['52', 'Bình Định', '1', 1],
            ['54', 'Phú Yên', '1', 1],
            ['56', 'Khánh Hòa', '1', 1],
            ['58', 'Ninh Thuận', '1', 1],
            ['60', 'Bình Thuận', '1', 1],
            ['62', 'Kon Tum', '1', 1],
            ['64', 'Gia Lai', '1', 1],
            ['66', 'Đắk Lắk', '1', 1],
            ['67', 'Đắk Nông', '1', 1],
            ['68', 'Lâm Đồng', '1', 1],
            ['70', 'Bình Phước', '1', 1],
            ['72', 'Tây Ninh', '1', 1],
            ['74', 'Bình Dương', '1', 1],
            ['75', 'Đồng Nai', '1', 1],
            ['77', 'Bà Rịa - Vũng Tàu', '1', 1],
            ['79', 'Hồ Chí Minh', '2', 1],
            ['80', 'Long An', '1', 1],
            ['82', 'Tiền Giang', '1', 1],
            ['83', 'Bến Tre', '1', 1],
            ['84', 'Trà Vinh', '1', 1],
            ['86', 'Vĩnh Long', '1', 1],
            ['87', 'Đồng Tháp', '1', 1],
            ['89', 'An Giang', '1', 1],
            ['91', 'Kiên Giang', '1', 1],
            ['92', 'Cần Thơ', '2', 1],
            ['93', 'Hậu Giang', '1', 1],
            ['94', 'Sóc Trăng', '1', 1],
            ['95', 'Bạc Liêu', '1', 1],
            ['96', 'Cà Mau', '1', 1]
        ];

        foreach ($data as $province) {
            Province::firstOrCreate(array(
                'province_id' => $province[0],
                'title' => $province[1],
                'type' => $province[2],
                'ins_id' => $province[3]
            ));
        }
    }
}
