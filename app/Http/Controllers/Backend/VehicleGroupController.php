<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\VehicleGroup;
use App\Repositories\PartnerRepository;
use App\Repositories\VehicleGroupRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class VehicleGroupController
 * @package App\Http\Controllers\Backend
 */
class VehicleGroupController extends BackendController
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
     * @param VehicleGroupRepository $VehicleGroupRepository
     * @param PartnerRepository $partnerRepository
     */
    public function __construct(VehicleGroupRepository $VehicleGroupRepository,
                                PartnerRepository $partnerRepository)
    {
        parent::__construct();
        $this->setRepository($VehicleGroupRepository);
        $this->setPartnerRepository($partnerRepository);
        $this->setBackUrlDefault('vehicle-group.index');
        $this->setConfirmRoute('vehicle-group.confirm');
        $this->setMenu('vehicle');
        $this->setTitle(trans('models.vehicle_group.name'));

    }

    public function index()
    {
        parent::_checkPermission('view');
        $default = collect([ -1 => '-- Đối tác vận tải --']);
        $partnerList = $this->getPartnerRepository()->getListForSelect();
        $categories = null;
        $allCategories = null;

        foreach ($partnerList as $key => $partner) {
            $default[$key] = $partner;
        }

        $partnerList = $default;

        parent::_cleanFormData();
        $this->detectCurrentPage();
        if ($partnerList && count($partnerList) > 0) {
            $partnerId = -1;
            $model = $this->getRepository()->getModel();
            $categories = $model::where($model->getParentColumnName(), '=', null);
            
            if ($partnerId > 0) {
                $categories->where('partner_id', '=', $partnerId);
            }
            $categories = $categories->get();

            $allCategories = $model::where('partner_id', '=', $partnerId)->get()->pluck('name', 'id');
        }

        $this->setViewData([
            'categories' => $categories,
            'allCategories' => $allCategories,
            'partnerList' => $partnerList,
        ]);
        return $this->render();
    }


    protected function _prepareForm()
    {
        $model = $this->getRepository()->getModel();
        $vehicle_groups = $model::getNestedList('name', 'id', '-');
        $this->setViewData([
            'vehicle_groups' => $vehicle_groups,
            'partnerList' => $this->getPartnerRepository()->getListForSelect()
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

    public function getVehicleGroups()
    {
        $partnerId = empty(Request::get('partner_id')) ? null : Request::get('partner_id');
        $model = $this->getRepository()->getModel();
        $categories = $model::where($model->getParentColumnName(), '=', null);

        if ($partnerId > 0) {
            $categories->where('partner_id', '=', $partnerId);
        }

        $categories = $categories->get();

        $allCategories = $model::where('partner_id', '=', $partnerId)->get()->pluck('name', 'id');
        $this->setViewData([
            'categories' => $categories,
            'allCategories' => $allCategories,
            'deleteRoute' => 'vehicle-group.destroy'
        ]);

        return [
            'content' => $this->render('backend.vehicle_group.item_list')->render(),
        ];
    }

    public function getDataCombobox()
    {
        $partnerId = empty(Request::get('partner_id')) ? null : Request::get('partner_id');

        $data = $this->getRepository()->getVehicleGroupWithPartnerId($partnerId);
        return response()->json($data);
    }

}
