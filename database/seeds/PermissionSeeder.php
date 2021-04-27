<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Role;
use App\Model\Entities\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached role and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // order permission
        if (!Permission::where('name', '=', 'view order')->exists()) {
            Permission::create(['name' => 'view order', 'display' => 1, 'group' => 'order']);
            Permission::create(['name' => 'add order', 'display' => 1, 'group' => 'order']);
            Permission::create(['name' => 'edit order', 'display' => 1, 'group' => 'order']);
            Permission::create(['name' => 'delete order', 'display' => 1, 'group' => 'order']);
            Permission::create(['name' => 'import order', 'display' => 1, 'group' => 'order']);
            Permission::create(['name' => 'export order', 'display' => 1, 'group' => 'order']);
        }

        // customer permission
        if (!Permission::where('name', '=', 'view customer')->exists()) {
            Permission::create(['name' => 'view customer', 'display' => 1, 'group' => 'customer']);
            Permission::create(['name' => 'add customer', 'display' => 1, 'group' => 'customer']);
            Permission::create(['name' => 'edit customer', 'display' => 1, 'group' => 'customer']);
            Permission::create(['name' => 'delete customer', 'display' => 1, 'group' => 'customer']);
            Permission::create(['name' => 'import customer', 'display' => 1, 'group' => 'customer']);
            Permission::create(['name' => 'export customer', 'display' => 1, 'group' => 'customer']);
        }

        // client permission
        if (!Permission::where('name', '=', 'view client')->exists()) {
            Permission::create(['name' => 'view client', 'display' => 6, 'group' => 'customer']);
            Permission::create(['name' => 'add client', 'display' => 6, 'group' => 'customer']);
            Permission::create(['name' => 'edit client', 'display' => 6, 'group' => 'customer']);
            Permission::create(['name' => 'delete client', 'display' => 6, 'group' => 'customer']);
            Permission::create(['name' => 'import client', 'display' => 6, 'group' => 'customer']);
            Permission::create(['name' => 'export client', 'display' => 6, 'group' => 'customer']);
        }

        // contract permission
        if (!Permission::where('name', '=', 'view contract')->exists()) {
            Permission::create(['name' => 'view contract', 'display' => 4, 'group' => 'customer']);
            Permission::create(['name' => 'add contract', 'display' => 4, 'group' => 'customer']);
            Permission::create(['name' => 'edit contract', 'display' => 4, 'group' => 'customer']);
            Permission::create(['name' => 'delete contract', 'display' => 4, 'group' => 'customer']);
        }

        // driver permission
        if (!Permission::where('name', '=', 'view driver')->exists()) {
            Permission::create(['name' => 'view driver', 'display' => 0, 'group' => 'driver']);
            Permission::create(['name' => 'add driver', 'display' => 0, 'group' => 'driver']);
            Permission::create(['name' => 'edit driver', 'display' => 0, 'group' => 'driver']);
            Permission::create(['name' => 'delete driver', 'display' => 0, 'group' => 'driver']);
            Permission::create(['name' => 'import driver', 'display' => 0, 'group' => 'driver']);
            Permission::create(['name' => 'export driver', 'display' => 0, 'group' => 'driver']);
        }

        // vehicle-team permission
        if (!Permission::where('name', '=', 'view vehicle_team')->exists()) {
            Permission::create(['name' => 'view vehicle_team', 'display' => 1, 'group' => 'driver']);
            Permission::create(['name' => 'add vehicle_team', 'display' => 1, 'group' => 'driver']);
            Permission::create(['name' => 'edit vehicle_team', 'display' => 1, 'group' => 'driver']);
            Permission::create(['name' => 'delete vehicle_team', 'display' => 1, 'group' => 'driver']);
        }

        // vehicle permission
        if (!Permission::where('name', '=', 'view vehicle')->exists()) {
            Permission::create(['name' => 'view vehicle', 'display' => 1, 'group' => 'vehicle']);
            Permission::create(['name' => 'add vehicle', 'display' => 1, 'group' => 'vehicle']);
            Permission::create(['name' => 'edit vehicle', 'display' => 1, 'group' => 'vehicle']);
            Permission::create(['name' => 'delete vehicle', 'display' => 1, 'group' => 'vehicle']);
        }

        // vehicle-group permission
        if (!Permission::where('name', '=', 'view vehicle_group')->exists()) {
            Permission::create(['name' => 'view vehicle_group', 'display' => 2, 'group' => 'vehicle']);
            Permission::create(['name' => 'add vehicle_group', 'display' => 2, 'group' => 'vehicle']);
            Permission::create(['name' => 'edit vehicle_group', 'display' => 2, 'group' => 'vehicle']);
            Permission::create(['name' => 'delete vehicle_group', 'display' => 2, 'group' => 'vehicle']);
        }

        // admin permission
        if (!Permission::where('name', '=', 'view admin')->exists()) {
            Permission::create(['name' => 'view admin', 'display' => 1, 'group' => 'management']);
            Permission::create(['name' => 'add admin', 'display' => 1, 'group' => 'management']);
            Permission::create(['name' => 'edit admin', 'display' => 1, 'group' => 'management']);
            Permission::create(['name' => 'delete admin', 'display' => 1, 'group' => 'management']);
        }

        // contact permission
        if (!Permission::where('name', '=', 'view contact')->exists()) {
            Permission::create(['name' => 'view contact', 'display' => 5, 'group' => 'customer']);
            Permission::create(['name' => 'add contact', 'display' => 5, 'group' => 'customer']);
            Permission::create(['name' => 'edit contact', 'display' => 5, 'group' => 'customer']);
            Permission::create(['name' => 'delete contact', 'display' => 5, 'group' => 'customer']);
        }

        // role permission
        if (!Permission::where('name', '=', 'view role')->exists()) {
            Permission::create(['name' => 'view role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'add role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'edit role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'delete role', 'display' => 2, 'group' => 'management']);
        }

        // receipt-payment permission
        if (!Permission::where('name', '=', 'view receipt_payment')->exists()) {
            Permission::create(['name' => 'view receipt_payment', 'display' => 3, 'group' => 'category']);
            Permission::create(['name' => 'add receipt_payment', 'display' => 3, 'group' => 'category']);
            Permission::create(['name' => 'edit receipt_payment', 'display' => 3, 'group' => 'category']);
            Permission::create(['name' => 'delete receipt_payment', 'display' => 3, 'group' => 'category']);
        }

        // contract-type permission
        if (!Permission::where('name', '=', 'view contract_type')->exists()) {
            Permission::create(['name' => 'view contract_type', 'display' => 5, 'group' => 'category']);
            Permission::create(['name' => 'add contract_type', 'display' => 5, 'group' => 'category']);
            Permission::create(['name' => 'edit contract_type', 'display' => 5, 'group' => 'category']);
            Permission::create(['name' => 'delete contract_type', 'display' => 5, 'group' => 'category']);
        }

        // currency permission
        if (!Permission::where('name', '=', 'view currency')->exists()) {
            Permission::create(['name' => 'view currency', 'display' => 4, 'group' => 'category']);
            Permission::create(['name' => 'add currency', 'display' => 4, 'group' => 'category']);
            Permission::create(['name' => 'edit currency', 'display' => 4, 'group' => 'category']);
            Permission::create(['name' => 'delete currency', 'display' => 4, 'group' => 'category']);
        }

        // location permission
        if (!Permission::where('name', '=', 'view location')->exists()) {
            Permission::create(['name' => 'view location', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'add location', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'edit location', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'delete location', 'display' => 6, 'group' => 'order']);
        }

        // alert-log permission
        if (!Permission::where('name', '=', 'view alert_log')->exists()) {
            Permission::create(['name' => 'view alert_log', 'display' => 7, 'group' => 'setting']);
            Permission::create(['name' => 'add alert_log', 'display' => 7, 'group' => 'setting']);
            Permission::create(['name' => 'edit alert_log', 'display' => 7, 'group' => 'setting']);
            Permission::create(['name' => 'delete alert_log', 'display' => 7, 'group' => 'setting']);
        }

        // driver-config-file permission
        if (!Permission::where('name', '=', 'view driver_config_file')->exists()) {
            Permission::create(['name' => 'view driver_config_file', 'display' => 4, 'group' => 'setting']);
            Permission::create(['name' => 'add driver_config_file', 'display' => 4, 'group' => 'setting']);
            Permission::create(['name' => 'edit driver_config_file', 'display' => 4, 'group' => 'setting']);
            Permission::create(['name' => 'delete driver_config_file', 'display' => 4, 'group' => 'setting']);
        }

        // vehicle-config-file permission
        if (!Permission::where('name', '=', 'view vehicle_config_file')->exists()) {
            Permission::create(['name' => 'view vehicle_config_file', 'display' => 5, 'group' => 'setting']);
            Permission::create(['name' => 'add vehicle_config_file', 'display' => 5, 'group' => 'setting']);
            Permission::create(['name' => 'edit vehicle_config_file', 'display' => 5, 'group' => 'setting']);
            Permission::create(['name' => 'delete vehicle_config_file', 'display' => 5, 'group' => 'setting']);
        }

        // vehicle-config-specification permission
        if (!Permission::where('name', '=', 'view vehicle_config_specification')->exists()) {
            Permission::create(['name' => 'view vehicle_config_specification', 'display' => 6, 'group' => 'setting']);
            Permission::create(['name' => 'add vehicle_config_specification', 'display' => 6, 'group' => 'setting']);
            Permission::create(['name' => 'edit vehicle_config_specification', 'display' => 6, 'group' => 'setting']);
            Permission::create(['name' => 'delete vehicle_config_specification', 'display' => 6, 'group' => 'setting']);
        }

        // system-code-config permission
        if (!Permission::where('name', '=', 'view system_code_config')->exists()) {
            Permission::create(['name' => 'view system_code_config', 'display' => 3, 'group' => 'setting']);
            Permission::create(['name' => 'add system_code_config', 'display' => 3, 'group' => 'setting']);
            Permission::create(['name' => 'edit system_code_config', 'display' => 3, 'group' => 'setting']);
            Permission::create(['name' => 'delete system_code_config', 'display' => 3, 'group' => 'setting']);
        }

        if (!Permission::where('name', '=', 'import vehicle')->exists()) {
            Permission::create(['name' => 'import vehicle', 'display' => 1, 'group' => 'vehicle']);
            Permission::create(['name' => 'export vehicle', 'display' => 1, 'group' => 'vehicle']);
        }

        if (!Permission::where('name', '=', 'view route')->exists()) {
            Permission::create(['name' => 'view route', 'display' => 4, 'group' => 'order']);
            Permission::create(['name' => 'add route', 'display' => 4, 'group' => 'order']);
            Permission::create(['name' => 'edit route', 'display' => 4, 'group' => 'order']);
            Permission::create(['name' => 'delete route', 'display' => 4, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view auditing')->exists()) {
            Permission::create(['name' => 'view auditing', 'display' => 0, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view quota')->exists()) {
            Permission::create(['name' => 'view quota', 'display' => 1, 'group' => 'quota']);
            Permission::create(['name' => 'add quota', 'display' => 1, 'group' => 'quota']);
            Permission::create(['name' => 'edit quota', 'display' => 1, 'group' => 'quota']);
            Permission::create(['name' => 'delete quota', 'display' => 1, 'group' => 'quota']);
        }

        if (!Permission::where('name', '=', 'view report_schedule')->exists()) {
            Permission::create(['name' => 'view report_schedule', 'display' => 2, 'group' => 'report']);
            Permission::create(['name' => 'add report_schedule', 'display' => 2, 'group' => 'report']);
            Permission::create(['name' => 'edit report_schedule', 'display' => 2, 'group' => 'report']);
            Permission::create(['name' => 'delete report_schedule', 'display' => 2, 'group' => 'report']);
        }

        if (!Permission::where('name', '=', 'view report')->exists()) {
            Permission::create(['name' => 'view report', 'display' => 1, 'group' => 'report']);
        }

        // system-config permission
        if (!Permission::where('name', '=', 'view system_config')->exists()) {
            Permission::create(['name' => 'view system_config', 'display' => 2, 'group' => 'setting']);
            Permission::create(['name' => 'edit system_config', 'display' => 2, 'group' => 'setting']);
        }

        if (!Permission::where('name', '=', 'view dashboard')->exists()) {
            Permission::create(['name' => 'view dashboard', 'display' => 0, 'group' => 'dash_board']);
        }

        // notification-log permission
        if (!Permission::where('name', '=', 'view notification_log')->exists()) {
            Permission::create(['name' => 'view notification_log', 'display' => 8, 'group' => 'management']);
        }

        // import export quota
        if (!Permission::where('name', '=', 'import quota')->exists()) {
            Permission::create(['name' => 'import quota', 'display' => 1, 'group' => 'quota']);
            Permission::create(['name' => 'export quota', 'display' => 1, 'group' => 'quota']);
        }

        if (!Permission::where('name', '=', 'view document')->exists()) {
            Permission::create(['name' => 'view document', 'display' => 3, 'group' => 'order']);
            Permission::create(['name' => 'import document', 'display' => 3, 'group' => 'order']);
            Permission::create(['name' => 'export document', 'display' => 3, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'import location')->exists()) {
            Permission::create(['name' => 'import location', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'export location', 'display' => 6, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view order_customer')->exists()) {
            Permission::create(['name' => 'view order_customer', 'display' => 5, 'group' => 'order']);
            Permission::create(['name' => 'add order_customer', 'display' => 5, 'group' => 'order']);
            Permission::create(['name' => 'edit order_customer', 'display' => 5, 'group' => 'order']);
            Permission::create(['name' => 'delete order_customer', 'display' => 5, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view template')->exists()) {
            Permission::create(['name' => 'view template', 'display' => 3, 'group' => 'management']);
            Permission::create(['name' => 'add template', 'display' => 3, 'group' => 'management']);
            Permission::create(['name' => 'edit template', 'display' => 3, 'group' => 'management']);
            Permission::create(['name' => 'delete template', 'display' => 3, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'import route')->exists()) {
            Permission::create(['name' => 'import route', 'display' => 4, 'group' => 'order']);
            Permission::create(['name' => 'export route', 'display' => 4, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view location_type')->exists()) {
            Permission::create(['name' => 'view location_type', 'display' => 6, 'group' => 'category']);
            Permission::create(['name' => 'add location_type', 'display' => 6, 'group' => 'category']);
            Permission::create(['name' => 'edit location_type', 'display' => 6, 'group' => 'category']);
            Permission::create(['name' => 'delete location_type', 'display' => 6, 'group' => 'category']);
        }

        if (!Permission::where('name', '=', 'view location_group')->exists()) {
            Permission::create(['name' => 'view location_group', 'display' => 7, 'group' => 'order']);
            Permission::create(['name' => 'add location_group', 'display' => 7, 'group' => 'order']);
            Permission::create(['name' => 'edit location_group', 'display' => 7, 'group' => 'order']);
            Permission::create(['name' => 'delete location_group', 'display' => 7, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'import order_customer')->exists()) {
            Permission::create(['name' => 'import order_customer', 'display' => 5, 'group' => 'order']);
            Permission::create(['name' => 'export order_customer', 'display' => 5, 'group' => 'order']);
        }


        if (!Permission::where('name', '=', 'view customer_group')->exists()) {
            Permission::create(['name' => 'view customer_group', 'display' => 3, 'group' => 'customer']);
            Permission::create(['name' => 'add customer_group', 'display' => 3, 'group' => 'customer']);
            Permission::create(['name' => 'edit customer_group', 'display' => 3, 'group' => 'customer']);
            Permission::create(['name' => 'delete customer_group', 'display' => 3, 'group' => 'customer']);
        }

        if (!Permission::where('name', '=', 'view import_history')->exists()) {
            Permission::create(['name' => 'view import_history', 'display' => 6, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view price_quote')->exists()) {
            Permission::create(['name' => 'view price_quote', 'display' => 2, 'group' => 'quota']);
            Permission::create(['name' => 'add price_quote', 'display' => 2, 'group' => 'quota']);
            Permission::create(['name' => 'edit price_quote', 'display' => 2, 'group' => 'quota']);
            Permission::create(['name' => 'delete price_quote', 'display' => 2, 'group' => 'quota']);
            Permission::create(['name' => 'import price_quote', 'display' => 2, 'group' => 'quota']);
            Permission::create(['name' => 'export price_quote', 'display' => 2, 'group' => 'quota']);
        }

        if (!Permission::where('name', '=', 'view revenue')->exists()) {
            Permission::create(['name' => 'view revenue', 'display' => 3, 'group' => 'report']);
            Permission::create(['name' => 'add revenue', 'display' => 3, 'group' => 'report']);
            Permission::create(['name' => 'edit revenue', 'display' => 3, 'group' => 'report']);
            Permission::create(['name' => 'export revenue', 'display' => 3, 'group' => 'report']);
        }

        if (!Permission::where('name', '=', 'view cost')->exists()) {
            Permission::create(['name' => 'view cost', 'display' => 4, 'group' => 'report']);
            Permission::create(['name' => 'add cost', 'display' => 4, 'group' => 'report']);
            Permission::create(['name' => 'edit cost', 'display' => 4, 'group' => 'report']);
            Permission::create(['name' => 'export cost', 'display' => 4, 'group' => 'report']);
        }

        if (!Permission::where('name', '=', 'view template_payment')->exists()) {
            Permission::create(['name' => 'view template_payment', 'display' => 4, 'group' => 'management']);
            Permission::create(['name' => 'add template_payment', 'display' => 4, 'group' => 'management']);
            Permission::create(['name' => 'edit template_payment', 'display' => 4, 'group' => 'management']);
            Permission::create(['name' => 'delete template_payment', 'display' => 4, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view payroll')->exists()) {
            Permission::create(['name' => 'view payroll', 'display' => 3, 'group' => 'quota']);
            Permission::create(['name' => 'add payroll', 'display' => 3, 'group' => 'quota']);
            Permission::create(['name' => 'edit payroll', 'display' => 3, 'group' => 'quota']);
            Permission::create(['name' => 'delete payroll', 'display' => 3, 'group' => 'quota']);
            Permission::create(['name' => 'import payroll', 'display' => 3, 'group' => 'quota']);
            Permission::create(['name' => 'export payroll', 'display' => 3, 'group' => 'quota']);
        }

        if (!Permission::where('name', '=', 'view activity_log')->exists()) {
            Permission::create(['name' => 'view activity_log', 'display' => 7, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view order_price')->exists()) {
            Permission::create(['name' => 'view order_price', 'display' => 4, 'group' => 'quota']);
            Permission::create(['name' => 'add order_price', 'display' => 4, 'group' => 'quota']);
            Permission::create(['name' => 'edit order_price', 'display' => 4, 'group' => 'quota']);
            Permission::create(['name' => 'delete order_price', 'display' => 4, 'group' => 'quota']);
            Permission::create(['name' => 'import order_price', 'display' => 4, 'group' => 'quota']);
            Permission::create(['name' => 'export order_price', 'display' => 4, 'group' => 'quota']);
        }

        if (!Permission::where('name', '=', 'view merge_order')->exists()) {
            Permission::create(['name' => 'view merge_order', 'display' => 2, 'group' => 'order']);
            Permission::create(['name' => 'add merge_order', 'display' => 2, 'group' => 'order']);
            Permission::create(['name' => 'edit merge_order', 'display' => 2, 'group' => 'order']);
            Permission::create(['name' => 'delete merge_order', 'display' => 2, 'group' => 'order']);
            Permission::create(['name' => 'import merge_order', 'display' => 2, 'group' => 'order']);
            Permission::create(['name' => 'export merge_order', 'display' => 2, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view accessory')->exists()) {
            Permission::create(['name' => 'view accessory', 'display' => 3, 'group' => 'vehicle']);
            Permission::create(['name' => 'add accessory', 'display' => 3, 'group' => 'vehicle']);
            Permission::create(['name' => 'edit accessory', 'display' => 3, 'group' => 'vehicle']);
            Permission::create(['name' => 'delete accessory', 'display' => 3, 'group' => 'vehicle']);
        }

        if (!Permission::where('name', '=', 'view repair_ticket')->exists()) {
            Permission::create(['name' => 'view repair_ticket', 'display' => 4, 'group' => 'vehicle']);
            Permission::create(['name' => 'add repair_ticket', 'display' => 4, 'group' => 'vehicle']);
            Permission::create(['name' => 'edit repair_ticket', 'display' => 4, 'group' => 'vehicle']);
            Permission::create(['name' => 'delete repair_ticket', 'display' => 4, 'group' => 'vehicle']);
            Permission::create(['name' => 'import repair_ticket', 'display' => 4, 'group' => 'vehicle']);
            Permission::create(['name' => 'export repair_ticket', 'display' => 4, 'group' => 'vehicle']);
        }

        if (!Permission::where('name', '=', 'view company_info')->exists()) {
            Permission::create(['name' => 'view company_info', 'display' => 1, 'group' => 'setting']);
        }

        if (!Permission::where('name', '=', 'view customer_default_data')->exists()) {
            Permission::create(['name' => 'view customer_default_data', 'display' => 2, 'group' => 'customer']);
            Permission::create(['name' => 'add customer_default_data', 'display' => 2, 'group' => 'customer']);
            Permission::create(['name' => 'edit customer_default_data', 'display' => 2, 'group' => 'customer']);
            Permission::create(['name' => 'delete customer_default_data', 'display' => 2, 'group' => 'customer']);
        }

        // good-type permission
        if (!Permission::where('name', '=', 'view goods_type')->exists()) {
            Permission::create(['name' => 'view goods_type', 'display' => 1, 'group' => 'category']);
            Permission::create(['name' => 'add goods_type', 'display' => 1, 'group' => 'category']);
            Permission::create(['name' => 'edit goods_type', 'display' => 1, 'group' => 'category']);
            Permission::create(['name' => 'delete goods_type', 'display' => 1, 'group' => 'category']);
        }

        if (!Permission::where('name', '=', 'view goods_unit')->exists()) {
            Permission::create(['name' => 'view goods_unit', 'display' => 2, 'group' => 'category']);
            Permission::create(['name' => 'add goods_unit', 'display' => 2, 'group' => 'category']);
            Permission::create(['name' => 'edit goods_unit', 'display' => 2, 'group' => 'category']);
            Permission::create(['name' => 'delete goods_unit', 'display' => 2, 'group' => 'category']);
        }

        if (!Permission::where('name', '=', 'lock order')->exists()) {
            Permission::create(['name' => 'lock order', 'display' => 1, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'unlock order')->exists()) {
            Permission::create(['name' => 'unlock order', 'display' => 1, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'lock route')->exists()) {
            Permission::create(['name' => 'lock route', 'display' => 4, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'unlock route')->exists()) {
            Permission::create(['name' => 'unlock route', 'display' => 4, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'lock order_customer')->exists()) {
            Permission::create(['name' => 'lock order_customer', 'display' => 5, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'unlock order_customer')->exists()) {
            Permission::create(['name' => 'unlock order_customer', 'display' => 5, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view goods_group')->exists()) {
            Permission::create(['name' => 'view goods_group', 'display' => 7, 'group' => 'category']);
            Permission::create(['name' => 'add goods_group', 'display' => 7, 'group' => 'category']);
            Permission::create(['name' => 'edit goods_group', 'display' => 7, 'group' => 'category']);
            Permission::create(['name' => 'delete goods_group', 'display' => 7, 'group' => 'category']);
        } else {
            Permission::where('name', 'like', '% goods_group')->update(['group' => 'category']);
        }

        if (!Permission::where('name', '=', 'view template_excel_converter')->exists()) {
            Permission::create(['name' => 'view template_excel_converter', 'display' => 5, 'group' => 'management']);
            Permission::create(['name' => 'add template_excel_converter', 'display' => 5, 'group' => 'management']);
            Permission::create(['name' => 'edit template_excel_converter', 'display' => 5, 'group' => 'management']);
            Permission::create(['name' => 'delete template_excel_converter', 'display' => 5, 'group' => 'management']);
        } else {
            Permission::where('name', 'like', '% template_excel_converter')->update(['group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view partner_order')->exists()) {
            Permission::create(['name' => 'view partner_order', 'display' => 1, 'group' => 'partner']);
            // Permission::create(['name' => 'add partner_order', 'display' => 1, 'group' => 'partner']);
            // Permission::create(['name' => 'edit partner_order', 'display' => 1, 'group' => 'partner']);
            // Permission::create(['name' => 'delete partner_order', 'display' => 1, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner_vehicle')->exists()) {
            Permission::create(['name' => 'view partner_vehicle', 'display' => 2, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_vehicle', 'display' => 2, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_vehicle', 'display' => 2, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_vehicle', 'display' => 2, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner_vehicle_team')->exists()) {
            Permission::create(['name' => 'view partner_vehicle_team', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_vehicle_team', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_vehicle_team', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_vehicle_team', 'display' => 3, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner_driver')->exists()) {
            Permission::create(['name' => 'view partner_driver', 'display' => 4, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_driver', 'display' => 4, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_driver', 'display' => 4, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_driver', 'display' => 4, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner_admin')->exists()) {
            Permission::create(['name' => 'view partner_admin', 'display' => 5, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_admin', 'display' => 5, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_admin', 'display' => 5, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_admin', 'display' => 5, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner_dashboard')->exists()) {
            Permission::create(['name' => 'view partner_dashboard', 'display' => 6, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'view partner')->exists()) {
            Permission::create(['name' => 'view partner', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'add partner', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'edit partner', 'display' => 6, 'group' => 'order']);
            Permission::create(['name' => 'delete partner', 'display' => 6, 'group' => 'order']);
        }

        if (!Permission::where('name', '=', 'view partner_vehicle_group')->exists()) {
            Permission::create(['name' => 'view partner_vehicle_group', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_vehicle_group', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_vehicle_group', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_vehicle_group', 'display' => 3, 'group' => 'partner']);
        }

        // role permission
        if (!Permission::where('name', '=', 'view client_role')->exists()) {
            Permission::create(['name' => 'view client_role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'add client_role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'edit client_role', 'display' => 2, 'group' => 'management']);
            Permission::create(['name' => 'delete client_role', 'display' => 2, 'group' => 'management']);
        }

        if (!Permission::where('name', '=', 'view partner_template')->exists()) {
            Permission::create(['name' => 'view partner_template', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'add partner_template', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'edit partner_template', 'display' => 3, 'group' => 'partner']);
            Permission::create(['name' => 'delete partner_template', 'display' => 3, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'export partner_driver')->exists()) {
            Permission::create(['name' => 'export partner_driver', 'display' => 1, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'import partner_driver')->exists()) {
            Permission::create(['name' => 'import partner_driver', 'display' => 1, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'export partner_order')->exists()) {
            Permission::create(['name' => 'export partner_order', 'display' => 1, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'import partner_order')->exists()) {
            Permission::create(['name' => 'import partner_order', 'display' => 1, 'group' => 'partner']);
        }

        if (!Permission::where('name', '=', 'import partner_vehicle')->exists()) {
            Permission::create(['name' => 'import partner_vehicle', 'display' => 1, 'group' => 'partner']);
            Permission::create(['name' => 'export partner_vehicle', 'display' => 1, 'group' => 'partner']);
        }

        $role = Role::firstOrCreate(['name' => 'super-admin', 'title' => 'Super Admin']);
        $role->givePermissionTo(Permission::where([['group', '<>', 'partner'], ['web', '=', 'admin']])->get());

        $rolePartner = Role::firstOrCreate(['name' => 'partner-super-admin', 'title' => 'Đối tác vận tải']);
        $rolePartner->givePermissionTo(Permission::where(function ($query) {
            $query->where([
                ['group', '=', 'partner'],
                ['web', '=', 'admin']
            ])
                ->orWhereIn('name', ['view route', 'add route', 'edit route', 'delete route', 'import route', 'export route',
                    'view document', 'import document', 'export document', 'view revenue', 'view cost', 'view report',
                    'view report_schedule', 'add report_schedule', 'edit report_schedule', 'delete report_schedule']);
        })
            ->whereNotIn('name', ['add partner_order', 'edit partner_order', 'delete partner_order'])
            ->get());

    }
}
