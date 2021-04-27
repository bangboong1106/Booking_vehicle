<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Role;
use App\Model\Entities\Permission;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Permission::where(['name' => 'view calendar'])->exists()) {
            Permission::create(['name' => 'view calendar', 'display' => 1, 'group' => 'calendar', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_order_customer'])->exists()) {
            Permission::create(['name' => 'view customer_order_customer', 'display' => 1, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_order_customer', 'display' => 1, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_order_customer', 'display' => 1, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_order_customer', 'display' => 1, 'group' => 'order', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_order'])->exists()) {
            Permission::create(['name' => 'view customer_order', 'display' => 2, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_order', 'display' => 2, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_order', 'display' => 2, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_order', 'display' => 2, 'group' => 'order', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_route'])->exists()) {
            Permission::create(['name' => 'view customer_route', 'display' => 3, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_route', 'display' => 3, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_route', 'display' => 3, 'group' => 'order', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_route', 'display' => 3, 'group' => 'order', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_client'])->exists()) {
            Permission::create(['name' => 'view customer_client', 'display' => 1, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_client', 'display' => 1, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_client', 'display' => 1, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_client', 'display' => 1, 'group' => 'customer', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_staff'])->exists()) {
            Permission::create(['name' => 'view customer_staff', 'display' => 2, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_staff', 'display' => 2, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_staff', 'display' => 2, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_staff', 'display' => 2, 'group' => 'customer', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_role'])->exists()) {
            Permission::create(['name' => 'view customer_role', 'display' => 3, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_role', 'display' => 3, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_role', 'display' => 3, 'group' => 'customer', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_role', 'display' => 3, 'group' => 'customer', 'web' => 'customer']);
        }
        
        if (!Permission::where(['name' => 'view customer_location'])->exists()) {
            Permission::create(['name' => 'view customer_location', 'display' => 1, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_location', 'display' => 1, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_location', 'display' => 1, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_location', 'display' => 1, 'group' => 'category', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_location_group'])->exists()) {
            Permission::create(['name' => 'view customer_location_group', 'display' => 2, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_location_group', 'display' => 2, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_location_group', 'display' => 2, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_location_group', 'display' => 2, 'group' => 'category', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_location_type'])->exists()) {
            Permission::create(['name' => 'view customer_location_type', 'display' => 3, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_location_type', 'display' => 3, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_location_type', 'display' => 3, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_location_type', 'display' => 3, 'group' => 'category', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_goods'])->exists()) {
            Permission::create(['name' => 'view customer_goods', 'display' => 4, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_goods', 'display' => 4, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_goods', 'display' => 4, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_goods', 'display' => 4, 'group' => 'category', 'web' => 'customer']);
        }

        if (!Permission::where(['name' => 'view customer_goods_unit'])->exists()) {
            Permission::create(['name' => 'view customer_goods_unit', 'display' => 5, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'add customer_goods_unit', 'display' => 5, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'edit customer_goods_unit', 'display' => 5, 'group' => 'category', 'web' => 'customer']);
            Permission::create(['name' => 'delete customer_goods_unit', 'display' => 5, 'group' => 'category', 'web' => 'customer']);
        }
    
        $roleCustomer = Role::firstOrCreate(['name' => 'customer-super-admin', 'title' => 'Chủ hàng full quyền']);
        $roleCustomer->givePermissionTo(Permission::where('web', '=', 'customer')->get());
    }
}
