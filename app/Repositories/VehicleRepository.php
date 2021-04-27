<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Vehicle;
use App\Repositories\Base\CustomRepository;
use App\Repositories\Traits\VehicleExportTrait;

use App\Validators\VehicleValidator;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class VehicleRepository extends CustomRepository
{
    use VehicleExportTrait;

    protected $_fieldsSearch = ['reg_no', 'current_location'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Vehicle::class;
    }

    public function validator()
    {
        return VehicleValidator::class;
    }

    public function getCode()
    {
        return 'reg_no';
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with('vehicleGroup', 'vehicleGeneralInfo');
    }

    public function getListForHistory($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));

        $queryBuilder = $this->search($query);

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function getVehicleById($id)
    {
        if (empty($id)) {
            return [];
        }
        return Vehicle::query()->find($id);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'vgi.max_fuel',
            'vgi.max_fuel_with_goods',
            'vgi.register_year',
            'vgi.brand',
            'vgi.category_of_barrel',
            'vgi.weight_lifting_system',
            'vg.name as name_of_group_id',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (driver.full_name),\'</span>\') SEPARATOR \'\'), \'</div>\') as drivers_name ')
        ];
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'drivers_name' => [
                'filter_field' => 'driver.full_name',
            ],
            'name_of_gps_company_id' => [
                'filter_field' => 'vg.name',
            ],
            'name_of_group_id' => [
                'filter_field' => 'vg.name',
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ],
            'name_of_partner_id' => [
                'filter_field' => 'p.full_name',
            ],
            'max_fuel' => [
                'filter_field' => 'vgi.max_fuel',
            ],
            'max_fuel_with_goods' => [
                'filter_field' => 'vgi.max_fuel_with_goods',
            ],
            'register_year' => [
                'filter_field' => 'vgi.register_year',
            ],
            'brand' => [
                'filter_field' => 'vgi.brand',
            ],
            'category_of_barrel' => [
                'filter_field' => 'vgi.category_of_barrel',
            ],
            'weight_lifting_system' => [
                'filter_field' => 'vgi.weight_lifting_system',
            ],
        ];
    }

    // Hàm build câu lệnh tài xế
    // CreatedBy nlhoang 04/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('vehicle_general_info as vgi', $this->getTableName() . '.id', '=', 'vgi.vehicle_id')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('drivers as driver', 'driver.id', '=', 'dv.driver_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', $this->getTableName() . '.group_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('partner as p', $this->getTableName() . '.partner_id', '=', 'p.id')
            ->groupBy('vehicle.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function updateGpsVehicle($vehiclePlate, $latitude, $longitude, $address)
    {
        $vehicle = $this->search([
            'vehicle_plate_eq' => $vehiclePlate,
            'del_flag_eq' => 0
        ])->get();
        if (isset($vehicle) && isset($vehicle[0])) {
            $vehicle[0]->latitude = $latitude;
            $vehicle[0]->longitude = $longitude;
            $vehicle[0]->current_location = $address;
            $vehicle[0]->save();
        }
    }

    public function updateGpsVehicleByGpsId($gpsId, $latitude, $longitude, $address)
    {
        $vehicle = $this->search([
            'gps_id_eq' => $gpsId,
            'del_flag_eq' => 0
        ])->get();
        if (isset($vehicle) && isset($vehicle[0])) {
            $vehicle[0]->latitude = $latitude;
            $vehicle[0]->longitude = $longitude;
            $vehicle[0]->current_location = $address;
            $vehicle[0]->save();
        }
    }

    public function updateGpsVehicleByVehiclePlate($vehiclePlate, $latitude, $longitude, $address)
    {
        $vehicle = $this->search([
            'vehicle_plate_eq' => $vehiclePlate,
            'del_flag_eq' => 0
        ])->get();
        if (isset($vehicle) && isset($vehicle[0])) {
            $vehicle[0]->latitude = $latitude;
            $vehicle[0]->longitude = $longitude;
            $vehicle[0]->current_location = $address;
            $vehicle[0]->save();
        }
    }

    /**
     * @param $id
     * @param string $q
     * @return mixed
     */
    public function getVehiclesForSelectByUserId($id, $q = '')
    {
        return Vehicle::query()->select(
            "vehicle.id",
            "vehicle.reg_no as title",
            "vehicle.current_location",
            "vehicle.volume",
            "vehicle.weight",
            "vehicle.length",
            "vehicle.width",
            "vehicle.height"
        )
            ->join('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
            ->join('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
            ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('vehicle.reg_no', 'LIKE', '%' . $q . '%')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $id)
            ->where('vehicle.active', '=', '1')
            ->groupBy('vehicle.id')
            ->orderBy('reg_no', 'asc')
            ->paginate(1000);
    }

    // Lấy ra toàn bộ xe như phân biệt đâu là xe có quyền
    // CreatedBy nlhoang 18/08/2020
    public function getListWithPermission($id)
    {
        $permission_list = Vehicle::query()->select(
            "vehicle.id",
            "vehicle.reg_no",
            DB::raw('1 as is_permission')
        )
            ->join('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
            ->join('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
            ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $id)
            ->where('vehicle.active', '=', '1')
            ->groupBy('vehicle.id')
            ->orderBy('reg_no', 'asc')
            ->get();
        $not_permission_list = Vehicle::whereNotIn('id', $permission_list->pluck('id')->toArray())
            ->select(
                "vehicle.id",
                "vehicle.reg_no",
                DB::raw('0 as is_permission')
            )
            ->orderBy('reg_no', 'asc')
            ->get();
        $merged = $permission_list->merge($not_permission_list);
        return $merged;
    }


    /**
     * @return LengthAwarePaginator
     */
    public function getVehiclesByUserId()
    {
        $user = Auth::user();
        $query = Vehicle::query()->select(
            "vehicle.id",
            "vehicle.reg_no as title",
            "vehicle.current_location",
            "vehicle.volume",
            "vehicle.weight",
            "vehicle.length",
            "vehicle.width",
            "vehicle.height",
            'vehicle.latitude',
            'vehicle.longitude',
            'vehicle.vehicle_plate',
            'vehicle.status',
            'vehicle.partner_id'
        );

        if ($user->role == 'partner') {
            $query->where($this->getTableName() . '.partner_id', $user->partner_id);
        }

        $query->groupBy('vehicle.id')
        ->orderBy('reg_no', 'asc');
      
        return $query->paginate(1000);
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'asc',
            'sort_field' => 'reg_no'
        ])->pluck('reg_no', 'id');
    }

    /**
     * @param $data
     * @param Vehicle $entity
     * @return mixed
     */
    public function processExtendData($data, $entity)
    {
        if (empty($data)) {
            return $entity;
        }

        $entity->setExtendData(['vehicleGeneralInfo' => isset($data['vehicleGeneralInfo']) ? $data['vehicleGeneralInfo'] : []]);
        return $entity;
    }

    public function getVehicleByStatus($status)
    {
        $vehicle = $this->search([
            'status_in' => $status,
            'del_flag_eq' => 0
        ])->get();
        return $vehicle;
    }

    public function updateVehicleStatus()
    {
        // Cập nhật trạng thái xe đang thực hiện.
        // Tat ca cac xe có đơn hàng đang vận chuyển
        $query = "SELECT v2.id as id
                     FROM vehicle as v2
                       LEFT JOIN routes ON v2.id = routes.vehicle_id
                       LEFT JOIN orders
                         ON routes.id = orders.route_id
                     WHERE v2.del_flag = '0'
                           AND orders.del_flag = '0'
                           AND routes.del_flag = '0'
                           AND orders.status = '4'
                     GROUP BY v2.id";
        $out = DB::select($query);
        $ids = array();
        if (isset($out) && 0 < sizeof($out)) {
            foreach ($out as $item) {
                $ids[] = $item->id;
            }
            DB::table('vehicle')
                ->whereIn('id', $ids)
                ->update(array('status' => 2));

            // Cập nhật trạng thái xe đang trống, chờ việc
            DB::table('vehicle')
                ->whereNotIn('id', $ids)
                ->where('status', '!=', '4')
                ->where('del_flag', '=', '0')
                ->update(['status' => '1']);
        } else {
            // Cập nhật tất cả trạng thái xe đang trống, chờ việc
            DB::table('vehicle')
                ->where('status', '!=', '4')
                ->where('del_flag', '=', '0')
                ->update(['status' => '1']);
        }
    }

    public function getDriversById($id)
    {
        return DB::table('vehicle as v')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'v.id')
            ->leftJoin('drivers as d', 'd.id', '=', 'dv.driver_id')
            ->groupBy('v.id')
            ->where('v.id', '=', $id)
            ->first(DB::raw('GROUP_CONCAT(DISTINCT(d.full_name) SEPARATOR \' , \') as drivers_name'));
    }

    public function getVehicleByGpsCompanyAndGpsId($gpsCompanyId, $gpsId)
    {
        return $this->search([
            'gps_id_eq' => $gpsId,
            'gps_company_id_eq' => $gpsCompanyId
        ])->first();
    }

    public function getVehicleByGpsCompanyAndVehiclePlate($gpsCompanyId, $vehiclePlate)
    {
        return $this->search([
            'vehicle_plate_eq' => $vehiclePlate,
            'gps_company_id_eq' => $gpsCompanyId
        ])->first();
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->with(['vehicleGeneralInfo', 'drivers', 'vehicleGroup'])->paginate($limit, ['*'], 'page', 1);
    }

    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->listDriver = isset($data['listDriver']) ? $data['listDriver'] : [];
        return $entity;
    }

    public function getVehicleAndDriverList()
    {
        $query = DB::table('vehicle')
            ->leftJoin('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
            ->leftJoin('drivers', 'driver_vehicle.driver_id', '=', 'drivers.id')
            ->where([
                ['vehicle.del_flag', '=', '0'],
                ['drivers.del_flag', '=', '0'],
                ['driver_vehicle.del_flag', '=', '0']
            ]);
        return $query->select([
            'vehicle.id as vehicle_id', 'vehicle.reg_no as reg_no', 'drivers.id as driver_id',
            DB::raw('concat(drivers.code,\'|\',drivers.full_name) as driver_name')
        ])->orderBy('vehicle.id', 'desc')->orderBy('driver_vehicle.id', 'desc')->get();
    }

    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByIDs($ids, $parameter, $template)
    {
        return $this->getDataForTemplateByID($ids, $parameter, $template);
    }

    // Tìm ra các xe của tài xế mà ko sử dụng GPS gắn với xe
    public function getVehicleWithoutGPSByDriverId($driverId)
    {
        $query = DB::table('vehicle as v')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'v.id')
            ->groupBy('v.id')
            ->where('dv.driver_id', '=', $driverId)
            ->whereNull('v.gps_id')
            ->whereNull('v.gps_company_id');
        return $query->select(['v.id'])->pluck('id');
    }

    // Lấy ra danh sách xe theo ng dùng
    public function getItemsByUserID($all, $q, $userID, $partnerId, $userRole)
    {
        $query = Vehicle::select(
            "vehicle.id",
            "vehicle.reg_no as title",
            "vehicle.current_location",
            "vehicle.volume",
            "vehicle.weight",
            "vehicle.length",
            "vehicle.width",
            "vehicle.height"
        );
        if (empty($all)) {
            if ($userRole == 'partner') {
                // $query->join('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
                //     ->join('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
                //     ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
                //     ->where('admin_users_vehicle_teams.admin_user_id', '=', $userID);
            }
        }

        if (!empty($partnerId)) {
            $query->where('vehicle.partner_id', '=', $partnerId);
        }

        $query->where('vehicle.reg_no', 'LIKE', '%' . $q . '%')
            ->where('vehicle.active', '=', 1)
            ->groupBy('vehicle.id')
            ->orderBy('reg_no', 'asc');

        return $query->paginate(20);
    }

    // Lấy thông tin xe tại thời điểm hiện tại
    public function getCurrentItem($id)
    {
        $vehicle = Vehicle::find($id);
        $driverNames = $this->getDriversById($vehicle->id);
        $vehicle->primaryDriverName = $driverNames->drivers_name;

        $query = DB::table('routes as r')
            ->leftJoin('orders as o', 'o.route_id', '=', 'r.id')
            ->where([
                ['r.vehicle_id', '=', $vehicle->id],
                ['r.route_status', '=', 0],
                ['r.del_flag', '=', 0],
                ['o.status', '=', 4],
                ['o.del_flag', '=', 0],
            ])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw('now() >= CONCAT(o.ETD_date ,\' \' ,o.ETD_time)')
                        ->whereRaw('now() <= CONCAT(o.ETA_date ,\' \' ,o.ETA_time)');
                })->orWhere(function ($q) {
                    $q->whereRaw('now() >= CONCAT(o.ETD_date_reality ,\' \' ,o.ETD_time_reality)')
                        ->whereNull('o.ETA_date_reality')
                        ->whereNull('o.ETA_time_reality');
                });
            })
            ->select([
                DB::raw("SUM(o.volume) as volume"),
                DB::raw("SUM(o.weight) as weight"),
            ])->first();
        $vehicle->current = $query;
        return $vehicle;
    }

    public function getItemsForSheet($userID)
    {
        return Vehicle::where('del_flag', '=', 0)
            ->orderBy('reg_no')
            ->get(['reg_no as name', 'id']);
    }

    // Lấy thông tin chuyến xe hiện tại của xe
    // CreatedBy nlhoang 05/10/2020
    public function getCurrentItemByID($vehicleID)
    {
        $vehicle = Vehicle::find($vehicleID);

        $routeQuery = DB::table('routes as r')
            ->where([
                ['r.vehicle_id', '=', $vehicle->id],
                ['r.route_status', '=', 0],
                ['r.del_flag', '=', 0]
            ])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw('now() >= CONCAT(r.ETD_date ,\' \' ,r.ETD_time)')
                        ->whereRaw('now() <= CONCAT(r.ETA_date ,\' \' ,r.ETA_time)');
                })->orWhere(function ($q) {
                    $q->whereRaw('now() >= CONCAT(r.ETD_date_reality ,\' \' ,r.ETD_time_reality)')
                        ->whereNull('r.ETA_date_reality')
                        ->whereNull('r.ETA_time_reality');
                });
            })
            ->select([
                'r.id',
                DB::raw("r.capacity_weight_ratio"),
                DB::raw("r.capacity_volume_ratio"),
            ])->first();

        if ($routeQuery) {
            $orderQuery = DB::table('orders as o')
                ->where([
                    ['o.status', '=', 4],
                    ['o.del_flag', '=', 0],
                    ['o.route_id', '=', $routeQuery->id],
                ])
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereRaw('now() >= CONCAT(o.ETD_date ,\' \' ,o.ETD_time)')
                            ->whereRaw('now() <= CONCAT(o.ETA_date ,\' \' ,o.ETA_time)');
                    })->orWhere(function ($q) {
                        $q->whereRaw('now() >= CONCAT(o.ETD_date_reality ,\' \' ,o.ETD_time_reality)')
                            ->whereNull('o.ETA_date_reality')
                            ->whereNull('o.ETA_time_reality');
                    });
                })
                ->select([
                    DB::raw("COUNT(o.id) as count_order"),
                    DB::raw("SUM(o.volume) as volume"),
                    DB::raw("SUM(o.weight) as weight"),
                ])->first();
            $vehicle->current = new stdClass();
            $vehicle->current->capacity_weight_ratio = $routeQuery->capacity_weight_ratio;
            $vehicle->current->capacity_volume_ratio = $routeQuery->capacity_volume_ratio;
            $vehicle->current->count_order = $orderQuery->count_order;
            $vehicle->current->volume = $orderQuery->volume;
            $vehicle->current->weight = $orderQuery->weight;
        }

        return $vehicle;
    }


    public function getVehicleByPartnerId($partnerId)
    {
        return $this->search([
            'partner_id_eq' => $partnerId,
        ])->with('vehicleGroup')->get();
    }
}
