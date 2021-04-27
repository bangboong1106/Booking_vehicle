<?php

namespace App\Exports;

use App\Repositories\DriverRepository;
use App\Repositories\AccessoryRepository;
use App\Repositories\RepairTicketRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ExcelColumnConfigRepository;

class RepairTicketExport extends BaseExport
{
    protected $_repository = null;
    protected $_driverRepository;
    protected $_vehicleRepository;
    protected $_accessoryRepository;
    protected $_excelColumnConfigRepository;

    protected $_data = [];

    public function getAccessoryRepository()
    {
        return $this->_accessoryRepository;
    }

    public function setAccessoryRepository($accessoryRepository): void
    {
        $this->_accessoryRepository = $accessoryRepository;
    }

    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    public function getExcelColumnConfigRepository()
    {
        return $this->_excelColumnConfigRepository;
    }

    public function setExcelColumnConfigRepository($excelColumnConfigRepository): void
    {
        $this->_excelColumnConfigRepository = $excelColumnConfigRepository;
    }

    public function __construct(
        RepairTicketRepository $repairTicketRepository,
        AccessoryRepository $accessoryRepository,
        DriverRepository $driverRepository,
        VehicleRepository $vehicleRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository,

        $data = []
    ) {
        $this->setRepository($repairTicketRepository);
        $this->setAccessoryRepository($accessoryRepository);
        $this->setDriverRepository($driverRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);

        $this->_data = $data;
    }


    protected function getFileName(): string
    {
        return 'Danh_sach_phieu_sua_chua';
    }

    protected function getFileTemplateName(): string
    {
        return 'repairTicketTemplate';
    }

    protected function getUnitSheet(): int
    {
        return 1;
    }

    protected function prepareData($user_id)
    {
        $this->accessoryData = $this->getAccessoryRepository()->all(['id', 'name'])->sortBy('name');
        $this->vehicleData = $this->getVehicleRepository()->getListWithPermission($user_id);
        $this->driverData = $this->getDriverRepository()->getListWithPermission($user_id);
    }
}
