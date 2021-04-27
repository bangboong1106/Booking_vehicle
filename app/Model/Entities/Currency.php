<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class Currency extends ModelSoftDelete
{
    protected $table = "currency";
    protected $_alias = 'currency';

    protected $fillable = ['currency_code', 'currency_name'];

    protected static $_destroyRelations = [];
    protected $_detailNameField = 'currency_code';

}