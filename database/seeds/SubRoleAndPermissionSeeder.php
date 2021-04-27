<?php

use App\Model\Entities\Role;
use Illuminate\Database\Seeder;
use App\Model\Entities\Permission;

class SubRoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Permission::where('name', '=', 'import vehicle')->exists()) {
            Permission::create(['name' => 'import vehicle', 'display' => 60]);
            Permission::create(['name' => 'export vehicle', 'display' => 60]);
        }

        if (!Permission::where('name', '=', 'view route')->exists()) {
            Permission::create(['name' => 'view route', 'display' => 11]);
            Permission::create(['name' => 'add route', 'display' => 11]);
            Permission::create(['name' => 'edit route', 'display' => 11]);
            Permission::create(['name' => 'delete route', 'display' => 11]);
        }

        if (!Permission::where('name', '=', 'lock route')->exists()) {
            Permission::create(['name' => 'lock route', 'display' => 11]);
        }

        if (!Permission::where('name', '=', 'unlock route')->exists()) {
            Permission::create(['name' => 'unlock route', 'display' => 11]);
        }

        if (Permission::where('name', '=', 'view report_vehicle')->exists()) {
            Permission::where('name', '=', 'view report_vehicle')->forceDelete();
            Permission::where('name', '=', 'view report_driver')->forceDelete();
            Permission::where('name', '=', 'view report_client')->forceDelete();
        }

        if (!Permission::where('name', '=', 'view quota')->exists()) {
            Permission::create(['name' => 'view quota', 'display' => 12]);
            Permission::create(['name' => 'add quota', 'display' => 12]);
            Permission::create(['name' => 'edit quota', 'display' => 12]);
            Permission::create(['name' => 'delete quota', 'display' => 12]);
        }

        if (Permission::where('name', '=', 'view routes')->exists()) {
            Permission::where('name', '=', 'view routes')->forceDelete();
            Permission::where('name', '=', 'add routes')->forceDelete();
            Permission::where('name', '=', 'edit routes')->forceDelete();
            Permission::where('name', '=', 'delete routes')->forceDelete();
        }

        if (!Permission::where('name', '=', 'view auditing')->exists()) {
            Permission::create(['name' => 'view auditing', 'display' => 260]);
        }

        if (!Permission::where('name', '=', 'view report_schedule')->exists()) {
            Permission::create(['name' => 'view report_schedule', 'display' => 5]);
            Permission::create(['name' => 'add report_schedule', 'display' => 5]);
            Permission::create(['name' => 'edit report_schedule', 'display' => 5]);
            Permission::create(['name' => 'delete report_schedule', 'display' => 5]);
        }

        if (!Permission::where('name', '=', 'view report')->exists()) {
            Permission::create(['name' => 'view report', 'display' => 4]);
        }

        if (!Role::where('name', '=', 'super-admin')->exists()) {
            $role = Role::where('name', '=', 'super-admin')->first();
            $role->givePermissionTo(Permission::all());
        }


        Permission::where('name', '=', 'view report')->update(['name' => 'view report', 'display' => 15]);
        Permission::where('name', '=', 'view report_schedule')->update(['name' => 'view report_schedule', 'display' => 16]);
        Permission::where('name', '=', 'add report_schedule')->update(['name' => 'add report_schedule', 'display' => 16]);
        Permission::where('name', '=', 'edit report_schedule')->update(['name' => 'edit report_schedule', 'display' => 16]);
        Permission::where('name', '=', 'delete report_schedule')->update(['name' => 'delete report_schedule', 'display' => 16]);

        if (Permission::where('name', '=', 'view province')->exists()) {
            Permission::where('name', '=', 'view province')->forceDelete();
            Permission::where('name', '=', 'add province')->forceDelete();
            Permission::where('name', '=', 'edit province')->forceDelete();
            Permission::where('name', '=', 'delete province')->forceDelete();

            Permission::where('name', '=', 'view district')->forceDelete();
            Permission::where('name', '=', 'add district')->forceDelete();
            Permission::where('name', '=', 'edit district')->forceDelete();
            Permission::where('name', '=', 'delete district')->forceDelete();

            Permission::where('name', '=', 'view ward')->forceDelete();
            Permission::where('name', '=', 'add ward')->forceDelete();
            Permission::where('name', '=', 'edit ward')->forceDelete();
            Permission::where('name', '=', 'delete ward')->forceDelete();
        }

        if (!Permission::where('name', '=', 'view dashboard')->exists()) {
            Permission::create(['name' => 'view dashboard', 'display' => 5]);
        }

        // system-config permission
        if (!Permission::where('name', '=', 'view system_config')->exists()) {
            Permission::create(['name' => 'view system_config', 'display' => 250]);
            Permission::create(['name' => 'edit system_config', 'display' => 250]);
        }

        // notification-log permission
        if (!Permission::where('name', '=', 'view notification_log')->exists()) {
            Permission::create(['name' => 'view notification_log', 'display' => 270]);
        }

        // import export quota
        if (!Permission::where('name', '=', 'import quota')->exists()) {
            Permission::create(['name' => 'import quota', 'display' => 12]);
            Permission::create(['name' => 'export quota', 'display' => 12]);
        }

        if (!Permission::where('name', '=', 'view document')->exists()) {
            Permission::create(['name' => 'view document', 'display' => 280]);
            Permission::create(['name' => 'import document', 'display' => 280]);
            Permission::create(['name' => 'export document', 'display' => 280]);
        }

        if (!Permission::where('name', '=', 'view alert_log')->exists()) {
            Permission::where('name', '=', 'view alert_log')->forceDelete();
            Permission::where('name', '=', 'add alert_log')->forceDelete();
            Permission::where('name', '=', 'edit alert_log')->forceDelete();
            Permission::where('name', '=', 'delete alert_log')->forceDelete();
        }

        if (!Permission::where('name', '=', 'import location')->exists()) {
            Permission::create(['name' => 'import location', 'display' => 160]);
            Permission::create(['name' => 'export location', 'display' => 160]);
        }

        if (!Permission::where('name', '=', 'view order_customer')->exists()) {
            Permission::create(['name' => 'view order_customer', 'display' => 290]);
            Permission::create(['name' => 'add order_customer', 'display' => 290]);
            Permission::create(['name' => 'edit order_customer', 'display' => 290]);
            Permission::create(['name' => 'delete order_customer', 'display' => 290]);
        }


        if (!Permission::where('name', '=', 'lock order_customer')->exists()) {
            Permission::create(['name' => 'lock order_customer', 'display' => 290]);
        }

        if (!Permission::where('name', '=', 'unlock order_customer')->exists()) {
            Permission::create(['name' => 'unlock order_customer', 'display' => 290]);
        }

        if (!Permission::where('name', '=', 'view template')->exists()) {
            Permission::create(['name' => 'view template', 'display' => 300]);
            Permission::create(['name' => 'add template', 'display' => 300]);
            Permission::create(['name' => 'edit template', 'display' => 300]);
            Permission::create(['name' => 'delete template', 'display' => 300]);
        }

        if (!Permission::where('name', '=', 'import route')->exists()) {
            Permission::create(['name' => 'import route', 'display' => 11]);
            Permission::create(['name' => 'export route', 'display' => 11]);
        }

        if (!Permission::where('name', '=', 'view location_type')->exists()) {
            Permission::create(['name' => 'view location_type', 'display' => 310]);
            Permission::create(['name' => 'add location_type', 'display' => 310]);
            Permission::create(['name' => 'edit location_type', 'display' => 310]);
            Permission::create(['name' => 'delete location_type', 'display' => 310]);
        }

        if (!Permission::where('name', '=', 'view location_group')->exists()) {
            Permission::create(['name' => 'view location_group', 'display' => 320]);
            Permission::create(['name' => 'add location_group', 'display' => 320]);
            Permission::create(['name' => 'edit location_group', 'display' => 320]);
            Permission::create(['name' => 'delete location_group', 'display' => 320]);
        }

        if (!Permission::where('name', '=', 'import order_customer')->exists()) {
            Permission::create(['name' => 'import order_customer', 'display' => 290]);
            Permission::create(['name' => 'export order_customer', 'display' => 290]);
        }

        if (!Permission::where('name', '=', 'view customer_group')->exists()) {
            Permission::create(['name' => 'view customer_group', 'display' => 330]);
            Permission::create(['name' => 'add customer_group', 'display' => 330]);
            Permission::create(['name' => 'edit customer_group', 'display' => 330]);
            Permission::create(['name' => 'delete customer_group', 'display' => 330]);
        }

        if (!Permission::where('name', '=', 'view import_history')->exists()) {
            Permission::create(['name' => 'view import_history', 'display' => 340]);
        }
        if (!Permission::where('name', '=', 'view price_quote')->exists()) {
            Permission::create(['name' => 'view price_quote', 'display' => 350]);
            Permission::create(['name' => 'add price_quote', 'display' => 350]);
            Permission::create(['name' => 'edit price_quote', 'display' => 350]);
            Permission::create(['name' => 'delete price_quote', 'display' => 350]);
            Permission::create(['name' => 'import price_quote', 'display' => 350]);
            Permission::create(['name' => 'export price_quote', 'display' => 350]);
        }

        if (!Permission::where('name', '=', 'view revenue')->exists()) {
            Permission::create(['name' => 'view revenue', 'display' => 360]);
            Permission::create(['name' => 'add revenue', 'display' => 360]);
            Permission::create(['name' => 'edit revenue', 'display' => 360]);
            Permission::create(['name' => 'export revenue', 'display' => 360]);
        }

        if (!Permission::where('name', '=', 'view revenue')->exists()) {
            Permission::create(['name' => 'view revenue', 'display' => 360]);
            Permission::create(['name' => 'add revenue', 'display' => 360]);
            Permission::create(['name' => 'edit revenue', 'display' => 360]);
            Permission::create(['name' => 'export revenue', 'display' => 360]);
        }

        if (!Permission::where('name', '=', 'view cost')->exists()) {
            Permission::create(['name' => 'view cost', 'display' => 370]);
            Permission::create(['name' => 'add cost', 'display' => 370]);
            Permission::create(['name' => 'edit cost', 'display' => 370]);
            Permission::create(['name' => 'export cost', 'display' => 370]);
        }
        if (!Permission::where('name', '=', 'view template_payment')->exists()) {
            Permission::create(['name' => 'view template_payment', 'display' => 380]);
            Permission::create(['name' => 'add template_payment', 'display' => 380]);
            Permission::create(['name' => 'edit template_payment', 'display' => 380]);
            Permission::create(['name' => 'delete template_payment', 'display' => 380]);
        }

        if (!Permission::where('name', '=', 'view order_price')->exists()) {
            Permission::create(['name' => 'view order_price', 'display' => 390]);
            Permission::create(['name' => 'add order_price', 'display' => 390]);
            Permission::create(['name' => 'edit order_price', 'display' => 390]);
            Permission::create(['name' => 'delete order_price', 'display' => 390]);
            Permission::create(['name' => 'import order_price', 'display' => 390]);
            Permission::create(['name' => 'export order_price', 'display' => 390]);
        }
        if (!Permission::where('name', '=', 'view merge_order')->exists()) {
            Permission::create(['name' => 'view merge_order', 'display' => 400]);
            Permission::create(['name' => 'add merge_order', 'display' => 400]);
            Permission::create(['name' => 'edit merge_order', 'display' => 400]);
            Permission::create(['name' => 'delete merge_order', 'display' => 400]);
            Permission::create(['name' => 'import merge_order', 'display' => 400]);
            Permission::create(['name' => 'export merge_order', 'display' => 400]);
        }

        if (!Permission::where('name', '=', 'view payroll')->exists()) {
            Permission::create(['name' => 'view payroll', 'display' => 410]);
            Permission::create(['name' => 'add payroll', 'display' => 410]);
            Permission::create(['name' => 'edit payroll', 'display' => 410]);
            Permission::create(['name' => 'delete payroll', 'display' => 410]);
            Permission::create(['name' => 'import payroll', 'display' => 410]);
            Permission::create(['name' => 'export payroll', 'display' => 410]);
        }

        if (!Permission::where('name', '=', 'view accessory')->exists()) {
            Permission::create(['name' => 'view accessory', 'display' => 420]);
            Permission::create(['name' => 'add accessory', 'display' => 420]);
            Permission::create(['name' => 'edit accessory', 'display' => 420]);
            Permission::create(['name' => 'delete accessory', 'display' => 420]);
        }

        if (!Permission::where('name', '=', 'view repair_ticket')->exists()) {
            Permission::create(['name' => 'view repair_ticket', 'display' => 430]);
            Permission::create(['name' => 'add repair_ticket', 'display' => 430]);
            Permission::create(['name' => 'edit repair_ticket', 'display' => 430]);
            Permission::create(['name' => 'delete repair_ticket', 'display' => 430]);
            Permission::create(['name' => 'import repair_ticket', 'display' => 430]);
            Permission::create(['name' => 'export repair_ticket', 'display' => 430]);
        }

        if (!Permission::where('name', '=', 'view activity_log')->exists()) {
            Permission::create(['name' => 'view activity_log', 'display' => 440]);
        }

        if (!Permission::where('name', '=', 'view company_info')->exists()) {
            Permission::create(['name' => 'view company_info', 'display' => 450]);
        }
        if (!Permission::where('name', '=', 'view customer_default_data')->exists()) {
            Permission::create(['name' => 'view customer_default_data', 'display' => 460]);
            Permission::create(['name' => 'add customer_default_data', 'display' => 460]);
            Permission::create(['name' => 'edit customer_default_data', 'display' => 460]);
            Permission::create(['name' => 'delete customer_default_data', 'display' => 460]);
        }

        Permission::where('name', '=', 'view merge_order')->update(['display' => 470]);
        Permission::where('name', '=', 'add merge_order')->update(['display' => 470]);
        Permission::where('name', '=', 'edit merge_order')->update(['display' => 470]);
        Permission::where('name', '=', 'delete merge_order')->update(['display' => 470]);
        Permission::where('name', '=', 'import merge_order')->update(['display' => 470]);
        Permission::where('name', '=', 'export merge_order')->update(['display' => 470]);

        // vehicle-group permission
        if (!Permission::where('name', '=', 'view goods_group')->exists()) {
            Permission::create(['name' => 'view goods_group', 'display' => 480]);
            Permission::create(['name' => 'add goods_group', 'display' => 480]);
            Permission::create(['name' => 'edit goods_group', 'display' => 480]);
            Permission::create(['name' => 'delete goods_group', 'display' => 480]);
        }

        if (!Permission::where('name', '=', 'view template_excel_converter')->exists()) {
            Permission::create(['name' => 'view template_excel_converter', 'display' => 500]);
            Permission::create(['name' => 'add template_excel_converter', 'display' => 500]);
            Permission::create(['name' => 'edit template_excel_converter', 'display' => 500]);
            Permission::create(['name' => 'delete template_excel_converter', 'display' => 500]);
        }

        if (Role::where('name', '=', 'super-admin')->exists()) {
            $role = Role::where('name', '=', 'super-admin')->first();
            $role->givePermissionTo(Permission::all());
        }
    }
}
