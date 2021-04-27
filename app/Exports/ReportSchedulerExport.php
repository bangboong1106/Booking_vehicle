<?php

namespace App\Exports;

use App\Common\AppConstant;
use App\Repositories\ReportScheduleRepository;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;
use Illuminate\Support\Arr;

class ReportSchedulerExport implements ShouldAutoSize
{
    protected $_repository = null;

    /**
     * @return null
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


    public function __construct(ReportScheduleRepository $repository)
    {
        $this->setRepository($repository);
    }

    public function exportReportInteractive($type, $startDate, $endDate)
    {
        try {
            $nameFile = config('constant.APP_NAME').'_ThongKe_TuongTac_VanTai_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/'.config('constant.APP_NAME').'_ThongKe_TuongTac_VanTai.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);
            $reports = $this->getRepository()->reportSchedule($type, $startDate, $endDate);
            $datas = collect($reports['data'])->groupBy('entity_name');
            $summary = $reports['summary'];

            $setCell = $objPHPExcel->getActiveSheet();
            $row = 4;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[0] . 1, "THỐNG KÊ TƯƠNG TÁC VÂN TẢI " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            $i = 1;
            foreach ($datas as $key => $item) {

                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, $i)
                    ->setCellValue($cellLocation[$column++] . $row, $key);

                foreach ($item as $tmp) {
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, empty($tmp->status_all) ? 0 : $tmp->status_all, null);
                }

                $column = 0;
                $row++;
                $i++;
            }

            //Dòng tổng
            $column = 0;
            $setCell
                ->setCellValue($cellLocation[$column++] . $row, "Tổng");
            $column++;
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $summary && count($summary) > 0 ? $summary[0]->total : 0, null);

            $setCell->getStyle("A4" . ":C" . $row)->applyFromArray(array(
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

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/' . $nameFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;

        } catch (\Exception $e) {
            logError($e);
        }
    }

    public function exportVehiclePerformance($startDate, $endDate)
    {
        try {
            $nameFile = config('constant.APP_NAME').'_BaoCaoNangSuat_DinhKy_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/'.config('constant.APP_NAME').'_BaoCaoNangSuat_DinhKy.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);
            $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            $datas = $this->getRepository()->reportVehiclePerformance(null, null, $dayCondition, $startDate, $endDate);

            //Sheet năng xuất
            $setCell = $objPHPExcel->getActiveSheet();
            $row = 4;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[1] . 1, "BÁO CÁO NĂNG SUẤT HOẠT ĐỘNG " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            foreach ($datas as $i => $item) {

                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, ($i + 1))
                    ->setCellValue($cellLocation[$column++] . $row, $item['reg_no'])
                    ->setCellValue($cellLocation[$column++] . $row, $item['driver_names']);

                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['distance'] ? $item['distance'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['distance_average_per_day'] ? $item['distance_average_per_day'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_order'] ? $item['total_order'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_route'] ? $item['total_route'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_amount'] ? $item['total_amount'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_cost'] ? $item['total_cost'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_commission'] ? $item['total_commission'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_cod'] ? $item['total_cod'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['revenue'] ? $item['revenue'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['ratio_revenue'] ? $item['ratio_revenue'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $column = 0;
                $row++;
            }
            $setCell->getStyle("A4" . ":M" . $row)->applyFromArray(array(
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

            //Sheet chất lượng dịch vụ
            $setCell = $objPHPExcel->getSheet(1);
            $row = 4;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[1] . 1, "BÁO CÁO CHẤT LƯỢNG DỊCH VỤ " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            foreach ($datas as $i => $item) {

                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, ($i + 1))
                    ->setCellValue($cellLocation[$column++] . $row, $item['reg_no'])
                    ->setCellValue($cellLocation[$column++] . $row, $item['driver_names']);

                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_order'] ? $item['total_order'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_route'] ? $item['total_route'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_route_average_per_day'] ? $item['total_route_average_per_day'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_order_on_time'] ? $item['total_order_on_time'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['total_order_late'] ? $item['total_order_late'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['ratio_order'] ? $item['ratio_order'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $column = 0;
                $row++;
            }

            $setCell->getStyle("A4" . ":I" . $row)->applyFromArray(array(
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

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/' . $nameFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;

        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        }
    }

    //Báo cáo hoạt động theo đội xe và tài xế
    public function exportVehicleTeam($startDate, $endDate, $driverIds, $vehicleTeamIds)
    {
        try {
            $nameFile = config('constant.APP_NAME').'_BaoCao_HoatDong_DoiXe_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/'.config('constant.APP_NAME').'_BaoCao_HoatDong_DoiXe.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);

            $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            $datas = $this->getRepository()->reportVehicleTeam($dayCondition, $startDate, $endDate, $driverIds, $vehicleTeamIds);

            $setCell = $objPHPExcel->getActiveSheet();
            $row = 5;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[0] . 1, "BÁO CÁO HOẠT ĐỘNG THEO ĐỘI XE VÀ TÀI XẾ " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            foreach ($datas as $i => $item) {

                $setCell->mergeCells('A' . $row . ':A' . ($row + count($item['drivers'])));
                $setCell->mergeCells('B' . $row . ':B' . ($row + count($item['drivers']) - 1));
                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, ($i + 1))
                    ->setCellValue($cellLocation[$column++] . $row, $item['vehicle_team_name']);

                foreach ($item['drivers'] as $k => $driver) {
                    if ($k > 0) {
                        $column = 2;
                        $row++;
                    }
                    $setCell->setCellValue($cellLocation[$column++] . $row, $driver['driver_name']);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['total_order'], null);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['total_order_complete'], null);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['ratio_order_complete'], PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['total_order_on_time'], null);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['ratio_order_on_time'], PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['total_order_interactive'], null);
                    setNumberValueExcel($setCell, $cellLocation, $column, $row, $driver['ratio_order_interactive'], PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                }

                $column = 1;
                $row++;
                $setCell->setCellValue($cellLocation[$column++] . $row, 'Tổng');
                setNumberValueExcel($setCell, $cellLocation, $column, $row, count($item['drivers']), null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['summary_order'], null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['summary_order_complete'], null);
                $column++;
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['summary_order_on_time'], null);
                $column++;
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['summary_order_interactive'], null);

                $column = 0;
                $row++;
            }


            $setCell->getStyle("A5" . ":J" . $row)->applyFromArray(array(
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

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/' . $nameFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;

        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        }
    }

    //Báo cáo doanh thu chi phi theo khách hàng
    public function exportCustomer($startDate, $endDate, $customerIds, $customerGroupIds)
    {
        try {
            $nameFile = config('constant.APP_NAME').'_BaoCao_DoanhThu_ChiPhi_KH_' . Carbon::now()->format('d-m-Y') . '.xlsx';

            $fileTemplatePath = public_path('file/'.config('constant.APP_NAME').'_BaoCao_DoanhThu_ChiPhi_KH.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);
            $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            $datas = $this->getRepository()->reportCustomer($dayCondition, $startDate, $endDate, $customerIds, $customerGroupIds);
            $summary = null;
            if (!empty($datas) && count($datas) > 0) {
                $summary = Arr::last($datas);
                unset($datas[count($datas) - 1]);
            }

            $setCell = $objPHPExcel->getActiveSheet();
            $row = 4;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[0] . 1, "BÁO CÁO DOANH THU , CHI PHÍ THEO KHÁCH HÀNG " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            foreach ($datas as $i => $item) {

                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, ($i + 1))
                    ->setCellValue($cellLocation[$column++] . $row, $item['customer_name']);

                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['order_number'] ? $item['order_number'] : 0, null);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['revenue'] ? $item['revenue'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['cost'] ? $item['cost'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                setNumberValueExcel($setCell, $cellLocation, $column, $row, $item['profit'] ? $item['profit'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $column = 0;
                $row++;
            }

            $setCell->setCellValue($cellLocation[$column++] . $row, 'Tổng');
            setNumberValueExcel($setCell, $cellLocation, $column, $row, !empty($datas) ? count($datas) : 0, null);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $summary ? $summary['order_number'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $summary ? $summary['revenue'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $summary ? $summary['cost'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            setNumberValueExcel($setCell, $cellLocation, $column, $row, $summary ? $summary['profit'] : 0, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $setCell->getStyle("A4" . ":F" . $row)->applyFromArray(array(
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

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/' . $nameFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;

        } catch (\Exception $e) {
            logError($e);
        }
    }

    public function exportReportInteractiveDriver($scheduleType)
    {
        try {
            $date = new DateTime();
            $strDateFile = '';
            $startDate = '';
            $endDate = '';
            if ($scheduleType == '0') {
                $previousDate = $date->modify("-1 days");
                $startDate = $previousDate;
                $endDate = $previousDate;
                $strDateFile = $previousDate->format('Ymd');
            }
            if ($scheduleType == '1') {
                $startDate = date("Y-m-d", strtotime('monday', strtotime('last week')));
                $endDate = date("Y-m-d", strtotime('sunday', strtotime('last week')));
                $strDateFile = AppConstant::convertDate($startDate, 'Ymd') . '_' . AppConstant::convertDate($endDate, 'Ymd');
            }
            if ($scheduleType == '2') {
                $startDate = date('Y-m-d', strtotime('first day of last month'));
                $endDate = date('Y-m-d', strtotime('last day of last month'));
                $strDateFile = AppConstant::convertDate($startDate, 'Ymd') . '_' . AppConstant::convertDate($endDate, 'Ymd');
            }
            if (empty($startDate) || empty($endDate))
                return '';

            $startDate = $startDate->format('Y-m-d');
            $endDate = $endDate->format('Y-m-d');

            $nameFile = config('constant.APP_NAME').'_ThongKe_TuongTac_TaiXe_' . $strDateFile . '.xlsx';

            $fileTemplatePath = public_path('file/'.config('constant.APP_NAME').'_ThongKe_TuongTac_TaiXe.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $objPHPExcel->setActiveSheetIndex(0);
            $datas = $this->getRepository()->reportReportInteractiveDriver($startDate, $endDate);

            $setCell = $objPHPExcel->getActiveSheet();
            $row = 5;
            $column = 0;
            $cellLocation = config('constant.cell_locations');

            $setCell->setCellValue($cellLocation[0] . 1, "THỐNG KÊ TƯƠNG TÁC TÀI XẾ " . AppConstant::convertDate($startDate, 'd/m/Y') . ' - ' . AppConstant::convertDate($endDate, 'd/m/Y'));
            $i = 1;
            foreach ($datas as $item) {

                $setCell
                    ->setCellValue($cellLocation[$column++] . $row, $item->username)
                    ->setCellValue($cellLocation[$column++] . $row, $item->full_name)
                    ->setCellValue($cellLocation[$column++] . $row, $item->vehicle_team_name);

                setNumberValueExcel($setCell, $cellLocation, $column, $row, empty($item->tong_don) ? 0 : $item->tong_don, '#,##0');
                setNumberValueExcel($setCell, $cellLocation, $column, $row, empty($item->xac_nhan) ? 0 : $item->xac_nhan, '#,##0');
                $setCell->setCellValue($cellLocation[$column++] . $row, (empty($item->xac_nhan) || $item->tong_don == 0 ? 0 : round($item->xac_nhan / $item->tong_don, 2) * 100) . '%');
                setNumberValueExcel($setCell, $cellLocation, $column, $row, empty($item->nhan_hang) ? 0 : $item->nhan_hang, '#,##0');
                $setCell->setCellValue($cellLocation[$column++] . $row, (empty($item->nhan_hang) || $item->tong_don == 0 ? 0 : round($item->nhan_hang / $item->tong_don, 2) * 100) . '%');
                setNumberValueExcel($setCell, $cellLocation, $column, $row, empty($item->tra_hang) ? 0 : $item->tra_hang, '#,##0');
                $setCell->setCellValue($cellLocation[$column++] . $row, (empty($item->tra_hang) || $item->tong_don == 0 ? 0 : round($item->tra_hang / $item->tong_don, 2) * 100) . '%');

                $column = 0;
                $row++;
                $i++;
            }

            $setCell->getStyle("A5" . ":I" . $row)->applyFromArray(array(
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

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $filePath = public_path('file/export/' . $nameFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);

            return $filePath;

        } catch (\Exception $e) {
            logError($e);
        }
    }
}
