<?php

namespace App\Imports;

use Illuminate\Support\Str;

class RouteImport extends BaseImport
{
    protected $_data = [];
    public $_headerCost = [];

    public function __construct()
    {
    }

    // public function map($row): array
    // {
    //     $result = [];
    //     $row = array_values($row);
    //     $columnCost = 13;
    //     if ($this->indexRow == 2) {
    //         for ($i = $columnCost; $i < 1000; $i += 3) {
    //             if (isset($row[$i]))
    //                 ($this->_headerCost)[] = $row[$i];
    //         }
    //     } else {
    //         $costValues = [];
    //         foreach ($this->_headerCost as $name) {
    //             $costDriver = 0;
    //             $costFinal = 0;
    //             if (isset($row[$columnCost + 1])) {
    //                 $costDriver = $this->importNumber($row[$columnCost + 1]);
    //             }
    //             if (isset($row[$columnCost + 2])) {
    //                 $costFinal = $this->importNumber($row[$columnCost + 2]);
    //             }
    //             $costValues[$name] = $costDriver . '|' . $costFinal;
    //             $columnCost += 3;
    //         }
    //         $result = [
    //             'row' => $this->headingRow() + $this->indexRow,
    //             'route_code' => isset($row[0]) ? $row[0] : '',
    //             'name' => isset($row[1]) ? $row[1] : '',
    //             'is_approved' => $this->convertIsApproval($row[11]),
    //             'is_approved_text' => $row[11],
    //             'approved_note' => isset($row[12]) ? $row[12] : '',
    //             'costs' => $costValues
    //         ];
    //     }

    //     $this->indexRow++;

    //     return $result;
    // }

    // public function headingRow(): int
    // {
    //     return 10;
    // }

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

    public function convertIsApproval($isApproval)
    {
        if (empty($isApproval)) {
            return config('constant.CHUA_PHE_DUYET');
        }
        $text = mb_strtoupper(Str::slug($isApproval));
        if ($text == 'DA-PHE-DUYET') {
            return config('constant.DA_PHE_DUYET');
        }
        return config('constant.CHUA_PHE_DUYET');
    }
}
