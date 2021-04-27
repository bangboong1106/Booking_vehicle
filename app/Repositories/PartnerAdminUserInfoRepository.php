<?php

namespace App\Repositories;

use App\Model\Entities\AdminUserInfo;
use App\Repositories\Base\CustomRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class PartnerAdminUserInfoRepository extends CustomRepository
{
    protected $_fieldsSearch = ['email', 'role'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return AdminUserInfo::class;
    }

    public function validator()
    {
        return \App\Validators\AdminUserInfo::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('avatarFile', 'roles');
    }

    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->listRole = isset($data['listRole']) ? $data['listRole'] : [];
        $entity->listVehicleTeam = isset($data['listVehicleTeam']) ? $data['listVehicleTeam'] : [];
        $entity->listCustomerGroup = isset($data['listCustomerGroup']) ? $data['listCustomerGroup'] : [];
        return $entity;
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $queryBuilder = $this->search($query);
        $queryBuilder->whereIn('role', ['partner'])
            ->where('partner_id', Auth::user()->partner_id);
        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    /**
     * @param array $query
     * @param array $columns
     * @return Builder
     */
    public function search($query = array(), $columns = [])
    {
        if (empty($query['role_eq'])) {
            return parent::search($query, $columns);
        }

        $roleId = $query['role_eq'];
        unset($query['role_eq']);
        /** @var Builder $query */
        $query = parent::search($query, $columns);
        $query->leftJoin('model_has_roles', 'admin_users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', $roleId);

        return $query;
    }

    /**
     * @param $data
     * @param AdminUserInfo $entity
     * @return mixed
     */
    public function processExtendData($data, $entity)
    {
        if (empty($data)) {
            return $entity;
        }

        $extendData = [
            'current_roles' => isset($data['current_roles']) ? $data['current_roles'] : null,
            'roles' => isset($data['listRole']) ? $data['listRole'] : null,
            'current_vehicle_teams' => isset($data['current_vehicle_teams']) ? $data['current_vehicle_teams'] : [],
            'vehicle_teams' => isset($data['listVehicleTeam']) ? $data['listVehicleTeam'] : [],
        ];

        $entity->setExtendData($extendData);
        return $entity;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getListDeletedForBackend($query)
    {
        isset($query['sort_field']) ? null : $query['sort_field'] = 'upd_date';

        $perPage = backendPaginate('per_page.' . $this->getModel()->getAlias(), config('pagination.backend.per_page.default'));
        $queryBuilder = $this->search($query)->onlyTrashed()->where('role', '=', 'partner');

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function getAdminUserByUserName($username)
    {
        $query = DB::table('admin_users')
            ->where('admin_users.del_flag', '=', 0)
            ->where('admin_users.role', '=', 'partner')
            ->where('admin_users.username', '=', $username);

        $result = $query->get()->first();
        return $result;
    }

    public function getListForSelect()
    {
        return DB::table('admin_users')
            ->where('admin_users.del_flag', '=', 0)
            ->where('admin_users.role', '=', 'partner')
            ->orderBy('username', 'asc')
            ->get(['id', DB::raw('CONCAT(username, "|", COALESCE(full_name,"")) AS title, id')])
            ->pluck('title', 'id');;
    }

    public function getItemsForSheet($userID)
    {
        return AdminUserInfo::where('del_flag', '=', 0)
            ->where('role', '=', 'admin')
            ->orderBy('username')
            ->get([
                DB::raw('username as name'),
                'id'
            ]);
    }
}
