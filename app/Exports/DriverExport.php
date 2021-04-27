<?php

namespace App\Exports;

use App\Repositories\DriverRepository;
use App\Repositories\VehicleTeamRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataValidation;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DriverExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_data = [];

    /**
     * @return DriverRepository
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

    public function __construct(DriverRepository $driverRepository, $data = [])
    {
        $this->setRepository($driverRepository);
        $this->_data = $data;
    }

    public function view(): View
    {
        return view('backend.driver.export', [
            'drivers' => $this->getRepository()->getListForExport($this->_data)
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
        return 9;
    }

    public function exportFromTemplate($update = false, $dataVehicleTeam, $dataPartner)
    {
        try {
            $maxIndex = 1000;
            $fileTemplatePath = public_path('file/Danh_sach_tai_xe.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $setCell = $objPHPExcel->getSheetByName("Unit");

            $rowVehicleTeam = 1;
            foreach ($dataVehicleTeam as $item) {
                $setCell->setCellValue('A' . $rowVehicleTeam, $item->code . '|' . $item->name);
                $rowVehicleTeam++;
            }
            $rowPartner = 1;
            foreach ($dataPartner as $item) {
                $setCell->setCellValue('B' . $rowPartner, $item->code . '|' . $item->full_name);
                $rowPartner++;
            }

            if ($rowVehicleTeam > 1) {
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('G10')->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('Unit!$A$1:$A$' . ($rowVehicleTeam - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("G10:G" . $maxIndex, $objValidation);
            }
            if ($rowPartner > 1) {
                $objValidation1 = $objPHPExcel->getActiveSheet()->getCell('H10')->getDataValidation();
                $objValidation1->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation1->setShowDropDown(true);
                $objValidation1->setFormula1('Unit!$B$1:$B$' . ($rowPartner - 1));
                $objPHPExcel->getActiveSheet()->setDataValidation("H10:H" . $maxIndex, $objValidation1);
            }

            $data = $this->getRepository()->getListForExport($this->_data);
            if ($update)
                $this->addDataToExcelFile($objPHPExcel->setActiveSheetIndex(0), $data);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/drivers_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            $fileName = $update ? 'DanhSachTaiXeCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx' :
                'Danh_sach_tai_xe.xlsx';
            return Response::download($filePath, $fileName, []);

        } catch (\PHPExcel_Reader_Exception $e) {
        } catch (PHPExcel_Exception $e) {
        }
    }

    /**
     * @param PHPExcel_Worksheet $setCell
     * @param $data
     * @return $this
     * @throws PHPExcel_Exception
     */
    private function addDataToExcelFile($setCell, $data)
    {
        $row = $this->headingRow() + 1;
        $column = 0;
        $cellLocation = config('constant.cell_locations');

        foreach ($data as $item) {

            $setCell
                ->setCellValue($cellLocation[$column++] . $row, $item->code)
                ->setCellValue($cellLocation[$column++] . $row, $item->full_name)
                ->setCellValue($cellLocation[$column++] . $row, $item->mobile_no)
                ->setCellValue($cellLocation[$column++] . $row, $item->tryGet('adminUser')->username)
                ->setCellValue($cellLocation[$column++] . $row, "")
                ->setCellValue($cellLocation[$column++] . $row, $item->tryGet('adminUser')->email)
                ->setCellValue($cellLocation[$column++] . $row, $item->vehicle_team_codes)
                ->setCellValue($cellLocation[$column++] . $row, $item->id_no)
                ->setCellValue($cellLocation[$column++] . $row, $item->driver_license)
                ->setCellValue($cellLocation[$column++] . $row, $item->getDateTime('birth_date'))
                ->setCellValue($cellLocation[$column++] . $row, $item->getSexText())
                ->setCellValue($cellLocation[$column++] . $row, $item->experience_drive)
                ->setCellValue($cellLocation[$column++] . $row, $item->getDateTime('work_date'))
                ->setCellValue($cellLocation[$column++] . $row, $item->address)
                ->setCellValue($cellLocation[$column++] . $row, $item->hometown)
                ->setCellValue($cellLocation[$column++] . $row, $item->evaluate)
                ->setCellValue($cellLocation[$column++] . $row, $item->rank)
                ->setCellValue($cellLocation[$column++] . $row, $item->work_description)
                ->setCellValue($cellLocation[$column++] . $row, $item->note);

            $row++;
            $column = 0;
        }
        $setCell->getStyle("A" . ($this->headingRow() + 1) . ":R" . $row)->applyFromArray(array(
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
                ),
            ),
        ));

        return $this;
    }

    public function exportFileTemplate($data)
    {
        try {
            $fileTemplatePath = public_path('file/Danh_sach_tai_xe.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objValidation = $objPHPExcel->getActiveSheet()->getCell('C10')->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setShowDropDown(true);
            $objValidation->setFormula1('Unit!$C$3:$C$4');
            $objPHPExcel->getActiveSheet()->setDataValidation("C10:C1000", $objValidation);

            $dataList = $this->getRepository()->getListForExport($data);
            $this->addDataToExcelFile($objPHPExcel->setActiveSheetIndex(0), $dataList);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/driverTemplate.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            $fileName = 'DanhSachTaiXeCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx';
            return Response::download($filePath, $fileName, []);

        } catch (\PHPExcel_Reader_Exception $e) {
        } catch (PHPExcel_Exception $e) {
        }
    }
}
