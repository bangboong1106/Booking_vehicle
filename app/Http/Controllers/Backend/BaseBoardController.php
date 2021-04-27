<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Helpers\Url;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\VehicleGroup;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\DriverRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\SystemConfigRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\DriverVehicleRepository;
use App\Repositories\MergeOrderRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\CustomerRepository;


use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\DataTables;
use Exception;

class BaseBoardController extends BackendController
{
    public $_adminRepository = null;

    public $provinceRepository = null;
    protected $_locationRepository;
    protected $_orderRepository;
    protected $_driverRepository;
    protected $_orderHistoryRepository;
    protected $_systemConfigRepository;
    protected $_vehicleRepository;
    protected $_driverVehicleRepository;
    protected $_mergeOrderRepository;
    protected $_routeRepository;
    protected $_orderCustomerRepository;
    protected $_customerRepository;
    protected $_isRaw = true;
    protected $_notificationService;
    protected $_partnerRepository;

    /**
     * @return null
     */
    public function getAdminRepository()
    {
        return $this->_adminRepository;
    }

    /**
     * @param null $adminRepository
     */
    public function setAdminRepository($adminRepository)
    {
        $this->_adminRepository = $adminRepository;
    }

    /**
     * @return mixed
     */
    public function getLocationRepository()
    {
        return $this->_locationRepository;
    }

    /**
     * @param mixed $locationRepository
     */
    public function setLocationRepository($locationRepository): void
    {
        $this->_locationRepository = $locationRepository;
    }

    /**
     * @return OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    /**
     * @param mixed $orderRepository
     */
    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    /**
     * @return mixed
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderHistoryRepository()
    {
        return $this->_orderHistoryRepository;
    }

    /**
     * @param $orderHistoryRepository
     */
    public function setOrderHistoryRepository($orderHistoryRepository): void
    {
        $this->_orderHistoryRepository = $orderHistoryRepository;
    }

    /**
     * @return mixed
     */
    public function getSystemConfigRepository()
    {
        return $this->_systemConfigRepository;
    }

    /**
     * @param $systemConfigRepository
     */
    public function setSystemConfigRepository($systemConfigRepository): void
    {
        $this->_systemConfigRepository = $systemConfigRepository;
    }

    /**
     * @return mixed
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return mixed
     */
    public function getRouteRepository()
    {
        return $this->_routeRepository;
    }

    /**
     * @param $vehicleRepository
     */
    public function setRouteRepository($routesRepository): void
    {
        $this->_routeRepository = $routesRepository;
    }


    /**
     * @return mixed
     */
    public function getDriverVehicleRepository()
    {
        return $this->_driverVehicleRepository;
    }

    /**
     * @param $driverVehicleRepository
     */
    public function setDriverVehicleRepository($driverVehicleRepository): void
    {
        $this->_driverVehicleRepository = $driverVehicleRepository;
    }

    /**
     * @return mixed
     */
    public function getMergeOrderRepository()
    {
        return $this->_mergeOrderRepository;
    }

    /**
     * @param $mergeOrderRepository
     */
    public function setMergeOrderRepository($mergeOrderRepository): void
    {
        $this->_mergeOrderRepository = $mergeOrderRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderCustomerRepository()
    {
        return $this->_orderCustomerRepository;
    }

    /**
     * @param $orderCustomerRepository
     */
    public function setOrderCustomerRepository($orderCustomerRepository): void
    {
        $this->_orderCustomerRepository = $orderCustomerRepository;
    }

    /**
     * @return mixed
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @return mixed
     */
    public function getNotificationService()
    {
        return $this->_notificationService;
    }

    /**
     * @param mixed $notificationService
     */
    public function setNotificationService($notificationService): void
    {
        $this->_notificationService = $notificationService;
    }

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->_partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->_partnerRepository = $partnerRepository;
    }


    protected $_action_list = '';
    protected $_isEventResize = true;
    protected $_isDrag = true;
    protected $_isEditable = false;

