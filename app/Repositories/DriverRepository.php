<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Driver;
use App\Model\Entities\Routes;
use App\Repositories\Base\CustomRepository;
use App\Repositories\Traits\DriverExportTrait;

use DB;
use Exception;
use Illuminate\Support\Str;

class DriverRepository extends CustomRepository
{
    use DriverExportTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Driver::class;
    }

    public function validator()
    {
        return \App\Validators\DriverValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('adminUser', 'vehicleTeams');
    }

    public function getFullInfoDriverWithUserId($driverId)
    {
        return $this->search([
            'user_id_eq' => $driverId
        ])->with('adminUser')->first();
    }

    public function getDriverByUserId($userId)
    {
        return $this->search([
            'user_id_eq' => $userId,
        ])->first();
    }

    public function getListForHistory($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));

        $queryBuilder = $this->search($query);

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function countOrderByDriverIdAndStatus($driverId)
    {
        $queryCount = DB::table('orders')
            ->where([
                ['orders.primary_driver_id', '=', $driverId],
                ['orders.del_flag', '=', '0']
            ])
            ->groupBy('orders.status')
            ->selectRaw('count(*) as count, orders.status')
            ->get();
        return $queryCount;
    }

    public function getOrdersByDriverIdAndStatus($driverId, $request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $sorts = $request['sort'];

        $queryCount = DB::table('orders')
            ->leftjoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
            ->whereIn('orders.status', $status)
            ->where([
                ['orders.primary_driver_id', '=', $driverId],
                ['orders.del_flag', '=', '0'],
            ]);

        $ordersQuery = DB::table('orders')
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftjoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->leftJoin('customer', 'orders.customer_id', 'customer.id')
            ->where([
                ['orders.primary_driver_id', '=', $driverId],
                ['orders.del_flag', '=', '0'],
            ])
            ->whereIn('orders.status', $status);

        if (!empty($textSearch)) {
            $queryCount->where(function ($query) use ($textSearch) {
                $query->where('orders.customer_name', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.order_code', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.customer_mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.id', 'like', '%' . $textSearch . '%');
            });
            $ordersQuery->where(function ($query) use ($textSearch) {
                $query->where('orders.customer_name', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.order_code', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.customer_mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('orders.id', 'like', '%' . $textSearch . '%');
            });
        }
        if (!empty($fromDate)) {
            $queryCount->whereDate('orders.ETD_date', '>=', $fromDate);
            $ordersQuery->whereDate('orders.ETD_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $queryCount->whereDate('orders.ETA_date', '<=', $toDate);
            $ordersQuery->whereDate('orders.ETA_date', '<=', $toDate);
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $ordersQuery->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $queryCount->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $ordersQuery->skip($offset)->take($pageSize);
        $orders = $ordersQuery->get([
            'orders.*',
            'orders.id as order_id',
            'customer.full_name as customer_full_name',
            'location_destination.full_address as location_destination',
            'location_arrival.full_address as location_arrival',
            'location_destination.latitude as location_des_lat',
            'location_destination.longitude as location_des_long',
            'location_arrival.latitude as location_arr_lat',
            'location_arrival.longitude as location_arr_long',
            'drivers.full_name',
            'vehicle.reg_no'
        ]);
        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'orders' => $orders
        ];

        return $result;
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            DB::raw('GROUP_CONCAT(distinct CONCAT(vt.code,"|",vt.name) SEPARATOR \',\') as vehicle_team_codes')
        ];

        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (vehicle.reg_no),\'</span>\') SEPARATOR \'\'), \'</div>\') as vehicles_reg_no '),
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (vt.name),\'</span>\') SEPARATOR \'\'), \'</div>\') as vehicle_team_names ')

        ];
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser', 'adminUser']);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'admin_user_username' => [
                'filter_field' => 'ad.username',
            ],
            'username' => [
                'filter_field' => 'ad.username',
            ],
            'full_name' => [
                'filter_field' => 'drivers.full_name',
            ],
            'name_of_vehicle_team_id' => [
                'filter_field' => 'vt.name',
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ],
            'name_of_partner_id' => [
                'filter_field' => 'p.full_name',
            ]
        ];
    }

    // Hàm build câu lệnh tài xế
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('admin_users as ad', $this->getTableName() . '.user_id', '=', 'ad.id')
            ->leftJoin('driver_vehicle_team as dvt', $this->getTableName() . '.id', '=', 'dvt.driver_id')
            ->leftJoin('vehicle_team as vt', 'dvt.vehicle_team_id', '=', 'vt.id')
            ->leftJoin('driver_vehicle as dv', 'dv.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle as vehicle', 'vehicle.id', '=', 'dv.vehicle_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('partner as p', $this->getTableName() . '.partner_id', '=', 'p.id')
            ->groupBy('drivers.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function getDriverByVehicleId($vehicleId)
    {
        if (empty($vehicleId)) {
            return [];
        }

        $vehicle = $this->search()
            ->where('dv.vehicle_id', '=', $vehicleId)->first();

        return empty($vehicle) ? [] : $vehicle->toArray();
    }

    public function getAvailableDriversForUser($userId, $partnerId)
    {
        return Driver::select("*")
            ->where(function ($query) {
                $query->where('full_name', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . request('q') . '%');
            })
            ->orderBy('full_name', 'asc')
            ->leftJoin('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
            ->leftJoin('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $userId)
            ->where('drivers.partner_id', '=', $partnerId)
            ->where('drivers.active', '=', '1')
            ->groupBy('drivers.id')->get();
    }

    // Lấy ra toàn bộ xe như phân biệt đâu là xe có quyền
    // CreatedBy nlhoang 18/08/2020
    public function getListWithPermission($userId)
    {
        $permission_list = Driver::query()->select(
            "drivers.*",
            DB::raw('1 as is_permission')
        )
            ->orderBy('full_name', 'asc')
            ->leftJoin('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
            ->leftJoin('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $userId)
            ->where('drivers.active', '=', '1')
            ->groupBy('drivers.id')->get();
        $not_permission_list = Driver::whereNotIn('id', $permission_list->pluck('id')->toArray())
            ->select(
                "drivers.*",
                DB::raw('0 as is_permission')
            )
            ->orderBy('full_name', 'asc')
            ->get();
        $merged = $permission_list->merge($not_permission_list);
        return $merged;
    }

    public function getDriverList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $sorts = $request['sort'];

        $queryCount = DB::table('drivers')
            ->where([
                ['drivers.del_flag', '=', '0'],
                ['drivers.active', '=', '1']
            ]);

        $driversQuery = DB::table('drivers')
            ->leftjoin('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
            ->leftjoin('vehicle_team', 'driver_vehicle_team.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftjoin('driver_vehicle', 'drivers.id', '=', 'driver_vehicle.driver_id')
            ->leftjoin('vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicle.id')
            ->where([
                ['drivers.del_flag', '=', '0'],
                ['drivers.active', '=', '1']
            ])
            ->where(function ($query) {
                $query->where('vehicle_team.del_flag', '=', '0')
                    ->orWhereNull('vehicle_team.del_flag');
            })
            ->where(function ($query) {
                $query->where('driver_vehicle.del_flag', '=', '0')
                    ->orWhereNull('driver_vehicle.del_flag');
            })
            ->where(function ($query) {
                $query->where('vehicle.del_flag', '=', '0')
                    ->orWhereNull('vehicle.del_flag');
            });

        if (!empty($textSearch)) {
            $queryCount->where(function ($query) use ($textSearch) {
                $query->where('drivers.code', 'like', '%' . $textSearch . '%')
                    ->orWhere('drivers.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('drivers.full_name', 'like', '%' . $textSearch . '%');
            });
            $driversQuery->where(function ($query) use ($textSearch) {
                $query->where('drivers.code', 'like', '%' . $textSearch . '%')
                    ->orWhere('drivers.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('drivers.full_name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $driversQuery->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $queryCount->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $driversQuery->skip($offset)->take($pageSize);
        $driversQuery->groupBy(['vehicle_team.name', 'drivers.code']);
        $drivers = $driversQuery->get(
            [
                'drivers.code', 'drivers.user_id', 'drivers.mobile_no', 'drivers.full_name', 'drivers.address', 'drivers.sex', 'drivers.note', 'drivers.driver_license',
                'vehicle_team.name as vehicle_team_name', 'vehicle.reg_no as reg_no'
            ]
        );
        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $drivers
        ];
        return $result;
    }

    public function getListForSelect()
    {
        return Driver::select(DB::raw('CONCAT(code, "|", full_name) AS title, id'))
            ->where('del_flag', '=', 0)
            ->pluck('title', 'id');
    }

    /**
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    public function findFirstOrNew($data, $forUpdate = false)
    {
        if (isset($data['create_account']) && $data['create_account'] == 0 && isset($data['adminUser'])) {
            unset($data['adminUser']);
        }

        return parent::findFirstOrNew($data, $forUpdate);
    }

    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByIDs($ids, $parameter, $template)
    {
        return $this->getDataForTemplateByID($ids, $parameter, $template);
    }

    public function getItemsByUserID($all, $q, $user_id, $partner_id, $user_role, $vehicle_id)
    {
        $query = Driver::select("drivers.id", "drivers.full_name as title", "drivers.mobile_no")
            ->where(function ($query) use ($q) {
                $query->where('full_name', 'LIKE', '%' . $q . '%')
                    ->orWhere('code', 'LIKE', '%' . $q . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . $q . '%');
            })
            ->orderBy('full_name', 'asc');
        if (!empty($partner_id))
            $query
                ->where('drivers.partner_id', '=', $partner_id);

        if ($vehicle_id > 0) {
            $query->leftjoin('driver_vehicle', 'drivers.id', '=', 'driver_vehicle.driver_id')
                ->where('driver_vehicle.vehicle_id', $vehicle_id);
        }

        if (empty($all)) {
            if ($user_role == 'partner') {
                // $query
                //     ->join('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
                //     ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
                //     ->where('admin_users_vehicle_teams.admin_user_id', '=', $user_id);
            }
        }

        $data = $query->groupBy('drivers.id')->orderBy('ready_status', 'desc')->paginate(20);
        return $data;
    }

    public function getItemsForSheet($userID)
    {
        return Driver::where('del_flag', '=', 0)
            ->orderBy('full_name')
            ->get([
                DB::raw('CONCAT(code,"|", full_name) as name'),
                'id'
            ]);
    }

    public function getDriverByPartnerId($partnerId)
    {
        return $this->search([
            'partner_id_eq' => $partnerId,
        ])->get();
    }
}
