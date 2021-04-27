<?php

namespace App\Repositories;

use App\Model\Entities\GpsSyncLog;
use App\Repositories\Base\CustomRepository;
use App\Validators\GpsSyncLogValidator;

class GpsSyncLogRepository extends CustomRepository
{
    function model()
    {
        return GpsSyncLog::class;
    }

    function validator()
    {
        return GpsSyncLogValidator::class;
    }
}