<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\VehicleGroup;
use App\Repositories\PartnerRepository;
use App\Repositories\PartnerVehicleGroupRepository;
use App\Repositories\VehicleGroupRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PartnerVehicleGroupController
 * @package App\Http\Controllers\Backend
 */
class PartnerVehicleGroupController extends BackendController
{
    public $partnerRepository;

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->partnerRepository = $partnerRepository;
    }

    /**
     * VehicleGroupController constructor.
     * @param PartnerVehicleGroupRepository $VehicleGroupRepository
     * @param PartnerRepository $partnerRepository
     */
    public function __construct(PartnerVehicleGroupRepository $VehicleGroupRepository,
                                PartnerRepository $partnerRepository)
    {
        parent::__construct();
        $this->setRepository($VehicleGroupRepository);
        $this->setPartnerRepository($partnerRepository);
        $this->setBackUrlDefault('partner-vehicle-group.index');
        $this->setConfirmRoute('partner-vehicle-group.confirm');
        $this->setMenu('vehicle');
        $this->setTitle(trans('models.vehicle_group.name'));

    }

    public function index()
    {
        $partnerId = $this->getCurrentUser()->partner_id;
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $model = $this->getRepository()->getModel();
        $categories = $model::where($model->getParentColumnName(), '=', null)
            ->where('partner_id', '=', $partnerId)->get();
        $allCategories = $model::where('partner_id', '=', $partnerId)->get()->pluck('name', 'id');
        $this->setViewData(['categories' => $categories]);
        $this->setViewData(['allCategories' => $allCategories]);
        return $this->render();
    }


    protected function _prepareForm()
    {
        $partnerId = $this->getCurrentUser()->partner_id;
        $model = $this->getRepository()->getModel();
        $vehicle_groups = $model::where('partner_id', '=', $partnerId)->get()->pluck('name', 'id');
        $this->setViewData([
            'vehicle_groups' => $vehicle_groups
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_vehicle_group'));
            }
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $parentId = $this->getEntity()->parent_id;
        $parent = empty($parentId) ? '' : $this->getRepository()->find($this->getEntity()->parent_id);
        $this->setViewData([
            'parent' => empty($parent) ? null : $parent
        ]);
    }

    //Xử lý cập nhật node bằng Baum
    protected function _updateNestedSet($entity)
    {
        $pid = $entity->getParentId();
        $name = $entity->name;
        $code = $entity->code;
        if (empty($pid)) {
            $entity->makeRoot();
            $entity->name = $name;
            $entity->code = $code;
        } else if ($pid !== FALSE) {
            $entity->makeChildOf($pid);
        }
        $entity->save();
    }

}
