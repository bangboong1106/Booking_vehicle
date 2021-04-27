<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed order_code
 * @property mixed order_no
 * @property mixed bill_no
 * @property mixed customer_name
 * @property mixed customer_mobile_no
 * @property mixed amount
 * @property mixed quantity
 * @property mixed volume
 * @property mixed weight
 * @property mixed contact_name_destination
 * @property mixed contact_mobile_no_destination
 * @property mixed contact_email_destination
 * @property mixed loading_destination_fee
 * @property mixed contact_name_arrival
 * @property mixed contact_mobile_no_arrival
 * @property mixed contact_email_arrival
 * @property mixed loading_arrival_fee
 * @property mixed note
 * @property mixed listGoods
 * @property mixed listLocations
 * @property int|string location_destination_id
 * @property false|null|string ETD_date
 * @property null ETD_time
 * @property mixed status
 * @property false|null|string ETD_date_reality
 * @property null ETD_time_reality
 * @property mixed location_arrival_id
 * @property false|null|string ETA_date
 * @property null ETA_time
 * @property false|null|string ETA_date_reality
 * @property null ETA_time_reality
 * @property mixed location_title_destination
 * @property mixed full_address_destination
 * @property mixed status_collected_documents
 * @property mixed commission_type
 * @property mixed commission_value
 * @property mixed|null time_collected_documents
 */
class Order extends ModelSoftDelete implements Auditable
{
    protected $table = "orders";

    protected $_alias = 'order';
    protected $fillable = [
        'order_no', 'status', 'customer_id', 'customer_name', 'customer_mobile_no',
        'order_code', 'order_purchasing_no', 'bill_no', 'order_date', 'contract_no',
        'ETD_date', 'ETD_time', 'ETD_date_reality', 'ETD_time_reality', 'location_destination_id', 'contact_name_destination',
        'contact_mobile_no_destination', 'contact_email_destination', 'informative_destination',
        'ETA_date', 'ETA_time', 'ETA_date_reality', 'ETA_time_reality', 'location_arrival_id', 'contact_name_arrival',
        'contact_mobile_no_arrival', 'contact_email_arrival', 'informative_arrival',
        'amount', 'quantity', 'volume', 'weight', 'note', 'description', 'remark', 'precedence',
        'draft', 'good_details', 'order_review_id', 'is_insured_goods',
        'loading_destination', 'loading_arrival', 'loading_destination_fee', 'loading_arrival_fee', 'currency_id', 'ins_id', 'upd_id',
        'upd_date', 'del_flag',
        'document_type', 'document_note',
        'is_collected_documents', 'status_collected_documents', 'date_collected_documents', 'time_collected_documents', 'commission_amount', 'commission_type', 'commission_value',
        'time_collected_documents', 'date_collected_documents_reality', 'time_collected_documents_reality', 'num_of_document_page', 'cod_amount', 'cod_currency_id',
        'bill_print_url',
        'gps_distance',
        'is_merge_item', 'vin_no', 'model_no',
        'vehicle_id', 'primary_driver_id', 'secondary_driver_id', 'route_id', 'order_customer_id', 'source_create',
        'quantity_order_customer', 'volume_order_customer', 'weight_order_customer',
        'number_of_delivery_points', 'number_of_arrival_points',
        'is_lock',
        'partner_id', 'client_id', 'status_partner', 'reason'
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'order_no', 'status', 'customer_id', 'customer_name', 'customer_mobile_no',
        'order_code', 'order_purchasing_no', 'bill_no', 'order_date', 'contract_no',
        'ETD_date', 'ETD_time', 'ETD_date_reality', 'ETD_time_reality', 'location_destination_id', 'contact_name_destination',
        'contact_mobile_no_destination', 'contact_email_destination', 'informative_destination',
        'ETA_date', 'ETA_time', 'ETA_date_reality', 'ETA_time_reality', 'location_arrival_id', 'contact_name_arrival',
        'contact_mobile_no_arrival', 'contact_email_arrival', 'informative_arrival',
        'amount', 'quantity', 'volume', 'weight', 'note', 'description', 'remark', 'precedence',
        'good_details', 'order_review_id', 'is_insured_goods',
        'loading_destination', 'loading_arrival', 'loading_destination_fee', 'loading_arrival_fee', 'currency_id', 'ins_id', 'upd_id',
        'document_type', 'document_note',
        'is_collected_documents', 'status_collected_documents', 'date_collected_documents', 'time_collected_documents', 'commission_amount', 'commission_type', 'commission_value',
        'time_collected_documents', 'date_collected_documents_reality', 'time_collected_documents_reality', 'num_of_document_page', 'cod_amount', 'cod_currency_id',
        'bill_print_url',
        'gps_distance',
        'is_merge_item', 'vin_no', 'model_no',
        'vehicle_id', 'primary_driver_id', 'secondary_driver_id', 'route_id', 'order_customer_id', 'source_create',
        'quantity_order_customer', 'volume_order_customer', 'weight_order_customer',
        'number_of_delivery_points', 'number_of_arrival_points',
        'is_lock',
        'partner_id', 'client_id', 'status_partner', 'reason'

    ];
    protected $_detailNameField = 'order_code';
    public $goods;
    public $locationDestinations;
    public $locationArrivals;
    public $orderCustomerReview;

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function orderReviewFromCustomer()
    {
        return $this->hasOne(OrderCustomerReview::class, 'id', 'order_review_id');
    }

