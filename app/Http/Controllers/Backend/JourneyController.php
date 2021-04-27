<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\RoutesRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\Request;


class JourneyController extends BackendController
{
    protected $vehicleRepository;
    protected $routeRepository;


    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->vehicleRepository;
    }

    /**
     * @param mixed $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->vehicleRepository = $vehicleRepository;
    }


    /**
     * @return VehicleRepository
     */
    public function getRouteRepository()
    {
        return $this->routeRepository;
    }

    /**
     * @param mixed $vehicleRepository
     */
    public function setRouteRepository($routesRepository): void
    {
        $this->routeRepository = $routesRepository;
    }

    public function __construct(
        VehicleRepository $vehicleRepository,
        RoutesRepository $routesRepository
    ) {
        parent::__construct();
        $this->setBackUrlDefault('journey.index');
        $this->setConfirmRoute('journey.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.journey.attributes.map'));
        $this->setVehicleRepository($vehicleRepository);
        $this->setRouteRepository($routesRepository);

        $this->setMap(true);
    }

    public function index()
    {
        $vehicles = $this->getVehicleRepository()->getVehiclesByUserId();
        $result = [];
        foreach ($vehicles as $vehicle) {
            if (empty($vehicle['latitude']) || empty($vehicle['longitude'])) {
                continue;
            }

            $result[] = [
                'id' => $vehicle->id,
                'title' => $vehicle->title,
                'latitude' => $vehicle->latitude,
                'longitude' => $vehicle->longitude,
                'plate' => $vehicle->vehicle_plate,
                'weight' => $vehicle->weight,
                'volume' => $vehicle->volume,
                'length' => $vehicle->length,
                'width' => $vehicle->width,
                'height' => $vehicle->height,
                'status' => $vehicle->status,
                'current_location' => $vehicle->current_location,
                'partner_id' => $vehicle->partner_id,
            ];
        }

        $this->setViewData([
            'vehicles' => json_encode($result),
            'vehicleList' => $vehicles,
            'sync' => true
        ]);

        return $this->render();
    }

    // Xem chi tiết thông tin xe
    // CreatedBy nlhoang 05/10/2020
    public function detail($id)
    {
        $vehicle = $this->getVehicleRepository()->getCurrentItemByID($id);
        $this->setViewData([
            'vehicle' => ($vehicle),
        ]);
        $html = [
            'content' => $this->render('backend.journey.detail')->render(),
        ];
        $this->setData($html);
        return $this->renderJson();
    }
}
