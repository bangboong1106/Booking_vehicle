<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Role;
use App\Model\Entities\Permission;

class PartnerRoleSeeder extends CustomSeeder
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

        parent::givepermissionToRole($this->_setRolePermissionPartner());
    }

    public function _setRolePermissionPartner()
    {
        return [
            [
                'name' => 'partner-management',
                'title' => 'Quản lý',
                'group' => 'partner',
                'permissions' => [
                    '* partner_order', '* partner_driver', '* partner_vehicle', '* partner_vehicle_team', '* partner', '* partner_admin'
                ]
            ],
            [
                'name' => 'partner-driver',
                'title' => 'Tài xế',
                'group' => 'partner',
                'permissions' => [
                    '* partner_order'
                ]
            ],
        ];
    }
}
