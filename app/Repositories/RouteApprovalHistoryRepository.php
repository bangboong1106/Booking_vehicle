<?php

namespace App\Repositories;

use App\Model\Entities\RouteApprovalHistory;
use App\Repositories\Base\CustomRepository;
use App\Validators\RouteApprovalHistoryValidator;

class RouteApprovalHistoryRepository extends CustomRepository
{
    function model()
    {
        return RouteApprovalHistory::class;
    }

    public function validator()
    {
        return RouteApprovalHistoryValidator::class;
    }

}