<?php

namespace App\Repositories;

use App\Model\Entities\Permission;
use App\Repositories\Base\CustomRepository;

class PermissionRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Permission::class;
    }

    public function validator()
    {
        return null;
    }
}