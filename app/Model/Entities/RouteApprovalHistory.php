<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed route_status
 * @property mixed is_approved
 */
class RouteApprovalHistory extends ModelSoftDelete
{
    protected $table = "route_approval_history";

    protected $_alias = 'route_approval_history';
    protected $fillable = ['route_id', 'approved_date', 'approved_id', 'approved_note'];


    public function approvedUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'approved_id');
    }
}