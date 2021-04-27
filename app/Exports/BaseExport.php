<?php

namespace App\Exports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Response;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_NumberFormat;
use PHPExcel_ReferenceHelper;
use stdClass;

class BaseExport
{
    protected $_data = [];
    public $is_extend = false;
    public $is_update = false;
    public $excelColumnConfig = null;
    protected $extend_template = null;

    public function __construct(
        $data = []
    ) {
        $this->_data = $data;
    }

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

    protected function getFileName(): string
    {
        return '';
    }

    protected function getFileTemplateName(): string
    {
        return '';
    }

    protected function getUnitSheet(): int
    {
        return 0;
    }

    protected function getExtendSheet(): int
    {
        return 0;
    }

    // Hàm chuẩn bị data để xuất dữ liệu
    protected function prepareData($user_id)
    {
    }

    // Hàm custom thêm datavalidation ở các sheet khác
    protected function afterAddDataValidation($objPHPExcel)
    {
    }

    // Hàm xuất file biểu mẫu Excel
    public function exportFileTemplate($user_id)
    {
        $file_name = $this->getFileName();
        if ($this->is_update == true) {
            $nameFile = $file_name . '_' . Carbon::now()->format('d_m_Y') . '.xlsx';
        } else {
            $nameFile = $file_name . '.xlsx';
        }
        $filePath =  $this->generateExcelFile($user_id);
        return Response::download($filePath, $nameFile, []);
    }

    public function generateExcelFile($user_id, $is_unique = false)
    {
        try {

            $this->prepareData($user_id);

            $file_name = $this->getFileName();

            $fileTemplatePath = public_path('file/' . $file_name . '.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            if ($this->is_extend) {
                $this->extend_template = $this->getExtendTemplate();
                if (isset($this->extend_template)) {
                    $template_path = public_path($this->extend_template->tryGet('getFile')->path);
                    if (file_exists(($template_path))) {
                        $temple_file_type = PHPExcel_IOFactory::identify($fileTemplatePath);
                        $template_reader = PHPExcel_IOFactory::createReader($temple_file_type);
                        $template_excel = $template_reader->load($template_path);
                        $k = 0;
                        foreach ($template_excel->getSheetNames() as $sheet_name) {
                            $sheet = $template_excel->getSheetByName($sheet_name);
                            $sheet->setTitle('BieuMau_' . ($k == 0 ? 'BangTinh' : $sheet_name));
                            $objPHPExcel->addExternalSheet($sheet);
                            unset($sheet);
                            $k++;
                        }
                    }
                }
            }

            if ($this->is_update == true) {
                $data = $this->getRepository()->getListForExport($this->_data);
                $this->addDataToExcelFile($objPHPExcel, $data);
            }


            $this->processFile($objPHPExcel, $this->extend_template);


            if ($this->is_extend && isset($this->extend_template)) {
                $paymentTemplatePath = public_path($this->extend_template->tryGet('getFile')->path);
                if (file_exists(($paymentTemplatePath))) {
                    $templateLayouts = $this->getTemplateLayouts();
                    $extendData =  $this->getRepository()->getExportByIDs($data->pluck('id')->toArray(), $this->extend_template);
                    $objPHPExcel = $this->replaceExtendTemplateSheet($this->extend_template, $templateLayouts, $objPHPExcel, $extendData);
                }
            }

            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            if ($is_unique) {
                $filePath = public_path('file/export/' . $this->getFileTemplateName() . '_' . uniqid() . '.xlsx');
            } else {
                $filePath = public_path('file/export/' . $this->getFileTemplateName() . '.xlsx');
            }

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
        finally {
            if (isset($objPHPExcel)) {
                $objPHPExcel->disconnectWorksheets();// Good to disconnect
                $objPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($objPHPExcel)) {
                unset($objWriter, $objPHPExcel);
            }
        }
    }

    // Lấy ra biểu mẫu mở rộng nếu có
    protected function getExtendTemplate()
    {
        return new stdClass();
    }

    // Lấy ra danh sách trường trộn để in mẫu
    protected function getTemplateLayouts()
    {
        return [];
    }


    //Lấy danh sách các trường maping với mẫu Excel
    protected function getTemplateMappingsByID($id)
    {
        return [];
    }

    // Hàm xử lý file Excel
    private function processFile($objPHPExcel, $extend_template)
    {
        $excelColumnMappingConfigs = $this->excelColumnConfig->excelColumnMappingConfigs;
        $header_index = $this->excelColumnConfig->header_index;
        $max_row = $this->excelColumnConfig->max_row;
        $unit_index = 0;
        $arr_header_group = [];
        $key_index = 0;

        $is_nested = false;

        foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig) {

            $data_type = $excelColumnMappingConfig->data_type;
            $column_index = $excelColumnMappingConfig->column_index;
            $code = $excelColumnMappingConfig->code;
            $title = $excelColumnMappingConfig->title;
            $column_name = $excelColumnMappingConfig->column_name;
            $comment = $excelColumnMappingConfig->comment;
            $header_group = $excelColumnMappingConfig->header_group;
            $width = $excelColumnMappingConfig->width;
            $background_color = $excelColumnMappingConfig->background_color;

            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();
            if ($excelColumnMappingConfig->is_key == 1) {
                $key_index = $column_index;
            }
            $this->_generateHeader($sheet, $column_index . $header_index, $column_name, $background_color, $comment);

            $sheet->getColumnDimension($column_index)->setWidth(empty($width) ? 20 : $width);

            if ($excelColumnMappingConfig->collapse == 1) {
                $sheet->getColumnDimension($column_index)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);
            }

            if (empty($excelColumnMappingConfig->nested_field)) {
                $this->_generateHeaderGroup($sheet, $excelColumnMappingConfigs, $column_index, $header_index, $header_group, $arr_header_group);
            } else {
                if (!$is_nested) {
                    $this->_generateNestedHeaderGroup($sheet, $excelColumnMappingConfigs, $column_index, $header_index, $excelColumnMappingConfig, $arr_header_group);
                    $is_nested = true;
                }
            }

            if ($data_type != 'list') {
                continue;
            }

            $unit_column_index = PHPExcel_Cell::stringFromColumnIndex($unit_index);

            $total = $this->_generateUnitData($objPHPExcel, $excelColumnMappingConfig, $unit_column_index, $column_name, $width, $code, $title);

            $this->_generateDataValidation($objPHPExcel, $header_index + 1, $total, $column_index, $unit_column_index, $max_row);
            $unit_index++;
        }

