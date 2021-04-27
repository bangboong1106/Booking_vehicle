<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ReportScheduleRepository;

/**
 * Class ReportScheduleController
 * @package App\Http\Controllers\Backend
 */
class ReportScheduleController extends BackendController
{
    public function __construct(ReportScheduleRepository $reportScheduleRepository)
    {
        parent::__construct();
        $this->setRepository($reportScheduleRepository);
        $this->setBackUrlDefault('report-schedule.index');
        $this->setConfirmRoute('report-schedule.confirm');
        $this->setMenu('report');
        $this->setTitle(trans('models.report_schedule.name'));
    }

    public function _prepareForm()
    {
        $scheduleType = config('system.schedule_type');
        $reportType = config('system.scheduler_report_type');

        $this->setViewData([
            'schedule_type' => $scheduleType,
            'report_type' => $reportType]);
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = parent::_findEntityForUpdate($id);
        if (!empty($entity->report_type)) {
            $entity->report_type = implode(';', $entity->report_type);
        }
        return $entity;
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        if (!empty($entity->report_type)) {
            $entity->report_type = implode(';', $entity->report_type);
        }
        return $entity;
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $attributes = $this->_getFormData()->getAttributes();

        $reportTypes = config("system.scheduler_report_type");
        $reportTypeList = [];
        if (isset($attributes['report_type'])) {
            foreach ($attributes['report_type'] as $value) {
                $reportTypeList[$value] = $reportTypes[$value];
            }
        }
        $this->setViewData([
            'reportTypeList' => $reportTypeList
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $entity = $this->getRepository()->getReportScheduleWithID($id);
        $reportTypes = config("system.scheduler_report_type");
        $reportTypeList = [];
        if (isset($entity->report_type)) {
            $list = explode(';', $entity->report_type);
            foreach ($list as $value) {
                $reportTypeList[$value] = $reportTypes[$value];
            }
        }
        $this->setViewData([
            'reportTypeList' => $reportTypeList
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData()->getAttributes();
        $reportTypeList = "";
        if (isset($attributes['report_type'])) {
            $reportTypeList = implode(';', $attributes['report_type']);
        } else {
            $entity = $this->getRepository()->getReportScheduleWithID($id);
            if (!empty($entity->report_type)) {
                $reportTypeList = $entity->report_type;
            }
        }
        $this->setViewData([
            'reportTypeList' => $reportTypeList
        ]);
    }
}
