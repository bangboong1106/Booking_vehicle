<?php

namespace App\Imports;

use App\Model\Entities\Driver;
use DateTime;

class PartnerDriverImport extends BaseImport
{
    protected $indexRow = 1;
    protected $_data = [];

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $row = array_values($row);

        $result = [
            'row' => $this->headingRow() + $this->indexRow,
            'code' => isset($row[0]) ? $row[0] : '',
            'full_name' => isset($row[1]) ? $row[1] : '',
            'mobile_no' => isset($row[2]) ? $row[2] : '',
            'adminUser' => [
                'id' => null,
                'username' => isset($row[3]) ? $row[3] : '',
                'email' => isset($row[5]) ? $row[5] : '',
                'role' => 'driver'
            ],
            'create_account' => $this->getCreateAccountFlag(isset($row[3]) ? $row[3] : '', isset($row[4]) ? $row[4] : ''),
            'vehicle_team_codes' => $this->getCodeList(isset($row[6]) ? $row[6] : ''),
            'vehicle_team_text' => isset($row[6]) ? $row[6] : '',
            'id_no' => isset($row[7]) ? $row[7] : '',
            'driver_license' => isset($row[8]) ? $row[8] : '',
            'birth_date' => isset($row[9]) ? $this->importDate($row[9]) : null,
            'sex' => isset($row[10]) ? $this->convertSex($row[10]) : null,
            'sex_text' => isset($row[10]) ? $row[10] : '',
            'experience_drive' => isset($row[11]) ? $row[11] : '',
            'work_date' => isset($row[12]) ? $this->importDate($row[12]) : null,
            'experience_work' => isset($row[12]) ? $this->convertExperienceWork($row[12]) : '',
            'address' => isset($row[13]) ? $row[13] : '',
            'hometown' => isset($row[14]) ? $row[14] : '',
            'evaluate' => isset($row[15]) ? $row[15] : '',
            'rank' => isset($row[16]) ? $row[16] : '',
            'work_description' => isset($row[17]) ? $row[17] : '',
            'note' => isset($row[18]) ? $row[18] : '',
        ];
        if (!empty($row[4])) {
            $result['adminUser'] += [
                'password' => $row[4],
                'password_confirmation' => $row[4],
            ];
        }
        if (!empty($row[3])) {
            $result['create_account'] = 1;
        }

        $this->indexRow++;

        return $result;
    }

    public function model(array $row)
    {
        if (empty($row)) {
            return null;
        }
        return new Driver($row);
    }

    public function sheets(): array
    {
        return [
            0 => new PartnerDriverImport(),
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

    public function convertExperienceWork($workDate)
    {
        if (!isset($workDate))
            return "";
        $now = new DateTime();
        $datetime = new DateTime($this->importDate($workDate));
        $diff = $now->diff($datetime);
        return $diff->y;
    }

    public function getCreateAccountFlag($username, $password)
    {
        if (empty($username) || empty($password))
            return false;
        return true;
    }

    public function headingRow(): int
    {
        return 9;
    }
}
