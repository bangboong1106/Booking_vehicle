<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ContactRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ProvinceRepository;
use Illuminate\Http\RedirectResponse;

/**
 * Class ContractController
 * @package App\Http\Controllers\Backend
 */
class ContactController extends BackendController
{
    protected $_locationRepository;
    protected $_provinceRepository;

    /**
     * @return mixed
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

    /**
     * @return mixed
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

    public function __construct(ContactRepository $contactRepository, LocationRepository $locationRepository, ProvinceRepository $provinceRepository)
    {
        parent::__construct();
        $this->setRepository($contactRepository);
        $this->setBackUrlDefault('contact.index');
        $this->setConfirmRoute('contact.confirm');
        $this->setMenu('customer');
        $this->setTitle(trans('models.contact.name'));
        $this->setLocationRepository($locationRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setMap(true);

    }

    protected function _prepareForm()
    {
        $actives = config('system.active');
        $this->setViewData([
            'actives' => $actives,
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => []
        ]);
    }

    public function getSelected($entity)
    {
        $locationIds = [];
        empty($entity->location_id) ? null : $locationIds[] = $entity->location_id;
        $locationList = $this->getLocationRepository()->getLocationsByIds($locationIds);


        $this->setViewData([
            'locationList' => $locationList,
        ]);
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        parent::_prepareAfterSetEntity($prepare);
        $entity = $this->getEntity();
        $this->getSelected($entity);
    }

    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $entity = $this->getEntity();
        $this->getSelected($entity);
        return $parent;
    }

}
