<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\GoodsGroup;
use App\Repositories\GoodsGroupRepository;
use Illuminate\Support\Facades\DB;


class GoodsGroupController extends BackendController
{
    public function __construct(GoodsGroupRepository $goodsGroupRepository)
    {
        parent::__construct();
        $this->setRepository($goodsGroupRepository);
        $this->setBackUrlDefault('goods-group.index');
        $this->setConfirmRoute('goods-group.confirm');
        $this->setMenu('goods');
        $this->setTitle(trans('models.goods_group.name'));

    }

    public function index()
    {
        parent::_checkPermission();
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $model = $this->getRepository()->getModel();
        $categories = $model::where($model->getParentColumnName(), '=', null)->get();
        $allCategories = $model::pluck('name', 'id')->all();
        $this->setViewData(['categories' => $categories]);
        $this->setViewData(['allCategories' => $allCategories]);
        return $this->render();
    }


    protected function _prepareForm()
    {
        $model = $this->getRepository()->getModel();
        $goods_groups = $model::getNestedList('name', 'id', '-');
        $this->setViewData(['goods_groups' => $goods_groups]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_goods_group'));
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
