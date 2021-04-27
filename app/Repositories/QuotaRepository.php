<?php

namespace App\Repositories;

use App\Model\Entities\Quota;
use App\Repositories\Base\CustomRepository;
use App\Validators\QuotaValidator;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use stdClass;

class QuotaRepository extends CustomRepository
{
    function model()
    {
        return Quota::class;
    }

    public function validator()
    {
        return QuotaValidator::class;
    }

    public function getQuotaByLocationsAndVehicleGroup($location_ids, $vehicle_group_id, $quotaId = null)
    {
        if (!$location_ids) {
            return null;
        }

        $query = ['location_ids_eq' => $location_ids];
        if (!empty($quotaId)) {
            $query['vehicle_group_id_eq'] = $vehicle_group_id;
        }
        if (!empty($quotaId)) {
            $query['id_neq'] = $quotaId;
        }
        return $this->search($query)->first();
    }

    public function getQuotaByLocation(
        $location_destination_id,
        $location_arrival_id,
        $vehicle_group_id,
        $location_destination_group_id,
        $location_arrival_group_id
    ) {
        $locationDestinationKey = 'location_destination_id';
        $locationArrivalKey = 'location_arrival_id';
        $locationDestinationValue = $location_destination_id;
        $locationArrivalValue = $location_arrival_id;
        if ($location_destination_group_id && $location_destination_group_id != 0) {
            $locationDestinationKey = 'location_destination_group_id';
            $locationDestinationValue = $location_destination_group_id;
        }
        if ($location_arrival_group_id && $location_arrival_group_id != 0) {
            $locationArrivalKey = 'location_arrival_group_id';
            $locationArrivalValue = $location_arrival_group_id;
        }

        if ($location_destination_id && $location_arrival_id && $vehicle_group_id) {
            return $this->search([
                $locationDestinationKey . '_eq' => $locationDestinationValue,
                $locationArrivalKey . '_eq' => $locationArrivalValue,
                'vehicle_group_id_eq' => $vehicle_group_id
            ])->first();
        } else if ($location_destination_id && $location_arrival_id) {
            return $this->search([
                $locationDestinationKey . '_eq' => $locationDestinationValue,
                $locationArrivalKey . '_eq' => $locationArrivalValue,
            ])->first();
        }
        return null;
    }

    public function getQuotaByCode($code)
    {
        if (!$code)
            return null;
        return  $this->search([
            'quota_code_eq' => $code
        ])->first();
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'name_of_location_destination_id' => [
                'filter_field' => 'ld.title',
            ],
            'name_of_location_destination_group_id' => [
                'filter_field' => 'ldg.title',
            ],
            'name_of_location_arrival_id' => [
                'filter_field' => 'la.title',
            ],
            'name_of_location_arrival_group_id' => [
                'filter_field' => 'lag.title',
            ],
            'name_of_vehicle_group_id' => [
                'filter_field' => 'vg.name',
            ],
        ];
    }

    // Hàm build câu lệnh đơn hàng khách hàng
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('m_vehicle_group as vg', $this->getTableName() . '.vehicle_group_id', '=', 'vg.id')
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('location_group as ldg', $this->getTableName() . '.location_destination_group_id', '=', 'ldg.id')
            ->leftJoin('location_group as lag', $this->getTableName() . '.location_arrival_group_id', '=', 'lag.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        $columns = [
            '*',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            'ldg.title as name_of_location_destination_group_id',
            'lag.title as name_of_location_arrival_group_id',
            'vg.name as name_of_vehicle_group_id',
        ];

        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->with(['costs', 'vehicleGroup', 'locations'])->paginate($limit, ['*'], 'page', 1);
    }

    protected function _withRelations($query)
    {
        return $query->with(['vehicleGroup']);
    }

    // Lấy dữ liệu Bảng định mức để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByID($id)
    {
        $table_name = $this->getTableName();
        $query = \DB::table($table_name . ' as q')
            ->leftJoin('m_vehicle_group as vg', 'q.vehicle_group_id', '=', 'vg.id')
            ->where([
                ['q.id', '=', $id],
            ]);
        $data = $query->get([
            'q.*',
            'vg.name as vehicle_group'
        ])->first();

        $data = $data == null ? new stdClass() : $data;

        $data->list_locations = \DB::table($table_name . ' as q')
            ->leftJoin('quota_location as dv', 'dv.quota_id', '=', 'q.id')
            ->join('locations as l', 'l.id', '=', 'dv.location_id')
            ->where('q.id', '=', $id)
            ->get([
                'l.title as loaction_title'
            ]);

        $data->list_costs = \DB::table($table_name . ' as q')
            ->leftJoin('quota_cost as dv', 'dv.quota_id', '=', 'q.id')
            ->where('q.id', '=', $id)
            ->get([
                'dv.*'
            ]);
        return $data;
    }
}
