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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


/**
 * Class ReportVehicleController
 * @package App\Http\Controllers\Backend
 */
class ReportVehicleController extends BackendController
{

    public function __construct(ReportScheduleRepository $repository)
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($repository);
        $this->setMenu('report');
        $this->setTitle('Báo cáo năng suất xe');
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
        $vehicleIDs = empty(Request::get('VehicleIDs')) ? null : explode(",", Request::get('VehicleIDs'));
        $vehicleTeamIDs = empty(Request::get('VehicleTeamIDs')) ? null : explode(",", Request::get('VehicleTeamIDs'));
        $fromDate = Request::get('FromDate');
        $toDate = Request::get('ToDate');
        $dayCondition = Request::get('DayCondition');
        $displayType = Request::get('DisplayType');
        $partnerIds = empty(Request::get('PartnerIDs')) ? [] : explode(",", Request::get('PartnerIDs'));

        // $this->setViewData([
        //     'entities' => $this->getRepository()->reportVehiclePerformance($vehicleTeamIDs, $vehicleIDs, $dayCondition, $fromDate, $toDate)
        // ]);
        // $view = $displayType == 2 ? 'backend.report_vehicle._list_quality' : 'backend.report_vehicle._list_performance';

        // $this->setData(['content' => $this->render($view)->render()]);
        // return $this->renderJson();

        $data = $this->getRepository()->reportVehiclePerformance($vehicleTeamIDs, $vehicleIDs, $dayCondition, $fromDate, $toDate, $partnerIds);

        $results = json_encode([
            "data" => $data,
        ]);
        return $results;
    }
}
