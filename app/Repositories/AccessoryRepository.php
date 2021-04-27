<?php

namespace App\Repositories;

use App\Model\Entities\Accessory;
use App\Repositories\Base\CustomRepository;
use DB;

class AccessoryRepository extends CustomRepository
{
    protected $_fieldsSearch = ['name', 'description'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Accessory::class;
    }

    public function validator()
    {
        return \App\Validators\AccessoryValidator::class;
    }
}