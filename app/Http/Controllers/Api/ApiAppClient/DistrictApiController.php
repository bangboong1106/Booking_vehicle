<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\DistrictClientRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\WardRepository;
use DB;
use Input;
use JWTAuth;
use Validator;

class DistrictApiController extends ClientApiController
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

    public function __construct(
        CustomerRepository $customerRepository,
        DistrictClientRepository $repository,
        DistrictRepository $districtRepository,
        ProvinceRepository $provinceRepository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($repository);

        $this->setProvinceRepos($provinceRepository);
        $this->setDistrictRepos($districtRepository);
    }
}