        $freeze_column_index = PHPExcel_Cell::columnIndexFromString($key_index) + 1;
        $freeze_column_string = PHPExcel_Cell::stringFromColumnIndex($freeze_column_index);
        $sheet->freezePane($freeze_column_string . ($header_index + 1));

        $this->afterAddDataValidation($objPHPExcel);
    }


    // Hàm sinh động cell theo danh mục: chi phí trong chuyến
    private function _generateNestedHeaderGroup($sheet, $excelColumnMappingConfigs, $column_index, $header_index, $excelColumnMappingConfig)
    {
        $nested_field = $excelColumnMappingConfig->nested_field;
        $nested_field_group = $excelColumnMappingConfigs->filter(function ($value) use ($nested_field) {
            return $value->nested_field == $nested_field;
        });
        $count = count($nested_field_group);
        $data = isset($this->{$excelColumnMappingConfig->nested_field . 'Data'}) ? $this->{$excelColumnMappingConfig->nested_field . 'Data'} : [];

        $temp_index = PHPExcel_Cell::columnIndexFromString($column_index);

        $j = 0;
        foreach ($data as $item) {
            $t_index = PHPExcel_Cell::stringFromColumnIndex($temp_index - 1);

            $sheet->setCellValue(($t_index) . ($header_index - 1), $item->{$excelColumnMappingConfig->nested_name});

            $end_index = $temp_index + $count - 2;
            $end_index_string = PHPExcel_Cell::stringFromColumnIndex($end_index);


            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE597')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $sheet->getStyle(($t_index) . ($header_index - 1) . ':' . $end_index_string . ($header_index - 1))->applyFromArray($style);
            $sheet->getStyle($t_index . ($header_index - 1))->getFont()->setBold(true);

            $sheet->mergeCells(($t_index) . ($header_index - 1) . ':' . $end_index_string . ($header_index - 1));

            $i = 0;
            foreach ($nested_field_group as $nested_field_group_item) {
                $col_index = PHPExcel_Cell::stringFromColumnIndex($temp_index - 1);

                $sheet->setCellValue($col_index . ($header_index), $nested_field_group_item->column_name);

                if ($i < $count - 1) {
                    $sheet->getColumnDimension($col_index)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);
                }
                $sheet->getColumnDimension($col_index)->setWidth(empty($nested_field_group_item->width) ? 20 : $nested_field_group_item->width);

                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => empty($nested_field_group_item->background_color) ? 'B8CCE4' : $nested_field_group_item->background_color)
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle($col_index . ($header_index))->getFont()->setBold(true);
                $sheet->getStyle($col_index . ($header_index))->applyFromArray($style);
                $temp_index = $temp_index + 1;
                $i++;
            }
            $j++;
        }
    }

    // Hàm merge cell
    private function _generateHeaderGroup($sheet, $excelColumnMappingConfigs, $column_index, $header_index, $header_group, &$arr_header_group)
    {
        if (!empty($header_group)) {
            if (!array_key_exists($header_group, $arr_header_group)) {
                $count = $excelColumnMappingConfigs->filter(function ($value) use ($header_group) {
                    return $value->header_group == $header_group;
                })->count();
                $arr_header_group[$header_group]["count"] = $count;
                $arr_header_group[$header_group]["is_set"] = true;
            }
            if ($arr_header_group[$header_group]["is_set"] == true) {
                $sheet->setCellValue($column_index . ($header_index - 1), $header_group);
                $temp_column_index = PHPExcel_Cell::columnIndexFromString($column_index) + $arr_header_group[$header_group]["count"] - 2;
                $temp_column_string = PHPExcel_Cell::stringFromColumnIndex($temp_column_index);
                $sheet->mergeCells($column_index . ($header_index - 1) . ':' . $temp_column_string . ($header_index - 1));
                $arr_header_group[$header_group]["is_set"] = false;
            }
        }
        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFE597')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getStyle($column_index . ($header_index - 1))->getFont()->setBold(true)->setName('Times New Roman');
        $sheet->getStyle($column_index . ($header_index - 1))->applyFromArray($style);
    }

    // Tạo data validation đối với dữ liệu dạng list
    private function _generateHeader($sheet, $cell_coordinate, $column_name, $background_color = null, $comment = null)
    {
        if (!empty($comment)) {
            $dataValidation = $sheet->getCell($cell_coordinate)->getDataValidation();
            $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_NONE);
            $dataValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $dataValidation->setAllowBlank(true);
            $dataValidation->setShowInputMessage(true);
            $dataValidation->setShowErrorMessage(true);
            $dataValidation->setPromptTitle($column_name);
            $dataValidation->setPrompt($comment);

            $sheet->setDataValidation($cell_coordinate, $dataValidation);
        }
        $sheet->setCellValue($cell_coordinate, $column_name);
        $style = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => empty($background_color) ? 'B8CCE4' : $background_color)
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $sheet->getStyle($cell_coordinate)->applyFromArray($style);
        $sheet->getStyle($cell_coordinate)->getFont()->setBold(true)->setName('Times New Roman');
    }

    // Tạo data dữ liệu danh mục
    private function _generateUnitData($objPHPExcel, $excelColumnMappingConfig, $unit_column_index, $column_name, $width, $code, $title)
    {
        $objPHPExcel->setActiveSheetIndex($this->getUnitSheet());
        $sheet = $objPHPExcel->getActiveSheet();

        $rowIndex = 1;
        $total = 0;

        $cell_coordinate = $unit_column_index . $rowIndex;
        $this->_generateHeader($sheet, $cell_coordinate, $column_name);
        $sheet->getColumnDimension($unit_column_index)->setWidth(empty($width) ? 20 : $width);

        $rowIndex++;

        if (property_exists($this, $excelColumnMappingConfig->entity . 'Data')) {
            $data = $this->{$excelColumnMappingConfig->entity . 'Data'};
            $temp_count = 0;
            foreach ($data as $item) {
                if (isset($item->is_permission)) {
                    if ($item->is_permission == 1) {
                        $sheet->setCellValue($unit_column_index . $rowIndex, $item->{$code} . (empty($title) ? '' : '|' . $item->{$title}));
                        $rowIndex++;
                        $temp_count++;
                    }
                } else {
                    $sheet->setCellValue($unit_column_index . $rowIndex, $item->{$code} . (empty($title) ? '' : '|' . $item->{$title}));
                    $rowIndex++;
                    $temp_count++;
                }
            }
            $total = $temp_count;
        } else {
            $list = config('system.' . $excelColumnMappingConfig->data);
            if ($list) {
                $total = count($list);
                foreach ($list as $item) {
                    $sheet->setCellValue($unit_column_index . $rowIndex, $item);
                    $rowIndex++;
                }
            }
        }
        return $total;
    }

    // Tạo data validation đối với dữ liệu dạng list
    private function _generateDataValidation($objPHPExcel, $start_index, $total_row, $column_index, $unit_column_index, $max_row)
    {
        if ($total_row == 0) {
            return;
        }

        $temp = $objPHPExcel->setActiveSheetIndex($this->getUnitSheet());
        $name = $temp->getTitle();

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $dataValidation = $sheet->getCell($column_index . $start_index)->getDataValidation();
        $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $dataValidation->setShowDropDown(true);
        $unit_start_index = 2;
        $dataValidation->setFormula1('\'' . $name . '\'' . '!$' . $unit_column_index . '$' . $unit_start_index . ':$' . $unit_column_index . '$' . ($total_row + $unit_start_index - 1));
        $sheet->setDataValidation($column_index . $start_index . ':' . $column_index . ($start_index + $max_row - 1), $dataValidation);
    }

    // Thêm data vào trong file Excel
    private function addDataToExcelFile($objPHPExcel, $data)
    {
        $objPHPExcel->setActiveSheetIndex(0);

        $row = $this->excelColumnConfig->header_index + 1;

        $cellLocation = [];
        $excelColumnMappingConfigs = $this->excelColumnConfig->excelColumnMappingConfigs;

        $sheet = $objPHPExcel->getActiveSheet();


        $is_group = $excelColumnMappingConfigs->first(function ($value) {
            return $value->is_group == 1;
        });
        if (!empty($is_group)) {
            $ids = $data->pluck('id')->toArray();
            $extend_list = $this->getRepository()->getExtendProperty($ids);
        }
        if (!empty($this->extend_template)) {
            $template_mappings = $this->getTemplateMappingsByID($this->extend_template->id);
        }
        foreach ($data as &$item) {
            $arr = [];
            $array_nested_field = [];
            foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig) {

                $field = $excelColumnMappingConfig->field;
                $data_type = $excelColumnMappingConfig->data_type;
                $dataList = $excelColumnMappingConfig->data;
                $columnIndex = $excelColumnMappingConfig->column_index;
                $mapping_data = $excelColumnMappingConfig->mapping_data;
                $mapping_field = $excelColumnMappingConfig->mapping_field;

                if ($excelColumnMappingConfig->is_multiple == 1) {
                    $arr[] = count($item->{$mapping_data});

                    $temp_row = 0;
                    foreach ($item->{$mapping_data} as $temp) {
                        $pivot = $temp->pivot;
                        $val = null;
                        switch ($data_type) {
                            case 'string':
                                $val = $pivot->{$mapping_field};
                                break;
                            case 'number':
                                $val = $pivot->{$mapping_field};
                                break;
                            case 'date':
                                $val = format($pivot->{$mapping_field}, 'd-m-Y', 'Y-m-d');
                                break;
                            case 'time':
                                $val = format($pivot->{$mapping_field}, 'H:i', 'H:i');
                                break;
                            case 'list':
                                $globalData = $this->{$excelColumnMappingConfig->entity . 'Data'};
                                $itemData = $globalData->first(function ($value, $key) use ($pivot, $mapping_field) {
                                    return $value->id === ($pivot->{$mapping_field});
                                });
                                if (!empty($itemData)) {
                                    $val = $itemData->{$excelColumnMappingConfig->code} . (empty($itemData->{$excelColumnMappingConfig->title}) ? '' : '|' . $itemData->{$excelColumnMappingConfig->title});
                                }
                                break;
                            default:
                                $val = $pivot->{$mapping_field};
                                break;
                        }
                        $temp_loop_row = ($row + $temp_row);
                        $sheet->setCellValue($columnIndex . ($temp_loop_row), $val);
                        if ($data_type == 'number') {
                            setNumberValueExcel($sheet, $cellLocation, $columnIndex, $temp_loop_row, $val ? $val : 0, null);
                        }
                        $temp_row++;
                    }
                } else {
                    if ($excelColumnMappingConfig->is_group == 1) {
                        $info = $extend_list->filter(function ($extend) use ($item) {
                            return $extend->id == $item->id;
                        })->first();

                        if ($info) {
                            $item->{$field} = $info->{$field};
                            $sheet->setCellValue($columnIndex . $row, $item->{$field});
                            continue;
                        }
                    }
                    switch ($data_type) {
                        case 'string':
                            $sheet->setCellValue($columnIndex . $row, $item->{$field});
                            break;
                        case 'number':
                            $sheet->setCellValue($columnIndex . $row, $item->{$field});
                            setNumberValueExcel($sheet, $cellLocation, $columnIndex, $row, $item->{$field} ? $item->{$field} : 0, null);
                            break;
                        case 'date':
                            $sheet->setCellValue($columnIndex . $row, format($item->{$field}, 'd-m-Y', 'Y-m-d'));
                            break;
                        case 'time':
                            $sheet->setCellValue($columnIndex . $row, format($item->{$field}, 'H:i', 'H:i'));
                            break;
                        case 'list':
                            if (empty($dataList)) {
                                $name_of_field = 'name_of_' . $field;
                                $name_field = isset($item->{$name_of_field}) ? ('|' . $item->{$name_of_field}) : '';
                                $value = $item->{'code_of_' . $field} . $name_field;
                                $sheet->setCellValue($columnIndex . $row, $value);
                            } else {
                                $list = config('system.' . $dataList);
                                if ($list != null) {
                                    $val = array_key_exists($item->{$field}, $list) ? $list[$item->{$field}] : '';
                                    $sheet->setCellValue($columnIndex . $row, $val);
                                }
                            }
                            break;
                        case 'nested':
                            $nested_field = $excelColumnMappingConfig->nested_field;
                            if (array_key_exists('count', $array_nested_field)) {
                                continue;
                            }

                            $nested_field_group = $excelColumnMappingConfigs->filter(function ($value) use ($nested_field) {
                                return $value->nested_field == $nested_field;
                            });

                            $count = $nested_field_group->count();
                            $array_nested_field['count'] = $count;

                            $temp_index = PHPExcel_Cell::columnIndexFromString($columnIndex) - 1;
                            $temp_items = $item->{$excelColumnMappingConfig->nested_field};

                            $nested_items = $this->{$excelColumnMappingConfig->nested_field . 'Data'};
                            foreach ($nested_items as $nested_item) {
                                foreach ($nested_field_group as $temp_nested_field) {
                                    $temp_item = collect($temp_items)->filter(function ($value) use ($temp_nested_field, $nested_item) {
                                        return $value->{$temp_nested_field->nested_match} == $nested_item->id;
                                    })->first();
                                    $col_index = PHPExcel_Cell::stringFromColumnIndex($temp_index);

                                    $field = $temp_nested_field->field;


                                    if (!empty($this->extend_template)) {
                                        $startIndex = $this->extend_template->header_row_index;
                                        $endIndex = $this->extend_template->header_row_index + count($data);
                                        $matching_column_index = $this->extend_template->matching_column_index;
                                        $matching_col_index = PHPExcel_Cell::columnIndexFromString($matching_column_index);
                                        $template_mapping = collect($template_mappings)->filter(function ($value) use ($temp_nested_field, $nested_item) {
                                            return $value->{$temp_nested_field->nested_match} == $nested_item->id;
                                        })->first();
                                        if (!empty($template_mapping)) {
                                            $column = PHPExcel_Cell::columnIndexFromString($template_mapping->column_index);

                                            $sheet->setCellValue(
                                                $col_index . $row,
                                                '=IFERROR(VLOOKUP(A' . $row . ', BieuMau_BangTinh!' . $matching_column_index . $startIndex . ':ZZ' . $endIndex . ', ' . ($column - $matching_col_index + 1) . ', FALSE), 0)'
                                            );
                                        }
                                        if (!empty($temp_item) && $temp_item->{$field} != 0) {
                                            $style = array(
                                                'fill' => array(
                                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                    'color' => array('rgb' => 'FFE597')
                                                )
                                            );
                                            $sheet->getStyle($col_index . $row)->applyFromArray($style);

                                            $dataValidation = $sheet->getCell($col_index . $row)->getDataValidation();
                                            $dataValidation->setType(PHPExcel_Cell_DataValidation::TYPE_NONE);
                                            $dataValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                                            $dataValidation->setAllowBlank(true);
                                            $dataValidation->setShowInputMessage(true);
                                            $dataValidation->setShowErrorMessage(true);
                                            $dataValidation->setPromptTitle('');
                                            $dataValidation->setPrompt($temp_item->{$field});

                                            $sheet->setDataValidation($col_index . $row, $dataValidation);
                                        }
                                    } else {
                                        switch ($excelColumnMappingConfig->nested_data_type) {
                                            case 'string':
                                                $sheet->setCellValue($col_index . $row, empty($temp_item) ? '' : $temp_item->{$field});
                                                break;
                                            case 'number':
                                                $sheet->setCellValue($col_index . $row, empty($temp_item) ? 0 : ($temp_item->{$field}));
                                                setNumberValueExcel($sheet, $cellLocation, $col_index, $row, empty($temp_item) ? 0 : ($temp_item->{$field} ? $temp_item->{$field} : 0), null);
                                            default:
                                                $sheet->setCellValue($col_index . $row, empty($temp_item) ? '' : $temp_item->{$field});
                                                break;
                                        }
                                    }
                                    $temp_index++;
                                }
                            }

                            break;
                        default:
                            $sheet->setCellValue($columnIndex . $row, $item->{$field});
                            break;
                    }
                }
            }

            $max = !empty($arr) ? max($arr) : 0;

            if ($max > 1) {
                $key_property = $excelColumnMappingConfigs->first(function ($value) {
                    return $value->is_key == 1;
                });
                for ($i = 1; $i <= $max - 1; $i++) {
                    $sheet->setCellValue($key_property->column_index . ($row + $i), $item->{$key_property->field});
                }
            }
            $row = $row + ($max != 0 ? $max : 1);
        }

        $this->afterAddData($objPHPExcel, $data);

        return $this;
    }

    // Hàm thêm data
    protected function afterAddData($objPHPExcel, $data)
    {
    }

    // Thay thế biểu mẫu trường trộn
    // CreatedBy nlhoang 17/07/2020
    protected function replaceExtendTemplateSheet($extendTemplate, $templateLayouts, $objPHPExcel, $datas)
    {
        $objPHPExcel->setActiveSheetIndex($this->getExtendSheet());
        $sheet = $objPHPExcel->getActiveSheet();
        $count = count($datas);
        $ignoreFieldType = ['ARRAY.INDEX'];
        $isInsertedRows = [];
        $isDynamicHeader = false;

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

                            foreach ($isInsertedRows as $key) {
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
                foreach ($datas as $data) {
                    $singleCells = $this->findCells($sheet, '${' . $displayName . '#' . ($index + 1) . '}');
                    foreach ($singleCells as $cell) {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        switch ($fieldType) {
                            case 'ARRAY':
                            case 'ARRAY.INDEX':
                                $sheet->setCellValue($column . $row, null);
                                break;
                            case 'NUMBER':
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                break;
                            case 'CURRENCY':
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                setNumberValueExcelNR($sheet, $column, $row, $this->formatValue($value, $dataType), null);
                                break;
                            default:
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                $sheet->setCellValue($column . $row, $this->formatValue($value, $dataType));
                                $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
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

                                $array = isset($data->{$nestProperty}) ? collect($data->{$nestProperty}) : [];

                                // Xử lý những dữ liệu dạng mảng thì cộng gộp thành chuỗi string ngăn cách bằng dầu Xuống dòng
                                if ($templateLayout->allow_group == 1 && count($array) > 0) {
                                    $value = $array->map(function ($x) use ($prop, $dataType) {
                                        $result = isset($x->{$prop}) ? $x->{$prop} : null;
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
                                                $value = isset($array[$i]->{$prop}) ? $array[$i]->{$prop} : null;
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
                                            $value = isset($array[$countDynamic - $i]->{$prop}) ? $array[$countDynamic - $i]->{$prop} : null;
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
                            case 'NUMBER':
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                setNumberValueExcelNR($sheet, $column, $nextRow, $this->formatValue($value, $dataType), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                break;
                            case 'CURRENCY':
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                setNumberValueExcelNR($sheet, $column, $nextRow, $this->formatValue($value, $dataType), null);
                                break;
                            default:
                                $value = isset($data->{$property}) ? $data->{$property} : null;
                                $sheet->setCellValue($coordinate, $this->formatValue($value, $dataType));
                                $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                                break;
                        }
                    }
                    $index++;
                }
            } else {
                foreach ($cells as $cell) {
                    $sheet->setCellValue($cell, null);
                    $cell->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                }
            }
        }


        // Cập nhật các trường công thức trong file Excel
        $referenceHelper = PHPExcel_ReferenceHelper::getInstance();

        $highestCol = $sheet->getHighestDataColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestCol);
        $formulaColumns = [];
        $headerIndex = $extendTemplate->header_row_index;
        for ($column = 0; $column < $highestColumnIndex; $column++) {
            $cell = $sheet->getCellByColumnAndRow($column, $headerIndex);
            if ($cell->isFormula()) {
                $formulaColumns[] = PHPExcel_Cell::stringFromColumnIndex($column);
            }
        }
        for ($row = $headerIndex + 1; $row < $headerIndex + count($datas); $row++) {
            foreach ($formulaColumns as $formulaColumn) {
                $formula = $sheet->getCell($formulaColumn . $headerIndex)->getValue();
                $adjustFormula = $referenceHelper->updateFormulaReferences($formula, 'A1', 0, $row - $headerIndex);
                $sheet->setCellValue($formulaColumn . $row, $adjustFormula);
            }
        }
        return $objPHPExcel;
    }

    // Tìm trường trộn trong biểu mẫu
    // CreatedBy nlhoang 17/07/2020
    protected function findCells($currentWorksheet, $searchTerm)
    {
        $highestRow = 150;
        $highestCol = $currentWorksheet->getHighestDataColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestCol);

        $foundInCells = array();

        for ($col = 0; $col < $highestColumnIndex; $col++) {
            for ($row = 1; $row <= $highestRow; $row++) {
                $cell = $currentWorksheet->getCellByColumnAndRow($col, $row);
                if ($cell->getValue() === $searchTerm) {

                    $foundInCells[] = $cell->getCoordinate();
                }
            }
        }
        return ($foundInCells);
    }

    //Hàm format dữ liệu hiển thị đầu ra
    //CreatedBy nlhoang 17/07/2020
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
}
