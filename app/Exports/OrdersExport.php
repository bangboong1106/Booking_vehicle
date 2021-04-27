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
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Cell;
use PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use App\Exports\BaseExport;

class OrdersExport extends BaseExport implements FromView, ShouldAutoSize, WithColumnFormatting
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

    public function view(): View
    {
        $orders = $this->getRepository()->getListForExport($this->_data);
        $goodTypes = $this->_goodTypeRepository->getListForSelect();
        return view('backend.order.export', [
            'orders' => $orders,
            'goodTypes' => $goodTypes,
        ]);
    }

    public function headingRow(): int
    {
        return 8;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }

    protected function getFileName(): string
    {
        return 'Danh_sach_don_hang';
    }

    protected function getFileTemplateName(): string
    {
        return 'orderTemplate';
    }

    protected function getGoodsSheet(): int
    {
        return 1;
    }

    protected function getUnitSheet(): int
    {
        return 3;
    }

    protected function prepareData($user_id)
    {
        $this->customerData = $this->_customerRepository->getAllCustomerByRole();
        $this->goodTypeData = $this->_goodTypeRepository->all(['id', 'code', 'title'])->sortBy('title');
        $this->locationData = $this->_locationRepository->all(['id', 'code', 'title'])->sortBy('title');
        $this->goodUnitData = $this->_goodsUnitRepository->all(['id', 'code', 'title'])->sortBy('title');
        $this->vehicleData = $this->_vehicleRepository->getListWithPermission($user_id);
        $this->driverData = $this->_driverRepository->getListWithPermission($user_id);
        $this->adminUserData = $this->_adminUserRepository->getAllUserIsAdmin();
    }

    protected function afterAddDataValidation($objPHPExcel)
    {
        $objPHPExcel->setActiveSheetIndex($this->getGoodsSheet());
        $goods_sheet = $objPHPExcel->getActiveSheet();
        $column = 1;
        foreach ($this->goodTypeData as $item) {
            $goods_sheet->getCellByColumnAndRow($column, 2)->setValue($item->code . '|' . $item->title);
            $goods_sheet->getStyleByColumnAndRow($column, 2)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B8CCE4')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
            $column++;
        }
        if($column > 1){
            $goods_sheet->mergeCellsByColumnAndRow(1, 1, $column - 1, 1);
        }
        $goods_sheet->getStyle('B1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE597')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            )
        );
    }

    protected function afterAddData($objPHPExcel, $data)
    {

        $objPHPExcel->setActiveSheetIndex($this->getGoodsSheet());
        $goods_sheet = $objPHPExcel->getActiveSheet();

        $goods_column = 1;
        $goods_row = 2;
        foreach ($this->goodTypeData as  $item) {
            $goods_sheet->getStyleByColumnAndRow($goods_column, 2)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B8CCE4')
                    )
                )
            );
            $columnIndex = PHPExcel_Cell::stringFromColumnIndex($goods_column);
            $goods_sheet->setCellValue($columnIndex . $goods_row, $item->code . '|' . $item->title);
            $goods_column++;
        }

        $goods_row = 3;
        foreach ($data as $order) {
            $goods_column = 1;
            $goods_list = $order->listGoods->pluck('pivot')->keyBy('goods_type_id')->toArray();

            $goods_sheet->setCellValue('A' . $goods_row, $order->order_code);
            foreach ($this->goodTypeData as $item) {
                $goods_column++;
                $columnIndex = PHPExcel_Cell::stringFromColumnIndex($goods_column - 1);
                $value = collect($goods_list)->filter(function ($value) use ($item) {
                    return $value['goods_type_id'] === $item->id;
                })->first();
                $quantity = empty($value) ? '' : $value["quantity"];
                $goods_sheet->setCellValue($columnIndex . $goods_row, $quantity);
            }
            $goods_row++;
        }
    }

    // Xuất bảng kê
    public function exportReportTemplate($filter)
    {
        $objWriter = null;
        $objPHPExcel = null;

        try {
            $nameFile = 'BangKeDoanhThuChiPhi_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/BangKeDoanhThuChiPhi_v1.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $data = $this->getRepository()->getOrderByFilterReport($filter);
            $costType = $this->_receiptPaymentRepository->getAll(null);
            $locationType = DB::table('location_type')->select('*')->where(['del_flag' => 0])->get();
            foreach ($data as $order) {
                if (null != $costType && 0 < sizeof($costType)) {
                    $routeCost = $this->_receiptPaymentRepository->getAll($order->route_id);
                    $order->costs = $routeCost;
                    if (!empty($order->driver_id))
                        $vehicleTeam = $this->getRepository()->getVehicleTeamFromDriverId($order->driver_id);
                    $order->vehicle_team_name = isset($vehicleTeam) && count($vehicleTeam) > 0 ? (string)$vehicleTeam[0]->vehicle_team_name : "";
                }
            }

            $dataOrderCus = $this->_orderCusRepository->getOrderCustomerByFilterReport($filter);

            $this->addDataToExcelFileReport(
                $objPHPExcel->setActiveSheetIndex(0),
                $objPHPExcel->getSheetByName("Hang hoa"),
                $objPHPExcel->getSheetByName("DHKH"),
                $data,
                $dataOrderCus,
                $costType,
                $locationType
            );

            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/report_orders_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            return Response::download($filePath, $nameFile, []);
        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        } finally {
            if (isset($objPHPExcel)) {
                $objPHPExcel->disconnectWorksheets(); // Good to disconnect
                $objPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($objPHPExcel)) {
                unset($objWriter, $objPHPExcel);
            }
        }
    }

    //Dữ liệu trong bảng kê
    private function addDataToExcelFileReport($setCell, $goods_sheet, $setCellOrderCus, $data, $dataOrderCus, $costType, $locationTypes)
    {
        $startRow = 6;
        $row = 6;
        $column = 0;
        $index = 0;
        $routes = [];
        $routesPrepare = [];
        $routesRevenue = [];
        $indexLocationColumn = 0;
        $startCostColumn = 30;
        $indexCostColumn = 0;
        $cellLocation = config('constant.cell_locations');
        $currencyFormat = '#,##0';

        $currentUser = backendGuard()->user();

        for ($i = 0; $i < sizeof($data); $i++) {
            $finalAmount = $data[$i]->amount - $data[$i]->commission_amount;
            if (!in_array($data[$i]->route_code, $routesPrepare)) {
                $routesPrepare[] = $data[$i]->route_code;
                $routesRevenue[$data[$i]->route_code] = $finalAmount;
            } else {
                $routesRevenue[$data[$i]->route_code] = $routesRevenue[$data[$i]->route_code] + $finalAmount;
            }
        }

        if (null != $locationTypes && 0 < sizeof($locationTypes)) {
            $setCell->unmergeCells('B4:Q4');
            $setCell->insertNewColumnBefore('Q', sizeof($locationTypes));
            $startCell = array_search('Q', $cellLocation);
            $endCell = $cellLocation[$startCell + (sizeof($locationTypes))];
            $setCell->mergeCells('B4:' . $endCell . '4');
            foreach ($locationTypes as $locationType) {
                $setCell->setCellValue(config('constant.cell_locations')[$startCell + $indexLocationColumn] . ($row - 1), $locationType->title);
                $indexLocationColumn++;
            }
        }

        $startCostColumn = 30 + $indexLocationColumn;
        if (null != $costType && 0 < sizeof($costType)) {
            $startCellMerge = $cellLocation[$startCostColumn];
            $setCell->insertNewColumnBefore($startCellMerge, sizeof($costType));
            $endCellMerge = $cellLocation[$startCostColumn + (sizeof($costType) - 1)];
            $setCell->mergeCells($cellLocation[$startCostColumn] . '4:' . $endCellMerge . '4');
            $setCell->setCellValue($cellLocation[$startCostColumn] . '4', 'Chi phí');

            foreach ($costType as $cost) {
                $setCell->setCellValue($cellLocation[$startCostColumn + $indexCostColumn] . ($row - 1), $cost->costName);
                $indexCostColumn++;
            }
        }

        $columnCostStart = 0;
        $goods_columnStart = 0;
        $sumRoutesRevenue = 0;
        $sumRoutesCost = 0;
        $sumRoutesProfit = 0;
        $sumWeight = 0;
        $sumVolume = 0;
        $sumQuantity = 0;
        $sumPrice = 0;
        $sumCommission = 0;
        $sumPriceFinal = 0;
        $sumCosts = [];
        $sumCodAmount = 0;

        foreach ($data as $order) {
            $setCell
                ->setCellValue($cellLocation[$column++] . $row, ($index + 1))
                ->setCellValue($cellLocation[$column++] . $row, $order->order_code)
                ->setCellValue($cellLocation[$column++] . $row, $order->order_no)
                ->setCellValue($cellLocation[$column++] . $row, $order->customer_code)
                ->setCellValue($cellLocation[$column++] . $row, $order->created_by)
                ->setCellValue($cellLocation[$column++] . $row, $order->created_date)
                ->setCellValue($cellLocation[$column++] . $row, $order->location_destination)
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertTime($order->ETD_time, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertDate($order->ETD_date, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertTime($order->ETD_time_reality, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertDate($order->ETD_date_reality, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $row, $order->location_arrival)
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertTime($order->ETA_time, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertDate($order->ETA_date, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertTime($order->ETA_time_reality, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $row, AppConstant::convertDate($order->ETA_date_reality, 'd-m-Y'));

            $listType = $order->listLocations->countBy(function ($location) {
                return $location->location_type_id;
            })->toArray();
            foreach ($locationTypes as $locationType) {
                $setCell->setCellValue($cellLocation[$column++] . $row, empty($listType[$locationType->id]) ? '0' :
                    $listType[$locationType->id]);
            }

            $setCell->setCellValue($cellLocation[$column++] . $row, $order->note)
                ->setCellValue($cellLocation[$column++] . $row, $order->reg_no)
                ->setCellValue($cellLocation[$column++] . $row, $order->vehicle_group_name)
                ->setCellValue($cellLocation[$column++] . $row, $order->driver_full_name)
                ->setCellValue($cellLocation[$column++] . $row, $order->vehicle_team_name);

            $goods_columnStart = $column;

            setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->weight ? $order->weight : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->volume ? $order->volume : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->quantity ? $order->quantity : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $setCell->setCellValue($cellLocation[$column++] . $row, $order->good_units_title);

            if ($currentUser->can('export revenue')) {
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->amount ? $order->amount : 0, null);
            } else {
                setNumberValueExcel($setCell, $cellLocation, $column, $row, null, null);
            }
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->commission_amount ? $order->commission_amount : 0, null);

            if ($currentUser->can('export revenue')) {
                setNumberValueExcel($setCell, $cellLocation, $column, $row, ($order->amount ? $order->amount : 0) - ($order->commission_amount ? $order->commission_amount : 0), null);
            } else {
                setNumberValueExcel($setCell, $cellLocation, $column, $row, null, null);
            }

            $setCell->setCellValue($cellLocation[$column++] . $row, $order->route_code)
                ->setCellValue($cellLocation[$column++] . $row, $order->route_name);

            $sumWeight += $order->weight ? $order->weight : 0;
            $sumVolume += $order->volume ? $order->volume : 0;
            $sumQuantity += $order->quantity ? $order->quantity : 0;
            $sumPrice += $order->amount ? $order->amount : 0;
            $sumCommission += $order->commission_amount ? $order->commission_amount : 0;
            $sumPriceFinal += ($order->amount ? $order->amount : 0) - ($order->commission_amount ? $order->commission_amount : 0);

            $columnCostStart = $column;

            if (null != $costType && 0 < sizeof($costType) && null != $order->costs && 0 < sizeof($order->costs)) {
                foreach ($costType as $cost) {
                    if (!$currentUser->can('export cost')) {
                        $setCell->setCellValue($cellLocation[$column++] . $row, 0);
                        continue;
                    }
                    $check = false;
                    foreach ($order->costs as $item) {
                        if ($item->costId == $cost->costId) {
                            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->amount ? $item->amount : 0, null);
                            $check = true;
                            if (isset($sumCosts[$cost->costId])) {
                                $sumCosts[$cost->costId] += $sumCosts[$cost->costId] + $item->amount ? $item->amount : 0;
                            } else {
                                $sumCosts[$cost->costId] = $item->amount ? $item->amount : 0;
                            }
                            break;
                        }
                    }
                    if (false == $check) {
                        $setCell->setCellValue($cellLocation[$column++] . $row, 0);
                    }
                }
            }
            if (!in_array($order->route_code, $routes)) {
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $routesRevenue[$order->route_code] ? $routesRevenue[$order->route_code] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->route_final_cost ? $order->route_final_cost : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, ($routesRevenue[$order->route_code] ? $routesRevenue[$order->route_code] : 0)
                    - ($order->route_final_cost ? $order->route_final_cost : 0), null);
                $routes[] = $order->route_code;

                $sumRoutesRevenue += $routesRevenue[$order->route_code] ? $routesRevenue[$order->route_code] : 0;
                $sumRoutesCost += $order->route_final_cost ? $order->route_final_cost : 0;
                $sumRoutesProfit += ($routesRevenue[$order->route_code] ? $routesRevenue[$order->route_code] : 0)
                    - ($order->route_final_cost ? $order->route_final_cost : 0);
            } else {
                $setCell->setCellValue($cellLocation[$column++] . $row, 0);
                $setCell->setCellValue($cellLocation[$column++] . $row, 0);
                $setCell->setCellValue($cellLocation[$column++] . $row, 0);
            }

            setNumberValueExcel($setCell, $cellLocation, $column, $row, $order->cod_amount ? $order->cod_amount : 0, null);
            $sumCodAmount += $order->cod_amount ? $order->cod_amount : 0;

            $row++;
            $index++;
            $column = 0;
        }

        if ($data && count($data) > 0) {
            //Bỏ waraptext cột chi phí
            $setCell->getStyle($cellLocation[$columnCostStart] . $startRow . ':' . $cellLocation[$columnCostStart + sizeof($costType) - 1] . $row)
                ->getAlignment()->setWrapText(false);
            foreach ($costType as $i => $cost) {
                $setCell->getStyle($cellLocation[$columnCostStart + $i] . $row)
                    ->getNumberFormat()->setFormatCode($currencyFormat);
                $setCell->setCellValueExplicit($cellLocation[$columnCostStart + $i] . $row, isset($sumCosts[$cost->costId]) ? $sumCosts[$cost->costId] : 0, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $setCell->getStyle($cellLocation[$columnCostStart + $i] . $row)->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FFFF00')
                        )
                    )
                );
            }

            //Tổng hợp
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType)] . $startRow . ':' . $cellLocation[$columnCostStart + sizeof($costType) + 2] . $row)
                ->getAlignment()->setWrapText(false);
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType)] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$columnCostStart + sizeof($costType)] . $row, $sumRoutesRevenue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType) + 1] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$columnCostStart + sizeof($costType) + 1] . $row, $sumRoutesCost, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType) + 2] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$columnCostStart + sizeof($costType) + 2] . $row, $sumRoutesProfit, PHPExcel_Cell_DataType::TYPE_NUMERIC);

            //COD
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType)] . $startRow . ':' . $cellLocation[$columnCostStart + sizeof($costType) + 3] . $row)
                ->getAlignment()->setWrapText(false);
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType) + 3] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$columnCostStart + sizeof($costType) + 3] . $row, $sumCodAmount, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$columnCostStart + sizeof($costType)] . $row . ':' . $cellLocation[$columnCostStart + sizeof($costType) + 3] . $row)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFF00')
                    )
                )
            );

            //Hàng hoa, Doanh thu
            $setCell->getStyle($cellLocation[$goods_columnStart] . $startRow . ':' . $cellLocation[$goods_columnStart + 6] . $row)
                ->getAlignment()->setWrapText(false);

            $setCell->getStyle($cellLocation[$goods_columnStart] . $row)
                ->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart] . $row, $sumWeight, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart + 1] . $row)
                ->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart + 1] . $row, $sumVolume, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart + 2] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart + 2] . $row, $sumQuantity, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart + 4] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart + 4] . $row, $sumPrice, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart + 5] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart + 5] . $row, $sumCommission, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart + 6] . $row)
                ->getNumberFormat()->setFormatCode($currencyFormat);
            $setCell->setCellValueExplicit($cellLocation[$goods_columnStart + 6] . $row, $sumPriceFinal, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $setCell->getStyle($cellLocation[$goods_columnStart] . $row . ':' . $cellLocation[$goods_columnStart + 6] . $row)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFF00')
                    )
                )
            );
        }

        $goods_column = 1;
        $goods_row = 2;
        $goodTypes = $this->_goodTypeRepository->search()->get()->sortBy('title')->keyBy('id');
        foreach ($goodTypes as $item) {
            $goods_sheet->getStyleByColumnAndRow($goods_column, 2)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'B8CCE4'),
                    ),
                    'alignment' => array(
                        'wrap' => true
                    )
                )
            );
            $goods_sheet->setCellValue($cellLocation[$goods_column++] . $goods_row, $item->code . '|' . $item->title);
            $goods_sheet->setCellValue($cellLocation[$goods_column] . $goods_row, 'Tổng khối lượng (kg)');
            $goods_sheet->setCellValue($cellLocation[$goods_column + 1] . $goods_row, 'Tổng thể tích (m3)');
        }
        $goods_sheet->mergeCellsByColumnAndRow(1, 1, $goods_column > 1 ? $goods_column - 1 : $goods_column, 1);
        $goods_sheet->getStyle('B1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE597')
                ),
                'alignment' => array(
                    'wrap' => true
                )
            )
        );

        $goods_row = 3;
        foreach ($data as $order) {
            $goods_column = 1;
            $listGoods = $this->getRepository()->getGoodsItemsByOrderID($order->id);
            $totalWeight = 0;
            $totalVolume = 0;

            $goods_sheet->setCellValue('A' . $goods_row, $order->order_code);
            if (isset($listGoods) && count($listGoods) > 0) {
                foreach ($goodTypes as $goodType) {
                    foreach ($listGoods as $goods) {
                        if ($goodType->id == $goods->goods_type_id) {
                            setNumberValueExcelNR($goods_sheet, $cellLocation[$goods_column], $goods_row, isset($goods->quantity) ? $goods->quantity : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                            $totalWeight += isset($goods->total_weight) ? $goods->total_weight : 0;
                            $totalVolume += isset($goods->total_volume) ? $goods->total_volume : 0;
                            break;
                        }
                    }
                    $goods_column++;
                }
                $goods_sheet->setCellValue($cellLocation[$goods_column++] . $goods_row, numberFormatExcel($totalWeight));
                $goods_sheet->setCellValue($cellLocation[$goods_column++] . $goods_row, numberFormatExcel($totalVolume));
            }
            $goods_row++;
        }

        $goods_sheet->getStyle("B2" . ":" . $cellLocation[count($goodTypes) + 2] . $goods_row)->applyFromArray(array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
                'inside' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                )
            ),
        ));

        $rowOrderCus = 6;
        foreach ($dataOrderCus as $index => $orderCustomer) {
            $column = 0;
            $setCellOrderCus
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, ($index + 1))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->code)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->name)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->order_no)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, AppConstant::convertDate($orderCustomer->order_date, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->customer_full_name)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, empty($orderCustomer->customer_name) ? '' : $orderCustomer->customer_name)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, empty($orderCustomer->customer_mobile_no) ? '' : $orderCustomer->customer_mobile_no)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->location_destination)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, AppConstant::convertTime($orderCustomer->ETD_time, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, AppConstant::convertDate($orderCustomer->ETD_date, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, $orderCustomer->location_arrival)
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, AppConstant::convertTime($orderCustomer->ETA_time, 'H:i'))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, AppConstant::convertDate($orderCustomer->ETA_date, 'd-m-Y'))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->distance ? $orderCustomer->distance : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->volume ? $orderCustomer->volume : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->weight ? $orderCustomer->weight : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->vehicle_group_name ? $orderCustomer->vehicle_group_name : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->vehicle_number ? $orderCustomer->vehicle_number : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->route_number ? $orderCustomer->route_number : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->amount ? $orderCustomer->amount : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel($orderCustomer->commission_amount ? $orderCustomer->commission_amount : 0))
                ->setCellValue($cellLocation[$column++] . $rowOrderCus, numberFormatExcel((empty($orderCustomer->amount) ? 0 : $orderCustomer->amount) - (empty($orderCustomer->commission_amount) ? 0 : $orderCustomer->commission_amount)));

            $rowOrderCus++;
        }

        $setCellOrderCus->getStyle("A6" . ":W" . $rowOrderCus)->applyFromArray(array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
                'inside' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                )
            ),
        ));

        return $this;
    }
}
