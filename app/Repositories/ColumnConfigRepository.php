<?php

namespace App\Repositories;

use App\Model\Entities\ColumnConfig;
use App\Repositories\Base\CustomRepository;
use Illuminate\Support\Facades\Cache;

class ColumnConfigRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ColumnConfig::class;
    }

    public function validator()
    {
        return \App\Validators\ColumnConfigValidator::class;
    }

    // Lấy danh sách column cấu hình theo từng user
    // Modified nlhoang 19/03/2020: bổ sung lấy ra từng cache
    public function getColumnConfig($user_id, $table_id)
    {
        $cacheKey = 'column_config_' . $user_id . '_' . $table_id;
        //        if (!Cache::has($cacheKey)) {
        if (true) {
            if ($user_id && $table_id) {
                $result = $this->search([
                    'user_id_eq' => $user_id,
                    'table_id_eq' => $table_id
                ])->first();
            } else {
                $result = null;
            }
            Cache::forever($cacheKey, $result);
            return $result;
        } else {
            return Cache::get($cacheKey);
        }
    }

    public function getConfigList($user_id, $table_id)
    {
        $configList = null;
        $columnConfig = $this->getColumnConfig($user_id, $table_id);
        if ($columnConfig != null && !empty($columnConfig->config)) {
            $configList = json_decode($columnConfig->config, true);
            usort($configList, function ($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });

            $sort_field = $columnConfig->sort_field;
            $sort_type = $columnConfig->sort_type;
            $page_size = $columnConfig->page_size;
        } else {
            $sort_field = 'id';
            $sort_type = 'desc';
            $page_size = null;
        }
        return
            [
                'sort_field' => $sort_field,
                'sort_type' => $sort_type,
                'page_size' => $page_size,
                'configList' => $configList
            ];
    }
}
