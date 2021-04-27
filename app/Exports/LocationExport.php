<?php

namespace App\Exports;

use App\Model\Entities\Location;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\WardRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel;
use PHPExcel_Cell_DataValidation;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Exception;
use PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LocationExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_wardRepository;
    protected $_districtRepository;
    protected $_provinceRepository;
    protected $_customerRepository;
    protected $_data = [];

    /**
     * @return LocationRepository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * @param LocationRepository $repository
     */
    public function setRepository($repository): void
    {
        $this->_repository = $repository;
    }

    /**
     * @return WardRepository
     */
    public function getWardRepository()
    {
        return $this->_wardRepository;
    }

    /**
     * @param WardRepository $wardRepository
     */
    public function setWardRepository($wardRepository): void
    {
        $this->_wardRepository = $wardRepository;
    }

    /**
     * @return DistrictRepository
     */
    public function getDistrictRepository()
    {
        return $this->_districtRepository;
    }

    /**
     * @param DistrictRepository $districtRepository
     */
    public function setDistrictRepository($districtRepository): void
    {
        $this->_districtRepository = $districtRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->_provinceRepository;
    }

    /**
     * @param ProvinceRepository $provinceRepository
     */
    public function setProvinceRepository($provinceRepository): void
    {
        $this->_provinceRepository = $provinceRepository;
    }

    /**
     * @return mixed
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param mixed $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    public function __construct(LocationRepository $locationRepository, CustomerRepository $customerRepository, $data = [])
    {
        $this->setRepository($locationRepository);
//        $this->setProvinceRepository($provinceRepository);
//        $this->setDistrictRepository($districtRepository);
//        $this->setWardRepository($wardRepository);
        $this->setCustomerRepository($customerRepository);
        $this->_data = $data;
    }

    public function view(): View
    {
        return view('backend.location.export', [
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
            $fileTemplatePath = public_path('file/Danh_sach_dia_diem.xlsx');

            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $setCell = $objPHPExcel->getSheetByName("Data");
            $dataCustomer = $this->getCustomerRepository()->getGoodsOwnerList();
            $rowCustomer = 100;
            foreach ($dataCustomer as $item) {
                $setCell->setCellValue('A' . $rowCustomer, $item->customer_code . '|' . $item->full_name);
                $rowCustomer++;
            }

            $objPHPExcel->setActiveSheetIndex(0);
            $objValidation = $objPHPExcel->getActiveSheet()->getCell('C10')->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setShowDropDown(true);
            $objValidation->setFormula1('Data!$A$2:$A$64');
            $objPHPExcel->getActiveSheet()->setDataValidation("C10:C1000", $objValidation);

            $objPHPExcel->setActiveSheetIndex(0);
            $objValidation = $objPHPExcel->getActiveSheet()->getCell('G10')->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setShowDropDown(true);
            $objValidation->setFormula1('Data!$A$100:$A$'.$rowCustomer);
            $objPHPExcel->getActiveSheet()->setDataValidation("G10:G1000", $objValidation);


            for ($i = 10; $i <= 500; $i++) {
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('=INDIRECT("_"&SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(C' . $i . '," ","_"),"|", "_"),"-","_"),"\'","_"))');

                $objValidation = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setShowDropDown(true);
                $objValidation->setFormula1('=INDIRECT("_"&SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(SUBSTITUTE(D' . $i . '," ","_"),"|", "_"),"-","_"),"\'","_"))');
            }

            if ($update) {
                $data = $this->getRepository()->getListForExport($this->_data);
                $this->addDataToExcelFile($objPHPExcel, $data);
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/location_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            $fileName = $update ? 'DanhSachDiaDiemCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx' :
                'Danh_sach_dia_diem.xlsx';

            return Response::download($filePath, $fileName, []);

        } catch (PHPExcel_Reader_Exception $e) {
        } catch (PHPExcel_Exception $e) {
        }
        return null;
    }

    /**
     * @param PHPExcel $objPHPExcel
     * @param $data
     * @return $this
     * @throws PHPExcel_Exception
     */
    private function addDataToExcelFile($objPHPExcel, $data)
    {
        $row = 10;
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $column = 0;
        $cellLocation = config('constant.cell_locations');
        /** @var Location $location */
        foreach ($data as $location) {
            $sheet
                ->setCellValue($cellLocation[$column++] . $row, $location->code)
                ->setCellValue($cellLocation[$column++] . $row, $location->title)
                ->setCellValue($cellLocation[$column++] . $row, empty($location->province) ? '' :
                    $location->province->province_id . '|' . $location->province->title)
                ->setCellValue($cellLocation[$column++] . $row, empty($location->district) ? '' :
                    $location->district->district_id . '|' . $location->district->title)
                ->setCellValue($cellLocation[$column++] . $row, empty($location->ward) ? '' :
                    $location->ward->ward_id . '|' . $location->ward->title)
                ->setCellValue($cellLocation[$column++] . $row, $location->address);
            $row++;
            $column = 0;
        }

        return $this;
    }

    protected function _getNameFromNumber($num)
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
