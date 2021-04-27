<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\CustomerRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsGroupRepository;
use App\Repositories\GoodsUnitRepository;
use Illuminate\Support\Arr;
use stdClass;
use Storage;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class GoodsTypeController extends BackendController
{
    protected $goodUnitRepository;
    protected $goodsGroupRepository;
    protected $customerRepository;

    public function getGoodsUnitRepository()
    {
        return $this->goodUnitRepository;
    }

    public function setGoodsUnitRepository($goodUnitRepository)
    {
        $this->goodUnitRepository = $goodUnitRepository;
    }

    public function getGoodsGroupRepository()
    {
        return $this->goodsGroupRepository;
    }

    public function setGoodsGroupRepository($goodsGroupRepository)
    {
        $this->goodsGroupRepository = $goodsGroupRepository;
    }

    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    public function setCustomerRepository($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function __construct(
        GoodsTypeRepository $goodsTypeRepository,
        GoodsUnitRepository $goodsUnitRepository,
        GoodsGroupRepository $goodsGroupRepository,
        CustomerRepository $customerRepository
    ) {
        parent::__construct();
        $this->setRepository($goodsTypeRepository);
        $this->setGoodsUnitRepository($goodsUnitRepository);
        $this->setGoodsGroupRepository($goodsGroupRepository);
        $this->setCustomerRepository($customerRepository);

        $this->setBackUrlDefault('goods-type.index');
        $this->setConfirmRoute('goods-type.confirm');
        $this->setMenu('category');
        $this->setTitle(trans('models.goods_type.name'));
    }

    public function _prepareForm()
    {
        $temps = $this->getGoodsGroupRepository()->getScopedNestedList('name', 'id', '-', false);
        $goodsGroups = [];
        $null = new stdClass();
        $null->id = "";
        $null->name = 'Vui lòng chọn nhóm hàng hoá';
        $goodsGroups[] = $null;

        foreach ($temps as $key => $temp) {
            $goodsGroup = new stdClass();
            $goodsGroup->id = $key;
            $goodsGroup->name = $temp;
            $goodsGroups[] = $goodsGroup;
        }
        $this->setViewData([
            'goodsUnits' => $this->getGoodsUnitRepository()->search()->get(),
            'goodsGroups' => $goodsGroups,
            'customers' => $this->getCustomerRepository()->search()->get()
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
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_good_type'));
            }
        }
        $this->setViewData([
            'code' => $code,
        ]);
    }

    protected function _moveFileFromTmpToMedia(&$entity)
    {
        if (empty($entity->file_id)) {
            return;
        }
        app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->file_id, 'goods');
    }
}
