<?php

namespace App\Http\Controllers\Backend;

use App\Common\HttpCode;
use App\Exports\ReportExport;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\DistanceDailyReport;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\ReportDataRepository;
use App\Repositories\ReportScheduleRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


/**
 * Class ReportVehicleTeamController
 * @package App\Http\Controllers\Backend
 */
class ReportVehicleTeamController extends BackendController
{

    public function __construct(ReportScheduleRepository $reportScheduleRepository)
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($reportScheduleRepository);
        $this->setMenu('report');
        $this->setTitle('Báo cáo hoạt động theo đội tài xế và tài xế');
    }

    public function index()
    {
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
        $this->setViewData([
            'dayCondition' => $dayCondition
        ]);
        return $this->render();
    }

    public function report()
    {
        $driverIDs = empty(Request::get('DriverIDs')) ? null : explode(",", Request::get('DriverIDs'));
        $vehicleTeamIDs = empty(Request::get('VehicleTeamIDs')) ? null : explode(",", Request::get('VehicleTeamIDs'));
        $customerIDs = empty(Request::get('CustomerIDs')) ? null : explode(",", Request::get('CustomerIDs'));
        $partnerIDs = empty(Request::get('PartnerIDs')) ? null : explode(",", Request::get('PartnerIDs'));

        $fromDate = Request::get('FromDate');
        $toDate = Request::get('ToDate');
        $dayCondition = Request::get('DayCondition');

        $data = $this->getRepository()->reportVehicleTeam($dayCondition, $fromDate, $toDate, $driverIDs, $vehicleTeamIDs, $customerIDs, $partnerIDs);

        $results = json_encode([
            "data" => $data,
        ]);
        return $results;
    }
}
