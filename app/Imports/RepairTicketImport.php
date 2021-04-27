<?php

namespace App\Imports;

class RepairTicketImport extends BaseImport
{
    protected $_vehicleDriverList;

    protected $_data = [];

    public function __construct($vehicleDriverList)
    {
        if (!empty($vehicleDriverList)) {
            $this->_vehicleDriverList = $vehicleDriverList->pluck('driver_name', 'reg_no');
        }
    }

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $result = parent::map($row, $excelColumnConfig, $dataEx);

        //Lấy tài xế mặc định của xe nếu ko nhập
        $vehicle = isset($result['vehicle']) ? $result['vehicle'] : '';
        $primaryDriver = isset($result['driver']) ? $result['driver'] : '';
        if (!empty($vehicle) && empty(trim($primaryDriver))) {
            $result['driver'] = isset($this->_vehicleDriverList[$vehicle]) ? $this->getCode($this->_vehicleDriverList[$vehicle]) : null;
        }
        return $result;
    }

    public function processItemImport($data)
    {
        $data_items = array();
        foreach ($data as $key => &$row) {
            if (isset($row['code'])) {
                if (isset($data_items[$row['code']])) {
                    $data_items[$row['code']]['items'][] = [
                        'accessory_code' => $row['accessory_code'],
                        'quantity' => $row['quantity'],
                        'price' => $row['price'],
                        'amount' => $row['amount'],
                        'next_repair_date' => $row['next_repair_date'],
                        'next_repair_distance' => $row['next_repair_distance']
                    ];
                } else {
                    $row['items'][] = [
                        'accessory_code' => $row['accessory_code'],
                        'quantity' => $row['quantity'],
                        'price' => $row['price'],
                        'amount' => $row['amount'],
                        'next_repair_date' => $row['next_repair_date'],
                        'next_repair_distance' => $row['next_repair_distance']
                    ];
                    $data_items[$row['code']] = $row;
                }
            }
        }
        return array_values($data_items);
    }
}