    public function __construct(
        AdminUserInfoRepository $adminUserInfoRepository,
        LocationRepository $locationRepository,
        OrderRepository $orderRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        SystemConfigRepository $systemConfigRepository,
        VehicleRepository $vehicleRepository,
        DriverVehicleRepository $driverVehicleRepository,
        RoutesRepository $routesRepository,
        MergeOrderRepository $mergeOrderRepository,
        OrderCustomerRepository $orderCustomerRepository,
        CustomerRepository $customerRepository,
        NotificationService $notificationService,
        PartnerRepository $partnerRepository

    )
    {
        parent::__construct();

        $this->setAdminRepository($adminUserInfoRepository);
        $this->setLocationRepository($locationRepository);
        $this->setOrderRepository($orderRepository);
        $this->setDriverRepository($driverRepository);
        $this->setOrderHistoryRepository($orderHistoryRepository);
        $this->setSystemConfigRepository($systemConfigRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setDriverVehicleRepository($driverVehicleRepository);
        $this->setMergeOrderRepository($mergeOrderRepository);
        $this->setRouteRepository($routesRepository);
        $this->setOrderCustomerRepository($orderCustomerRepository);
        $this->setCustomerRepository($customerRepository);
        $this->setNotificationService($notificationService);
        $this->setPartnerRepository($partnerRepository);

        $this->setMap(true);
        $this->setMenu('board');
    }

    public function index()
    {
        // $this->_checkPermission();
        $this->setTitle(trans('models.' . $this->getModel() . '.name'));
        $this->setViewData([
            'vehicle_groups' => VehicleGroup::getNestedList('name', 'id', ''),
        ]);
        $obj = $this->getSystemConfigRepository()->search()->get();

        $dashboardReload = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.Reload";
            });
        $dashboardVehiclePageSize = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.VehiclePageSize";
            });
        $dashboardNotifyVehicle = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.NotifyVehicle";
            });
        $dashboardViewType = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.ViewType";
            });
        $dashboardGroup = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.Group";
            });
        $viewType = $dashboardViewType->isEmpty() ? 'timelineTwoWeek' : $dashboardViewType->first()->value;

        $calendar = \Calendar::setOptions([
            'defaultView' => $viewType == 'timelineTwoWeek' ? 'timelineDay' : $viewType,
            'textEscape' => false,
            'contentHeight' => 400,
            'aspectRation' => 1.35,
            'droppable' => $this->_isDrag,
            'editable' => $this->_isEditable,
            'locale' => 'vi',
            'dragRevertDuration' => 0,
            'schedulerLicenseKey' => 'CC-Attribution-NonCommercial-NoDerivatives',
            'firstDay' => 1,
            'themeButtonIcons' => true,
            'nextDayThreshold' => '00:00',
            'nowIndicator' => true,
            'scrollTime' => '00:00',
            'timeFormat' => "HH:mm",
            'columnFormat' => [
                'month' => 'dddd',    // Monday, Wednesday, etc
                'week' => 'dddd, MMM dS', // Monday 9/7
                'day' => 'dddd, MMM dS'  // Monday 9/7
            ],
            'lazyFetching' => true,
            'customButtons' => $this->getCustomButtons(),
            'header' => [
                'left' => 'today,prev,next',
                'center' => 'title',
                'right' => $this->_action_list
            ],
            'views' => [
                'customTimelineView' => [
                    'type' => 'timeline',
                    'duration' => ['months' => 1],
                    'slotDuration' => ['days' => 1],
                ],
                'customTimelineWeek' => [
                    'type' => 'timeline',
                    'duration' => ['weeks' => 1],
                    'slotDuration' => ['days' => 1],
                ],
                'customTimelineTwoWeek' => [
                    'type' => 'timeline',
                    'duration' => ['weeks' => 2],
                    'slotDuration' => ['days' => 1],
                ],
                'customTimelineMonth' => [
                    'type' => 'timeline',
                    'duration' => ['months' => 1],
                    'slotDuration' => ['days' => 1],
                ]
            ],
            'resourceAreaWidth' => '150px',
            'resourceColumns' => [
                [
                    'labelText' => $this->getResourceColumn(),
                    'field' => 'title',
                    'width' => '150px'
                ],
            ],
            'displayEventEnd' => true,
            'loading' => 'function( isLoading, view ) { loading(isLoading, view)}',
        ])->setCallbacks($this->getCallbacks());

        $vehicleTeams = $this->getVehicleTeamByUser();
        $orders = !empty($this->getOrderListFunction()) ? $this->{$this->getRepositoryFunction()}()->{$this->getOrderListFunction()}(['per_page' => config('pagination.backend.per_page.order_dashboard')]) : [];
        $partners = $this->getPartnerRepository()->search([])->get();
        $this->setViewData([
            'calendar' => $calendar,
            'locationList' => [],
            'orders' => $orders,
            'dashboardReload' => $dashboardReload->isEmpty() ? 5 : $dashboardReload->first()->value,
            'dashboardVehiclePageSize' => $dashboardVehiclePageSize->isEmpty() ? 50 : $dashboardVehiclePageSize->first()->value,
            'dashboardNotifyVehicle' => $dashboardNotifyVehicle->isEmpty() ? 30 : $dashboardNotifyVehicle->first()->value,
            'dashboardViewType' => $viewType,
            'dashboardGroup' => $dashboardGroup->isEmpty() ? 'order_no' : $dashboardGroup->first()->value,
            'vehicleTeams' => $vehicleTeams,
            'partners' => $partners,
            'backUrlKey' => Url::getBackUrlKey()
        ]);

        return $this->render();
    }

    protected function getResourceColumn()
    {
        return "Số xe";
    }

    // Lấy ra tên hàm để lấy danh sách đơn hàng
    protected function getOrderListFunction()
    {
        return '';
    }

    // Lấy danh sách các nút trên Calendar
    protected function getCustomButtons()
    {
        return [
            'deleteButton' => [
                'icon' => "fa fa fa-trash",
                'id' => 'calendarTrash'
            ],
            'refreshButton' => [
                'icon' => "fa fa fa-refresh",
                'id' => 'calendarRefresh'
            ],
            'exportButton' => [
                'icon' => "fa fa fa-file-pdf-o",
                'id' => 'calendarExport'
            ],
            'fullscreenButton' => [
                'icon' => "fa fa fa-window-maximize",
                'id' => 'calendarFullscreen'
            ],
            'customTwoWeekDate' => [
                'text' => '14 ngày',
                'id' => 'calendarTwoWeek'
            ],
            'customDate' => [
                'text' => 'Tùy chỉnh',
                'icon' => "fa fa fa-cog",
                'id' => 'calendarCustomDate'
            ],
            'hiddenDate' => [
                'text' => 'Tùy chỉnh',
                'icon' => "fa fa fa-hidden",
                'id' => 'calendarHiddenDate'
            ]
        ];
    }

    // Lấy danh sách các hàm callback cần xử lý
    protected function getCallbacks()
    {
        $callbacks = [
            'loading' => 'function( isLoading, view ) { loading(isLoading, view)}',
            'resources' => 'function(callback) {callback(resources);}',
            'eventRender' => 'function (event,element) { eventRender(event, element); }',
            'eventReceive' => 'function (event) { eventReceive(event); }',
            'eventAfterRender' => 'function (event, element, view) { eventAfterRender(event, element, view); }',
            'eventAfterAllRender' => 'function (callback){ eventAfterAllRender(callback);}',
            'resourceRender' => 'function (resource, labelTds, bodyTds) { resourceRender(resource, labelTds, bodyTds); }',
        ];
        if ($this->_isDrag) {
            $callbacks['eventDrop'] = 'function (event, delta, revertFunc, jsEvent, ui, view) { eventDrop(event, delta, revertFunc, jsEvent, ui, view); }';
            $callbacks['eventDragStart'] = 'function (event, jsEvent, ui, view) { eventDragStart(event, jsEvent, ui, view); }';
            $callbacks['eventDragStop'] = 'function (event, jsEvent, ui, view) { eventDragStop(event, jsEvent, ui, view); }';
        }
        if ($this->_isEventResize) {
            $callbacks['eventResize'] = 'function (event, delta, revertFunc, jsEvent, ui, view) { eventResize(event, delta, revertFunc, jsEvent, ui, view);}';
        }
        return $callbacks;
    }

    //Lấy ra thông tin chi tiết chuyến xe
    public function vehicle()
    {
        $id = Request::get('id');
        $vehicle = $this->getVehicleRepository()->getCurrentItem($id);
        return json_encode($vehicle, JSON_NUMERIC_CHECK);
    }

    // Lấy ra danh sách chuyến xe hiển thị lên dashboard
    public function event()
    {
        $vehiclePageIndex = Request::get('vehiclePageIndex');
        $vehiclePageSize = Request::get('vehiclePageSize');
        $start = Request::get('start');
        $end = Request::get('end');
        $statuses = [];
        $vehicleTeamIDs = Request::get('vehicleTeamIDs');
        $vehicleIDs = Request::get('vehicleIDs');
        $vehicleGroupIDs = Request::get('vehicleGroupIDs');
        $customerIDs = Request::get('customerIDs');
        $group = Request::get('group');
        $orderNo = Request::get('orderNo');
        $isShowCustomer = Request::get('isShowCustomer');

        $isFilterVehicle = intval(Request::get('isFilterVehicle', 0));

        $partnerId = Request::get('partnerId', 0);

        if (empty(Request::get('statuses')) || Request::get('statuses') == -1) {
            $statuses = $this->getStatusList();
        } else {
            $statuses = explode(';', Request::get('statuses'));
        }
        $params = [
            'vehiclePageIndex' => $vehiclePageIndex,
            'vehiclePageSize' => $vehiclePageSize,
            'vehicleTeamIDs' => $vehicleTeamIDs,
            'vehicleIDs' => $vehicleIDs,
            'vehicleGroupIDs' => $vehicleGroupIDs,
            'customerIDs' => $customerIDs,
            'statuses' => $statuses,
            'isFilterVehicle' => $isFilterVehicle,
            'group' => $group,
            'orderNo' => $orderNo,
            'isShowCustomer' => $isShowCustomer,
            'originalStatus' => Request::get('statuses'),
            'start' => $start,
            'end' => $end,
            'userId' => $this->getCurrentUser()->id,
            'partnerId' => $partnerId
        ];

        $query = $this->buildQuery($params);
        $items = $this->getResourceItems($query['resourceIDs']);
        $this->setEntities($items);
        $this->setViewData([
            'total' => $query['totalResource'],
            'page_index' => $vehiclePageIndex,
            'page_size' => $vehiclePageSize,

        ]);
        $model = [
            'total' => $query['totalResource'],
            'items' => $items,
            'page' => $vehiclePageIndex,
            'paginator' => $this->render('backend.order_board._resource_pagination')->render()
        ];

        $result = [
            'events' => $query['result'],
            'resources' => $model,
            'group' => isset($query['group']) ? $query['group'] : []
        ];

        return json_encode($result);
    }

    public function getResourceItems($ids)
    {
        return $this->getVehicleRepository()->getItemsByIds($ids);
    }

    // Lấy chi tiết 1 đơn hàng
    public function order()
    {
        $orderId = Request::get('id', '');

        $order = DB::select("
        SELECT    o.`order_code` AS `title`,
                  o.`id` AS `orderId`,
                FORMAT(IFNULL(o.weight, 0), '2') AS weight,
                FORMAT(IFNULL(o.volume, 0), '2') AS volume,
                IFNULL((SELECT 
                                locations.full_address
                            FROM
                                locations
                            WHERE
                                locations.id = o.location_destination_id),
                        '') AS location_destination,
                IFNULL((SELECT 
                                locations.full_address
                            FROM
                                locations
                            WHERE
                                locations.id = o.location_arrival_id),
                        '') AS location_arrival
             FROM orders as o where id = :orderId limit 1;
        ", array(
            'orderId' => $orderId,
        ));
        return json_encode($order);
    }

    // Lấy danh sách hóa đơn chưa hoàn thành và chưa nằm trong chuyến xe nào
    public function orderList()
    {
        try {
            $keyword = Request::get('keyword', '');

            $data = [
                'per_page' => Request::get('per_page', config('pagination.backend.per_page.order_dashboard')),
                'sort_field' => Request::get('sort_field', null),
                'sort_type' => Request::get('sort_type', null),
                'keyword' => $keyword
            ];

            $this->detectCurrentPage();
            $function = $this->getOrderListFunction();
            $entities = $this->{$this->getRepositoryFunction()}()->{$function}($data);
            $this->setEntities($entities);

            $html = [
                'content' => $this->render('backend.' . $this->getModel() . '._order_list')->render(),
                'paginator' => $this->render('backend.' . $this->getModel() . '._order_pagination')->render(),
            ];

            $this->setData($html);

            return $this->renderJson();
        } catch (Exception $e) {
            logError($e);
        }
    }

    //Lấy ds đội xe quản lý user
    public function getVehicleTeamByUser()
    {
        $userId = Auth::user()->id;
        $vehicleTeams = DB::table('admin_users_vehicle_teams')
            ->select('vehicle_team.id as id', 'vehicle_team.name as title')
            ->join('vehicle_team', 'admin_users_vehicle_teams.vehicle_team_id', '=', 'vehicle_team.id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $userId)
            ->where('vehicle_team.del_flag', '=', 0)
            ->get();

        return $vehicleTeams;
    }
}
