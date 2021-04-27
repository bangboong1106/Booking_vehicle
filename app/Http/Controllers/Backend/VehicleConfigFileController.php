<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\DriverConfigFileRepository;
use App\Repositories\VehicleConfigFileRepository;
use App\Validators\VehicleConfigFileValidator;

/**
 * Class VehicleConfigFileController
 * @package App\Http\Controllers\Backend
 */
class VehicleConfigFileController extends BackendController
{
    /**
     * VehicleConfigFileController constructor.
     * @param VehicleConfigFileController $vehicleConfigFileRepository
     */

    public function __construct(VehicleConfigFileRepository $vehicleConfigFileRepository)
    {
        parent::__construct();
        $this->setRepository($vehicleConfigFileRepository);
        $this->setBackUrlDefault('vehicle-config-file.index');
        $this->setConfirmRoute('vehicle-config-file.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.vehicle_config_file.name'));
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
