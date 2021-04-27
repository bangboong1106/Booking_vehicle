<?php

namespace App\Imports;

use DateTime;
use Illuminate\Support\Str;
use PHPExcel_Cell;

abstract class BaseImport
{
    protected $indexRow = 1;

    public function importDate($date, $format = 'Y-m-d')
    {
        if (empty($date)) {
            return '';
        }
        $date = str_replace(['/'], '-', $date);

        //xử lý ngày tháng nhap 1 chư sô
        $patterns = explode('-', $date);
        if (count($patterns) == 3) {
            $date = sprintf("%02d", $patterns[0]) . sprintf("%02d", $patterns[1]) . $patterns[2];
        }

        //xử lý giờ tháng nhap 1 chư sô
        $patterns = explode(':', $date);
        if (count($patterns) == 2) {
            $date = sprintf("%02d", $patterns[0]) . sprintf("%02d", $patterns[1]);
        }

        if ($this->validateDate($date, 'dmY')) {
            $d = DateTime::createFromFormat('dmY', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'dmy')) {
            $d = DateTime::createFromFormat('dmy', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'Ymd')) {
            $d = DateTime::createFromFormat('Ymd', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'ymd')) {
            $d = DateTime::createFromFormat('ymd', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'dmY H:i:s')) {
            $d = DateTime::createFromFormat('dmY H:i:s', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'Ymd H:i:s')) {
            $d = DateTime::createFromFormat('Ymd H:i:s', $date);
            $result = $d->format($format);
        } else if ($this->validateDate($date, 'Hi')) {
            $result = date('H:i', strtotime($date));
        } else
            return $date;

        return $result;
    }

    public function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getCode($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return '';
        }
        $values = explode("|", $value);
        return $values[0];
    }

    public function getCodeList($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return '';
        }

        $values = explode(",", $value);
        $result = [];
        foreach ($values as $v) {
            $result[] = $this->getCode($v);
        }
        return $result;
    }

    public function importNumber($number)
    {
        $tmp = trim(str_replace(',', '', $number));
        return doubleval($tmp);
    }

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        if (empty($row[0])) {
            $this->indexRow++;
            return [];
        }

        $result = [
            'row' => $excelColumnConfig->header_index + $this->indexRow,
        ];
        $array_nested_field = [];
        $excelColumnMappingConfigs = $excelColumnConfig->excelColumnMappingConfigs;
        foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig) {

            if ($excelColumnMappingConfig->is_import == 0) continue;
            $field = $excelColumnMappingConfig->field;
            $data_type = $excelColumnMappingConfig->data_type;
            $function = $excelColumnMappingConfig->function;
            $default_value = $excelColumnMappingConfig->default_value;

            $columnIndex = PHPExcel_Cell::columnIndexFromString($excelColumnMappingConfig->column_index) - 1;

            switch ($data_type) {
                case 'string':
                    $result[$field] = isset($row[$columnIndex]) ? trim($row[$columnIndex]) : '';
                    break;
                case 'number':
                    $result[$field] = isset($row[$columnIndex]) ? $this->importNumber($row[$columnIndex]) : '';
                    break;
                case 'date':
                    $result[$field] = isset($row[$columnIndex]) ? $this->importDate($row[$columnIndex], 'd-m-Y') : '';
                    break;
                case 'time':
                    $result[$field] = isset($row[$columnIndex]) ? $this->importDate($row[$columnIndex], 'H:i') : '';
                    break;
                case 'list':
                    $result[$field] = isset($row[$columnIndex]) ? (empty($function) ? trim($row[$columnIndex]) : $this->{$function}($row[$columnIndex])) : (empty($default_value) ? '' : config('constant.' . $default_value));
                    $result['name_of_' . $field] = isset($row[$columnIndex]) ? trim($row[$columnIndex]) : '';
                    break;
                case 'nested':
                    $nested_field = $excelColumnMappingConfig->nested_field;
                    if (array_key_exists('count', $array_nested_field)) continue;

                    $nested_field_group = $excelColumnMappingConfigs->filter(function ($value) use ($nested_field) {
                        return $value->nested_field == $nested_field;
                    });

                    $count = $nested_field_group->count();
                    $array_nested_field['count'] = $count;

                    $result[$nested_field] = [];

                    for ($i = $columnIndex; $i < 500; $i += $count) {
                        $item = [];
                        if (empty($dataEx[0][$i])) continue;
                        $item[$excelColumnMappingConfig->nested_name] = $dataEx[0][$i];
                        $temp = $i;
                        foreach ($nested_field_group as $temp_nested_field) {
                            switch ($excelColumnMappingConfig->nested_data_type) {
                                case 'string':
                                    $item[$temp_nested_field->field] = isset($row[$temp]) ? trim($row[$temp]) : '';
                                    break;
                                case 'number':
                                    $item[$temp_nested_field->field] = isset($row[$temp]) ? $this->importNumber($row[$temp]) : '';
                                    break;
                            }
                            $temp++;
                        }
                        $result[$nested_field][] = $item;
                    }
                    break;
                default:
                    $result[$field] = isset($row[$columnIndex]) ? trim($row[$columnIndex]) : '';
                    break;
            }
        }
        $this->indexRow++;
        return $result;
    }

    public function convertYesNo($value)
    {
        if (empty($value)) {
            return config('constant.no');
        }
        $text = mb_strtoupper(Str::slug($value));
        if ($text == 'CO') {
            return config('constant.yes');
        }
        return config('constant.no');
    }
}
