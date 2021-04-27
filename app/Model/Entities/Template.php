<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class Template extends ModelSoftDelete
{
    protected $table = "templates";

    protected $_alias = 'template';
    protected $fillable = [
        'title', 'type', 'file_id', 'description', 'ins_id', 'upd_id', 'export_type',
        'ins_date', 'upd_date', 'del_flag', 'is_print_empty_cost', 'is_print_empty_goods','list_item','partner_id'
    ];

    public function getType()
    {
        return config('system.template_type.' . $this->type);
    }

    public function getFile()
    {
        return $this->hasOne(File::class, 'file_id', 'file_id');
    }

    public function getExportType()
    {
        return config('system.template_export_type.' . $this->export_type);
    }
}
