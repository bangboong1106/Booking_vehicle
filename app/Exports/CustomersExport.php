<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Repositories\CustomerRepository;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataValidation;
use PHPExcel_Exception;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CustomersExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_data = [];

    /**
     * @return CustomerRepository
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

    public function __construct(CustomerRepository $customerRepository, $data = [])
    {
        $this->setRepository($customerRepository);
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
            $fileTemplatePath = public_path('file/Danh_sach_khach_hang.xlsx');

            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);
            $objPHPExcel->setActiveSheetIndex(0);

            $objValidation = $objPHPExcel->getActiveSheet()->getCell('C10')->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setShowDropDown(true);
            $objValidation->setFormula1('Unit!$C$3:$C$4');
            $objPHPExcel->getActiveSheet()->setDataValidation("C10:C1000", $objValidation);

            $objValidation = $objPHPExcel->getActiveSheet()->getCell('L10')->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setShowDropDown(true);
            $objValidation->setFormula1('Unit!$B$3:$B$4');
            $objPHPExcel->getActiveSheet()->setDataValidation("L10:L1000", $objValidation);

            $data = $this->getRepository()->getListForExport($this->_data);
            if ($update)
                $this->addDataToExcelFile($objPHPExcel->setActiveSheetIndex(0), $data);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/customers_' . $userId = Auth::User()->id . '.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            $fileName = $update ? 'DanhSachKhachHangCapNhat_' . Carbon::now()->format('d-m-Y') . '.xlsx' : 'Danh_sach_khach_hang.xlsx';

            return Response::download($filePath, $fileName, []);

        } catch (\PHPExcel_Reader_Exception $e) {
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

        foreach ($data as $item) {
            $setCell
                ->setCellValue($cellLocation[$column++] . $row, $item->customer_code)
                ->setCellValue($cellLocation[$column++] . $row, $item->full_name)
                ->setCellValue($cellLocation[$column++] . $row, $item->getCustomerType())
                ->setCellValue($cellLocation[$column++] . $row, isset($item->adminUser) ? $item->adminUser->username : null);
            $column++;
            $setCell->setCellValue($cellLocation[$column++] . $row, isset($item->adminUser) ? $item->adminUser->email : null)
                ->setCellValue($cellLocation[$column++] . $row, $item->mobile_no)
                ->setCellValue($cellLocation[$column++] . $row, $item->delegate)
                ->setCellValue($cellLocation[$column++] . $row, $item->tax_code)
                ->setCellValue($cellLocation[$column++] . $row, $item->current_address)
                ->setCellValue($cellLocation[$column++] . $row, $item->type == config('constant.INDIVIDUAL_CUSTOMERS') ? $item->getDateTime('birth_date') : '')
                ->setCellValue($cellLocation[$column++] . $row, $item->getSexText())
                ->setCellValue($cellLocation[$column++] . $row, $item->note);

            $row++;
            $column = 0;
        }
        $setCell->getStyle("A" . ($this->headingRow() + 1) . ":M" . ($row - 1))->applyFromArray(array(
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
}
