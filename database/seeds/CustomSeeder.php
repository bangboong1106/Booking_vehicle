<?php

use Illuminate\Database\Seeder;
use App\Model\Entities\Role;
use App\Model\Entities\Permission;

class CustomSeeder extends Seeder
{
    public function givepermissionToRole($listRole)
    {
        foreach ($listRole as $value) {
            $role = Role::firstOrCreate(['name' => $value['name'], 'title' => $value['title'], 'group' => $value['group']]);

            $listPermission = [];
            foreach ($value['permissions'] as $permission) {
                if (count(explode(' ', $permission)) < 2) {
                    continue;
                }

                $action = explode(' ', $permission)[0];
                $per = explode(' ', $permission)[1];

                if ($action == '*') {
                    $arr = Permission::where('name', 'like', '% '.$per)->select('name')->get()->toArray();
                    $listPermission = array_merge($listPermission, $arr);
                } else {
                    $listPermission[] = $permission;
                }
            }
            $role->givePermissionTo($listPermission);
        }
    }
}
