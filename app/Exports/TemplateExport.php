<?php

namespace App\Exports;


use App\Repositories\TemplateRepository;

use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;
use PhpOffice\PhpWord\IOFactory;


class TemplateExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    protected $_templateRepository = null;
    protected $_datas = [];

    public function __construct(
        TemplateRepository $templateRepository,
        $datas = []
    ) {
        $this->_datas = $datas;
        $this->_templateRepository = $templateRepository;
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 4/4/2020
    public function exportCustomTemplate($templateId)
    {
        $template = $this->_templateRepository->getTemplateByTemplateId($templateId);

        $fileTemplatePath = public_path($template->tryGet('getFile')->path);

        $templateLayouts = $this->_templateRepository->getTemplateLayoutByType($template->type);

        $config = [
            'template' => $template,
            'fileTemplatePath' => $fileTemplatePath,
            'templateLayouts' => $templateLayouts,
            'type' => $template->type
        ];

        $ext = pathinfo($fileTemplatePath, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'docx':
                return $this->exportWordFile($config);
                break;
            case 'xlsx':
                return $this->exportExcelFile($config);
                break;
        }
    }

    //Hàm xuất file định dạng Excel
    //CreatedBy nlhoang 13/04/2020
    private function exportExcelFile($config)
    {
        $objWriter = null;
        $objPHPExcel = null;
        $template = $config['template'];
        $fileTemplatePath = $config['fileTemplatePath'];
        $templateLayouts = $config['templateLayouts'];
        $type = $config['type'];

        $respond = [
            'is_success' => true,
            'error_message' => '',
            'message' => ''
        ];

        try {

            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);


            $template->export_type = $template->export_type == null ? config('constant.MULTIPLE_SHEET') : $template->export_type;
            switch ($template->export_type) {
                case config('constant.MULTIPLE_SHEET'):
                    $objPHPExcel = $this->exportMultipleSheet($templateLayouts, $objPHPExcel);
                    break;
                case config('constant.SINGLE_SHEET'):
                    $objPHPExcel = $this->exportSingleSheet($templateLayouts, $objPHPExcel);
                    break;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }
            $templateTypes = config('system.template_type');
            $templateType = array_key_exists($type, $templateTypes) ? $templateTypes[$type] : '';
            $templateExportType = vietnameseToLatin($templateType);
            $exportFilePath = $templateExportType . '_' . Carbon::now()->format('dmYHi') . '.xlsx';


            $filePath = public_path('file/export/' . $exportFilePath);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $nameFile = $exportFilePath;
            $objWriter->save($filePath);
            return Response::download($filePath, $nameFile, []);
        } catch (\PHPExcel_Reader_Exception $e) {
            $respond['is_success'] = false;
            $respond['error_message'] = $e->getMessage();
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            $respond['is_success'] = false;
            $respond['error_message'] = $e->getMessage();

            logError($e);
        } catch (\Exception $e) {
            $respond['is_success'] = false;
            $respond['error_message'] = $e->getMessage();
            logError($e);
        } finally {
            if (isset($objPHPExcel)) {
                $objPHPExcel->disconnectWorksheets(); // Good to disconnect
                $objPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($objPHPExcel)) {
                unset($objWriter, $objPHPExcel);
            }
            if (!$respond['is_success']) {
                return json_encode($respond);
            }
        }
    }

    //Hàm xuất file định dạng Word
    //CreatedBy nlhoang 13/04/2020
    private function exportWordFile($config)
    {
        $objWriter = null;
        $objPHPWord = null;
        $template = $config['template'];
        $fileTemplatePath = $config['fileTemplatePath'];
        $templateLayouts = $config['templateLayouts'];
        $type = $config['type'];
        $respond = [
            'is_success' => true,
            'error_message' => '',
            'message' => ''
        ];

        try {
            $phpWord = new \PhpOffice\PhpWord\TemplateProcessor($fileTemplatePath);

            switch ($template->export_type) {
                case config('constant.MULTIPLE_SHEET'):
                    $phpWord = $this->exportMultipleBlock($templateLayouts, $phpWord);
                    break;
                case config('constant.SINGLE_SHEET'):
                    $phpWord = $this->exportSingleBlock($templateLayouts, $phpWord);
                    break;
            }

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }
            $uuid = gen_uuid();

            $templateTypes = config('system.template_type');
            $templateType = array_key_exists($type, $templateTypes) ? $templateTypes[$type] : '';
            $templateExportType = vietnameseToLatin($templateType);
            $exportFilePath = $templateExportType . '_' . Carbon::now()->format('dmYHi') . '_' . $uuid . '.docx';

            $filePath = public_path('file/export/' . $exportFilePath);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $nameFile = $exportFilePath;
            $phpWord->saveAs($filePath);
            return Response::download($filePath, $nameFile, []);
        } catch (\Exception $e) {
            $respond['is_success'] = false;
            $respond['error_message'] = $e->getMessage();
            logError($e);
        } finally {
            if (isset($objWriter) && isset($objPHPWord)) {
                unset($objWriter, $objPHPWord);
            }
            if (!$respond['is_success']) {
                return json_encode($respond);
            }
        }
    }

    //Hàm xuất dữ liệu ra nhiều bloack trong file word
    //CreatedBy nlhoang 14/04/
    private function exportMultipleBlock($templateLayouts, $phpWord)
    {
        $count = count($this->_datas);
        $phpWord->cloneBlock(config('system.document_block'), $count);
        $blockIndex = 1;
        foreach ($this->_datas as $itm) {
            $data = $itm['data'];
            foreach ($templateLayouts as $templateLayout) {

                $property = $templateLayout->column_name;
                $merge_name = $templateLayout->merge_name;
                $displayName = $templateLayout->display_name;
                $dataType = $templateLayout->data_type;

                $fieldType = $templateLayout->field_type;
                $isInsertedRow = [];

                $length = strlen('ARRAY');
                if ((substr($fieldType, 0, $length) === 'ARRAY')) {
                    $temp = explode(".", $property);
                    $nestProperty = $temp[0];
                    $array = property_exists($data, $nestProperty) ? $data->{$nestProperty} : [];

                    if (!array_key_exists($nestProperty, $isInsertedRow)) {
                        if ($array->count() > 1) {
                            try {
                                $phpWord->cloneRow($displayName, $array->count());
                            } catch (\Exception $e) {
                            }
                        }
                        $isInsertedRow[$nestProperty] = true;
                    }

                    if ($array->count() >= 1) {
                        $index = 1;
                        foreach ($array as $item) {
                            $indexTable = $count == 1 ? '' : '#' . ($index);

                            $value = property_exists($item, $temp[1]) ? $item->{$temp[1]} : null;
                            switch ($fieldType) {
                                case 'ARRAY.INDEX':
                                    $phpWord->setValue($displayName . $indexTable, $index, $blockIndex);
                                    break;
                                case 'SYSDATE':
                                    $phpWord->setValue($displayName . $indexTable, $this->formatValue(Carbon::now(), $dataType), $blockIndex);
                                    break;
                                default:
                                    $phpWord->setValue($displayName . $indexTable, $this->formatValue($value, $dataType), $blockIndex);
                                    break;
                            }
                            $index++;
                        }
                    } else {
                        $phpWord->setValue($displayName, null);
                    }
                } else {
                    $value = property_exists($data, $property) ? $data->{$property} : '';
                    switch ($fieldType) {
                        case 'INDEX':
                            $phpWord->setValue($displayName, $blockIndex, $blockIndex);
                            break;
                        case 'SYSDATE':
                            $phpWord->setValue($displayName, $this->formatValue(Carbon::now(), $dataType), $blockIndex);
                            break;
                        default:
                            $phpWord->setValue($displayName, $this->formatValue($value, $dataType), $blockIndex);
                            break;
                    }
                }
            }

            $blockIndex++;
        }
        return $phpWord;
    }


    //Hàm xuất dữ liệu ra một block trong file word
    //CreatedBy nlhoang 14/04/
    private function exportSingleBlock($templateLayouts, $phpWord)
    {
        $count = count($this->_datas);

        $isInsertedRow = false;
        foreach ($templateLayouts as $templateLayout) {
            $property = $templateLayout->column_name;
            $displayName = $templateLayout->display_name;
            $dataType = $templateLayout->data_type;
            $fieldType = $templateLayout->field_type;

            if (!$isInsertedRow) {
                if ($count > 1) {
                    try {
                        $phpWord->cloneRow($displayName, $count);
                    } catch (\Exception $e) {
                    }
                }
                $isInsertedRow = true;
            }
            $index = 1;
            foreach ($this->_datas as $itm) {
                $data = $itm['data'];

                $indexTable = $count == 1 ? '' : '#' . ($index);
                switch ($fieldType) {
                    case 'ARRAY':
                        if ($templateLayout->allow_group == 1) {
                            $temp = explode(".", $property);
                            $nestProperty = $temp[0];
                            $prop = $temp[1];

                            $array = property_exists($data, $nestProperty) ? $data->{$nestProperty} : [];

                            if (is_array($array) && count($array) > 0) {
                                $value = $array->map(function ($x) use ($prop, $dataType) {
                                    $result = property_exists($x, $prop) ? $x->{$prop} : null;
                                    return '- ' . $this->formatValue($result, $dataType);
                                })->implode(PHP_EOL);
                                $phpWord->setValue($displayName . $indexTable, $value);
                            }
                        }
                        break;
                    case 'INDEX':
                        $phpWord->setValue($displayName . $indexTable, ($index));
                        break;
                    case 'SYSDATE':
                        $phpWord->setValue($displayName . $indexTable, $this->formatValue(Carbon::now(), $dataType));
                        break;
                    default:
                        $value = property_exists($data, $property) ? $data->{$property} : '';
                        $phpWord->setValue($displayName . $indexTable, $this->formatValue($value, $dataType));
                        break;
                }
                $index++;
            }
        }
        return $phpWord;
    }

    //Hàm xuất dữ liệu ra nhiều sheet
    //CreatedBy nlhoang 10/04/
    private function exportMultipleSheet($templateLayouts, $objPHPExcel)
    {
        $startSheet = $objPHPExcel->setActiveSheetIndex(0);
        $invalidCharacters = $startSheet->getInvalidCharacters();
        $title = str_replace($invalidCharacters, '', $this->_datas[0]['name']);
        $startSheet->setTitle($title);

        // Xuất dữ liệu nhiều sheet
        if (count($this->_datas) > 1) {
            for ($x = 1; $x <= count($this->_datas) - 1; $x++) {
                $newSheet = clone $startSheet;
                $newInvalidCharacters = $newSheet->getInvalidCharacters();
                $newTitle = str_replace($newInvalidCharacters, '', $this->_datas[$x]['name']);
                $newSheet->setTitle($newTitle);
                $objPHPExcel->addSheet($newSheet, $x);
            }
        }

        $idx = 0;
        foreach ($this->_datas as $itm) {
            $data = $itm['data'];
            $sheet = $objPHPExcel->setActiveSheetIndex($idx);

            $isInsertedRow = [];
            foreach ($templateLayouts as $templateLayout) {

                $property = $templateLayout->column_name;
                $fieldType = $templateLayout->field_type;
                $dataType = $templateLayout->data_type;

                // Nếu mapping với dữ liệu dạng bảng
                $length = strlen('ARRAY');
                if ((substr($fieldType, 0, $length) === 'ARRAY')) {
                    $temp = explode(".", $property);
                    $nestPropery = $temp[0];
                    $array = property_exists($data, $nestPropery) ? collect($data->{$nestPropery}) : [];

                    $cells = $this->findCells($sheet, $templateLayout->merge_name);
                    foreach ($cells as $cell) {
                        if (!array_key_exists($nestPropery, $isInsertedRow)) {
                            $row = $sheet->getCell($cell)->getRow();
                            $count = $array->count();
                            if ($count > 1) {
                                $sheet->insertNewRowBefore($row + 1, $count);
                            }
                            $isInsertedRow[$nestPropery] = true;
                            $this->_highestRow = $this->_highestRow != 0 ? $this->_highestRow + $count : $this->_highestRow;
                        }
                    }
                    if ($array->count() != 0) {
                        $index = 0;
                        foreach ($array as $item) {
                            $value = property_exists($item, $temp[1]) ? $item->{$temp[1]} : null;
                            foreach ($cells as $cell) {
                                $column = $sheet->getCell($cell)->getColumn();
                                $row = $sheet->getCell($cell)->getRow();
                                switch ($fieldType) {
                                    case 'ARRAY.INDEX':
                                        $sheet->setCellValue($column . ($row + $index), $index + 1);
                                        break;
                                    case 'SYSDATE':
                                        $sheet->setCellValue($column . ($row + $index), $this->formatValue(Carbon::now(), $dataType));
                                        break;
                                    default:
                                        $rowIdx = ($row + $index);
                                        if ($dataType == 'NUMBER') {
                                            setNumberValueExcelNR($sheet, $column, $rowIdx, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                        } else if ($dataType == 'CURRENCY') {
                                            setNumberValueExcelNR($sheet, $column, $rowIdx, $this->formatValue($value, $dataType), null);
                                        } else {
                                            $sheet->setCellValue($column . ($row + $index), $this->formatValue($value, $dataType));
                                        }
                                        break;
                                }
                            }
                            $index++;
                        }
                    } else {
                        foreach ($cells as $cell) {
                            $sheet->setCellValue($cell, null);
                        }
                    }
                } else {
                    $value = property_exists($data, $property) ? $data->{$property} : '';
                    $cells = $this->findCells($sheet, $templateLayout->merge_name);

                    foreach ($cells as $cell) {
                        switch ($fieldType) {
                            case 'SYSDATE':
                                $sheet->setCellValue($cell, $this->formatValue(Carbon::now(), $dataType));
                                break;
                            default:
                                $column = $sheet->getCell($cell)->getColumn();
                                $row = $sheet->getCell($cell)->getRow();
                                if ($dataType == 'NUMBER') {
                                    setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                } else if ($dataType == 'CURRENCY') {
                                    setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), null);
                                } else {
                                    $sheet->setCellValue($cell, $this->formatValue($value, $dataType));
                                }
                                break;
                        }
                    }
                }
            }

            $idx++;
        }

        return $objPHPExcel;
    }


    //Hàm xuất dữ liệu ra nhiều sheet
    //CreatedBy nlhoang 10/04/
    private function exportSingleSheet($templateLayouts, $objPHPExcel)
    {
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $count = count($this->_datas);
        $ignoreFieldType = ['ARRAY.INDEX'];
        $isInsertedRows = [];
        $isDynamicHeader = false;

        //        logError(json_encode($this->_datas));
        foreach ($templateLayouts as $templateLayout) {

            if (in_array($templateLayout->field_type, $ignoreFieldType)) continue;

            $property = $templateLayout->column_name;
            $fieldType = $templateLayout->field_type;
            $dataType = $templateLayout->data_type;
            $displayName = $templateLayout->display_name;

            $cells = $this->findCells($sheet, $templateLayout->merge_name);

            if (
                $templateLayout->allow_dynamic_column != 1 &&
                $templateLayout->dynamic_column_header != 1
            ) {
                if ($count > 1) {
                    // Xử lý sinh thêm dòng
                    foreach ($cells as $cell) {
                        $row = $sheet->getCell($cell)->getRow();
                        if (!array_key_exists($row, $isInsertedRows)) {
                            $sheet->insertNewRowBefore($row + 1, $count - 1);
                            $isInsertedRows[$row] = true;
                            $this->_highestRow = $this->_highestRow != 0 ? $this->_highestRow + $count : $this->_highestRow;

                            foreach ($isInsertedRows as $key => $isInsertedRow) {
                                if ($key > $row) {
                                    unset($isInsertedRows[$key]);
                                    $isInsertedRows[$key + $count] = true;
                                }
                            }
                        }
                    }
                }
            }
            if ($count != 0) {
                $index = 0;
                foreach ($this->_datas as $item) {
                    $data = $item['data'];

                    $singleCells = $this->findCells($sheet, '${' . $displayName . '#' . ($index + 1) . '}');
                    foreach ($singleCells as $cell) {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        switch ($fieldType) {
                            case 'ARRAY':
                            case 'ARRAY.INDEX':
                                $sheet->setCellValue($column . $row, null);
                                break;
                            default:
                                $value = property_exists($data, $property) ? $data->{$property} : null;
                                if ($dataType == 'NUMBER') {
                                    setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                } else if ($dataType == 'CURRENCY') {
                                    setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), null);
                                } else {
                                    $sheet->setCellValue($column . $row, $this->formatValue($value, $dataType));
                                }
                                break;
                        }
                    }

                    foreach ($cells as $cell) {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        $nextRow = ($row + $index);
                        $coordinate = $column . $nextRow;
                        switch ($fieldType) {
                            case 'ARRAY':
                                $temp = explode(".", $property);
                                $nestProperty = $temp[0];
                                $prop = $temp[1];

                                $array = property_exists($data, $nestProperty) ? collect($data->{$nestProperty}) : [];

                                // Xử lý những dữ liệu dạng mảng thì cộng gộp thành chuỗi string ngăn cách bằng dầu Xuống dòng
                                if ($templateLayout->allow_group == 1 && !empty($array)) {
                                    $value = $array->map(function ($x) use ($prop, $dataType) {
                                        $result = property_exists($x, $prop) ? $x->{$prop} : null;
                                        return $this->formatValue($result, $dataType);
                                    })->implode(PHP_EOL);

                                    $sheet->setCellValue($column . $nextRow, $value);
                                    $sheet->getStyle($coordinate)->getAlignment()->setWrapText(true);
                                }
                                //Xử lý dữ liệu dạng bảng sẽ sinh cột động như Dữ liệu chi phí
                                if ($templateLayout->allow_dynamic_column == 1) {
                                    $columnIndex = PHPExcel_Cell::columnIndexFromString($column);
                                    if ($templateLayout->dynamic_column_header == 1) {
                                        if ($isDynamicHeader == false) {
                                            $isDynamicHeader = true;
                                            $sheet->insertNewColumnBefore($column, count($array) > 1 ? count($array) - 1 : 0);

                                            for ($i = 0; $i < count($array); $i++) {
                                                $value = property_exists($array[$i], $prop) ? $array[$i]->{$prop} : null;
                                                $tempColumnIndex = $columnIndex + $i;
                                                $tempColumnString = PHPExcel_Cell::stringFromColumnIndex($tempColumnIndex - 1);

                                                if ($dataType == 'NUMBER') {
                                                    setNumberValueExcelNR($sheet, $tempColumnString, $nextRow, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                                } else if ($dataType == 'CURRENCY') {
                                                    setNumberValueExcelNR($sheet, $tempColumnString, $nextRow, $this->formatValue($value, $dataType), null);
                                                } else {
                                                    $sheet->setCellValue($tempColumnString . $nextRow, $this->formatValue($value, $dataType));
                                                }
                                            }
                                        }
                                    } else {
                                        // Do khi gen header trong Excel thì trường trộn bị nhảy về sau cùng nên cần -$i
                                        $countDynamic = count($array) - 1;
                                        for ($i = 0; $i <= $countDynamic; $i++) {
                                            $value = property_exists($array[$countDynamic - $i], $prop) ? $array[$countDynamic - $i]->{$prop} : null;
                                            $tempColumnIndex = $columnIndex - $i;
                                            $tempColumnString = PHPExcel_Cell::stringFromColumnIndex($tempColumnIndex - 1);

                                            if ($dataType == 'NUMBER') {
                                                setNumberValueExcelNR($sheet, $tempColumnString, $nextRow, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                            } else if ($dataType == 'CURRENCY') {
                                                setNumberValueExcelNR($sheet, $tempColumnString, $nextRow, $this->formatValue($value, $dataType), null);
                                            } else {
                                                $sheet->setCellValue($tempColumnString . $nextRow, $this->formatValue($value, $dataType));
                                            }
                                        }
                                    }
                                }
                                break;
                            case 'INDEX':
                                $sheet->setCellValue($coordinate, $index + 1);
                                break;
                            case 'SYSDATE':
                                $sheet->setCellValue($coordinate, $this->formatValue(Carbon::now(), $dataType));
                                break;
                            default:
                                $value = property_exists($data, $property) ? $data->{$property} : null;
                                if ($dataType == 'NUMBER') {
                                    setNumberValueExcelNR($sheet, $column, $nextRow, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                } else if ($dataType == 'CURRENCY') {
                                    setNumberValueExcelNR($sheet, $column, $nextRow, $this->formatValue($value, $dataType), null);
                                } else {
                                    $sheet->setCellValue($coordinate, $this->formatValue($value, $dataType));
                                }
                                break;
                                break;
                        }
                    }
                    $index++;
                }
            } else {
                foreach ($cells as $cell) {
                    $sheet->setCellValue($cell, null);
                }
            }
        }
        return $objPHPExcel;
    }

    //Hàm format dữ liệu hiển thị đầu ra
    //CreatedBy nlhoang 14/04/2020
    private function formatValue($value, $dataType)
    {
        $result = null;
        switch ($dataType) {
            case 'DATETIME':
                $result = empty($value) ? '' : Carbon::parse($value)->format('d/m/Y H:i');
                break;
            case 'DATE':
                $result = empty($value) ? '' : Carbon::parse($value)->format('d/m/Y');
                break;
            case 'TIME':
                $result = $value;
                break;
            case 'NUMBER':
                $result = empty($value) ? 0 : $value;
                break;
            case 'CURRENCY':
                $result = empty($value) ? 0 : $value;
                break;
            default:
                $result = $value;
                break;
        }
        return $result;
    }

    private $_highestRow = 0;
    private $_highestCol = 0;

    //Hàm thay thế trong Excel
    //CreatedBy nlhoang 05/04/2020
    private function findCells($currentWorksheet, $searchTerm)
    {
        $this->_highestRow = $this->_highestRow == 0 ? $currentWorksheet->getHighestRow() : $this->_highestRow;        // Error: always return 1000 ?
        $this->_highestCol = $this->_highestCol == 0 ? $currentWorksheet->getHighestDataColumn() : $this->_highestCol;
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($this->_highestCol);

        $foundInCells = array();

        for ($col = 0; $col < $highestColumnIndex; $col++) {
            for ($row = 1; $row <= $this->_highestRow; $row++) {
                $cell = $currentWorksheet->getCellByColumnAndRow($col, $row);
                if ($cell->getValue() === $searchTerm) {

                    $foundInCells[] = $cell->getCoordinate();
                }
            }
        }
        return ($foundInCells);


        //        $foundInCells = array();
        //        foreach ($currentWorksheet->getRowIterator() as $row) {
        //
        //            $cellIterator = $row->getCellIterator();
        //            foreach ($cellIterator as $cell) {
        //
        //
        //                if ($cell->getValue() == $searchTerm) {
        //
        //                    $foundInCells[] = $cell->getCoordinate();
        //                }
        //            }
        //        }
        //
        //        return ($foundInCells);
    }

    /**
     * @return View
     */
    public
    function view(): View
    {
        // TODO: Implement view() method.
    }

    /**
     * @return array
     */
    public
    function columnFormats(): array
    {
        // TODO: Implement columnFormats() method.
    }
}
