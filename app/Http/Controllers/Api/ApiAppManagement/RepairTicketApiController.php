<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\RepairTicketManagementRepository;
use Validator;

class RepairTicketApiController extends ManagementApiController
{

    public function __construct(
        RepairTicketManagementRepository $repairTicketRepository
    )
    {
        parent::__construct();
        $this->setRepository($repairTicketRepository);
    }

}
