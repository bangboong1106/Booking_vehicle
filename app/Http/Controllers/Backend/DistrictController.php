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


class DistrictController extends BackendController
{
    protected $provinceRepos;

    protected $_fieldsSearch = ['district_id', 'title'];

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

    public function __construct(DistrictRepository $districtRepository, ProvinceRepository $provinceRepository) {
        parent::__construct();
        $this->setRepository($districtRepository);
        $this->setBackUrlDefault('district.index');
        $this->setConfirmRoute('district.confirm');
        $this->setMenu('location');
        $this->setTitle(trans('models.district.name'));

        $this->setProvinceRepos($provinceRepository);
    }

    protected function _prepareForm()
    {

        $provinceList = $this->getProvinceRepos()
            ->orderBy('title', 'asc')
            ->get()
            ->pluck('title', 'province_id');

        $this->setViewData(['provinceList' => $provinceList]);
    }

    // Trc khi insert
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
    }
}