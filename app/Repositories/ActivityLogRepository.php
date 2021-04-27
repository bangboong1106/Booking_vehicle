<?php

namespace App\Repositories;

use App\Model\Entities\ActivityLog;
use App\Repositories\Base\CustomRepository;
use App\Validators\ActivityLogValidator;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ActivityLog::class;
    }

    public function validator()
    {
        return ActivityLogValidator::class;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->select(['activity_log.*', 'admin_users.username as username', 'admin_users.email as email'])
            ->leftJoin('admin_users', 'activity_log.causer_id', '=', 'admin_users.id');
    }
}
