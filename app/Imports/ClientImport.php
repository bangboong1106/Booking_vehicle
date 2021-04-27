<?php

namespace App\Imports;

use App\Model\Entities\Customer;
use Illuminate\Support\Str;

class ClientImport extends BaseImport
{
    protected $indexRow = 1;

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'customer_code' => isset($row[0]) ? $row[0] : '',
            'full_name' => isset($row[1]) ? $row[1] : '',
            'parent_code' => isset($row[2]) ? $this->getCode($row[2]) : '',
            'parent_text' => isset($row[2]) ? $row[2] : '',
            'type' => isset($row[3]) ? $this->convertType($row[3]) : '',
            'adminUser' => [
                'id' => null,
                'username' => isset($row[4]) ? $row[4] : '',
                'email' => isset($row[6]) ? $row[6] : '',
                'role' => 'customer'
            ],
            'mobile_no' => isset($row[7]) ? $row[7] : '',
            'delegate' => isset($row[8]) ? $row[8] : null,
            'tax_code' => isset($row[9]) ? $row[9] : '',
            'current_address' => isset($row[10]) ? $row[10] : '',
            'birth_date' => isset($row[11]) ? $this->importDate($row[11]) : null,
            'sex' => isset($row[12]) ? $this->convertSex($row[12]) : null,
            'sex_text' => isset($row[12]) ? $row[12] : '',
            'note' => isset($row[13]) ? $row[13] : '',
        ];
        if (!empty($row[4])) {
            $result['adminUser'] += [
                'password' => $row[5],
                'password_confirmation' => $row[5],
            ];
        }

        $this->indexRow++;

        return $result;
    }

    public function model(array $row)
    {
        if (empty($row)) {
            return null;
        }
        return new Customer($row);
    }

    public function convertType($typeText)
    {
        if (empty($typeText)) {
            return config('constant.INDIVIDUAL_CUSTOMERS');
        }

        $text = mb_strtoupper(str_slug($typeText, ' '));

        if ( Str::contains($text, 'DOANH NGHIEP')) {
            return config('constant.CORPORATE_CUSTOMERS');
        }

        return config('constant.INDIVIDUAL_CUSTOMERS');
    }

    public function sheets(): array
    {
        return [
            0 => new CustomersImport(),
        ];
    }

    public function convertSex($sex)
    {
        if (empty($sex)) {
            return null;
        }

        $text = mb_strtoupper(str_slug($sex));
        if ($text == 'NAM') {
            return config('constant.MALE_SEX_TYPE');
        }

        return config('constant.FEMALE_SEX_TYPE');
    }

    public function headingRow(): int
    {
        return 9;
    }
}
