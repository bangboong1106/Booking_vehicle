<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\Role;
use App\Repositories\CustomerRoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;

/**
 * Class RoleController
 * @package App\Http\Controllers\Backend
 */
class CustomerRoleController extends BackendController
{
    protected $_permissionRepository;
    protected $_modelHasRolesRepository;

    /**
     * @return PermissionRepository
     */
    public function getPermissionRepository()
    {
        return $this->_permissionRepository;
    }

    /**
     * @param mixed $permissionRepository
     */
    public function setPermissionRepository($permissionRepository): void
    {
        $this->_permissionRepository = $permissionRepository;
    }


    /**
     * RoleController constructor.
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(CustomerRoleRepository $customerRoleRepository, PermissionRepository $permissionRepository)
    {
        parent::__construct();
        $this->setRepository($customerRoleRepository);
        $this->setPermissionRepository($permissionRepository);
        $this->setBackUrlDefault('customerprole.index');
        $this->setConfirmRoute('customer-role.confirm');
        $this->setMenu('management');
        $this->setTitle(trans('models.role.name'));
    }

    protected function _prepareEdit($id = null)
    {
        parent::_prepareEdit($id);
        $this->_prepareContent();

        $entity = $this->getEntity();
        if ($entity->name === 'super-admin') {
            return $this->_to('customer-role.index');
        }

        return $this;
    }

    protected function _prepareCreate()
    {
        parent::_prepareCreate();
        $this->_prepareContent();
        return $this;
    }

    protected function _prepareContent()
    {
        /** @var Role $entity */
        $entity = $this->getEntity();
        if (isset($entity->permissionList)) {
            return;
        }

        $availablePermissions = [];
        if (isset($entity->id)) {
            $availablePermissions = $entity->permissions->pluck('name')->toArray();
        }
        $entity->permissionList = $availablePermissions;
        $this->setEntity($entity);
    }

    /**
     * @param Role $entity
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $permissionList = $entity->permissionList;
        $entity->guard_name = 'admins';
        $entity->syncPermissions($permissionList);
    }

    protected function _findEntityForStore()
    {
        /** @var Role $entity */
        $entity = parent::_findEntityForStore();
        $entity->fill(['guard_name' => config('auth.defaults.guard')]);
        return $entity;
    }

    protected function _prepareShow($id)
    {
        $parent = parent::_prepareShow($id);
        $this->_prepareContent();
        $this->_getPermissionList();
        $this->_getUserList($id);
        $this->setViewData([
            'show_history' => true,
        ]);
        return $parent;
    }

    protected function _prepareConfirm()
    {
        $this->_getPermissionList();
        $this->setViewData([
            'show_history' => false,
        ]);
        return parent::_prepareConfirm();
    }

    protected function _prepareForm()
    {
        $this->_getPermissionList();
    }

    protected function _prepareDuplicate($id = null)
    {
        parent::_prepareDuplicate($id);
        $this->_prepareContent();
        return $this;
    }

    protected function _getPermissionList()
    {
        $permissionOrder = ['view', 'add', 'edit', 'delete', 'import', 'export', 'lock', 'unlock'];

        $groups = ['calendar', 'order', 'client', 'category'];

        $permissions = $this->getPermissionRepository()->search([ 'web_eq' => 'customer', 'sort_type' => 'asc', 'del_flag' => 0])->get();

        $newPermissions = [];

        foreach ($groups as $group) {
            $temp = [];
            foreach ($permissions as $per) {
                $action = explode(' ', $per->name)[0];
                $key = explode(' ', $per->name)[1];

                foreach ($permissionOrder as $value) {
                    if ($action == $value && $group == $per->group) {
                        $temp[$per->display." ".$key][$action] = $per;
                        break;
                    }
                }
            }
            ksort($temp);
            $newPermissions[$group] = $temp;
        }

        $permissions = $newPermissions;

        $this->setViewData([
            'permissions' => $permissions,
            'permissionOrder' => $permissionOrder,
            'groups' => $groups
        ]);
    }

    protected function _getUserList($id)
    {
        $userList = $this->getRepository()->getUserList($id);
        $this->setViewData(['userList' => $userList]);
    }
}
