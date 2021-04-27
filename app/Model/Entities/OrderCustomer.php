<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderCustomer extends ModelSoftDelete
{
    protected $table = "order_customer";

    protected $_alias = 'order_customer';
    protected $fillable = [
        'code',
        'name',
        'order_no',
        'client_id',
        'customer_id',
        'customer_name',
        'customer_mobile_no',
        'customer_email',
        'order_date',
        'ETD_date',
        'ETD_time',
        'ETA_date',
        'ETA_time',
        'ETD_date_reality',
        'ETD_time_reality',
        'ETA_date_reality',
        'ETA_time_reality',
        'location_destination_id',
        'location_arrival_id',
        'distance',
        'route_number',
        'quantity',
        'weight',
        'volume',
        'amount',
        'commission_amount', 'commission_type',
        'commission_value',
        'status',
        'source_creation',
        'is_approved',
        'payment_type',
        'payment_user_id',
        'goods_amount',
        'vat',
        'anonymous_amount',
        'order_codes',
        'count_order',
        'vin_nos',
        'model_nos',
        'is_lock',
        'client_id',
        'status_goods',
        'reason',
        'goods_detail',
        'note',
        'amount_estimate',
        'ETA_date_desired',
        'ETA_time_desired'
    ];

    public $goods;

    public function getStatus()
    {
        $statusList = config('system.order_customer_status');
        return array_key_exists($this->status, $statusList) ? $statusList[$this->status] : 'Đã xuất hàng';
    }

    public function getStatusGoods()
    {
        $statusList = config('system.order_customer_status_goods');
        return array_key_exists($this->status_goods, $statusList) ? $statusList[$this->status_goods] : 'Còn hàng';
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function locationDestination()
    {
        return $this->hasOne(Location::class, 'id', 'location_destination_id');
    }

    public function locationArrival()
    {
        return $this->hasOne(Location::class, 'id', 'location_arrival_id');
    }

    public function getCommissionType()
    {
        $commissionTypes = config('system.order_customer_commission_type');

        if (empty($this->commission_type)) {
            return '';
        }

        return array_key_exists($this->commission_type, $commissionTypes) ? $commissionTypes[$this->commission_type] : '';
    }

    public function listVehicleGroups()
    {
        return $this->belongsToMany('App\Model\Entities\VehicleGroup', 'order_customer_vehicle_group', 'order_customer_id', 'vehicle_group_id')
            ->withPivot("id", 'order_customer_id', 'vehicle_group_id', 'vehicle_number');
    }

    public function paymentUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'payment_user_id');
    }

    public function getPaymentType()
    {
        $paymentTypes = config('system.order_payment_type');

        if (empty($this->payment_type)) {
            return '';
        }

        return array_key_exists($this->payment_type, $paymentTypes) ? $paymentTypes[$this->payment_type] : '';
    }

    public function listGoods()
    {
        return $this->belongsToMany(GoodsType::class, 'order_customer_goods', 'order_customer_id', 'goods_type_id')
            ->withPivot('id', 'quantity', 'quantity_out', 'goods_unit_id', 'insured_goods', 'note', 'weight', 'volume', 'total_weight', 'total_volume')
            ->wherePivot('del_flag', '=', 0);
    }

    public function getStatusOnList()
    {
        $statuses = config('system.order_customer_status');
        $class = '';
        $title = '';

        switch ($this->status) {
            case config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG"):
                $class = 'brown';
                $title = $statuses[$this->status];
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN"):
                $class = 'blue';
                $title = $statuses[$this->status];
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH"):
                $class = 'success';
                $title = $statuses[$this->status];
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.C20_HUY"):
                $class = 'dark';
                $title = $statuses[$this->status];
                break;
        }

        return '<span class="badge badge-' . $class . '">' . $title . '</span>';
    }
}
