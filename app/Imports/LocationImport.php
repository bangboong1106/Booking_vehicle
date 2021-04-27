<?php

namespace App\Imports;

use Illuminate\Support\Arr;

class LocationImport extends BaseImport
{
    protected $indexRow = 1;
    protected $_data = [];
    public $_header = [];

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'code' => isset($row[0]) ? $row[0] : '',
            'title' => isset($row[1]) ? $row[1] : '',
            'province_id' => isset($row[2]) ? $this->getCode($row[2]) : '',
            'province_text' => isset($row[2]) ? $this->_getTitle($row[2]) : '',
            'district_id' => isset($row[3]) ? $this->getCode($row[3]) : '',
            'district_text' => isset($row[3]) ? $this->_getTitle($row[3]) : '',
            'ward_id' => isset($row[4]) ? $this->getCode($row[4]) : '',
            'ward_text' => isset($row[4]) ? $this->_getTitle($row[4]) : '',
            'address' => isset($row[5]) ? $row['5'] : '',
            'customer_code' => isset($row[6]) ? $this->getCode($row[6]) : '',
            'customer_text' => isset($row[6]) ? $this->_getTitle($row[6]) : '',
        ];
        $this->indexRow++;

        return $result;
    }

    public function headingRow(): int
    {
        return 9;
    }

    /**
     * @inheritDoc
     */
    public function model(array $row)
    {
        // TODO: Implement model() method.
    }

    /**
     * @inheritDoc
     */
    public function sheets(): array
    {
        // TODO: Implement sheets() method.
    }

    protected function _getTitle($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return '';
        }
        $values = explode("|", $value);
        return count($values) == 2 ? $values[1] : $value;
    }
}
