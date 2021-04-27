<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class RouteFile extends ModelSoftDelete
{
    protected $table = "route_file";
    protected $fillable = ['route_id', 'file_id', 'note', 'type', 'cost_id'];

}