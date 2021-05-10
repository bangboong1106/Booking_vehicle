<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TypeShip extends ModelSoftDelete
{
    protected $table = "type_ships";

    protected $_alias = 'type_ships';
    protected $fillable = ['title', 'descriptions', 'amount','del_flag'];


}