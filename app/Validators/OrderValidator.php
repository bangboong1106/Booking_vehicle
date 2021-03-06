<?php

namespace App\Validators;

use App\Repositories\DriverRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\VehicleRepository;
use App\Validators\Base\BaseValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\Rule;

class OrderValidator extends BaseValidator
{
    protected $_vehicleRepository;
    protected $_driverRepository;
    protected $_partnerRepository;

    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param mixed $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param mixed $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->_partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->_partnerRepository = $partnerRepository;
    }


    public function __construct(Factory $validator, VehicleRepository $vehicleRepository, DriverRepository $driverRepository,
                                PartnerRepository $partnerRepository)
    {
        parent::__construct($validator);
        $this->setVehicleRepository($vehicleRepository);
        $this->setDriverRepository($driverRepository);
        $this->setPartnerRepository($partnerRepository);
    }

    /**
     * @return array
     */
    protected function _getRulesDraft()
    {
        return [
            'order_code' => 'required' . $this->_getUniqueInDbRule(false, ['order_code', 'id']),
            'bill_no' => 'nullable',
            'order_no' => 'required',
        ];
    }

    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'order_code' => 'required' . $this->_getUniqueInDbRule(false, ['order_code', 'id']),
            'precedence' => 'required',
            'customer_id' => 'required',
            'partner_id' => 'required_unless:status,8',
            'vehicle_id' => 'required_if:status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:status,' . config("constant.HOAN_THANH") . '|required_if:status,' . config("constant.TAI_XE_XAC_NHAN"),
            'primary_driver_id' => 'required_if:status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:status,' . config("constant.HOAN_THANH") . '|required_if:status,' . config("constant.TAI_XE_XAC_NHAN"),
            'locationArrivals.0.time_reality' => 'after_if_ETA_date_reality:locationDestinations.0.date_reality,locationArrivals.0.date_reality,locationDestinations.0.time_reality|required_if:status,' . config('constant.HOAN_THANH'),
            'locationDestinations.0.time_reality' => 'required_if:status,' . config('constant.HOAN_THANH') . ',' . config('constant.DANG_VAN_CHUYEN'),
            // 'locationArrivals.0.date_reality' => 'required_if:status,' . config('constant.HOAN_THANH') . '|nullable|after_or_equal:locationDestinations.0.date_reality',
            'locationDestinations.0.date_reality' => 'required_if:status,' . config('constant.HOAN_THANH') . ',' . config('constant.DANG_VAN_CHUYEN'),

            'locationDestinations.0.location_id' => 'required',
            'locationArrivals.0.location_id' => 'required' ,

            'locationDestinations.0.date' => 'nullable|required',
            'locationDestinations.0.time' => 'nullable|required',
            // 'locationArrivals.0.date' => 'nullable|required|after_or_equal:locationDestinations.0.date',
            'locationArrivals.0.time' => 'after_if_ETA_date:locationDestinations.0.date,locationArrivals.0.date,locationDestinations.0.time|required',
            'number_of_delivery_points' => 'nullable|min:0',
            'number_of_arrival_points' => 'nullable|min:0',
            'amount' => 'nullable|min:0',
            'loading_destination_fee' => 'nullable||min:0',
            'loading_arrival_fee' => 'nullable|min:0',
            'commission_value' => 'nullable|min:0',
            'cod_amount' => 'nullable|min:0',
            'goods_amount' => 'nullable|min:0',
            'anonymous_amount' => 'nullable|min:0',
            'quantity' => 'nullable|min:0',
            'weight' => 'nullable|min:0',
            'volume' => 'nullable|min:0',
            'quantity_order_customer' => 'nullable|min:0',
            'weight_order_customer' => 'nullable|min:0',
            'volume_order_customer' => 'nullable|min:0',
            'contact_email_destination' => 'nullable|email',
            'contact_email_arrival' => 'nullable|email',
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'bill_no.required_unless' => trans('validation.required'),
            'order_purchasing_no.required_unless' => trans('validation.required'),
            'precedence.required_unless' => trans('validation.required'),
            'customer_id.required_unless' => trans('validation.required'),
            'customer_name.required_unless' => trans('validation.required'),
            'customer_mobile_no.required_unless' => trans('validation.required'),
            'ETD_date.required_unless' => trans('validation.required'),
            'ETD_time.required_unless' => trans('validation.required'),
            'location_destination_id.required_unless' => trans('validation.required'),
            'contact_name_destination.required_unless' => trans('validation.required'),
            'contact_mobile_no_destination.required_unless' => trans('validation.required'),
            'ETA_date.required_unless' => trans('validation.required'),
            'ETA_time.required_unless' => trans('validation.required'),
            'location_arrival_id.required_unless' => trans('validation.required'),
            'contact_name_arrival.required_unless' => trans('validation.required'),
            'contact_mobile_no_arrival.required_unless' => trans('validation.required'),
            'partner_id.required_unless' => trans('validation.required'),
            'vehicle_id.required_if' => trans('validation.required'),
            'primary_driver_id.required_if' => trans('validation.required'),
            'locationArrivals.0.time_reality.required_if' => trans('validation.required',
                ['attribute' => trans('models.order.attributes.ETA_time_reality')]),
            'locationDestinations.0.time_reality.required_if' => trans('validation.required',
                ['attribute' => trans('models.order.attributes.ETD_time_reality')]),
            // 'locationArrivals.0.date_reality.required_if' => trans('validation.required',
            //     ['attribute' => trans('models.order.attributes.ETA_date_reality')]),
            'locationDestinations.0.date_reality.required_if' => trans('validation.required',
                ['attribute' => trans('models.order.attributes.ETD_date_reality')]),

            'locationDestinations.0.location_id.required_unless' => trans('validation.required',
                ['attribute' => trans('models.order.attributes.location_destination')]),
            'locationDestinations.0.date.required_unless' => 'Ng??y nh???n h??ng l?? b???t bu???c tr??? khi tr???ng th??i ????n h??ng l?? Kh???i t???o',
            'locationDestinations.0.time.required_unless' => 'Gi??? nh???n h??ng l?? b???t bu???c tr??? khi tr???ng th??i ????n h??ng l?? Kh???i t???o',
            'locationArrivals.0.location_id.required_unless' => trans('validation.required',
                ['attribute' => trans('models.order.attributes.location_arrival')]),
            'locationArrivals.0.date.required_unless' => 'Ng??y tr??? h??ng l?? b???t bu???c tr??? khi tr???ng th??i ????n h??ng l?? Kh???i t???o',
            'locationArrivals.0.time.required_unless' => 'Gi??? tr??? h??ng l?? b???t bu???c tr??? khi tr???ng th??i ????n h??ng l?? Kh???i t???o',
            // 'locationArrivals.0.date.after_or_equal' => 'Ng??y tr??? h??ng ph???i l???n h??n ho???c b???ng Ng??y nh???n h??ng',
            'customer_id.required_unless_if' => trans('validation.required'),
            'locationArrivals.0.date_reality.after_or_equal' => 'Ng??y tr??? h??ng th???c t??? ph???i l???n h??n ho???c b???ng Ng??y nh???n h??ng th???c t???',

            'contact_email_destination.email' => trans('validation.email', ['attribute' => trans('models.order.attributes.contact_email_destination')]),
            'contact_email_arrival.email' => trans('validation.email', ['attribute' => trans('models.order.attributes.contact_email_arrival')]),
            'amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.amount')]),
            'loading_destination_fee.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.loading_destination_fee')]),
            'loading_arrival_fee.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.loading_arrival_fee')]),
            'commission_value.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.commission_value')]),
            'cod_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.cod_amount')]),
            'goods_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.goods_amount')]),
            'anonymous_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.anonymous_amount')]),
            'quantity.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.quantity')]),
            'weight.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.weight')]),
            'volume.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.volume')]),
          /*  'quantity_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.quantity_order_customer')]),
            'weight_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.weight_order_customer')]),
            'volume_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.volume_order_customer')]),*/

            '*.bill_no.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.bill_no')]),
            '*.order_purchasing_no.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.order_purchasing_no')]),
            '*.precedence.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.precedence')]),
            '*.customer_id.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.customer_id')]),
            '*.customer_code.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.customer_id')]),
            '*.customer_name.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.customer_name')]),
            '*.customer_mobile_no.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.customer_mobile_no')]),
            '*.ETD_date.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.ETD_date')]),
            '*.ETD_time.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.ETD_time')]),
            '*.location_destination_id.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.location_destination_id')]),

            '*.name_of_location_destination_code.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.name_of_location_destination_code')]),
            '*.contact_name_destination.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.contact_name_destination')]),
            '*.contact_mobile_no_destination.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.contact_mobile_no_destination')]),
            '*.ETA_date.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.ETA_date')]),
            '*.ETA_time.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.ETA_time')]),
            '*.location_arrival_id.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.location_arrival_id')]),
            '*.name_of_location_arrival_code.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.name_of_location_arrival_code')]),

            '*.contact_name_arrival.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.contact_name_arrival')]),
            '*.contact_mobile_no_arrival.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.contact_mobile_no_arrival')]),
            '*.vehicle_id.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.vehicle_id')]),
            '*.primary_driver_id.required_unless' => trans('validation.required', ['attribute' => trans('models.order.attributes.primary_driver_id')]),
            '*.order_code.distinct' => trans('validation.distinct', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.required' => trans('validation.required', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.unique' => trans('validation.unique', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.order_code')]),

            '*.goods_unit_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.goods_unit_id')]),
            '*.location_destination_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.location_destination_id')]),
            '*.location_arrival_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.location_arrival_id')]),
            '*.customer_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.customer_id')]),

            '*.partner_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.partner_id')]),
            '*.partner_id.in' => '?????i t??c v???n t???i kh??ng ???????c ph??n quy???n qu???n l??',
            '*.partner_id.required_if' => '?????i t??c v???n t???i l?? b???t bu???c khi tr???ng th??i ????n h??ng ???? ???????c ph??n c??ng v???n chuy???n',

            '*.vehicle_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.vehicle_id')]),
            '*.vehicle_id.in' => 'Xe kh??ng ???????c ph??n quy???n qu???n l??',
            '*.vehicle_id.required_if' => 'Xe l?? b???t bu???c khi tr???ng th??i ????n h??ng ???? ???????c ph??n c??ng v???n chuy???n',
            '*.primary_driver_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.primary_driver')]),
            '*.primary_driver_id.required_if' => 'T??i x??? l?? b???t bu???c khi tr???ng th??i ????n h??ng ???? ???????c ph??n c??ng v???n chuy???n',
            '*.secondary_driver_id.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.secondary_driver')]),
            '*.ETD_time.date_format' => 'Gi??? nh???n h??ng nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_time.date_format' => 'Gi??? tr??? h??ng nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETD_date.date_format' => 'Ng??y nh???n h??ng nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_date.date_format' => 'Ng??y tr??? h??ng nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_date.after_or_equal' => 'Ng??y tr??? h??ng ph???i l???n h??n ho???c b???ng Ng??y nh???n h??ng',
            '*.order_date.date_format' => 'Ng??y ?????t h??ng nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.primary_driver_id.in' => 'L??i xe kh??ng ???????c ph??n quy???n qu???n l??',
            '*.secondary_driver_id.in' => 'L??i xe kh??ng ???????c ph??n quy???n qu???n l??',

            '*.ETD_time_reality.date_format' => 'Gi??? nh???n h??ng th???c t??? nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_time_reality.date_format' => 'Gi??? tr??? h??ng th???c t??? nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETD_date_reality.date_format' => 'Ng??y nh???n h??ng th???c t??? nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_date_reality.date_format' => 'Ng??y tr??? h??ng th???c t??? nh???p sai ho???c kh??ng ????ng ?????nh d???ng',
            '*.ETA_date_reality.after_or_equal' => 'Ng??y tr??? h??ng th???c t??? ph???i l???n h??n ho???c b???ng Ng??y nh???n h??ng th???c t???',
            '*.ETA_date_reality.required_if' => 'Ng??y tr??? h??ng th???c t??? l?? b???t bu???c n???u tr???ng th??i ????n l?? Ho??n Th??nh',
            '*.ETD_date_reality.required_if' => 'Ng??y nh???n h??ng th???c t??? l?? b???t bu???c n???u tr???ng th??i ????n l?? ???? v?? ??ang v???n chuy???n',
            '*.ETA_time_reality.required_if' => 'Gi??? tr??? h??ng th???c t??? l?? b???t bu???c n???u tr???ng th??i ????n l?? Ho??n Th??nh',
            '*.ETD_time_reality.required_if' => 'Gi??? nh???n h??ng th???c t??? l?? b???t bu???c n???u tr???ng th??i ????n l?? ???? v?? ??ang v???n chuy???n',

            '*.number_of_delivery_points.min' => '??i???m nh???n h??ng kh??ng ???????c nh??? h??n 0',
            '*.number_of_arrival_points.min' => '??i???m tr??? h??ng kh??ng ???????c nh??? h??n 0',
            '*.contact_email_destination.email' => trans('validation.email', ['attribute' => trans('models.order.attributes.contact_email_destination')]),
            '*.contact_email_arrival.email' => trans('validation.email', ['attribute' => trans('models.order.attributes.contact_email_arrival')]),

            '*.amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.amount')]),
            '*.loading_destination_fee.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.loading_destination_fee')]),
            '*.loading_arrival_fee.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.loading_arrival_fee')]),
            '*.commission_value.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.commission_value')]),
            '*.cod_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.cod_amount')]),
            '*.goods_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.goods_amount')]),
            '*.anonymous_amount.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.anonymous_amount')]),
            '*.quantity.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.quantity')]),
            '*.weight.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.weight')]),
            '*.volume.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.volume')]),
            '*.quantity_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.quantity_order_customer')]),
            '*.weight_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.weight_order_customer')]),
            '*.volume_order_customer.min' => trans('validation.min', ['attribute' => trans('models.order.attributes.volume_order_customer')]),
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        $partnerList = $this->getPartnerRepository()->search([])->get()->implode('id', ',');
        $vehicleList = $this->getVehicleRepository()->getVehiclesForSelectByUserId(backendGuard()->user()->id)->implode('id', ',');
        $driverList = $this->getDriverRepository()->getAvailableDriversForUser(backendGuard()->user()->id)->implode('id', ',');

        $rules = [
            '*.order_code' => 'required|distinct' . $this->_getUniqueInDbRule(false, ['order_code', 'id']),
            /*  '*.precedence' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),*/
            // '*.customer_id' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.order_date' => 'nullable|date_format:d-m-Y',
            '*.ETD_date' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:d-m-Y,d/m/Y',
            '*.ETD_time' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:H:i',
            '*.ETA_date' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:d-m-Y,d/m/Y' . '|after_or_equal:*.ETD_date',
            '*.ETA_time' => 'nullable|after_if_ETA_date:*.ETD_date,*.ETA_date,*.ETD_time|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:H:i',
            '*.goods_unit_id' => 'nullable|exists:goods_unit,code',
            '*.name_of_location_destination_code' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.name_of_location_arrival_code' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.partner_id' => 'nullable|in:' . $partnerList . '|required_if:*.status,' . config("constant.SAN_SANG") . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.vehicle_id' => 'nullable|in:' . $vehicleList . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.primary_driver_id' => 'nullable|required_with:*vehicle|in:' . $driverList . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.secondary_driver_id' => 'nullable|in:' . $driverList,
            '*.ETD_date_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|date_format:d-m-Y,d/m/Y',
            '*.ETD_time_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|date_format:H:i',
            '*.ETA_date_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|date_format:d-m-Y,d/m/Y' . '|after_or_equal:*.ETD_date_reality',
            '*.ETA_time_reality' => 'nullable|after_if_ETA_date_reality:*.ETD_date_reality,*.ETA_date_reality,*.ETD_time_reality|required_if:*.status,' . config("constant.HOAN_THANH") . '|date_format:H:i',
            '*.number_of_delivery_points' => 'nullable|numeric|min:0',
            '*.number_of_arrival_points' => 'nullable|numeric|min:0',
            '*.amount' => 'nullable|numeric|min:0',
            '*.loading_destination_fee' => 'nullable|numeric|min:0',
            '*.loading_arrival_fee' => 'nullable|numeric|min:0',
            '*.commission_value' => 'nullable|numeric|min:0',
            '*.cod_amount' => 'nullable|numeric|min:0',
            '*.goods_amount' => 'nullable|numeric|min:0',
            '*.anonymous_amount' => 'nullable|numeric|min:0',
            '*.quantity' => 'nullable|numeric|min:0',
            '*.weight' => 'nullable|numeric|min:0',
            '*.volume' => 'nullable|numeric|min:0',
            '*.quantity_order_customer' => 'nullable|numeric|min:0',
            '*.weight_order_customer' => 'nullable|numeric|min:0',
            '*.volume_order_customer' => 'nullable|numeric|min:0',
            '*.contact_email_destination' => 'nullable|email',
            '*.contact_email_arrival' => 'nullable|email',

        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $partnerList = $this->getPartnerRepository()->search([])->get()->implode('id', ',');
        $vehicleList = $this->getVehicleRepository()->getVehiclesForSelectByUserId(backendGuard()->user()->id)->implode('id', ',');
        $driverList = $this->getDriverRepository()->getAvailableDriversForUser(backendGuard()->user()->id)->implode('id', ',');

        $rules = [
            '*.order_code' => ['required_unless:*.status,' . config("constant.KHOI_TAO"),
                Rule::exists('orders', 'order_code')->where(function ($query) {
                    $query->where('del_flag', 0);
                })
            ],
            /*  '*.precedence' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),*/
            // '*.customer_id' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.order_date' => 'nullable|date_format:d-m-Y,d/m/Y',
            '*.ETD_date' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:d-m-Y,d/m/Y',
            '*.ETD_time' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:H:i',
            '*.ETA_date' => 'nullable|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:d-m-Y,d/m/Y' . '|after_or_equal:*.ETD_date',
            '*.ETA_time' => 'nullable|after_if_ETA_date:*.ETD_date,*.ETA_date,*.ETD_time|required_unless:*.status,' . config("constant.KHOI_TAO") . '|date_format:H:i',
            '*.goods_unit_id' => 'nullable|exists:goods_unit,code',
            '*.name_of_location_destination_code' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.name_of_location_arrival_code' => 'required_unless:*.status,' . config("constant.KHOI_TAO"),
            '*.partner_id' => 'nullable|in:' . $partnerList . '|required_if:*.status,' . config("constant.SAN_SANG") . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.vehicle_id' => 'nullable|in:' . $vehicleList . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.primary_driver_id' => 'nullable|required_with:*vehicle|in:' . $driverList . '|required_if:*.status,' . config("constant.CHO_NHAN_HANG")
                . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.TAI_XE_XAC_NHAN"),
            '*.secondary_driver_id' => 'nullable|in:' . $driverList,
            '*.ETD_date_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|date_format:d-m-Y,d/m/Y',
            '*.ETD_time_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|required_if:*.status,' . config("constant.DANG_VAN_CHUYEN") . '|date_format:H:i',
            '*.ETA_date_reality' => 'nullable|required_if:*.status,' . config("constant.HOAN_THANH") . '|date_format:d-m-Y,d/m/Y' . '|after_or_equal:*.ETD_date_reality',
            '*.ETA_time_reality' => 'nullable|after_if_ETA_date_reality:*.ETD_date_reality,*.ETA_date_reality,*.ETD_time_reality|required_if:*.status,' . config("constant.HOAN_THANH") . '|date_format:H:i',
            '*.number_of_delivery_points' => 'nullable|numeric|min:0',
            '*.number_of_arrival_points' => 'nullable|numeric|min:0',
            '*.amount' => 'nullable|numeric|min:0',
            '*.loading_destination_fee' => 'nullable|numeric|min:0',
            '*.loading_arrival_fee' => 'nullable|numeric|min:0',
            '*.commission_value' => 'nullable|numeric|min:0',
            '*.cod_amount' => 'nullable|numeric|min:0',
            '*.goods_amount' => 'nullable|numeric|min:0',
            '*.anonymous_amount' => 'nullable|numeric|min:0',
            '*.quantity' => 'nullable|numeric|min:0',
            '*.weight' => 'nullable|numeric|min:0',
            '*.volume' => 'nullable|numeric|min:0',
            '*.quantity_order_customer' => 'nullable|numeric|min:0',
            '*.weight_order_customer' => 'nullable|numeric|min:0',
            '*.volume_order_customer' => 'nullable|numeric|min:0',
            '*.contact_email_destination' => 'nullable|email',
            '*.contact_email_arrival' => 'nullable|email',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }
}