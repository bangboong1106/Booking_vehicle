<?php

namespace App\Http\Controllers\Backend;

use App\Common\HttpCode;
use App\Exports\ReportExport;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\DistanceDailyReport;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\ReportDataRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


/**
 * Class ReportJourneyController
 * @package App\Http\Controllers\Backend
 */
class ReportJourneyController extends BackendController
{
    protected $distanceDailyReportRepository;

    /**
     * @return DistanceDailyReportRepository
     */
    public function getDistanceDailyReportRepository()
    {
        return $this->distanceDailyReportRepository;
    }

    /**
     * @param mixed $distanceDailyReportRepository
     */
    public function setDistanceDailyReportRepository($distanceDailyReportRepository): void
    {
        $this->distanceDailyReportRepository = $distanceDailyReportRepository;
    }

    public function __construct(ReportDataRepository $demoReportDataRepository, DistanceDailyReportRepository $distanceDailyReportRepository)
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($demoReportDataRepository);
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
        $this->setMenu('report');
//        $this->setTitle(trans('models.report.attributes.title'));
    }

    public function index()
    {
        $this->setTitle('Báo cáo hành trình của xe');
        return $this->render();
    }

    public function reportVehicleDistance()
    {
        $vehicleIDs = empty(Request::get('VehicleIDs')) ? null : Request::get('VehicleIDs');
        $vehicleTeamIDs = empty(Request::get('VehicleTeamIDs')) ? null : Request::get('VehicleTeamIDs');
        $driverIDs = empty(Request::get('DriverIDs')) ? null : Request::get('DriverIDs');
        $fromDate = Request::get('FromDate');
        $toDate = Request::get('ToDate');
        $partnerId = $this->getCurrentUser()->partner_id;

        /* $vehicleIDs = '16';
         $fromDate = '2020-01-29';
         $toDate = '2020-02-02';*/

        $sql = 'call proc_report_vehicle_by_distance(?,?,?,?,?,?,?)';
        $query = DB::select($sql, array(0, $vehicleIDs, $vehicleTeamIDs, $driverIDs, $fromDate, $toDate,$partnerId));

        $summaryQuery = DB::select($sql, array(1, $vehicleIDs, $vehicleTeamIDs, $driverIDs, $fromDate, $toDate,$partnerId));

        $distance = 0;
        $distanceWithGoods = 0;
        $distanceWithoutGoods = 0;
        foreach ($summaryQuery as $item) {
            $distance += isset($item->distance) ? $item->distance : 0;
            $distanceWithGoods += isset($item->distance_with_goods) ? $item->distance_with_goods : 0;
            $distanceWithoutGoods += isset($item->distance_without_goods) ? $item->distance_without_goods : 0;
        }

        $results = json_encode(["data" => $query,
            "summary" => $summaryQuery,
            "distance" => $distance,
            "distanceWithGoods" => $distanceWithGoods,
            "distanceWithoutGoods" => $distanceWithoutGoods], JSON_NUMERIC_CHECK);
        return $results;
    }

    public function syncDistanceReportDaily()
    {
        try {
            $fromDate = new DateTime(Request::get('FromDate'));
            $toDate = new DateTime(Request::get('ToDate'));

            for ($i = $fromDate; $i <= $toDate; $i->modify('+1 day')) {
                app('App\Http\Controllers\Api\RouteApiController')->scheduleDistanceReportDaily($i->format("Y-m-d"));
            }
            return json_encode([
                "error_code" => "100",
                "message" => "Đồng bộ dữ liệu thành công ."
            ]);
        } catch (\Exception $e) {
            logError($e);
            return json_encode([
                "error_code" => "101",
                "message" => "Xảy ra lỗi . Vui lòng thực hiện lại."
            ]);
        }

    }
}
