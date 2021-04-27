<?php

use App\Model\Entities\Permission;
use App\Model\Entities\Province;
use App\Model\Entities\SystemConfig;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        if (!SystemConfig::where('key', '=', 'Notification.DistanceUnit')->exists()) {
            SystemConfig::firstOrCreate(array(
                'key' => 'Notification.DistanceUnit',
                'value' => '10',
                'description' => 'Khoảng cách thông báo địa điểm gần đến nơi',
                'ins_id' => 0
            ));
        }
        if (!SystemConfig::where('key', '=', 'Dashboard.VehiclePageSize')->exists()) {
            SystemConfig::firstOrCreate(array(
                'key' => 'Dashboard.VehiclePageSize',
                'value' => '50',
                'description' => 'Số lượng xe hiển thị trên bảng điều khiển',
                'ins_id' => 0
            ));
        }
        if (!SystemConfig::where('key', '=', 'Dashboard.NotifyVehicle')->exists()) {
            SystemConfig::firstOrCreate(array(
                'key' => 'Dashboard.NotifyVehicle',
                'value' => '30',
                'description' => 'Thông báo xe đến thời gian lấy hàng',
                'ins_id' => 0
            ));
        }
        if (!SystemConfig::where('key', '=', 'Dashboard.Reload')->exists()) {
            SystemConfig::firstOrCreate(array(
                'key' => 'Dashboard.Reload',
                'value' => '5',
                'description' => 'Thời gian tự động tải lại bảng điều khiển',
                'ins_id' => 0
            ));
        }
    }
}
