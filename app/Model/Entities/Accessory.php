<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Accessory extends ModelSoftDelete implements Auditable
{
    protected $table = "accessory";

    protected $fillable = [
        'name',
        'description'
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'name', 'description'
    ];
}

