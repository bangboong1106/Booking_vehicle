<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\VehicleConfigSpecificationRepository;

/**
 * Class VehicleConfigSpecificationController
 * @package App\Http\Controllers\Backend
 */
class VehicleConfigSpecificationController extends BackendController
{
    /**
     * VehicleConfigSpecificationController constructor.
     * @param VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository
     */
    public function __construct(VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository)
    {
        parent::__construct();
        $this->setRepository($vehicleConfigSpecificationRepository);
        $this->setBackUrlDefault('vehicle-config-specification.index');
        $this->setConfirmRoute('vehicle-config-specification.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.vehicle_config_specification.name'));
    }

    protected function _prepareForm()
    {
        $actives = config('system.active');
        $types = config('system.column_type');

        $this->setViewData(['actives' => $actives]);
        $this->setViewData(['types' => $types]);
    }
}
