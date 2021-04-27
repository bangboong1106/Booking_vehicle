<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Permission;

class MigrateOldPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permissions
        // order permission
        if (Permission::where('name', '=', 'view order')->exists()) {
            $this->_update('order', 1, 'order');
        }

        // customer permission
        if (Permission::where('name', '=', 'view customer')->exists()) {
            $this->_update('customer', 1, 'customer');
        }

        // contract permission
        if (Permission::where('name', '=', 'view contract')->exists()) {
            $this->_update('contract', 4, 'customer');
        }

        // driver permission
        if (Permission::where('name', '=', 'view driver')->exists()) {
            $this->_update('driver', 0, 'driver');
        }

        // vehicle-team permission
        if (Permission::where('name', '=', 'view vehicle_team')->exists()) {
            $this->_update('vehicle_team', 1, 'driver');
        }

        // vehicle permission
        if (Permission::where('name', '=', 'view vehicle')->exists()) {
            $this->_update('vehicle', 1, 'vehicle');
        }

        // vehicle-group permission
        if (Permission::where('name', '=', 'view vehicle_group')->exists()) {
            $this->_update('vehicle_group', 2, 'vehicle');
        }

        // admin permission
        if (Permission::where('name', '=', 'view admin')->exists()) {
            $this->_update('admin', 1, 'management');
        }

        // contact permission
        if (Permission::where('name', '=', 'view contact')->exists()) {
            $this->_update('contact', 5, 'customer');
        }

        // role permission
        if (Permission::where('name', '=', 'view role')->exists()) {
            $this->_update('role', 2, 'management');
        }

        // receipt-payment permission
        if (Permission::where('name', '=', 'view receipt_payment')->exists()) {
            $this->_update('receipt_payment', 3, 'category');
        }

        // contract-type permission
        if (Permission::where('name', '=', 'view contract_type')->exists()) {
            $this->_update('contract_type', 5, 'category');
        }

        // currency permission
        if (Permission::where('name', '=', 'view currency')->exists()) {
            $this->_update('currency', 4, 'category');
        }

        // location permission
        if (Permission::where('name', '=', 'view location')->exists()) {
            $this->_update('location', 6, 'order');
        }

        // alert-log permission
        if (Permission::where('name', '=', 'view alert_log')->exists()) {
            $this->_update('alert_log', 0, 'setting');

        }

        // driver-config-file permission
        if (Permission::where('name', '=', 'view driver_config_file')->exists()) {
            $this->_update('driver_config_file', 4, 'setting');
        }

        // vehicle-config-file permission
        if (Permission::where('name', '=', 'view vehicle_config_file')->exists()) {
            $this->_update('vehicle_config_file', 5, 'setting');
        }

        // vehicle-config-specification permission
        if (Permission::where('name', '=', 'view vehicle_config_specification')->exists()) {
            $this->_update('vehicle_config_specification', 6, 'setting');
        }

        // system-code-config permission
        if (Permission::where('name', '=', 'view system_code_config')->exists()) {
            $this->_update('system_code_config', 3, 'setting');
        }

        if (Permission::where('name', '=', 'view route')->exists()) {
            $this->_update('route', 4, 'order');
        }

        if (Permission::where('name', '=', 'view auditing')->exists()) {
            $this->_update('auditing', 0, 'management');
        }

        if (Permission::where('name', '=', 'view quota')->exists()) {
            $this->_update('quota', 1, 'quota');
        }

        if (Permission::where('name', '=', 'view report_schedule')->exists()) {
            $this->_update('report_schedule', 2, 'report');
        }

        if (Permission::where('name', '=', 'view report')->exists()) {
            $this->_update('report', 1, 'report');
        }

        // system-config permission
        if (Permission::where('name', '=', 'view system_config')->exists()) {
            $this->_update('system_config', 2, 'setting');
        }

        if (Permission::where('name', '=', 'view dashboard')->exists()) {
            $this->_update('dashboard', 0, 'dash_board');
        }

        // notification-log permission
        if (Permission::where('name', '=', 'view notification_log')->exists()) {
            $this->_update('notification_log', 8, 'management');
        }

        // import export quota
        if (Permission::where('name', '=', 'import quota')->exists()) {
            $this->_update('quota', 1, 'quota');
        }

        if (Permission::where('name', '=', 'view document')->exists()) {
            $this->_update('document', 3, 'order');
        }

        if (Permission::where('name', '=', 'import location')->exists()) {
            $this->_update('location', 6, 'order');
        }

        if (Permission::where('name', '=', 'view order_customer')->exists()) {
            $this->_update('order_customer', 5, 'order');
        }

        if (Permission::where('name', '=', 'view template')->exists()) {
            $this->_update('template', 3, 'management');
        }

        if (Permission::where('name', '=', 'import route')->exists()) {
            $this->_update('route', 4, 'order');
        }

        if (Permission::where('name', '=', 'view location_type')->exists()) {
            $this->_update('location_type', 6, 'category');
        }

        if (Permission::where('name', '=', 'view location_group')->exists()) {
            $this->_update('location_group', 7, 'order');
        }

        if (Permission::where('name', '=', 'import order_customer')->exists()) {
            $this->_update('order_customer', 5, 'order');
        }

        if (Permission::where('name', '=', 'view customer_group')->exists()) {
            $this->_update('customer_group', 3, 'customer');
        }

        if (Permission::where('name', '=', 'view import_history')->exists()) {
            $this->_update('import_history', 6, 'management');
        }

        if (Permission::where('name', '=', 'view price_quote')->exists()) {
            $this->_update('price_quote', 2, 'quota');
        }

        if (Permission::where('name', '=', 'view revenue')->exists()) {
            $this->_update('revenue', 3, 'report');
        }

        if (Permission::where('name', '=', 'view cost')->exists()) {
            $this->_update('cost', 4, 'report');
        }

        if (Permission::where('name', '=', 'view template_payment')->exists()) {
            $this->_update('template_payment', 4, 'management');
        }

        if (Permission::where('name', '=', 'view payroll')->exists()) {
            $this->_update('payroll', 3, 'quota');
        }

        if (Permission::where('name', '=', 'view activity_log')->exists()) {
            $this->_update('activity_log', 7, 'management');
        }

        if (Permission::where('name', '=', 'view order_price')->exists()) {
            $this->_update('order_price', 4, 'quota');
        }

        if (Permission::where('name', '=', 'view merge_order')->exists()) {
            $this->_update('merge_order', 2, 'order');
        }

        if (Permission::where('name', '=', 'view accessory')->exists()) {
            $this->_update('accessory', 3, 'vehicle');
        }

        if (Permission::where('name', '=', 'view repair_ticket')->exists()) {
            $this->_update('repair_ticket', 4, 'vehicle');
        }

        if (Permission::where('name', '=', 'view company_info')->exists()) {
            $this->_update('company_info', 1, 'setting');
        }

        if (Permission::where('name', '=', 'view customer_default_data')->exists()) {
            $this->_update('customer_default_data', 2, 'customer');
        }

        // good-type permission
        if (Permission::where('name', '=', 'view goods_type')->exists()) {
            $this->_update('goods_type', 1, 'category');
        }

        if (Permission::where('name', '=', 'view goods_unit')->exists()) {
            $this->_update('goods_unit', 2, 'category');
        }

        if (Permission::where('name', '=', 'view goods_group')->exists()) {
            $this->_update('goods_group', 7, '');
        }

        if (Permission::where('name', '=', 'view template_excel_converter')->exists()) {
            $this->_update('template_excel_converter', 5, '');
        }
    }

    private function _update($name, $display, $group, $del_flag = 0)
    {
        Permission::where('name', 'like', '% '.$name)->update(['display' => $display, 'group' => $group, 'del_flag' => $del_flag]);
    }
}
