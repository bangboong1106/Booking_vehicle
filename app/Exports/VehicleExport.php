<?php

namespace App\Exports;

use App\Model\Entities\Vehicle;
use App\Repositories\DriverRepository;
use App\Repositories\DriverVehicleRepository;
use App\Repositories\VehicleRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataValidation;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use PHPExcel_Style_Border;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class VehicleExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_driverVehicleRepository = null;
    protected $_driverRepository = null;
    protected $_data = [];

    /**
     * @return VehicleRepository
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
     * @return DriverVehicleRepository
     */
    public function getDriverVehicleRepository()
    {
        return $this->_driverVehicleRepository;
    }

    /**
     * @param null $driverVehicleRepository
     */
    public function setDriverVehicleRepository($driverVehicleRepository): void
    {
        $this->_driverVehicleRepository = $driverVehicleRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param null $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }


    public function __construct(
        VehicleRepository $vehicleRepository,
        DriverVehicleRepository $driverVehicleRepository,
        DriverRepository $driverRepository,
        $data = []
    )
    {
        $this->setRepository($vehicleRepository);
        $this->setDriverVehicleRepository($driverVehicleRepository);
        $this->setDriverRepository($driverRepository);
        $this->_data = $data;
    }

    public function view(): View
    {
        $vehicles = $this->getRepository()->getListForExport($this->_data);
        if ($vehicles != null) {
            for ($i = 0; $i < $vehicles->total(); $i++) {
                $driver_ids = null;
                $driverVehicle = $this->getDriverVehicleRepository()->getItemsByVehicleID($vehicles[$i]->id);
                if ($driverVehicle)
                    $driver_ids = $driverVehicle->pluck('driver_id')->toArray();
                $driverList = [];
                if ($driver_ids != null) {
                    foreach ($driver_ids as $driver_id) {
                        $driverEntity = $this->getDriverRepository()->getItemById($driver_id);
                        if ($driverEntity != null) {
                            $driverList[] = $driverEntity;
                        }
                    }
                }
                $driver_codes = collect($driverList)->pluck('code')->implode(';');
                $vehicles[$i]->driver_codes = $driver_codes;
            }
        }
        return view('backend.vehicle.export', [
            'vehicles' => $vehicles
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
        return 3;
    }

    public function exportFromTemplate()
    {
        try {
            $fileTemplatePath = public_path('file/Danh_sach_xe.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $vehicles = $this->getRepository()->getListForExport($this->_data);
            if ($vehicles != null) {
                for ($i = 0; $i < $vehicles->total(); $i++) {
                    $driver_ids = $this->getDriverVehicleRepository()->getItemsByVehicleID($vehicles[$i]->id)->pluck('driver_id')->toArray();
                    $driverList = [];
                    if ($driver_ids != null) {
                        foreach ($driver_ids as $driver_id) {
                            try {
                                $driverEntity = $this->getDriverRepository()->getItemById($driver_id);
                                if ($driverEntity != null)
                                    $driverList[] = $driverEntity;
                            } catch (Exception $e) {
                            }
                        }
                    }
                    $driver_codes = collect($driverList)->pluck('full_name')->implode(';');
                    $vehicles[$i]->driver_codes = $driver_codes;
                }
            }

            $this->addDataToExcelFile($objPHPExcel->setActiveSheetIndex(0), $vehicles);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/vehicles_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            return Response::download($filePath, 'Danh_sach_xe.xlsx', []);
        } catch (PHPExcel_Reader_Exception $e) {
        } catch (PHPExcel_Exception $e) {
        }

        return null;
    }

    /**
     * @param PHPExcel_Worksheet $setCell
     * @param $data
     * @return $this
     * @throws PHPExcel_Exception
     */
    private function addDataToExcelFile($setCell, $data)
    {
        $row = 10;
        $column = 0;
        $cellLocation = config('constant.cell_locations');

        /** @var Vehicle $item */
        foreach ($data as $item) {
            $driver = $item->drivers->count() > 0 ? $item->drivers->first() : null;
            $setCell
                ->setCellValue($cellLocation[$column++] . $row, $item->reg_no)
                ->setCellValue($cellLocation[$column++] . $row, $item->getStatus())
                ->setCellValue($cellLocation[$column++] . $row, $item->getType())
                ->setCellValue($cellLocation[$column++] . $row, $item->getActive())
                ->setCellValue($cellLocation[$column++] . $row, isset($item->vehicleGroup) ? $item->vehicleGroup->code . '|' . $item->vehicleGroup->name : null)
                ->setCellValue($cellLocation[$column++] . $row, isset($driver) ? $driver->code . '|' . $driver->full_name : null);

            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->volume ? $item->volume : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->weight ? $item->weight : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->length ? $item->length : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->width ? $item->width : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->height ? $item->height : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $setCell->setCellValue($cellLocation[$column++] . $row, $item->tryGet('vehicleGeneralInfo')->category_of_barrel)
                ->setCellValue($cellLocation[$column++] . $row, $item->tryGet('vehicleGeneralInfo')->weight_lifting_system);

            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->tryGet('vehicleGeneralInfo')->max_fuel ? $item->tryGet('vehicleGeneralInfo')->max_fuel : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->tryGet('vehicleGeneralInfo')->max_fuel_with_goods ? $item->tryGet('vehicleGeneralInfo')->max_fuel_with_goods : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $setCell->setCellValue($cellLocation[$column++] . $row, $item->tryGet('vehicleGeneralInfo')->register_year)
                ->setCellValue($cellLocation[$column++] . $row, $item->tryGet('vehicleGeneralInfo')->brand)
                ->setCellValue($cellLocation[$column++] . $row, isset($item->gpsCompany) ? $item->gpsCompany->id . '|' . $item->gpsCompany->name : null);

            setNumberValueExcel($setCell, $cellLocation, $column, $row, $item->repair_distance ? $item->repair_distance : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $setCell->setCellValue($cellLocation[$column++] . $row, $item->getDateTime('repair_date'));
            $row++;
            $column = 0;
        }
        $setCell->getStyle("A" . (10) . ":T" . ($row - 1))->applyFromArray(array(
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

    public function exportFileTemplate($dataVehicleGroup, $dataDriver, $dataGpsCompany, $dataPartner, $update)
    {
        try {
            $maxIndex = 1000;

            $fileTemplatePath = public_path('file/Danh_sach_xe.xlsx');

            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);
            $objPHPExcel->setActiveSheetIndex(0);
            $setCell = $objPHPExcel->getSheetByName("Data");

            $rowVehicleGroup = 1;
            foreach ($dataVehicleGroup as $item) {
                $setCell->setCellValue('A' . $rowVehicleGroup, $item->code . '|' . $item->name);
                $rowVehicleGroup++;
            }

            $rowDriver = 1;
            foreach ($dataDriver as $item) {
                $setCell->setCellValue('B' . $rowDriver, $item->code . '|' . $item->full_name);
                $rowDriver++;
            }

            $rowGpsCompany = 1;
            foreach ($dataGpsCompany as $item) {
                $setCell->setCellValue('C' . $rowGpsCompany, $item->id . '|' . $item->name);
                $rowGpsCompany++;
            }

            $rowPartner = 1;
            foreach ($dataPartner as $item) {
                $setCell->setCellValue('D' . $rowPartner, $item->code . '|' . $item->full_name);
                $rowPartner++;
            }

            if ($rowVehicleGroup > 1) {
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('E10')->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('Data!$A$1:$A$' . ($rowVehicleGroup - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("E10:E" . $maxIndex, $objValidation);
            }

            if ($rowPartner > 1) {
                $objValidation4 = $objPHPExcel->getActiveSheet()->getCell('F10')->getDataValidation();
                $objValidation4->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation4->setShowDropDown(true);
                $objValidation4->setFormula1('Data!$D$1:$D$' . ($rowPartner - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("F10:F" . $maxIndex, $objValidation4);
            }

            if ($rowDriver > 1) {
                $objValidation2 = $objPHPExcel->getActiveSheet()->getCell('G10')->getDataValidation();
                $objValidation2->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation2->setShowDropDown(true);
                $objValidation2->setFormula1('Data!$B$1:$B$' . ($rowDriver - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("G10:G" . $maxIndex, $objValidation2);
            }

            if ($rowGpsCompany > 1) {
                $objValidation3 = $objPHPExcel->getActiveSheet()->getCell('S10')->getDataValidation();
                $objValidation3->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation3->setShowDropDown(true);
                $objValidation3->setFormula1('Data!$C$1:$C$' . ($rowGpsCompany - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("S10:S" . $maxIndex, $objValidation3);
            }


            if ($update) {
                $vehicles = $this->getRepository()->getListForExport($this->_data);
                $this->addDataToExcelFile($objPHPExcel->setActiveSheetIndex(0), $vehicles);
            }
            $fileName = $update ? 'DanhSachXeCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx'
                : 'Danh_sach_xe.xlsx';

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/vehicleTemplate.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            return Response::download($filePath, $fileName, []);
        } catch (PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (PHPExcel_Exception $e) {
            logError($e);
        }

        return null;
    }
}
