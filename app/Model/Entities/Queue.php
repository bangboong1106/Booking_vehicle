<?php

namespace App\Model\Entities;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = "queue";
    protected $fillable = ['id', 'event', 'data', 'config', 'attempts','error_description'];
}