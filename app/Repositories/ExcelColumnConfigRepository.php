<?php

namespace App\Repositories;

use App\Model\Entities\ExcelColumnConfig;
use App\Repositories\Base\CustomRepository;
use Illuminate\Support\Facades\Cache;

class ExcelColumnConfigRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ExcelColumnConfig::class;
    }

    public function validator()
    {
        return \App\Validators\ExcelColumnConfigValidator::class;
    }

    // Lấy danh sách column cấu hình theo từng user
    public function getColumnConfig($user_id, $model)
    {
        $result = $this->search([
            'model_eq' => $model,
        ])
        ->with(['excelColumnMappingConfigs'])
        ->first();
        return $result;
    }
}
