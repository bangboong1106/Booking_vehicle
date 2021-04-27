<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TemplatePayment extends ModelSoftDelete
{
    protected $table = "template_payment";

    protected $_alias = 'template_payment';
    protected $fillable = [
        'title', 'file_id', 'description', 'matching_column_index', 'header_row_index'
    ];

    public function getFile()
    {
        return $this->hasOne(File::class, 'file_id', 'file_id');
    }

    public function templatePaymentMappings()
    {
        return $this->hasMany(TemplatePaymentMapping::class, 'template_payment_id', 'id');
    }
}
