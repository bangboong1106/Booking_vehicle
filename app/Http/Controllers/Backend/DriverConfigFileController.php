<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\DriverConfigFileRepository;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class DriverConfigFileController extends BackendController
{
    /**
     * DriverConfigFileController constructor.
     * @param DriverConfigFileController $driverConfigFileRepository
     */

    public function __construct(DriverConfigFileRepository $driverConfigFileRepository)
    {
        parent::__construct();
        $this->setRepository($driverConfigFileRepository);
        $this->setBackUrlDefault('driver-config-file.index');
        $this->setConfirmRoute('driver-config-file.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.driver_config_file.name'));
    }

    protected function _prepareForm()
    {
        $listOption = array_keys(config('system.option'));
        $options = [];

        foreach ($listOption as $option) {
            $options[$option] = trans('common.' . $option);
        }
        
        $fileTypes = config('system.file_type');
        $actives = config('system.active');

        $this->setViewData(['options' => $options]);
        $this->setViewData(['actives' => $actives]);
        $this->setViewData(['fileTypes' => $fileTypes]);
    }
}
