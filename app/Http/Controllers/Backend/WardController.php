<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\WardRepository;


class WardController extends BackendController
{
    protected $provinceRepos;

    protected $districtRepos;

    protected $_fieldsSearch = ['ward_id', 'title'];

    /**
     * @return mixed
     */
    public function getProvinceRepos()
    {
        return $this->provinceRepos;
    }

    /**
     * @param mixed $provinceRepos
     */
    public function setProvinceRepos($provinceRepos)
    {
        $this->provinceRepos = $provinceRepos;
    }

    /**
     * @return mixed
     */
    public function getDistrictRepos()
    {
        return $this->districtRepos;
    }

    /**
     * @param mixed $districtRepos
     */
    public function setDistrictRepos($districtRepos)
    {
        $this->districtRepos = $districtRepos;
    }

    public function __construct(WardRepository $wardRepository, DistrictRepository $districtRepository, ProvinceRepository $provinceRepository)
    {
        parent::__construct();
        $this->setRepository($wardRepository);
        $this->setBackUrlDefault('ward.index');
        $this->setConfirmRoute('ward.confirm');
        $this->setMenu('location');
        $this->setTitle(trans('models.ward.name'));

        $this->setProvinceRepos($provinceRepository);
        $this->setDistrictRepos($districtRepository);
    }

    protected function _prepareForm()
    {
        $provinceList = $this->getProvinceRepos()->get()->pluck('title', 'province_id')->toArray();
        $provinceId = key($provinceList);

        $districtList = $this->getDistrictRepos()
            ->where('province_id', $provinceId)
            ->orderBy('title', 'asc')
            ->pluck('title', 'district_id');

        $this->setViewData(['provinceList' => $provinceList]);
        $this->setViewData(['districtList' => $districtList]);
    }

    public function getDistrict()
    {
        $provinceId = \Request::get('province_id', '');

        $districtList = empty($provinceId) ? [] : $this->getDistrictRepos()
            ->where('province_id', $provinceId)
            ->orderBy('title', 'asc')
            ->pluck('title', 'district_id');

        $this->setViewData(['districtList' => $districtList]);
        $html = [
            'content' => $this->render('backend.ward._district_list')->render()
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    public function getWards()
    {
        $districtId = \Request::get('district_id', '');

        $wards = empty($districtId) ? [] :
            $this->getRepository()
                ->where('district_id', $districtId)
                ->orderBy('title', 'asc')
                ->pluck('title', 'ward_id');

        $this->setViewData(['wards' => $wards]);
        $html = [
            'content' => $this->render('backend.ward._ward_list')->render()
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();

        $entity = $this->getEntity();
        $provinceId = $entity->province_id;
        $query = [
            'province_id_eq' => $provinceId
        ];
        $provinceObj = $this->getProvinceRepos()->search($query)->first();
        $this->setViewData(['provinceObj' => $provinceObj]);

        $districtId = $entity->district_id;
        $query = [
            'district_id_eq' => $districtId
        ];
        $districtObj = $this->getDistrictRepos()->search($query)->first();
        $this->setViewData(['districtObj' => $districtObj]);
    }
}