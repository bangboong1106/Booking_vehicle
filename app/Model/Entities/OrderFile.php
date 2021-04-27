<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderFile extends ModelSoftDelete
{
    protected $table = "order_file";
    protected $fillable = ['order_id', 'order_status', 'file_id', 'reason', 'note', 'expire_date', 'register_date'];

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}