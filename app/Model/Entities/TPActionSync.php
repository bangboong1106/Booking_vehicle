<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TPActionSync extends ModelSoftDelete
{
    protected $table = "3p_action_sync";

    protected $_alias = '3p_api_info';
    protected $fillable = ['partner_name', 'order_id', 'order_code', 'order_no', 'bill_no', 'status', 'etd_reality', 'ETD_date_reality', 'ETD_time_reality'
        , 'ETA_date_reality', 'ETA_time_reality', 'amount', 'note', 'request_description', 'response_code', 'response_description', 'sended'];

}