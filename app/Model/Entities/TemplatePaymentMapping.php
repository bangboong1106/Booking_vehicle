<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TemplatePaymentMapping extends ModelSoftDelete
{
    protected $table = "template_payment_mapping";

    protected $_alias = 'template_payment_mapping';
    protected $fillable = [
        'template_payment_id', 'receipt_payment_id', 'column_index', 'ins_id', 'upd_id', 'export_type',
        'ins_date', 'upd_date', 'del_flag'
    ];
}
