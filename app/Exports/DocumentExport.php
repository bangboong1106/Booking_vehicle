<?php

namespace App\Exports;

use App\Model\Entities\Order;
use App\Repositories\DocumentRepository;
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
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DocumentExport implements  ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_driverRepository = null;
    protected $_vehicleRepository = null;
    protected $_locationRepository = null;
    protected $_receiptPaymentRepository = null;
    protected $_data = [];

    /**
     * @return DocumentRepository
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

    public function __construct(DocumentRepository $documentRepository, $data = [])
    {
        $this->setRepository($documentRepository);
        $this->_data = $data;
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

    private function addDataToExcelFileUpdate($setCell, $data)
    {
        $row = 10;
        $column = 0;

        /** @var Order $order */
        foreach ($data as $i => $order) {
            $setCell
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->order_code)
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->order_no)
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->is_collected_documents == 1 ? 'Có' : 'Không')
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->getStatusDocuments())
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->getDateTime('time_collected_documents', 'H:i'))
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->getDateTime('date_collected_documents'))
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->getDateTime('time_collected_documents_reality', 'H:i'))
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->getDateTime('date_collected_documents_reality'))
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->num_of_document_page)
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->document_type)
                ->setCellValue(config('constant.cell_locations')[$column++] . $row, $order->document_note);
            $column = 0;
            $row++;
        }

        $setCell->getStyle("A" . ($this->headingRow() + 1) . ":K" . $row)->applyFromArray(array(
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

    public function exportFileTemplateUpdate()
    {
        try {
            $nameFile = 'DanhSachCapNhatChungTu_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/Danh_sach_chung_tu.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);

            $data = $this->getRepository()->getListForExport($this->_data);
            $this->addDataToExcelFileUpdate($objPHPExcel->getActiveSheet(), $data);

            $startIndex = 10;
            $maxIndex = 500;

            $objCollectedDocument = $objPHPExcel->getActiveSheet()->getCell('C' . $startIndex)->getDataValidation();
            $objCollectedDocument->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objCollectedDocument->setShowDropDown(true);
            $objCollectedDocument->setFormula1('Unit!$A$2:$A$3');
            $objPHPExcel->getActiveSheet()->setDataValidation('C' . $startIndex . ':C' . $maxIndex, $objCollectedDocument);

            $objStatusDocument = $objPHPExcel->getActiveSheet()->getCell('D' . $startIndex)->getDataValidation();
            $objStatusDocument->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objStatusDocument->setShowDropDown(true);
            $objStatusDocument->setFormula1('Unit!$B$2:$B$3');
            $objPHPExcel->getActiveSheet()->setDataValidation('D' . $startIndex . ':D' . $maxIndex, $objStatusDocument);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/documentTemplate.xlsx');

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            return Response::download($filePath, $nameFile, []);

        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        } catch(Exception $exception) {
            logError($exception);
        } finally {
            if (isset($objPHPExcel)) {
                $objPHPExcel->disconnectWorksheets();// Good to disconnect
                $objPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($objPHPExcel)) {
                unset($objWriter, $objPHPExcel);
            }
        }
    }
    
}
