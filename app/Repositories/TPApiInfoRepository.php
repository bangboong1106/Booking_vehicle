<?php

namespace App\Repositories;

use App\Model\Entities\TPApiInfo;
use App\Repositories\Base\CustomRepository;
use App\Validators\TPApiInfoValidator;

class TPApiInfoRepository extends CustomRepository
{
    function model()
    {
        return TPApiInfo::class;
    }

    public function validator()
    {
        return TPApiInfoValidator::class;
    }

    public function getApiInfo($action, $partnerName)
    {
        if ($action == null)
            return null;
        return $this->search([
            'action_eq' => $action,
            'partner_name_eq' => $partnerName
        ])->first();
    }

    public function getApiInfoList($action)
    {
        if ($action == null)
            return null;
        return $this->search([
            'action_eq' => $action
        ])->get();
    }
}