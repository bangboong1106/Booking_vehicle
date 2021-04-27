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
 * Class ReportCustomerController
 * @package App\Http\Controllers\Backend
 */
class ReportCustomerController extends BackendController
{

    public function __construct(ReportScheduleRepository $repository)
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($repository);
        $this->setMenu('report');
        $this->setTitle('Báo cáo doanh thu theo khách hàng');
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
        $customerGroupIDs = empty(Request::get('CustomerGroupIDs')) ? null : explode(',', Request::get('CustomerGroupIDs'));
        $customerIDs = empty(Request::get('CustomerIDs')) ? null : explode(',', Request::get('CustomerIDs'));
        $fromDate = Request::get('FromDate');
        $toDate = Request::get('ToDate');
        $dayCondition = Request::get('DayCondition');

        $datas = $this->getRepository()->reportCustomer($dayCondition, $fromDate, $toDate, $customerIDs, $customerGroupIDs);
        $summary = null;
        if (!empty($datas) && count($datas) > 0) {
            $summary = Arr::last($datas);
            unset($datas[count($datas) - 1]);
        }
        $results = json_encode([
            "data" => $datas,
            "summary" => [
                'total_customer' => !empty($datas) ? count($datas) : 0,
                'total_order' => $summary ? $summary['order_number'] : 0,
                'total_revenue' => $summary ? $summary['revenue'] : 0,
                'total_cost' => $summary ? $summary['cost'] : 0,
                'total_profit' => $summary ? $summary['profit'] : 0
            ]
        ]);
        return $results;
    }
}
