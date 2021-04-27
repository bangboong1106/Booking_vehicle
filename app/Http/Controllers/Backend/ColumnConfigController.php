<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ColumnConfigRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ColumnConfigController
 * @package App\Http\Controllers\Backend
 */
class ColumnConfigController extends BackendController
{

    public function __construct(ColumnConfigRepository $columnConfigRepository)
    {
        parent::__construct();
        $this->setRepository($columnConfigRepository);
    }

    public function saveColumnConfig()
    {
        try {

            $table_id = Request::json('table_id');
            $config = Request::json('config');
            $userId = Auth::User()->id;
            $sort_field = Request::json('sort_field');
            $sort_type = Request::json('sort_type');
            $page_size = Request::json('page_size');

            $cacheKey = 'column_config_' . $userId . '_' . $table_id;

            //            if (Cache::has($cacheKey)) {
            //            Cache::forget($cacheKey);
            //            }
            Cache::flush();

            DB::beginTransaction();

            if (isset($table_id)) {
                $columnConfig = $this->getRepository()->getColumnConfig($userId, $table_id);
                if ($columnConfig == null)
                    $columnConfig = $this->getRepository()->findFirstOrNew([]);

                $columnConfig->user_id = $userId;
                $columnConfig->table_id = $table_id;
                $columnConfig->config = !empty($config) ? json_encode($config) : null;
                $columnConfig->sort_field = empty($sort_field) ? $columnConfig->sort_field :  $sort_field;
                $columnConfig->sort_type = empty($sort_type) ?  $columnConfig->sort_type :  $sort_type;
                $columnConfig->page_size = empty($page_size) ?  $columnConfig->page_size :  $page_size;

                $columnConfig->save();
            }

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
    }
}
