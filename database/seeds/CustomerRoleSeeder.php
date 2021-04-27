<?php

use Illuminate\Database\Seeder;

class CustomerRoleSeeder extends CustomSeeder
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

        parent::givepermissionToRole($this->_setRolePermissionClient());
    }

    public function _setRolePermissionClient()
    {
        return [
            [
                'name' => 'customer-management',
                'title' => 'Quản lý',
                'group' => 'default-customer',
                'permissions' => [
                    '* customer_calendar', '* customer_order_customer', '* customer_order', '* customer_route', '* customer_client', '* customer_staff', '* customer_role', '* customer_location',
                    '* customer_location_group', '* customer_location_type', '* customer_goods', '* customer_goods_unit'
                ]
            ],
            [
                'name' => 'client',
                'title' => 'Khách hàng',
                'group' => 'default-customer',
                'permissions' => [
                    '* customer_order_customer'
                ]
            ],
            [
                'name' => 'sale',
                'title' => 'Nhân viên kinh doanh',
                'group' => 'default-customer',
                'permissions' => [
                    '* customer_order_customer'
                ]
            ],
            [
                'name' => 'warehouse-keeper',
                'title' => 'Nhân viên thủ kho',
                'group' => 'default-customer',
                'permissions' => [
                    'view customer_order_customer', '* customer_order', '* customer_route', '* customer_location', '* customer_location_group', '* customer_location_type'
                ]
            ],
        ];
    }
}
