<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\CustomerDefaultDataRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ProvinceRepository;


use Input;

class CustomerDefaultDataController extends BackendController
{

    protected $_fieldsSearch = ['id', 'title'];

    protected $_customerRepository;
    protected $_provinceRepository;


    /**
     * @return CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param mixed $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->_provinceRepository;
    }

    /**
     * @param mixed $provinceRepository
     */
    public function setProvinceRepository($provinceRepository): void
    {
        $this->_provinceRepository = $provinceRepository;
    }

    public function __construct(
        CustomerDefaultDataRepository $customerDefaultDataRepository,
        CustomerRepository $customerRepository,
        ProvinceRepository $provinceRepository

    ) {
        parent::__construct();
        $this->setRepository($customerDefaultDataRepository);
        $this->setProvinceRepository($provinceRepository);

        $this->setCustomerRepository($customerRepository);
        $this->setBackUrlDefault('customer-default-data.index');
        $this->setConfirmRoute('customer-default-data.confirm');
        $this->setMenu('customer');
        $this->setTitle(trans('models.customer_default_data.name'));
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
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
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_customer_default'));
            }
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
    }

    // Lấy dữ liệu mặc định của khách hàng
    // CreatedBy nlhoang 08/09/2020
    protected function defaultData()
    {
        $customerID = Request::get('id', 0);
        $items = $this->getRepository()->getDefaultDataByCustomerID($customerID);
        $data = [
            'content' => ''
        ];
        if (count($items) > 0) {
            if (count($items) == 1) {
                $item = $items->first();
                $locationDestinations = $item->locationDestinationAttributes();
                $locationArrivals = $item->locationArrivalAttributes();

                if (count($locationDestinations) <= 1 && count($locationArrivals) <= 1) {
                    $data['locationDestination'] = empty($locationDestinations) ? null : $locationDestinations->first();
                    $data['locationArrival'] = empty($locationArrivals) ? null : $locationArrivals->first();
                    $data['systemCodeConfig'] = $item->systemCodeConfig;
                } else {
                    $this->setViewData([
                        'items' => $items
                    ]);
                    $content_view = $this->render('backend.customer_default_data.default_data')->render();
                    $data = [
                        'content' => $content_view,
                    ];
                }
            } else {
                $this->setViewData([
                    'items' => $items
                ]);
                $content_view = $this->render('backend.customer_default_data.default_data')->render();
                $data = [
                    'content' => $content_view,
                ];
            }
        }
        $this->setData($data);

        return $this->renderJson();
    }
}
