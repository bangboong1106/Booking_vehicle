<?php

namespace App\Repositories;

use App\Model\Entities\RepairTicketItem;
use App\Repositories\Base\CustomRepository;
use DB;

class RepairTicketItemRepository extends CustomRepository
{
    protected $_fieldsSearch = ['name', 'description'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return RepairTicketItem::class;
    }

    public function validator()
    {
        return \App\Validators\RepairTicketItemValidator::class;
    }
}