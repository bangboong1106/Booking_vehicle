<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ActivityLogRepository;

class ActivityLogController extends BackendController
{
    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        parent::__construct();

        $this->setRepository($activityLogRepository);

        $this->setBackUrlDefault('activity-log.index');
        $this->setConfirmRoute('activity-log.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.activity_log.name'));
        $this->setMap(true);
    }
}