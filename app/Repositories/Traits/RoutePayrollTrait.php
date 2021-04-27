<?php

namespace App\Repositories\Traits;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use stdClass;


trait RoutePayrollTrait
{

    // Tính lương tài xế theo từng đơn hàng
    //CreatedBy nlhoang 22/07/2020
    public function calculatePayroll($params)
    {
        $orders = $this->_getOrdersForCalcPrice($params);
        $payroll = DB::table('payroll')->find($params['payrollId']);
        $results = [];
        if ($payroll) {
            $results = $this->_calcPayrollByVehicleGroup($payroll, $orders);
        }
        return $results;
    }

    // Tính giá theo thuộc tính của xe
    // CreatedBy nlhoang 03/07/2020
    private function _calcPayrollByVehicleGroup($payroll, $orders)
    {
        return $this->_calcPayrollByVehicle($payroll, $orders, 'vehicle_group_id', 'equal');
    }

    // Tính giá theo thuộc tính của xe
    // CreatedBy nlhoang 03/07/2020
    private function _calcPayrollByVehicle($payroll, $orders, $attribute, $operator = null)
    {
        $results = [];
        if (count($orders) == 0) return $results;
        $columns = [
            'price',
            $attribute
        ];
        foreach ($orders as $item) {
            $result = [
                'order_id' => $item->{'order_id'},
                'amount' => 0,
            ];
            $result[$attribute] = $item->{$attribute};
            $infos = DB::table('payroll_formula as pqf')
                ->where('pqf.del_flag', '=', 0)
                ->where('pqf.payroll_id', '=', $payroll->{'id'})
                ->where('pqf.location_group_destination_id', '=', $item->{'location_group_destination_id'})
                ->where('pqf.location_group_arrival_id', '=', $item->{'location_group_arrival_id'})
                ->get($columns);
            $amounts = [];
            foreach ($infos as $info) {
                $tmp = $this->calcAmount($item, $info, $attribute, 'equal');
                $amounts[] = $tmp["amount"];
            }
            $result['amount'] = collect($amounts)->max();
            $results[] = $result;
        }

        return $results;
    }
}
