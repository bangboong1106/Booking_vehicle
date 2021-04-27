<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\GoodsTypeClientRepository;
use Storage;
use Input;
use App\Repositories\CustomerRepository;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class GoodsApiController extends ClientApiController
{
    protected $customerRepos;

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function __construct(GoodsTypeClientRepository $GoodsTypeRepository, CustomerRepository $customerRepository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($GoodsTypeRepository);
        $this->setCustomerRepos($customerRepository);
    }

}
