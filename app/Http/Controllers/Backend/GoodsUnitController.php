<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\GoodsUnit;
use App\Repositories\CustomerRepository;
use App\Repositories\GoodsUnitRepository;
use Illuminate\Support\Facades\Request;

/**
 * Class GoodsUnitController
 * @package App\Http\Controllers\Backend
 */
class GoodsUnitController extends BackendController
{
    protected $customerRepository;

    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    public function setCustomerRepository($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }


    public function __construct(GoodsUnitRepository $GoodsUnitRepository, CustomerRepository $customerRepository)
    {
        parent::__construct();
        $this->setRepository($GoodsUnitRepository);
        $this->setBackUrlDefault('goods-unit.index');
        $this->setConfirmRoute('goods-unit.confirm');
        $this->setMenu('category');
        $this->setTitle(trans('models.goods_unit.name'));
        $this->setCustomerRepository($customerRepository);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_good_unit'));
            }
        }

        $this->setViewData([
            'code' => $code
        ]);
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'customers' => $this->getCustomerRepository()->search()->get()
        ]);
    }

    public function getDataForComboBox()
    {
        $all = Request::get('all');
        $q = Request::get('q');
        $customer_id = Request::get('c_id', -1);
        $query = $this->getRepository()->getItemsByUserID($all, $q, $customer_id);
        return response()->json(['items' => $query->toArray()['data'], 'pagination' => $query->nextPageUrl() ? true : false]);
    }
}
