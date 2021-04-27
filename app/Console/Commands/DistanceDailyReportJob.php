<?php

namespace App\Console\Commands;

use App\Common\HttpCode;
use App\Model\Entities\DistanceDailyReport;
use App\Repositories\DistanceDailyReportRepository;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Log;

class DistanceDailyReportJob extends Command
{
    protected $signature = 'DistanceDailyReportJob:jobs';

    protected $description = 'DistanceDailyReportJob';

    protected $_distanceDailyReport;

    /**
     * @return DistanceDailyReportRepository
     */
    public function getDistanceDailyReportRepository()
    {
        return $this->_distanceDailyReport;
    }

    /**
     * @param mixed DistanceDailyReportRepository
     */
    public function setDistanceDailyReportRepository($distanceDailyReport): void
    {
        $this->_distanceDailyReport = $distanceDailyReport;
    }

    public function __construct(DistanceDailyReportRepository $distanceDailyReportRepository)
    {
        parent::__construct();
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        try {
//
//            $processDate = date('Y-m-d', strtotime("-1 days"));
//            $upTimeVehicle = $this->getDistanceDailyReportRepository()->getUpTimeVehicleDaily($processDate);
//
//            foreach ($upTimeVehicle as $item) {
//                $r = new Request();
//                $r['from'] = $item->ETD_reality;
//                $r['to'] = $item->ETA_reality;
//                $r['gpsId'] = $item->gps_id;
//
//                $out = app('App\Http\Controllers\Api\RouteApiController')->getDailyReportsByVehicle($r)->getData();
//
//                if (null != $out && $out->errorCode == HttpCode::EC_OK && 0 < sizeof($out->data)) {
//                    $obj = $out->data[0];
//
//                    $entity = new DistanceDailyReport();
//                    $entity->route_id = $item->gps_id;
//                    $entity->vehicle_id = $item->vehicle_id;
//                    $entity->gps_id = $item->gps_id;
//                    $entity->reg_no = $item->reg_no;
//                    $entity->vehicle_plate = $item->vehicle_plate;
//                    $entity->date = $processDate;
//                    $entity->distance = $item->distance;
//                    $entity->distance_with_goods = $obj->Distance;
//
//                    $entity->save();
//                }
//            }
//        } catch (\Exception $e) {
//            logError($e);
//        }
    }
}
