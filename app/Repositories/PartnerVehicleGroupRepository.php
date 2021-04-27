<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\VehicleGroup;
use App\Repositories\Base\CustomRepository;
use App\Validators\VehicleGroupValidator;
use Illuminate\Support\Facades\DB;

class PartnerVehicleGroupRepository extends CustomRepository
{
    function model()
    {
        return VehicleGroup::class;
    }

    public function validator()
    {
        return VehicleGroupValidator::class;
    }

    public function getVehicleGroupWithCode($code)
    {
        if ($code)
            return $this->search([
                'code_eq' => $code
            ])->first();
        return null;
    }

    public function getVehicleGroupWithId($id)
    {
        if ($id)
            return $this->search([
                'id_eq' => $id
            ])->first();
        return null;
    }

    public function _isUsed($id)
    {
        $quota = DB::table('quota')
            ->where('quota.del_flag', '=', '0')
            ->where('quota.vehicle_group_id', '=', $id)
            ->first();
        if ($quota)
            return true;

        return false;
    }
}
