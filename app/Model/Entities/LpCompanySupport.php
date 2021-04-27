<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class LpCompanySupport extends ModelSoftDelete
{
    protected $table = 'lp_company_supports';
    protected $_alias = 'lp_company_support';

    protected $fillable = ['company_name', 'full_name', 'phone', 'email', 'type', 'remark', 'status'];
    protected $_detailNameField = 'company_name';
}