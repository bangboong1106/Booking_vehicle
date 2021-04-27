<?php

namespace App\Imports;

use App\Model\Entities\Vehicle;

class PartnerVehicleImport extends BaseImport
{
    protected $indexRow = 1;
    protected $_data = [];

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        if (empty($row[0])) {
            return [];
        }

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'reg_no' => isset($row[0]) ? $row[0] : '',
            'status' => isset($row[1]) ? $this->convertStatus($row[1]) : '1',
            'status_text' => isset($row[1]) ? $row[1] : '',
            'type' => isset($row[2]) ? $this->convertType($row[2]) : '1',
            'type_text' => isset($row[2]) ? $row[2] : '',
            'active' => isset($row[3]) ? $this->convertActive($row[3]) : '1',
            'active_text' => isset($row[3]) ? $row[3] : '',
            'group_code' => isset($row[4]) ? $this->getCode($row[4]) : null,
            'group_text' => isset($row[4]) ? $row[4] : null,
            'driver_codes' => isset($row[5]) ? $this->getCodeList($row[5]) : null,
            'driver_codes_text' => isset($row[5]) ? $row[5] : null,
            'volume_text' => isset($row[6]) ? $row[6] : null,
            'volume' => isset($row[6]) ? $this->importNumber($row[6]) : null,
            'weight_text' => isset($row[7]) ? $row[7] : null,
            'weight' => isset($row[7]) ? $this->importNumber($row[7]) : null,

            'length_text' => isset($row[8]) ? $row[8] : null,
            'length' => isset($row[8]) ? $this->importNumber($row[8]) : null,

            'width' => isset($row[9]) ? $row[9] : null,
            'width_text' => isset($row[9]) ? $this->importNumber($row[9]) : null,

            'height_text' => isset($row[10]) ? $row[10] : null,
            'height' => isset($row[10]) ? $this->importNumber($row[10]) : null,
            
            'vehicleGeneralInfo' => [
                'category_of_barrel' => isset($row[11]) ? $row[11] : null,
                'weight_lifting_system' => isset($row[12]) ? $row[12] : null,
                'max_fuel_text' => isset($row[13]) ? $row[13] : null,
                'max_fuel' => isset($row[13]) ? $this->importNumber($row[13]) : null,
                'max_fuel_with_goods_text' => isset($row[14]) ? $row[14] : null,
                'max_fuel_with_goods' => isset($row[14]) ? $this->importNumber($row[14]) : null,
                'register_year' => isset($row[15]) ? $row[15] : null,
                'brand' => isset($row[16]) ? $row[16] : null
            ],
            'gps_company_id' => isset($row[17]) ? $this->getCode($row[17]) : null,
            'gps_company_text' => isset($row[17]) ? $row[17] : '',
            'repair_distance' => isset($row[18]) ? $row[18] : '',
            'repair_date' => isset($row[19]) ? $row[19] : '',

        ];
        $this->indexRow++;

        return $result;
    }

    public function model(array $row)
    {
        if (empty($row)) {
            return null;
        }
        return new Vehicle($row);
    }

    public function sheets(): array
    {
        return [
            0 => new PartnerVehicleImport(),
        ];
    }

    public function convertStatus($status)
    {
        $rs = '1';
        if (empty($status)) {
            return $rs;
        }

        $text = mb_strtoupper(str_slug($status, '-'));
        switch ($text) {
            case mb_strtoupper(str_slug(config('system.vehicle_status.1'), '-')):
                $rs = '1';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_status.2'), '-')):
                $rs = '2';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_status.3'), '-')):
                $rs = '3';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_status.4'), '-')):
                $rs = '4';
                break;
        }
        return $rs;
    }

    public function convertType($type)
    {
        $rs = '1';
        if (empty($type)) {
            return $rs;
        }

        $text = mb_strtoupper(str_slug($type, '-'));
        switch ($text) {
            case mb_strtoupper(str_slug(config('system.vehicle_type.1'), '-')):
                $rs = '1';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_type.2'), '-')):
                $rs = '2';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_type.3'), '-')):
                $rs = '3';
                break;
        }
        return $rs;
    }

    public function convertActive($active)
    {
        $rs = '1';
        if (empty($active)) {
            return $rs;
        }

        $text = mb_strtoupper(str_slug($active, '-'));
        switch ($text) {
            case mb_strtoupper(str_slug(config('system.vehicle_active.1'), '-')):
                $rs = '1';
                break;
            case mb_strtoupper(str_slug(config('system.vehicle_active.0'), '-')):
                $rs = '0';
                break;
        }
        return $rs;
    }

    public function headingRow(): int
    {
        return 9;
    }
}
