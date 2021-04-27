<?php

namespace App\Repositories;

use App\Model\Entities\RepairTicket;
use App\Repositories\Base\CustomRepository;
use DB;
use Illuminate\Support\Str;

class RepairTicketRepository extends CustomRepository
{
    function model()
    {
        return RepairTicket::class;
    }

    public function validator()
    {
        return \App\Validators\RepairTicketValidator::class;
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            'v.reg_no as code_of_vehicle',
            'd.code as code_of_driver',
            'd.full_name as name_of_driver',
        ];
        return $this->search($query, $columns)->with(['driver', 'vehicle', 'items'])->paginate($limit, ['*'], 'page', 1);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 07/09/2020
    protected function getKeyValue()
    {
        return [
            'name_of_driver_id' => [
                'filter_field' => 'd.full_name',
            ],
            'name_of_vehicle_id' => [
                'filter_field' => 'v.reg_no',
            ],
        ];
    }

    // Hàm build câu lệnh
    // CreatedBy nlhoang 07/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('drivers as d', $this->getTableName() . '.driver_id', '=', 'd.id')
            ->leftJoin('vehicle as v', $this->getTableName() . '.vehicle_id', '=', 'v.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    /**
     * Lấy phụ tùng sắp đến hạn sửa chữa
     * @param $groupBy : 1(theo xe) ; 2( theo xe - tài xế)
     * @return mixed
     */
    public function getRepairAccessoryNotice($groupBy)
    {
        $dateDiff = env("DAY_NUM_ACCESSORY_REPAIR_WARNING", 5);
        $sql = "SELECT rt.driver_id, v.reg_no, a.name accessory_name, MAX(rti.next_repair_date) next_repair_date , v.partner_id
                FROM repair_ticket rt
                INNER JOIN repair_ticket_item rti ON rti.repair_ticket_id = rt.id
                INNER JOIN accessory a ON a.id = rti.accessory_id
                INNER JOIN vehicle v ON v.id = rt.vehicle_id
                WHERE rt.del_flag = 0 AND rti.del_flag = 0 AND a.del_flag = 0 AND v.del_flag = 0 ";
        if ($groupBy == 1) {
            $sql .= " GROUP BY rt.vehicle_id, rti.accessory_id ";
        } else if ($groupBy == 2) {
            $sql .= " GROUP BY rt.driver_id, rt.vehicle_id, rti.accessory_id ";
        }
        $sql .= " HAVING datediff(MAX(rti.next_repair_date), NOW()) <= " . $dateDiff;

        return DB::select($sql);
    }
}