    public function locationDestination()
    {
        return $this->hasOne(Location::class, 'id', 'location_destination_id');
    }

    public function locationArrival()
    {
        return $this->hasOne(Location::class, 'id', 'location_arrival_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function primaryDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'primary_driver_id');
    }

    public function secondaryDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'secondary_driver_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }

    public function orderCustomer()
    {
        return $this->hasOne(OrderCustomer::class, 'id', 'order_customer_id');
    }

    public function listGoods()
    {
        return $this->belongsToMany(GoodsType::class, 'order_goods', 'order_id', 'goods_type_id')
            ->withPivot('quantity', 'goods_unit_id', 'insured_goods', 'note', 'weight', 'volume', 'total_weight', 'total_volume', 'id')
            ->wherePivot('del_flag', '=', 0);
    }

    public function listLocationDestinations()
    {
        return $this->belongsToMany(Location::class, 'order_locations', 'order_id', 'location_id')
            ->withPivot(
            // 'code_of_location_arrival_code',
            // 'locations.title as name_of_location_arrival_code',
                'type',
                'date',
                'date_reality',
                'time',
                'time_reality',
                'note'
            )
            ->wherePivot('type', '=', config('constant.DESTINATION'))
            ->wherePivot('del_flag', '=', 0);
    }

    public function listLocationArrivals()
    {
        return $this->belongsToMany('App\Model\Entities\Location', 'order_locations', 'order_id', 'location_id')
            ->withPivot(
            // 'locations.code as code_of_location_arrival_code',
            // 'locations.title as name_of_location_arrival_code',
                'type',
                'date',
                'date_reality',
                'time',
                'time_reality',
                'note'
            )
            ->wherePivot('type', '=', config('constant.ARRIVAL'))
            ->wherePivot('del_flag', '=', 0);
    }

    public function listLocations()
    {
        return $this->belongsToMany('App\Model\Entities\Location', 'order_locations', 'order_id', 'location_id')
            ->withPivot('type', 'date', 'date_reality', 'time', 'time_reality', 'note');
    }

    public function locations()
    {
        return $this->hasMany('App\Model\Entities\Location', 'order_locations', 'order_id', 'location_id')
            ->withPivot('type', 'date', 'date_reality', 'time', 'time_reality', 'note');
    }

    public function orderPayment()
    {
        return $this->hasOne(OrderPayment::class, 'order_id', 'id');
    }

    public function getPrecedence()
    {
        $precedences = config('system.order_precedences');

        if (empty($this->precedence)) {
            return '';
        }

        return array_key_exists($this->precedence, $precedences) ? $precedences[$this->precedence] : '';
    }

    public function getStatus()
    {
        $statuses = config('system.order_status');
        $status_partners = config('system.order_status_partner');


        if ($this->status == config("constant.KHOI_TAO")) {
            $title = isset($status_partners[$this->status_partner]) ? $status_partners[$this->status_partner] : '';
        } else {
            $title = $statuses[$this->status];
        }

        return $title;
    }

    public function getStatusDocuments()
    {
        $status = config('system.collected_documents_status');

        if (empty($this->status_collected_documents)) {
            return $status[1];
        }

        return array_key_exists($this->status_collected_documents, $status) ? $status[$this->status_collected_documents] : $status[1];
    }

    public function getStatusDocumentsOnList()
    {
        switch ($this->status_collected_documents) {
            case config("constant.CHUA_THU_DU"):
                $class = 'brown';
                break;
            case config("constant.DA_THU_DU"):
                $class = 'success';
                break;
            case config("constant.QUA_HAN"):
                $class = 'danger';
                break;
            case config("constant.DEN_HAN_VAO_HOM_SAU"):
                $class = 'dark';
                break;
            case config("constant.DEN_HAN_VAO_HOM_NAY"):
                $class = 'light';
                break;
            default:
                $class = 'brown';
                break;
        }

        return '<span class="badge badge-' . $class . '">' .
            ($this->getStatusDocuments()) .
            '</span>';
    }

    public function getCommissionType()
    {
        $commissionTypes = config('system.order_commission_type');

        if (empty($this->commission_type)) {
            return '';
        }

        return array_key_exists($this->commission_type, $commissionTypes) ? $commissionTypes[$this->commission_type] : '';
    }

    public function getPrecedenceLabel()
    {
        if (empty($this->precedence)) {
            return '';
        }

        switch ($this->precedence) {
            case 2:
                $class = 'bg-danger';
                break;
            case 3:
                $class = 'bg-warning';
                break;
            case 4:
            default:
                $class = 'bg-info';
                break;
            case 5:
                $class = 'bg-secondary';
                break;
        }

        return $class;
    }

    public function getPrecedenceOnList()
    {
        if (empty($this->precedence)) {
            return '';
        }
        $class = '';
        switch ($this->precedence) {
            case config('constant.ORDER_PRECEDENCE_SPECIAL'):
                $class = '<span class="fa fa-star text-warning"></span>
                <span class="fa fa-star text-warning"></span>
                <span class="fa fa-star text-warning"></span>';
                break;
            case config('constant.ORDER_PRECEDENCE_NORMAL'):
                $class = '<span class="fa fa-star text-warning"></span>
                <span class="fa fa-star text-warning"></span>';
                break;
            case config('constant.ORDER_PRECEDENCE_LOW'):
                $class = '<span class="fa fa-star text-warning"></span>';
                break;
        }

        return $class;
    }

    public function getStatusOnList()
    {
        $statuses = config('system.order_status');
        $status_partners = config('system.order_status_partner');

        switch ($this->status) {
            case config("constant.CHO_NHAN_HANG"):
                $class = 'brown';
                $title = $statuses[$this->status];
                break;
            case config("constant.DANG_VAN_CHUYEN"):
                $class = 'blue';
                $title = $statuses[$this->status];
                break;
            case config("constant.HOAN_THANH"):
                $class = 'success';
                $title = $statuses[$this->status];
                break;
            case config("constant.HUY"):
                $class = 'dark';
                $title = $statuses[$this->status];
                break;
            case config("constant.TAI_XE_XAC_NHAN"):
                $class = 'stpink';
                $title = $statuses[$this->status];
                break;
            case config("constant.SAN_SANG"):
                $class = 'secondary';
                $title = $statuses[$this->status];
                break;
            case config("constant.KHOI_TAO"):
                $class = 'light';
                $title = isset($status_partners[$this->status_partner]) ? $status_partners[$this->status_partner] : '';
                break;
        }

        return '<span class="badge badge-' . $class . '">' . $title . '</span>';
    }

    public function getPartnerStatusOnList()
    {
        $statuses = config('system.order_status');
        $status_partners = config('system.order_status_partner');

        switch ($this->status) {
            case config("constant.CHO_NHAN_HANG"):
                $class = 'brown';
                $title = $statuses[$this->status];
                break;
            case config("constant.DANG_VAN_CHUYEN"):
                $class = 'blue';
                $title = $statuses[$this->status];
                break;
            case config("constant.HOAN_THANH"):
                $class = 'success';
                $title = $statuses[$this->status];
                break;
            case config("constant.HUY"):
                $class = 'dark';
                $title = $statuses[$this->status];
                break;
            case config("constant.TAI_XE_XAC_NHAN"):
                $class = 'stpink';
                $title = $statuses[$this->status];
                break;
            case config("constant.SAN_SANG"):
                $class = 'secondary';
                $title = $statuses[$this->status];
                break;
            case config("constant.KHOI_TAO"):
                $class = 'light';
                $title = isset($status_partners[$this->status_partner]) ? $status_partners[$this->status_partner] : '';
                break;
        }

        return '<span class="badge badge-' . $class . '">' . $title . '</span>';
    }

    public function generateStatus($status, $code)
    {
        if ($status == null) {
            return '<span class="tag-order">' .
                ($code) .
                '</span>';
        }
        $statuses = config('system.order_status');
        switch ($status) {
            case config("constant.CHO_NHAN_HANG"):
                $class = 'brown';
                break;
            case config("constant.DANG_VAN_CHUYEN"):
                $class = 'blue';
                break;
            case config("constant.HOAN_THANH"):
                $class = 'success';
                break;
            case config("constant.HUY"):
                $class = 'dark';
                break;
            case config("constant.KHOI_TAO"):
                $class = 'light';
                break;
            case config("constant.TAI_XE_XAC_NHAN"):
                $class = 'stpink';
                break;
            default:
                $class = 'secondary';
                break;
        }

        return '<span class="badge badge-' . $class . '" data-toggle="tooltip" data-placement="top" title=""
                    data-original-title="' . (array_key_exists($status, $statuses) ? $statuses[$status] : '') . '">' .
            ($code) .
            '</span>';
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = str_replace(',', '', $value);
    }

    public function setLoadingDestinationFeeAttribute($value)
    {
        $this->attributes['loading_destination_fee'] = str_replace(',', '', $value);
    }

    public function setLoadingArrivalFeeAttribute($value)
    {
        $this->attributes['loading_arrival_fee'] = str_replace(',', '', $value);
    }

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = str_replace(',', '', $value);
    }

