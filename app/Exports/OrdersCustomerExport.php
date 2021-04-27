<?php

namespace App\Exports;

use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleGroupRepository;
use App\Repositories\AdminUserInfoRepository;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersCustomerExport extends BaseExport
{
    protected $_repository = null;
    protected $_orderRepository = null;
    protected $_locationRepository = null;
    protected $_vehicleGroupRepository = null;
    protected $_adminUserRepository = null;

    protected $_data = [];

    /**
     * @return OrderCustomerRepository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * @param null $repository
     */
    public function setRepository($repository): void
    {
        $this->_repository = $repository;
    }

    public function __construct(
        OrderCustomerRepository $orderCustomerRepository,
        CustomerRepository $customerRepository,
        LocationRepository $locationRepository,
        VehicleGroupRepository $vehicleGroupRepository,
        AdminUserInfoRepository $adminUserRepository,
        $data = []
    )
    {
        $this->setRepository($orderCustomerRepository);
        $this->_data = $data;
        $this->_customerRepository = $customerRepository;
        $this->_locationRepository = $locationRepository;
        $this->_vehicleGroupRepository = $vehicleGroupRepository;
        $this->_adminUserRepository = $adminUserRepository;

    }

    public function view(): View
    {
        $orders = $this->getRepository()->getListForExport($this->_data);
        return view('backend.order.export', [
            'orders' => $orders
        ]);
    }

    protected function getFileName(): string
    {
        return 'Danh_sach_don_hang_khach_hang';
    }

    protected function getFileTemplateName(): string
    {
        return 'orderCustomerTemplate';
    }

    protected function getUnitSheet() : int {
        return 1;
    }

    protected function prepareData($user_id){
        $this->customerData = $this->_customerRepository->all(['id', 'customer_code', 'full_name'])->sortBy('full_name');
        $this->locationData = $this->_locationRepository->all(['id', 'code', 'title'])->sortBy('title');
        $this->vehicleGroupData = $this->_vehicleGroupRepository->all(['id', 'code', 'name'])->sortBy('title');
        $this->adminUserData = $this->_adminUserRepository->getAllUserIsAdmin();

    }
}
