<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Role;
use App\Model\Entities\Permission;

class RoleAndPermissionSeeder extends Seeder
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
            Permission::create(['name' => 'view order', 'display' => 10]);
            Permission::create(['name' => 'add order', 'display' => 10]);
            Permission::create(['name' => 'edit order', 'display' => 10]);
            Permission::create(['name' => 'delete order', 'display' => 10]);
            Permission::create(['name' => 'import order', 'display' => 10]);
            Permission::create(['name' => 'export order', 'display' => 10]);
        }

        if (!Permission::where('name', '=', 'lock order')->exists()) {
            Permission::create(['name' => 'lock order', 'display' => 10]);
        }

        if (!Permission::where('name', '=', 'unlock order')->exists()) {
            Permission::create(['name' => 'unlock order', 'display' => 10]);
        }

        // customer permission
        if (!Permission::where('name', '=', 'view customer')->exists()) {
            Permission::create(['name' => 'view customer', 'display' => 20]);
            Permission::create(['name' => 'add customer', 'display' => 20]);
            Permission::create(['name' => 'edit customer', 'display' => 20]);
            Permission::create(['name' => 'delete customer', 'display' => 20]);
            Permission::create(['name' => 'import customer', 'display' => 20]);
            Permission::create(['name' => 'export customer', 'display' => 20]);
        }

        // contract permission
        if (!Permission::where('name', '=', 'view contract')->exists()) {
            Permission::create(['name' => 'view contract', 'display' => 30]);
            Permission::create(['name' => 'add contract', 'display' => 30]);
            Permission::create(['name' => 'edit contract', 'display' => 30]);
            Permission::create(['name' => 'delete contract', 'display' => 30]);
        }

        // driver permission
        if (!Permission::where('name', '=', 'view driver')->exists()) {
            Permission::create(['name' => 'view driver', 'display' => 40]);
            Permission::create(['name' => 'add driver', 'display' => 40]);
            Permission::create(['name' => 'edit driver', 'display' => 40]);
            Permission::create(['name' => 'delete driver', 'display' => 40]);
            Permission::create(['name' => 'import driver', 'display' => 40]);
            Permission::create(['name' => 'export driver', 'display' => 40]);
        }

        // vehicle-team permission
        if (!Permission::where('name', '=', 'view vehicle_team')->exists()) {
            Permission::create(['name' => 'view vehicle_team', 'display' => 50]);
            Permission::create(['name' => 'add vehicle_team', 'display' => 50]);
            Permission::create(['name' => 'edit vehicle_team', 'display' => 50]);
            Permission::create(['name' => 'delete vehicle_team', 'display' => 50]);
        }

        // vehicle permission
        if (!Permission::where('name', '=', 'view vehicle')->exists()) {
            Permission::create(['name' => 'view vehicle', 'display' => 60]);
            Permission::create(['name' => 'add vehicle', 'display' => 60]);
            Permission::create(['name' => 'edit vehicle', 'display' => 60]);
            Permission::create(['name' => 'delete vehicle', 'display' => 60]);
        }

        // vehicle-group permission
        if (!Permission::where('name', '=', 'view vehicle_group')->exists()) {
            Permission::create(['name' => 'view vehicle_group', 'display' => 70]);
            Permission::create(['name' => 'add vehicle_group', 'display' => 70]);
            Permission::create(['name' => 'edit vehicle_group', 'display' => 70]);
            Permission::create(['name' => 'delete vehicle_group', 'display' => 70]);
        }

        // admin permission
        if (!Permission::where('name', '=', 'view admin')->exists()) {
            Permission::create(['name' => 'view admin', 'display' => 80]);
            Permission::create(['name' => 'add admin', 'display' => 80]);
            Permission::create(['name' => 'edit admin', 'display' => 80]);
            Permission::create(['name' => 'delete admin', 'display' => 80]);
        }

        // contact permission
        if (!Permission::where('name', '=', 'view contact')->exists()) {
            Permission::create(['name' => 'view contact', 'display' => 90]);
            Permission::create(['name' => 'add contact', 'display' => 90]);
            Permission::create(['name' => 'edit contact', 'display' => 90]);
            Permission::create(['name' => 'delete contact', 'display' => 90]);
        }

        // role permission
        if (!Permission::where('name', '=', 'view role')->exists()) {
            Permission::create(['name' => 'view role', 'display' => 100]);
            Permission::create(['name' => 'add role', 'display' => 100]);
            Permission::create(['name' => 'edit role', 'display' => 100]);
            Permission::create(['name' => 'delete role', 'display' => 100]);
        }

        // receipt-payment permission
        if (!Permission::where('name', '=', 'view receipt_payment')->exists()) {
            Permission::create(['name' => 'view receipt_payment', 'display' => 110]);
            Permission::create(['name' => 'add receipt_payment', 'display' => 110]);
            Permission::create(['name' => 'edit receipt_payment', 'display' => 110]);
            Permission::create(['name' => 'delete receipt_payment', 'display' => 110]);
        }

        // contract-type permission
        if (!Permission::where('name', '=', 'view contract_type')->exists()) {
            Permission::create(['name' => 'view contract_type', 'display' => 120]);
            Permission::create(['name' => 'add contract_type', 'display' => 120]);
            Permission::create(['name' => 'edit contract_type', 'display' => 120]);
            Permission::create(['name' => 'delete contract_type', 'display' => 120]);
        }

        // good-type permission
        if (!Permission::where('name', '=', 'view goods_type')->exists()) {
            Permission::create(['name' => 'view goods_type', 'display' => 130]);
            Permission::create(['name' => 'add goods_type', 'display' => 130]);
            Permission::create(['name' => 'edit goods_type', 'display' => 130]);
            Permission::create(['name' => 'delete goods_type', 'display' => 130]);
        }

        // good-unit permission
        if (!Permission::where('name', '=', 'view goods_unit')->exists()) {
            Permission::create(['name' => 'view goods_unit', 'display' => 140]);
            Permission::create(['name' => 'add goods_unit', 'display' => 140]);
            Permission::create(['name' => 'edit goods_unit', 'display' => 140]);
            Permission::create(['name' => 'delete goods_unit', 'display' => 140]);
        }

        // currency permission
        if (!Permission::where('name', '=', 'view currency')->exists()) {
            Permission::create(['name' => 'view currency', 'display' => 150]);
            Permission::create(['name' => 'add currency', 'display' => 150]);
            Permission::create(['name' => 'edit currency', 'display' => 150]);
            Permission::create(['name' => 'delete currency', 'display' => 150]);
        }

        // location permission
        if (!Permission::where('name', '=', 'view location')->exists()) {
            Permission::create(['name' => 'view location', 'display' => 160]);
            Permission::create(['name' => 'add location', 'display' => 160]);
            Permission::create(['name' => 'edit location', 'display' => 160]);
            Permission::create(['name' => 'delete location', 'display' => 160]);
        }

        // province permission
        // if (!Permission::where('name', '=', 'view province')->exists()) {
        //     Permission::create(['name' => 'view province', 'display' => 170]);
        //     Permission::create(['name' => 'add province', 'display' => 170]);
        //     Permission::create(['name' => 'edit province', 'display' => 170]);
        //     Permission::create(['name' => 'delete province', 'display' => 170]);
        // }

        // // district permission
        // if (!Permission::where('name', '=', 'view district')->exists()) {
        //     Permission::create(['name' => 'view district', 'display' => 180]);
        //     Permission::create(['name' => 'add district', 'display' => 180]);
        //     Permission::create(['name' => 'edit district', 'display' => 180]);
        //     Permission::create(['name' => 'delete district', 'display' => 180]);
        // }

        // // ward permission
        // if (!Permission::where('name', '=', 'view ward')->exists()) {
        //     Permission::create(['name' => 'view ward', 'display' => 190]);
        //     Permission::create(['name' => 'add ward', 'display' => 190]);
        //     Permission::create(['name' => 'edit ward', 'display' => 190]);
        //     Permission::create(['name' => 'delete ward', 'display' => 190]);
        // }

        // alert-log permission
        if (!Permission::where('name', '=', 'view alert_log')->exists()) {
            Permission::create(['name' => 'view alert_log', 'display' => 200]);
            Permission::create(['name' => 'add alert_log', 'display' => 200]);
            Permission::create(['name' => 'edit alert_log', 'display' => 200]);
            Permission::create(['name' => 'delete alert_log', 'display' => 200]);
        }

        // driver-config-file permission
        if (!Permission::where('name', '=', 'view driver_config_file')->exists()) {
            Permission::create(['name' => 'view driver_config_file', 'display' => 210]);
            Permission::create(['name' => 'add driver_config_file', 'display' => 210]);
            Permission::create(['name' => 'edit driver_config_file', 'display' => 210]);
            Permission::create(['name' => 'delete driver_config_file', 'display' => 210]);
        }

        // vehicle-config-file permission
        if (!Permission::where('name', '=', 'view vehicle_config_file')->exists()) {
            Permission::create(['name' => 'view vehicle_config_file', 'display' => 220]);
            Permission::create(['name' => 'add vehicle_config_file', 'display' => 220]);
            Permission::create(['name' => 'edit vehicle_config_file', 'display' => 220]);
            Permission::create(['name' => 'delete vehicle_config_file', 'display' => 220]);
        }

        // vehicle-config-specification permission
        if (!Permission::where('name', '=', 'view vehicle_config_specification')->exists()) {
            Permission::create(['name' => 'view vehicle_config_specification', 'display' => 230]);
            Permission::create(['name' => 'add vehicle_config_specification', 'display' => 230]);
            Permission::create(['name' => 'edit vehicle_config_specification', 'display' => 230]);
            Permission::create(['name' => 'delete vehicle_config_specification', 'display' => 230]);
        }

        // system-code-config permission
        if (!Permission::where('name', '=', 'view system_code_config')->exists()) {
            Permission::create(['name' => 'view system_code_config', 'display' => 240]);
            Permission::create(['name' => 'add system_code_config', 'display' => 240]);
            Permission::create(['name' => 'edit system_code_config', 'display' => 240]);
            Permission::create(['name' => 'delete system_code_config', 'display' => 240]);
        }

        // create role and assign created permissions
        if (!Role::where('name', '=', 'super-admin')->exists()) {
            $role = Role::create(['title' => 'Super Admin', 'name' => 'super-admin']);
            $role->givePermissionTo(Permission::all());
        }
    }
}
