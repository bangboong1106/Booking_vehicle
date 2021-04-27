<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\AlertLogRepository;

/**
 * Class AlertLogController
 * @package App\Http\Controllers\Backend
 */
class AlertLogController extends BackendController
{
    public function __construct(AlertLogRepository $alertLogRepository)
    {
        parent::__construct();
        $this->setRepository($alertLogRepository);
        $this->setBackUrlDefault('alert-log.index');
        $this->setConfirmRoute('alert-log.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.alert_log.name'));
    }

    protected function _prepareFormWithID($id)
    {
        $alertTypes = config('system.alert_logs_type');

        $this->setViewData(['alert_types' => $alertTypes]);
    }

}
