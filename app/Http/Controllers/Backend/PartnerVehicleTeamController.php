<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\Driver;
use App\Model\Entities\VehicleTeam;
use App\Repositories\DriverRepository;
use App\Repositories\PartnerVehicleTeamRepository;
use Illuminate\Http\Request;


class PartnerVehicleTeamController extends BackendController
{
    protected $_fieldsSearch = ['name'];
    private $_driverRepository;

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param mixed $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    public function __construct(PartnerVehicleTeamRepository $partnerVehicleTeamRepository, DriverRepository $driverRepository)
    {
        parent::__construct();
        $this->setRepository($partnerVehicleTeamRepository);
        $this->setBackUrlDefault('partner-vehicle-team.index');
        $this->setConfirmRoute('partner-vehicle-team.confirm');
        $this->setMenu('partner');
        $this->setTitle(trans('models.vehicle_team.name'));
        $this->setDriverRepository($driverRepository);
    }

    public function dataAjax(Request $request)
    {
        $data = Driver::select("id", "full_name")
            ->where('full_name', 'LIKE', '%' . request('q') . '%')
            ->paginate(PHP_INT_MAX);
        return response()->json($data);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $this->_prepareEntity($this->getEntity());
        $this->setViewData([
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $vehicles = $this->getRepository()->getVehiclesByID($id);
        $this->setViewData([
            'show_history' => true,
            'vehicles' => $vehicles
        ]);
        $this->_prepareEntity($this->getEntity());
    }

    /**
     * @param $entity VehicleTeam
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $entity->drivers()->sync($entity->driver_ids);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);

        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_vehicle_team'));
            }
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    /**
     * @return BackendController
     */
    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $this->_prepareEntity($this->getEntity());
        return $parent;
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $this->_prepareEntity($this->getEntity());
        return $parent;
    }

    protected function _prepareEntity($entity)
    {
        if (empty($entity->driver_ids)) {
            return;
        }
        $drivers = $this->getDriverRepository()->search(['id_in' => $entity->driver_ids])->groupBy('drivers.id')->get();
        $entity->setRelation('drivers', $drivers);
        $this->setEntity($entity);
    }

    protected function _prepareForm()
    {
        $user = $this->getCurrentUser();
        $this->setViewData([
            'partnerId' => $user->partner_id
        ]);
    }

}