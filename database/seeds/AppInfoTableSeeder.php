<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_info')->truncate();

        DB::table('app_info')->insert([
            [
                'id' => '0',
                'name' => 'C20 Quản trị',
                'version_android' => '1.2',
                'version_ios' => '1.2',
                'what_new' => 'Cập nhật giao diện ứng dụng; Tối ưu hoá hiệu năng.',
                'play_store_id' => 'com.onelog.cetamanagement',
                'app_store_id' => '1517547595',
                'force_update' => '0',
                'del_flag' => '0',
            ],
            [
                'id' => '1',
                'name' => 'C20 Management',
                'version_android' => '1.5',
                'version_ios' => '1.4',
                'what_new' => 'Sửa một số lỗi nhỏ.',
                'play_store_id' => 'com.onelog.dashboardapp',
                'app_store_id' => '1479315037',
                'force_update' => '0',
                'del_flag' => '0',
            ],
            [
                'id' => '2',
                'name' => 'C20 Khách hàng',
                'version_android' => '1.2',
                'version_ios' => '1.2',
                'what_new' => 'Sửa một số lỗi nhỏ; Cập nhật giao diện ứng dụng.',
                'play_store_id' => 'com.onelog.cetaclient',
                'app_store_id' => '1521823404',
                'force_update' => '0',
                'del_flag' => '0',
            ],
            [
                'id' => '3',
                'name' => 'C20 Driver Plus',
                'version_android' => '2.13',
                'version_ios' => '2.9',
                'what_new' => 'Hiển thị số lượng đơn hàng, điểm nhận, điểm trả.',
                'play_store_id' => 'com.onelog.driverv2.main',
                'app_store_id' => '1502117441',
                'force_update' => '0',
                'del_flag' => '0',
            ],
        ]);
    }
}
