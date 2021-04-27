<?php

namespace App\Repositories;

use App\Model\Entities\Role;
use App\Repositories\Base\CustomRepository;
use App\Validators\RoleValidator;
use Illuminate\Support\Facades\DB;

class RoleRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Role::class;
    }

    public function validator()
    {
        return RoleValidator::class;
    }

    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        if (empty($data['permissionList'])) {
            return $entity;
        }
        $entity->permissionList = $data['permissionList'];
        return $entity;
    }

    public function getAllRole()
    {
        return $this->search(['group_eq' => 'admin'])->get();
    }

    public function getAllRolePartner()
    {
        return $this->search(['group_eq' => 'partner'])->get();
    }

    public function getListRole()
    {
        return $this->search()->get()->pluck('title', 'name');
    }

    public function getListForBackend($query)
    {
        $query['name_neq'] = 'super-admin';
        $query['group_eq'] = 'admin';
        return parent::getListForBackend($query);
    }

    public function getUserList($roleId)
    {
        if (!$roleId)
            return null;
        $userList = DB::table('model_has_roles as mr')
            ->leftjoin('admin_users as au', 'au.id', '=', 'mr.model_id')
            ->where([
                ['mr.role_id', '=', $roleId],
                ['au.del_flag', '=', '0'],
            ])
            ->get(['au.id', 'au.username']);
        return $userList;
    }
}