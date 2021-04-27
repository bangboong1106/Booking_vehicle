<?php

namespace App\Imports;

use App\Model\Entities\Order;
use Illuminate\Support\Str;

class DocumentImport extends BaseImport
{

    protected $indexRow = 1;

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        // Check cả trường mã đơn hàng và số đơn hàng
        if (empty($row[0]) && empty($row[1])) {
            $this->indexRow++;
            return [];
        }
        $isCollected = isset($row[3]) ? $this->convertStatusDocuments(trim($row[3])) : 0;

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'order_code' => isset($row[0]) ? trim($row[0]) : '', // A
            'order_no' => isset($row[1]) ? trim($row[1]) : '', // B
            'is_collected_documents_text' => isset($row[2]) ? trim($row[2]) : "",
            'is_collected_documents' => isset($row[2]) ? $this->convertIsCollectedDocuments(trim($row[2])) : 0,
            'status_collected_documents_text' => isset($row[3]) ? trim($row[3]) : "",
            'status_collected_documents' => $isCollected,
            'time_collected_documents' => isset($row[4]) ? $this->importDate($row[4], 'H:i') : null,
            'date_collected_documents' => isset($row[5]) ? $this->importDate($row[5]) : null,
            'time_collected_documents_reality' => isset($row[6]) ? $this->importDate($row[6], 'H:i') : null,
            'date_collected_documents_reality' => isset($row[7]) ? $this->importDate($row[7]) : (config('constant.DA_THU_DU') == $isCollected ? date('d-m-Y') : ''),
            'num_of_document_page' => isset($row[8]) ? $this->importNumber($row[8]) : '',
            'document_type' => isset($row[9]) ? trim($row[9]) : '',
            'document_note' => isset($row[10]) ? trim($row[10]) : '',
        ];
        $this->indexRow++;

        return $result;
    }


    public function model(array $row)
    {
        return new Order($row);
    }

    public function sheets(): array
    {
        return [
            0 => new DocumentImport(),
        ];
    }


    public function convertIsCollectedDocuments($isCollected)
    {
        if (empty($isCollected)) {
            return config('constant.no');
        }
        $text = mb_strtoupper(Str::slug($isCollected));
        if ($text == 'CO') {
            return config('constant.yes');
        }
        return config('constant.no');
    }

    public function convertStatusDocuments($statusDocument)
    {
        if (empty($statusDocument)) {
            return config('constant.CHUA_THU_DU');
        }
        $text = mb_strtoupper(Str::slug($statusDocument));
        if ($text == 'DA-THU-DU') {
            return config('constant.DA_THU_DU');
        }
        if ($text == 'CHUA-THU-DU') {
            return config('constant.CHUA_THU_DU');
        }
        if ($text == 'QUA-HAN') {
            return config('constant.QUA_HAN');
        }
        if ($text == 'DEN-HAN-VAO-NGAY-HOM-SAU') {
            return config('constant.DEN_HAN_VAO_HOM_SAU');
        }
        if ($text == 'DEN-HAN-VAO-NGAY-HOM-NAY') {
            return config('constant.DEN_HAN_VAO_HOM_NAY');
        }

        return config('constant.CHUA_THU_DU');
    }

    public function headingRow(): int
    {
        return 9;
    }
}
