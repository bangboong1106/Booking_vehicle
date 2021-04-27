<?php

namespace App\Imports;

use Illuminate\Support\Arr;

class QuotaImport extends BaseImport
{
    protected $indexRow = 1;
    protected $_data = [];
    protected $_listCode = [];
    public $_header = [];
    public $_updateRoute;

    public function __construct($listCost, $updateRoute)
    {
        $this->_setListCost($listCost);
        $this->_updateRoute = $updateRoute;
    }

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'quota_code' => isset($row[0]) ? $row[0] : '',
            'name' => isset($row[1]) ? $row[1] : '',
            'vehicle_group_id' => isset($row[2]) ? $this->getCode($row[2]) : '',
            'vehicle_group_text' => isset($row[2]) ? $row[2] : '',
            'location_destination_id' => isset($row[3]) ? $this->getCode($row[3]) : '',
            'location_destination_text' => isset($row[3]) ? $row[3] : '',
            'location_destination_group_id' => isset($row[4]) ? $this->getCode($row[4]) : '',
            'location_destination_group_text' => isset($row[4]) ? $row[4] : '',
            'location_arrival_id' => isset($row[5]) ? $this->getCode($row[5]) : '',
            'location_arrival_text' => isset($row[5]) ? $row[5] : '',
            'location_arrival_group_id' => isset($row[6]) ? $this->getCode($row[6]) : '',
            'location_arrival_group_text' => isset($row[6]) ? $row[6] : '',
            'distance' => isset($row[7]) ? $this->importNumber($row[7]) : 0,
        ];

        $result['listCost'] = key_exists($result['quota_code'], $this->_listCode) ? $this->_listCode[$result['quota_code']] :
            $this->_header;

        $result['updateRoute'] = !empty($this->_updateRoute) ? $this->_updateRoute : false;

        $this->indexRow++;

        return $result;
    }

    protected function _setListCost($listCost)
    {
        $header = Arr::pull($listCost, 0);
        Arr::pull($header, 0);

        $result = [];
        foreach ($listCost as $cost) {
            $quotaCode = Arr::pull($cost, 0);
            unset($cost[0]);

            if (count($cost) < count($header)) {
                $cost = array_pad($cost, count($header), null);
            }

            foreach ($cost as &$item) {
                $item = $this->importNumber($item);
            }

            $result[$quotaCode] = array_combine($header, $cost);
        }

        $this->_listCode = $result;
        $this->_header = array_combine($header, array_pad([], count($header), null));
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
}
