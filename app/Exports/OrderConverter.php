<?php

namespace App\Exports;

use App\Common\AppConstant;
use App\Model\Entities\Order;

use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleRepository;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use PHPExcel_ReferenceHelper;

class OrderConverter
{
    protected $_repository = null;
    protected $_goodTypeRepository = null;
    protected $_goodsUnitRepository = null;
    protected $_driverRepository = null;
    protected $_vehicleRepository = null;
    protected $_locationRepository = null;
    protected $_receiptPaymentRepository = null;
    protected $_orderCusRepository = null;
    protected $_adminUserRepository = null;

    protected $_data = [];
    protected $customerData = [];
    protected $goodTypeData = [];
    protected $locationData = [];
    protected $goodUnitData = [];
    protected $vehicleData = [];
    protected $driverData = [];
    protected $adminUserData = [];

    /**
     * @return OrderRepository
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
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        GoodsTypeRepository $goodTypeRepository,
        DriverRepository $driverRepository,
        VehicleRepository $vehicleRepository,
        GoodsUnitRepository $goodsUnitRepository,
        LocationRepository $locationRepository = null,
        ReceiptPaymentRepository $receiptPaymentRepository,
        OrderCustomerRepository $orderCustomerRepository,
        AdminUserInfoRepository $adminUserRepository,
        $data = []
    ) {
        $this->setRepository($orderRepository);
        $this->_data = $data;
        $this->_customerRepository = $customerRepository;
        $this->_goodTypeRepository = $goodTypeRepository;
        $this->_driverRepository = $driverRepository;
        $this->_vehicleRepository = $vehicleRepository;
        $this->_goodsUnitRepository = $goodsUnitRepository;
        $this->_locationRepository = $locationRepository;
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
        $this->_orderCusRepository = $orderCustomerRepository;
        $this->_adminUserRepository = $adminUserRepository;
    }

    public function convert($template, $templatePath, $orderPath, $excelColumnConfig)
    {
        try {

            $template_path = public_path($templatePath);

            if (!file_exists(($template_path))) {
                return;
            }
            $temple_file_type = PHPExcel_IOFactory::identify($template_path);
            $template_reader = PHPExcel_IOFactory::createReader($temple_file_type);
            $template_excel = $template_reader->load($template_path);


            $order_file_type = PHPExcel_IOFactory::identify($orderPath);
            $order_reader = PHPExcel_IOFactory::createReader($order_file_type);
            $orderPHPExcel = $order_reader->load($orderPath);

            $rowIndex = $excelColumnConfig->header_index;
            $start = $rowIndex + 1;
            $end = $rowIndex + (empty($template->max_row) ? 500 : $template->max_row);
            $mappings = $template->templateExcelConverterMappings;
            $mappings = $mappings->filter(function ($value) {
                return !empty($value->formula);
            });

            if ($template->is_use_convert_sheet == 1) {
                if (!empty($template->file_id)) {
                    $converter_template_path = public_path($template->tryGet('getFile')->path);
                    if (file_exists(($converter_template_path))) {

                        $converter_temple_file_type = PHPExcel_IOFactory::identify(($converter_template_path));
                        $converter_template_reader = PHPExcel_IOFactory::createReader($converter_temple_file_type);

                        $converter_template_excel = $converter_template_reader->load($converter_template_path);

                        foreach ($converter_template_excel->getSheetNames() as $sheet_name) {
                            $sheet = $converter_template_excel->getSheetByName($sheet_name);
                            $sheet->setTitle($sheet_name);
                            $orderPHPExcel->addExternalSheet($sheet);
                            unset($sheet);
                        }
                    }
                }
            }

            foreach ($template_excel->getSheetNames() as $sheet_name) {
                $sheet = $template_excel->getSheetByName($sheet_name);
                $sheet->setTitle($sheet_name);
                $orderPHPExcel->addExternalSheet($sheet);
                unset($sheet);
            }

            $sheet = $orderPHPExcel->setActiveSheetIndex(0);



            $excelColumnMappingConfigs = $excelColumnConfig->excelColumnMappingConfigs;

            $formulaColumns = [];
            for ($i = $start; $i < $end; $i++) {
                foreach ($mappings as $mapping) {
                    $column = $excelColumnMappingConfigs->first(function ($value) use ($mapping) {
                        return $value->field == $mapping->field;
                    });
                    if (empty($column)) {
                        continue;
                    }
                    $column_index = $column->column_index;
                    $formulaColumns[] = $column_index;

                    $sheet->setCellValue($column_index . $i, $mapping->formula);
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($orderPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/convert'))) {
                mkdir(public_path('file/convert'));
            }

            $uuid = uniqid();
            $filePath = public_path('file/convert/DonHangChuyenDoi_' . $uuid . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;
        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        } finally {
            if (isset($orderPHPExcel)) {
                $orderPHPExcel->disconnectWorksheets(); // Good to disconnect
                $orderPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($orderPHPExcel)) {
                unset($objWriter, $orderPHPExcel);
            }
        }
        return "";
    }
}
