<?php

namespace App\Exports;

use App\Model\Entities\ReceiptPayment;
use App\Repositories\QuotaRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\TemplatePaymentRepository;
use App\Repositories\TemplatePaymentMappingRepository;
use App\Repositories\RoutesRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use PHPExcel_ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPExcel_Cell;
use App\Model\Entities\TemplateLayout;

class RouteExport extends BaseExport implements ShouldAutoSize, WithColumnFormatting
{
    protected $_repository = null;
    protected $_receiptPaymentRepository;
    protected $_templateRepository;
    protected $_templatePaymentRepository;
    protected $_templatePaymentMappingRepository;

    protected $_data = [];

    /**
     * @return RoutesRepository
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
     * @return ReceiptPaymentRepository
     */
    public function getTemplateRepository()
    {
        return $this->_templateRepository;
    }

    /**
     * @param $templateRepository
     */
    public function setTemplateRepository($templateRepository): void
    {
        $this->_templateRepository = $templateRepository;
    }

    /**
     * @param $templatePaymentRepository
     */
    public function setTemplatePaymentRepository($templatePaymentRepository)
    {
        $this->_templatePaymentRepository = $templatePaymentRepository;
    }

    /**
     * @return TemplatePaymentRepository
     */
    public function getTemplatePaymentRepository()
    {
        return $this->_templatePaymentRepository;
    }

    /**
     * @param $templatePaymentMappingRepository
     */
    public function setTemplatePaymentMappingRepository($templatePaymentMappingRepository)
    {
        $this->_templatePaymentMappingRepository = $templatePaymentMappingRepository;
    }

    /**
     * @return TemplatePaymentMappingRepository
     */
    public function getTemplatePaymentMappingRepository()
    {
        return $this->_templatePaymentMappingRepository;
    }

    public function __construct(
        RoutesRepository $routesRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        TemplateRepository $templateRepository,
        TemplatePaymentRepository $templatePaymentRepository,
        TemplatePaymentMappingRepository $templatePaymentMappingRepository,
        $data = []
    ) {
        $this->setRepository($routesRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setTemplatePaymentRepository($templatePaymentRepository);
        $this->setTemplatePaymentMappingRepository($templatePaymentMappingRepository);
        $this->_data = $data;
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

    protected function getExtendTemplate()
    {
        return  $this->getTemplatePaymentRepository()->getTemplatePayment();
    }

    protected function getTemplateLayouts()
    {
        return $this->getTemplateRepository()->getTemplateLayoutByType(config('constant.ROUTE'));
    }

    protected function getTemplateMappingsByID($id)
    {
        return $this->getTemplatePaymentMappingRepository()->getTemplatePaymentMappingByTemplateMappingID($id);
    }

    protected function getExtendSheet(): int
    {
        return 4;
    }
    protected function getFileName(): string
    {
        return 'Danh_sach_phe_duyet_chi_phi_chuyen';
    }

    protected function getFileTemplateName(): string
    {
        return 'routeTemplate';
    }

    protected function getUnitSheet() : int {
        return 1;
    }

    protected function prepareData($user_id){
        $this->costsData = $this->getReceiptPaymentRepository()->getAllExcel();
    }
}
