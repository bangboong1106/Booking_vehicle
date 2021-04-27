<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TemplateLayout extends ModelSoftDelete
{
    protected $table = "templates_layouts";

    protected $_alias = 'templates_layout';
    protected $fillable = ['table_name', 'column_name', 'display_name', 'merge_name', 'type', 'field_type', 'data_type'];

    public function getType()
    {
        return config('system.template_type.' . $this->type);
    }

}