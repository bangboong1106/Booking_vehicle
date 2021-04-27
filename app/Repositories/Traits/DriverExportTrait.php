<?php

namespace App\Repositories\Traits;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use stdClass;


trait DriverExportTrait
{
    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getDataForTemplateByID($ids, $parameter, $template)
    {
        $table_name = 'drivers';
        $query = DB::table($table_name)
            ->leftJoin('admin_users as ad', $table_name . '.user_id', '=', 'ad.id')
            ->whereIn($table_name . '.id', $ids);

        $sexType = convertCaseWhenQuery(config('system.sex'), $table_name . '.sex', 'sex_type');

        $data = $query->get([
            $table_name . '.*',
            DB::raw($sexType),
            'ad.username as account_name'
        ]);

        $drivers = DB::table('drivers')
            ->leftJoin('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
            ->leftJoin('vehicle_team', 'driver_vehicle_team.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('driver_vehicle', 'drivers.id', '=', 'driver_vehicle.driver_id')
            ->leftJoin('vehicle', 'driver_vehicle.vehicle_id', '=', 'vehicle.id')
            ->whereIn('drivers.id', $ids)
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
            })->get([
                'drivers.id',
                'vehicle.reg_no as name'
            ]);

        $vehicleTeams = DB::table('drivers')
            ->leftJoin('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
            ->leftJoin('vehicle_team', 'driver_vehicle_team.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('driver_vehicle', 'drivers.id', '=', 'driver_vehicle.driver_id')
            ->whereIn('drivers.id', $ids)
            ->get([
                'drivers.id',
                'vehicle_team.name'
            ]);

        //Lấy danh sách cost chuyến theo tài xế
        $costTmp = DB::table('routes as r')
            ->leftJoin('route_cost as rc', 'rc.route_id', 'r.id')
            ->leftJoin('m_receipt_payment as rp', 'rp.id', 'rc.receipt_payment_id')
            ->where([
                ['r.del_flag', '=', '0'],
                ['rc.del_flag', '=', '0'],
                ['r.is_approved', '=', '1'],
                ['rc.amount', '>', '0'],
                ['r.route_status', '=', '1'],
                ['r.ETA_date_reality', '>=', $parameter->startDate],
                ['r.ETA_date_reality', '<=', $parameter->endDate]
            ])
            ->whereIn('r.driver_id', $ids);

        $costsData = $costTmp->groupBy('r.driver_id', 'rc.receipt_payment_id')
            ->get([
                'r.driver_id',
                'rc.receipt_payment_id',
                'rp.name',
                DB::raw('SUM(rc.amount) AS amount')
            ]);

        if ($template->is_print_empty_cost == 1) {
            $costs = DB::table('m_receipt_payment as rp')->orderBy('rp.sort_order')->get();
        } else {
            $costs = $costTmp->select('rp.id', 'rp.name')->orderBy('rp.sort_order')->distinct()->get();
        }

        $results = collect([]);
        foreach ($data as $item) {
            $array_filter = [];
            foreach ($drivers as $key => $var) {
                if (($var->{'id'} === $item->{'id'})) {
                    $array_filter[] = $var;
                }
            }

            $item->list_vehicles = collect($array_filter)->pluck('name')->unique()->implode('name', '| ');

            $vehicle_team_filter = [];
            foreach ($vehicleTeams as $key => $var) {
                if (($var->{'id'} === $item->{'id'})) {
                    $vehicle_team_filter[] = $var;
                }
            }

            $item->list_vehicle_teams = collect($vehicle_team_filter)->pluck('name')->unique()->implode('name', '| ');


            $listCosts = [];
            $filteredCost = $costsData->filter(function ($value) use ($item) {
                return $value->driver_id == $item->id;
            });
            $totalCost = 0;
            foreach ($costs as $costVar) {
                $costAmount = 0;
                foreach ($filteredCost as $cost) {
                    if ($costVar->name == $cost->name) {
                        $costAmount = $cost->amount;
                        $totalCost += $cost->amount;
                    }
                }
                $temp = new \stdClass();
                $temp->{'payment'} = $costVar->name;
                $temp->{'amount'} = $costAmount;
                $listCosts[] = $temp;
            }

            $item->list_costs = $listCosts;
            $item->total_cost = $totalCost;

            $results[] = $item;
        }

        return $results;
    }
}
