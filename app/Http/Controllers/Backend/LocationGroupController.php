<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\LocationGroup;
use App\Repositories\CustomerRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\LocationRepository;
use Input;

/**
 * Class LocationGroupController
 * @package App\Http\Controllers\Backend
 */
class LocationGroupController extends BackendController
{
    protected $_locationRepository;
    protected $customerRepository;

    /**
     * @return LocationRepository
     */
    public function getLocationRepository()
    {
        return $this->_locationRepository;
    }

    /**
     * @param mixed $locationRepository
     */
    public function setLocationRepository($locationRepository): void
    {
        $this->_locationRepository = $locationRepository;
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
        LocationGroupRepository $locationGroupRepository,
        LocationRepository $locationRepository,
        CustomerRepository $customerRepository
    ){
        parent::__construct();
        $this->setRepository($locationGroupRepository);
        $this->setBackUrlDefault('location-group.index');
        $this->setConfirmRoute('location-group.confirm');
        $this->setMenu('location-group');
        $this->setTitle(trans('models.location_group.name'));
        $this->setLocationRepository($locationRepository);
        $this->setCustomerRepository($customerRepository);
    }

    public function valid()
    {
        $response = parent::valid();
        $locationIds = Request::get('location_ids', []);
        $locationGroupId = Request::get('id', null);

        if (empty($locationIds)) return $response;
        $locations = $this->getLocationRepository()->search(['id_in' => $locationIds])->get();

        $existGroupLocations = [];
        foreach ($locations as $location) {
            /*  if ((isset($locationGroupId) && $location->location_group_id !== intval($locationGroupId))
                  || (empty($locationGroupId) && !empty($location->location_group_id))) {
                  $existGroupLocations[] = $location->title;
              }*/
            if (!empty($location->location_group_id) && $location->location_group_id !== intval($locationGroupId))
                $existGroupLocations[] = $location->title;
        }

        if (!empty($existGroupLocations)) {
            $validation = session()->get('validation', null);
            $inValid = isset($validation) ? $this->_getListErrorMessage() : [];
            $inValid['location_ids'] = trans(
                'validation.group_exist',
                ['name' => implode(',', $existGroupLocations)]
            );
            Session()->put('validation', ['inValid' => $inValid]);
            return $this->_back()->withInput();
        }

        return $response;
    }

    /**
     * @param LocationGroup $entity
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $locations = [];
        if (!empty($entity->location_ids)) {

            $oldLocations = $this->getLocationRepository()->search(['location_group_id_in' => $entity->id])->get();
            foreach ($oldLocations as &$location) {
                $location->location_group_id = null;
                $location->save();
            }

            $locations = $this->getLocationRepository()->search(['id_in' => $entity->location_ids])->get();
            foreach ($locations as $location) {
                $location->location_group_id = $entity->id;
                $location->save();
            }
        }
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $this->_prepareEntity($this->getEntity());
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $entity = $this->getEntity();
        $locations = $this->getLocationRepository()->search(['location_group_id_eq' => $id], [
            'locations.*',
            'p.title as p_title',
            'd.title as d_title',
            'w.title as w_title',
        ])->get();
        $entity->setRelation('locations', $locations);
        return $entity;
    }

    protected function _prepareEntity($entity)
    {
        if (empty($entity->location_ids)) {
            return;
        }
        $locations = $this->getLocationRepository()->search(['id_in' => $entity->location_ids], [
            'locations.*',
            'p.title as p_title',
            'd.title as d_title',
            'w.title as w_title',
        ])->get();
        $entity->setRelation('locations', $locations);
        $this->setEntity($entity);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location_group'));
            }
        }

        $this->setViewData([
            'code' => $code
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

    public function _prepareForm()
    {
        $this->setViewData([
            'customers' => $this->getCustomerRepository()->search()->get()
        ]);
    }

    public function getDataForSelect()
    {
        $customerId = Request::get('customer_id', -1);

        if ($customerId > 0) {
            $locationGroup =  $this->getRepository()->search([
                'customer_id_eq' => $customerId
            ])->get()->pluck('title', 'id');

            $this->setViewData(['locationGroup' => $locationGroup]);
            $html = [
                'content' => $this->render('backend.location._location_group_list')->render()
            ];

            $this->setData($html);
            return $this->renderJson();
        }

        return $this->getRepository()->search()->get()->pluck('title', 'id');
    }
}
