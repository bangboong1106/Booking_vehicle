<?php

namespace App\Exports;

use App\Repositories\LocationGroupRepository;
use App\Repositories\LocationRepository;
use App\Repositories\QuotaRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\VehicleGroupRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_DataValidation;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class QuotaExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_locationRepository = null;
    protected $_receiptPaymentRepository = null;
    protected $_vehicleGroupRepository = null;
    protected $_locationGroupRepository = null;
    protected $_data = [];

    /**
     * @return QuotaRepository
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

    /**
     * @return LocationRepository
     */
    public function getLocationRepository()
    {
        return $this->_locationRepository;
    }

    /**
     * @param null $locationRepository
     */
    public function setLocationRepository($locationRepository): void
    {
        $this->_locationRepository = $locationRepository;
    }

    /**
     * @return ReceiptPaymentRepository
     */
    public function getReceiptPaymentRepository()
    {
        return $this->_receiptPaymentRepository;
    }

    /**
     * @param null $receiptPaymentRepository
     */
    public function setReceiptPaymentRepository($receiptPaymentRepository): void
    {
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
    }

    /**
     * @return VehicleGroupRepository
     */
    public function getVehicleGroupRepository()
    {
        return $this->_vehicleGroupRepository;
    }

    /**
     * @param null $vehicleGroupRepository
     */
    public function setVehicleGroupRepository($vehicleGroupRepository): void
    {
        $this->_vehicleGroupRepository = $vehicleGroupRepository;
    }

    /**
     * @return null
     */
    public function getLocationGroupRepository()
    {
        return $this->_locationGroupRepository;
    }

    /**
     * @param null $locationGroupRepository
     */
    public function setLocationGroupRepository($locationGroupRepository): void
    {
        $this->_locationGroupRepository = $locationGroupRepository;
    }

    public function __construct(
        QuotaRepository $quotaRepository,
        LocationRepository $locationRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        VehicleGroupRepository $vehicleGroupRepository,
        LocationGroupRepository $locationGroupRepository,
        $data = []
    )
    {
        $this->setRepository($quotaRepository);
        $this->setLocationRepository($locationRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setVehicleGroupRepository($vehicleGroupRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->_data = $data;
    }

    public function view(): View
    {
        return view('backend.customer.export', [
            'customers' => $this->getRepository()->getListForExport($this->_data)
        ]);
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

    public function headingRow(): int
    {
        return 10;
    }

    public function exportFromTemplate($update = false)
    {
        try {
            $fileTemplatePath = public_path('file/Danh_sach_bang_dinh_muc.xlsx');

            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);
            $objPHPExcel->setActiveSheetIndex(0);
            $setCell = $objPHPExcel->getSheetByName("Data");

            $dataLocation = $this->getLocationRepository()->search()->get();
            $dataLocationGroup = $this->getLocationGroupRepository()->search()->get();
            $listCost = $this->getReceiptPaymentRepository()->search([
                'type_eq' => config('constant.COST'),
                'sort_field' => 'lidx',
                'sort_type' => 'ASC'
            ])->get();

            $rowLocation = 1;
            foreach ($dataLocation as $location) {
                $setCell->setCellValue('A' . $rowLocation, $location->code . '|' . $location->title);
                $rowLocation++;
            }

            $listGroup = $this->getVehicleGroupRepository()->search()->get();

            $rowGroup = 1;
            foreach ($listGroup as $group) {
                $setCell->setCellValue('B' . $rowGroup, $group->code . '|' . $group->name);
                $rowGroup++;
            }

            $rowLocationGroup = 1;
            foreach ($dataLocationGroup as $locationGroup) {
                $setCell->setCellValue('C' . $rowLocationGroup, $locationGroup->code . '|' . $locationGroup->title);
                $rowLocationGroup++;
            }

            $setCellCost = $objPHPExcel->getSheet(1);
            $column = 2;
            $costIds = [];
            foreach ($listCost as $item) {
                $setCellCost->getCellByColumnAndRow($column, 2)->setValue($item->id . '|' . $item->name);
                $setCellCost->getStyleByColumnAndRow($column, 2)->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'B8CCE4')
                        )
                    )
                );
                $column++;
                $costIds[] = $item->id;
            }
            // TODO
            $setCellCost->mergeCellsByColumnAndRow(2, 1, $column - 1, 1);
            $setCellCost->getStyle('C1')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFE597'),
                    )
                )
            );
            if ($rowLocation > 1) {
                $objPHPExcel->setActiveSheetIndex(0);
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('D10')->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('Data!$A$1:$A$' . ($rowLocation - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("D10:D1000", $objValidation);
                $objPHPExcel->getActiveSheet()->setDataValidation("F10:F1000", $objValidation);
            }

            if ($rowLocationGroup > 1) {
                $objPHPExcel->setActiveSheetIndex(0);
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('E10')->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('Data!$C$1:$C$' . ($rowLocation - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("E10:E1000", $objValidation);
                $objPHPExcel->getActiveSheet()->setDataValidation("G10:G1000", $objValidation);
            }

            if ($rowGroup > 1) {
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('C10')->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('Data!$B$1:$B$' . ($rowGroup - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("C10:C1000", $objValidation);
            }
            if ($update) {
                $data = $this->getRepository()->getListForExport($this->_data);
                $this->addDataToExcelFile($objPHPExcel, $data, $costIds, $dataLocationGroup);
            }

            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/quota_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            $fileName = $update ? 'DanhSachBangDinhMucCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx' :
                'Danh_sach_bang_dinh_muc.xlsx';

            return Response::download($filePath, $fileName, []);
        } catch (PHPExcel_Reader_Exception $e) {
        } catch (PHPExcel_Exception $e) {
        }
        return null;
    }

    /**
     * @param PHPExcel $objPHPExcel
     * @param $data
     * @param array $costIds
     * @param $dataLocationGroup
     * @return $this
     * @throws PHPExcel_Exception
     */
    private function addDataToExcelFile($objPHPExcel, $data, $costIds, $dataLocationGroup)
    {
        $row = 10;
        $column = 0;
        $cellLocation = config('constant.cell_locations');
        $rowCost = 3;
        $sheetData = $objPHPExcel->setActiveSheetIndex(0);
        $sheetCost = $objPHPExcel->getSheet(1);

        $locationGroups = [];
        if ($dataLocationGroup) {
            foreach ($dataLocationGroup as $locationGroup) {
                $locationGroups[$locationGroup->id] = $locationGroup;
            }
        }

        foreach ($data as $item) {
            $locationDestination = empty($item->locations->first()) ? null : $item->locations->first();
            $locationArrival = empty($item->locations->get(1)) ? null : $item->locations->get(1);
            $sheetData
                ->setCellValue($cellLocation[$column++] . $row, $item->quota_code)
                ->setCellValue($cellLocation[$column++] . $row, $item->name)
                ->setCellValue($cellLocation[$column++] . $row, empty($item->vehicleGroup) ? '' :
                    $item->vehicleGroup->code . '|' . $item->vehicleGroup->name)
                ->setCellValue($cellLocation[$column++] . $row, empty($locationDestination) ? '' :
                    $locationDestination->code . '|' . $locationDestination->title)
                ->setCellValue($cellLocation[$column++] . $row, isset($item->location_destination_group_id) && isset($locationGroups[$item->location_destination_group_id]) ?
                    $locationGroups[$item->location_destination_group_id]->code . '|' . $locationGroups[$item->location_destination_group_id]->title : "")
                ->setCellValue($cellLocation[$column++] . $row, empty($locationArrival) ? '' :
                    $locationArrival->code . '|' . $locationArrival->title)
                ->setCellValue($cellLocation[$column++] . $row, isset($item->location_arrival_group_id) && isset($locationGroups[$item->location_arrival_group_id]) ?
                    $locationGroups[$item->location_arrival_group_id]->code . '|' . $locationGroups[$item->location_arrival_group_id]->title : "");
            setNumberValueExcel($sheetData, $cellLocation, $column, $row, $item->distance ? $item->distance : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;
            $column = 0;

            $costs = $item->costs;
            $sheetCost
                ->setCellValue('A' . $rowCost, $item->quota_code)
                ->setCellValue('B' . $rowCost, $item->name);

            if ($costs->isEmpty()) {
                $rowCost++;
                continue;
            }

            $costList = [];
            foreach ($costs as $cost) {
                if (empty($cost['amount'])) continue;

                if (array_key_exists($cost['receipt_payment_id'], $costList)) {
                    $costList[$cost['receipt_payment_id']]['amount'] += (float)$cost['amount'];
                } else {
                    $costList[$cost['receipt_payment_id']] = [
                        'id' => $cost->receipt_payment_id,
                        'amount' => (float)$cost['amount']
                    ];
                }
            }

            foreach ($costList as $itemCost) {
                $index = array_search($itemCost['id'], $costIds);
                if ($index === false) continue;
                $columnCost = $index + 2;
                setNumberValueExcel($sheetCost, $cellLocation, $columnCost, $rowCost, $itemCost['amount'] ? $itemCost['amount'] : 0, null);
                /*  $sheetCost->setCellValueExplicit($this->_getNameFromNumber($index + 2) . $rowCost,
                    numberFormatExcel($itemCost['amount']), PHPExcel_Cell_DataType::TYPE_STRING);*/
            }
            $rowCost++;
        }
        $sheetData->getStyle("A" . ($this->headingRow() + 1) . ":F" . $row)->applyFromArray(array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
                'inside' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
            ),
        ));

        $sheetCost->getStyle("A2:" . $this->_getNameFromNumber(count($costIds) + 1) . ($row - 8))->applyFromArray(array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
                'inside' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                    'size' => 1,
                ),
            ),
        ));

        return $this;
    }

    public function _getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->_getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }
}
