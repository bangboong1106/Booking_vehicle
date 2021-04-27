<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PayrollFormula extends ModelSoftDelete
{
    protected $table = "payroll_formula";

    protected $_alias = 'payroll_formula';
    protected $fillable = ['payroll_id', 'location_group_destination_id', 'location_group_arrival_id'
        , 'vehicle_group_id', 'price'];


}