    public function setVolumeAttribute($value)
    {
        $this->attributes['volume'] = str_replace(',', '', $value);
    }

    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = str_replace(',', '', $value);
    }

    public function generateTags(): array
    {
        $data = $this->getExtendData();
        return [
            'route_id' => empty($data['route_id']) || $data['route_id'] == $data['current_route_id'] ? '' : Routes::find($data['route_id'])->name,
            'current_route_id' => empty($data['current_route_id']) || $data['route_id'] == $data['current_route_id'] ? '' : Routes::find($data['current_route_id'])->name,
            'vehicle_id' => empty($data['vehicle_id']) || $data['vehicle_id'] == $data['current_vehicle_id'] ? '' : Vehicle::find($data['vehicle_id'])->reg_no,
            'current_vehicle_id' => empty($data['current_vehicle_id']) || $data['vehicle_id'] == $data['current_vehicle_id'] ? '' : Vehicle::find($data['current_vehicle_id'])->reg_no,
            'primary_driver_id' => empty($data['primary_driver_id']) || $data['primary_driver_id'] == $data['current_primary_driver_id'] ? '' : Driver::find($data['primary_driver_id'])->full_name,
            'current_primary_driver_id' => empty($data['current_primary_driver_id']) || $data['primary_driver_id'] == $data['current_primary_driver_id'] ? '' : Driver::find($data['current_primary_driver_id'])->full_name,
            'secondary_driver_id' => empty($data['secondary_driver_id']) || $data['secondary_driver_id'] == $data['current_secondary_driver_id'] ? '' : Driver::find($data['secondary_driver_id'])->full_name,
            'current_secondary_driver_id' => empty($data['current_secondary_driver_id']) || $data['secondary_driver_id'] == $data['current_secondary_driver_id'] ? '' : Driver::find($data['current_secondary_driver_id'])->full_name,
        ];
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.status')) {
                $data['old_values']['status'] = empty($data['old_values']['status']) ? '' : config('system.order_status.' . $data['old_values']['status']);
                $data['new_values']['status'] = empty($data['new_values']['status']) ? '' : config('system.order_status.' . $data['new_values']['status']);
            }

            if (Arr::has($data, 'new_values.customer_id')) {
                $oldCustomer = Customer::find($this->getOriginal('customer_id'));
                $data['old_values']['customer_id'] = empty($oldCustomer) ? '' : $oldCustomer->full_name;
                $data['new_values']['customer_id'] = empty($data['new_values']['customer_id']) ? '' : Customer::find($this->getAttribute('customer_id'))->full_name;
            }

            if (Arr::has($data, 'new_values.location_destination_id')) {
                $loDes = Location::find($this->getOriginal('location_destination_id'));
                $data['old_values']['location_destination_id'] = empty($data['old_values']['location_destination_id']) ? '' : (isset($loDes) ? $loDes->title : '');
                $lo = Location::find($this->getAttribute('location_destination_id'));
                if (isset($lo) && !empty($lo)) {
                    $data['new_values']['location_destination_id'] = empty($data['new_values']['location_destination_id']) ? '' : $lo->title;
                } else {
                    if (strpos($this->getAttribute('location_destination_id'), 'id') === 0) {
                        $data['new_values']['location_destination_id'] = substr_replace($this->getAttribute('location_destination_id'), '', 0, strlen('id'));
                    } else {
                        $data['new_values']['location_destination_id'] = '';
                    }
                }
            }

            if (Arr::has($data, 'new_values.location_arrival_id')) {
                $loDes = Location::find($this->getOriginal('location_arrival_id'));
                $data['old_values']['location_arrival_id'] = empty($data['old_values']['location_arrival_id']) ? '' : (isset($loDes) ? $loDes->title : '');
                $lo = Location::find($this->getAttribute('location_arrival_id'));
                if (isset($lo) && !empty($lo)) {
                    $data['new_values']['location_arrival_id'] = empty($data['new_values']['location_arrival_id']) ? '' : $lo->title;
                } else {
                    if (strpos($this->getAttribute('location_arrival_id'), 'id') === 0) {
                        $data['new_values']['location_arrival_id'] = substr_replace($this->getAttribute('location_arrival_id'), '', 0, strlen('id'));
                    } else {
                        $data['new_values']['location_arrival_id'] = '';
                    }
                }
            }

            if (Arr::has($data, 'new_values.currency_id')) {
                $data['old_values']['currency_id'] = empty($data['old_values']['currency_id']) ? '' : Currency::find($this->getOriginal('currency_id'))->currency_name;
                $data['new_values']['currency_id'] = empty($data['new_values']['currency_id']) ? '' : Currency::find($this->getAttribute('currency_id'))->currency_name;
            }

            if (Arr::has($data, 'new_values.precedence')) {
                $data['old_values']['precedence'] = empty($data['old_values']['precedence']) ? '' : config('system.order_precedences.' . $data['old_values']['precedence']);
                $data['new_values']['precedence'] = empty($data['new_values']['precedence']) ? '' : config('system.order_precedences.' . $data['new_values']['precedence']);
            }

            if (Arr::has($data, 'new_values.ins_id')) {
                $data['new_values']['ins_id'] = backendGuard()->user()->full_name;
            }

            $data = $this->checkFloatValue($data, 'loading_arrival_fee');
            $data = $this->checkFloatValue($data, 'loading_destination_fee');
            $data = $this->checkFloatValue($data, 'amount');
            $data = $this->checkFloatValue($data, 'quantity');
            $data = $this->checkFloatValue($data, 'volume');
            $data = $this->checkFloatValue($data, 'weight');
            $data = $this->checkTimeValue($data, 'ETA_time');
            $data = $this->checkTimeValue($data, 'ETD_time');
            $data = $this->checkTimeValue($data, 'ETA_time_reality');
            $data = $this->checkTimeValue($data, 'ETD_time_reality');

            if (isset($data['tags'])) {
                $extendData = explode(',', $data['tags']);

                if (!empty($extendData[0]) || !empty($extendData[1])) {
                    $data['new_values']['choose-route'] = $extendData[0];
                    $data['old_values']['choose-route'] = $extendData[1];
                }
                if (!empty($extendData[2]) || !empty($extendData[3])) {
                    $data['new_values']['vehicle_id'] = $extendData[2];
                    $data['old_values']['vehicle_id'] = $extendData[3];
                }
                if (!empty($extendData[4]) || !empty($extendData[5])) {
                    $data['new_values']['primary_driver_id'] = $extendData[4];
                    $data['old_values']['primary_driver_id'] = $extendData[5];
                }
                if (!empty($extendData[6]) || !empty($extendData[7])) {
                    $data['new_values']['secondary_driver'] = $extendData[6];
                    $data['old_values']['secondary_driver'] = $extendData[7];
                }
            }

        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }
}
