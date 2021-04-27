<?php

namespace App\Repositories;

use App\Model\Entities\VehicleDailyReport;
use App\Repositories\Base\CustomRepository;
use App\Validators\VehicleDailyReportValidator;

class VehicleDailyReportRepository extends CustomRepository
{
    function model()
    {
        return VehicleDailyReport::class;
    }

    public function validator()
    {
        return VehicleDailyReportValidator::class;
    }
}