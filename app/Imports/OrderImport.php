<?php

namespace App\Imports;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Repositories\DistrictRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\WardRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class OrderImport extends BaseImport
{
    protected $_locationRepository;
    protected $_provinceRepository;
    protected $_districtRepository;
    protected $_wardRepository;
    protected $_vehicleRepository;
    protected $_vehicleDriverList;
    protected $_driverList;
    protected $_customer;
    protected $_locationId;
    protected $_locationCode;
    protected $_excelColumnConfigMap;
    protected $_fromEditor;

    public function __construct(
        LocationRepository $locationRepository,
        ProvinceRepository $provinceRepository,
        DistrictRepository $districtRepository,
        WardRepository $wardRepository,
        VehicleRepository $vehicleRepository,
        $vehicleDriverList,
        $driverList,
        $customerList,
        $locationList,
        $excelColumnConfigMap,
        $fromEditor
    )
    {
        $this->_locationRepository = $locationRepository;
        $this->_provinceRepository = $provinceRepository;
        $this->_districtRepository = $districtRepository;
        $this->_wardRepository = $wardRepository;
        $this->_vehicleRepository = $vehicleRepository;
        if (!empty($vehicleDriverList)) {
            foreach ($vehicleDriverList as $item) {
                $this->_vehicleDriverList[$item->reg_no] = $item;
            }
        }

        if (!empty($driverList)) {
            foreach ($driverList as $item) {
                $this->_driverList[$item->code] = $item;
            }
        }

        if (!empty($customerList)) {
            foreach ($customerList as $customer) {
                $this->_customer[$customer->customer_code] = $customer;
            }
        }

        if (!empty($locationList)) {
            foreach ($locationList as $location) {
                $this->_locationId[$location->id] = $location;
                $this->_locationCode[$location->code] = $location;
            }
        }

        $this->_excelColumnConfigMap = $excelColumnConfigMap;
        $this->_fromEditor = $fromEditor;
    }

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $result = parent::map($row, $excelColumnConfig, $dataEx);

        if ($this->_fromEditor) {
            //Convert field name
            foreach ($result as $key => $value) {
                if (array_key_exists($key, $this->_excelColumnConfigMap) && !empty($this->_excelColumnConfigMap[$key])) {
                    $result[$this->_excelColumnConfigMap[$key]] = $value;
                    unset($row[$key]);
                }
            }
        }

        //Lấy tài xế mặc định của xe nếu ko nhập
        $regNo = isset($result['vehicle']) ? trim($result['vehicle']) : '';
        $primaryDriverCode = isset($result['primary_driver']) ? trim($result['primary_driver']) : '';
        $secondaryDriverCode = isset($result['secondary_driver']) ? trim($result['secondary_driver']) : '';
        if (!empty($regNo)) {
            if (isset($this->_vehicleDriverList[$regNo])) {
                $item = $this->_vehicleDriverList[$regNo];
                $result['vehicle_id'] = $item->vehicle_id;

                if (empty($primaryDriverCode)) {
                    $result['primary_driver'] = $this->getCode($item->driver_name);
                    $result['primary_driver_id'] = $item->driver_id;
                } else {
                    if (isset($this->_driverList[$primaryDriverCode])) {
                        $result['primary_driver_id'] = $this->_driverList[$primaryDriverCode]->id;
                    }
                }

                if (!empty($secondaryDriverCode) && isset($this->_driverList[$secondaryDriverCode])) {
                    $result['secondary_driver_id'] = $this->_driverList[$primaryDriverCode]->id;
                }
            }
        }

        //Lấy người đại diện, SDT khách hàng mặc định nếu ko nhập
        $customerCode = isset($result['customer_code']) ? $result['customer_code'] : '';
        $customerName = isset($result['customer_name']) ? $result['customer_name'] : '';
        $customerPhone = isset($result['customer_mobile_no']) ? $result['customer_mobile_no'] : '';
        if (!empty($customerCode) && isset($this->_customer[$customerCode])) {
            $customer = $this->_customer[$customerCode];
            if (empty($customerName)) {
                $result['customer_name'] = $customer->delegate;
            }
            if (empty($customerPhone)) {
                $result['customer_mobile_no'] = $customer->mobile_no;
            }
            $result['customer_id'] = $customer->id;
        }

        //Lấy thông tin địa điểm
        $locationDestination = null;
        if (isset($result['location_destination_id'])) {
            $locationDestination = isset($this->_locationId[$result['location_destination_id']])
                ? $this->_locationId[$result['location_destination_id']] : null;
        } else if (!empty($result['location_destination_code'])) {
            $locationDestination = isset($this->_locationCode[$result['location_destination_code']])
                ? $this->_locationCode[$result['location_destination_code']] : null;
        }
        if ($locationDestination) {
            $result['location_destination_id'] = $locationDestination->id;
            $result['location_destination_code'] = $locationDestination->code;
            $result['location_destination_title'] = $locationDestination->title;
        }

        $locationArrival = null;
        if (isset($result['location_arrival_id'])) {
            $locationArrival = isset($this->_locationId[$result['location_arrival_id']])
                ? $this->_locationId[$result['location_arrival_id']] : null;
        } else if (!empty($result['location_arrival_code'])) {
            $locationArrival = isset($this->_locationCode[$result['location_arrival_code']])
                ? $this->_locationCode[$result['location_arrival_code']] : null;
        }
        if ($locationArrival) {
            $result['location_arrival_id'] = $locationArrival->id;
            $result['location_arrival_code'] = $locationArrival->code;
            $result['location_arrival_title'] = $locationArrival->title;
            $result['location_arrival_limited_day'] = $locationArrival->limited_day;
        }

        $result['order_date'] = empty($result['order_date']) ? date('d-m-Y') : $result['order_date'];
        $result['customer_id'] = isset($result['customer_id']) && $result['customer_id'] != "0" ? $result['customer_id'] : null;
        $result['vehicle_id'] = isset($result['vehicle_id']) && $result['vehicle_id'] != "0" ? $result['vehicle_id'] : null;
        $result['primary_driver_id'] = isset($result['primary_driver_id']) && $result['primary_driver_id'] != "0" ? $result['primary_driver_id'] : null;
        $result['secondary_driver_id'] = isset($result['secondary_driver_id']) && $result['secondary_driver_id'] != "0" ? $result['secondary_driver_id'] : null;
        $result['location_arrival_id'] = isset($result['location_arrival_id']) ? $result['location_arrival_id'] : null;
        $result['location_destination_id'] = isset($result['location_destination_id']) ? $result['location_destination_id'] : null;

        return $result;
    }


    public function mapGoods($rowCode, $row): array
    {
        $row = array_values($row);

        if (empty(trim($row[0]))) {
            $this->indexRow++;
            return [];
        }
        $result = array();
        for ($i = 0; $i < count($rowCode); $i++) {
            $result[$rowCode[$i]] = isset($row[$i + 1]) ? $this->importNumber(trim($row[$i + 1])) : 0;
        }
        return [trim($row[0]), $result];
    }

    public function mapRowGoodsCode($row): array
    {
        $row = array_values($row);

        $result = array();
        for ($i = 1; $i < 100; $i++) {
            if (!empty($row[$i]))
                $result[] = $row[$i];
        }
        return $result;
    }


    public function model(array $row)
    {
        return new Order($row);
    }

    public function convertPrecedence($precedence)
    {
        if (empty($precedence)) {
            return config('constant.ORDER_PRECEDENCE_NORMAL');
        }
        $text = mb_strtoupper(Str::slug($precedence, ' '));
        if ($text == 'DAC BIET') {
            return config('constant.ORDER_PRECEDENCE_SPECIAL');
        }
        if ($text == 'THAP') {
            return config('constant.ORDER_PRECEDENCE_LOW');
        }

        return config('constant.ORDER_PRECEDENCE_NORMAL');
    }

    public function convertStatus($status)
    {
        if (empty($status)) {
            return config('constant.KHOI_TAO');
        }
        $text = mb_strtoupper(Str::slug($status, ' '));
        if ($text == 'SAN SANG') {
            return config('constant.SAN_SANG');
        }
        if ($text == 'CHO TAI XE XAC NHAN') {
            return config('constant.TAI_XE_XAC_NHAN');
        }
        if ($text == 'CHO NHAN HANG') {
            return config('constant.CHO_NHAN_HANG');
        }
        if ($text == 'DANG VAN CHUYEN') {
            return config('constant.DANG_VAN_CHUYEN');
        }
        if ($text == 'HOAN THANH') {
            return config('constant.HOAN_THANH');
        }
        if ($text == 'HUY') {
            return config('constant.HUY');
        }

        return config('constant.KHOI_TAO');
    }

    public function convertStatusCollectedDocuments($isCollected)
    {
        if (empty($isCollected)) {
            return config('constant.CHUA_THU_DU');
        }
        $text = mb_strtoupper(Str::slug($isCollected));
        if ($text == 'DA-DU') {
            return config('constant.DA_THU_DU');
        }
        return config('constant.CHUA_THU_DU');
    }

    public function convertCommissionType($commissionType)
    {
        if (empty($commissionType)) {
            return config('constant.TONG_TIEN_HOA_HONG');
        }
        $text = mb_strtoupper(Str::slug($commissionType));
        if ($text == 'PHAN-TRAM') {
            return config('constant.PHAN_TRAM_HOA_HONG');
        }
        return config('constant.TONG_TIEN_HOA_HONG');
    }

    public function convertPaymentType($paymentType)
    {
        if (empty($paymentType)) {
            return config('constant.CHUYEN_KHOAN');
        }
        $text = mb_strtoupper(Str::slug($paymentType));
        if ($text == 'TIEN-MAT') {
            return config('constant.TIEN_MAT');
        }
        return config('constant.CHUYEN_KHOAN');
    }

    public function headingRow(): int
    {
        return 10;
    }

    public function processLocationImport($data)
    {
        $data_locations = array();
        foreach ($data as $key => &$row) {
            if (isset($row['order_code'])) {
                if (isset($data_locations[$row['order_code']])) {
                    $data_locations[$row['order_code']]['order_locations'][] = [
                        'location_destination_code' => $row['name_of_location_destination_code'],
                        'location_destination_id' => isset($row['location_destination_id']) ? $row['location_destination_id'] : null,
                        'location_destination_title' => isset($row['location_destination_title']) ? $row['location_destination_title'] : null,
                        'ETD_time' => $row['ETD_time'],
                        'ETD_date' => $row['ETD_date'],
                        'ETD_time_reality' => array_key_exists('ETD_time_reality', $row) ? $row['ETD_time_reality'] : null,
                        'ETD_date_reality' => array_key_exists('ETD_date_reality', $row) ? $row['ETD_date_reality'] : null,
                        'informative_destination' => array_key_exists('informative_destination', $row) ? $row['informative_destination'] : null,
                        'location_arrival_code' => $row['location_arrival_code'],
                        'location_arrival_id' => isset($row['location_arrival_id']) ? $row['location_arrival_id'] : null,
                        'location_arrival_title' => isset($row['location_arrival_title']) ? $row['location_arrival_title'] : null,
                        'ETA_time' => $row['ETA_time'],
                        'ETA_date' => $row['ETA_date'],
                        'ETA_time_reality' => array_key_exists('ETA_time_reality', $row) ? $row['ETA_time_reality'] : null,
                        'ETA_date_reality' => array_key_exists('ETA_date_reality', $row) ? $row['ETA_date_reality'] : null,
                        'informative_arrival' => array_key_exists('informative_arrival', $row) ? $row['informative_arrival'] : null,
                        'location_arrival_limited_day' => isset($row['location_arrival_limited_day']) ? $row['location_arrival_limited_day'] : null
                    ];
                } else {
                    $row['order_locations'][] = [
                        'location_destination_code' => $row['name_of_location_destination_code'],
                        'location_destination_id' => isset($row['location_destination_id']) ? $row['location_destination_id'] : null,
                        'location_destination_title' => isset($row['location_destination_title']) ? $row['location_destination_title'] : null,
                        'ETD_time' => $row['ETD_time'],
                        'ETD_date' => $row['ETD_date'],
                        'ETD_time_reality' => array_key_exists('ETD_time_reality', $row) ? $row['ETD_time_reality'] : null,
                        'ETD_date_reality' => array_key_exists('ETD_date_reality', $row) ? $row['ETD_date_reality'] : null,
                        'informative_destination' => array_key_exists('informative_destination', $row) ? $row['informative_destination'] : null,
                        'location_arrival_code' => $row['name_of_location_arrival_code'],
                        'location_arrival_id' => isset($row['location_arrival_id']) ? $row['location_arrival_id'] : null,
                        'location_arrival_title' => isset($row['location_arrival_title']) ? $row['location_arrival_title'] : null,
                        'ETA_time' => $row['ETA_time'],
                        'ETA_date' => $row['ETA_date'],
                        'ETA_time_reality' => array_key_exists('ETA_time_reality', $row) ? $row['ETA_time_reality'] : null,
                        'ETA_date_reality' => array_key_exists('ETA_date_reality', $row) ? $row['ETA_date_reality'] : null,
                        'informative_arrival' => array_key_exists('informative_arrival', $row) ? $row['informative_arrival'] : null,
                        'location_arrival_limited_day' => isset($row['location_arrival_limited_day']) ? $row['location_arrival_limited_day'] : null
                    ];
                    $data_locations[$row['order_code']] = $row;
                }
            }
        }
        return array_values($data_locations);
    }

    public function processGoodImport($data, $dataGoods)
    {
        foreach ($data as $key => &$row) {
            if (isset($dataGoods[$row['order_code']])) {
                $row['order_goods'] = $dataGoods[$row['order_code']];
            } else {
                $row['order_goods'] = [];
            }
        }
        return $data;
    }

    public function validRouteExcel($data, $update)
    {
        $routes = array();
        foreach ($data as $key => $row) {
            if (empty($row['route_name'])) {
                unset($orders);
                $orders[] = $row;
                $routes[$row['order_code']] = [
                    'failures' => '',
                    'orders' => $orders
                ];
            } else {
                if (isset($routes[$row['route_name']])) {
                    $routes[$row['route_name']]['orders'][] = $row;
                } else if (!$update) {
                    unset($orders);
                    $orders[] = $row;
                    $routes[$row['order_code']] = [
                        'failures' => 'Đơn chính không tồn tại',
                        'orders' => $orders
                    ];
                }
            }
        }

        foreach ($routes as &$value) {
            $validMessage = '';
            $vehicle = null;
            $primaryDriver = null;
            $totalVolume = 0;
            $totalWeight = 0;

            $countOrder = count($value['orders']);

            foreach ($value['orders'] as $i => $order) {

                $orderVolume = 0;
                $orderWeight = 0;
                if (!empty($order['order_goods'])) {
                    foreach ($order['order_goods'] as $key => $quantity) {
                        if (empty($quantity)) continue;
                        $code = Arr::first(explode('|', $key));
                        if (empty($code) || empty($goodTypes[$code])) continue;

                        $orderVolume = $goodTypes[$code]['volume'] * $quantity;
                        $orderWeight = $goodTypes[$code]['weight'] * $quantity;
                    }
                }
                // Nếu excel có nhập tổng thể tích, tổng khối lượng thì ưu tiên ko tính theo số lượng hàng hóa nhập vào
                $totalVolume += $order['volume'] && $order['volume'] > 0 ? $order['volume'] : $orderVolume;
                $totalWeight += $order['weight'] && $order['weight'] > 0 ? $order['weight'] : $orderWeight;

                if (!empty($value['failures'])) {
                    $validMessage = $value['failures'];
                    break;
                }
                if ($countOrder > 1 && (empty($order['vehicle']) || empty($order['primary_driver']))) {
                    $validMessage = 'Không thể ghép chuyến nếu có 1 đơn chưa gán xe - tài xế cho đơn hàng.';
                    break;
                }
                if ($i == 0) {
                    $vehicle = $order['vehicle'];
                    $primaryDriver = $order['primary_driver'];
                    continue;
                }
                if ($vehicle != $order['vehicle'] || $primaryDriver != $order['primary_driver']) {
                    $validMessage = 'Không thể ghép chuyến nếu xe - tài xế ở các đơn hàng khác nhau.';
                    break;
                }
            }

            $vehicleItem = empty($vehicle) ? null : $this->_vehicleRepository->search(['reg_no_eq' => $vehicle])->first();
            if (!empty($vehicleItem)) {
                if (!empty($vehicleItem->weight) && $totalWeight > floatval($vehicleItem->weight)) {
                    $value['warning'][] = trans('validation.out_of_weight', [
                        'vehicle' => numberFormat($vehicleItem->weight),
                        'weight' => numberFormat($totalWeight),
                    ]);
                }

                if (!empty($vehicleItem->volume) && $totalVolume > floatval($vehicleItem->volume)) {
                    $value['warning'][] = trans('validation.out_of_volume', [
                        'vehicle' => numberFormat($vehicleItem->volume),
                        'volume' => numberFormat($totalVolume),
                    ]);
                }
            }

            if (!empty($validMessage)) {
                $value['failures'] = $validMessage;
            }
        }

        foreach ($data as &$row) {
            foreach ($routes as $route) {
                foreach ($route['orders'] as $order) {
                    if ($order['order_code'] == $row['order_code']) {
                        if (!empty($route['failures']))
                            $row['failures'][] = $route['failures'];
                        if (!empty($route['warning']))
                            $row['warning'] = array_merge($row['warning'], $route['warning']);
                    }
                }
            }
        }
        return $data;
    }

    public function validOrderCustomerExcel($data)
    {
        $orderCustomers = array();
        foreach ($data as $key => $row) {
            if (!empty($row['order_no'])) {
                if (isset($orderCustomers[$row['order_no']])) {
                    $orderCustomers[$row['order_no']]['orders'][] = $row;
                } else {
                    unset($orders);
                    $orders[] = $row;
                    $orderCustomers[$row['order_no']] = [
                        'warning' => '',
                        'orders' => $orders
                    ];
                }
            }
        }

        foreach ($orderCustomers as &$value) {
            $validMessage = '';
            $paymentType = 0;
            $paymentUserId = 0;
            $vat = -1;
            $customerId = 0;
            foreach ($value['orders'] as $i => $order) {
                if ($i == 0) {
                    $paymentType = array_key_exists('payment_type', $order) ? $order['payment_type'] : null;
                    $paymentUserId = array_key_exists('payment_user_id', $order) ? $order['payment_user_id'] : null;
                    $vat = array_key_exists('vat', $order) ? $order['vat'] : null;
                    $customerId = $order['customer_code'];
                    continue;
                }
                if ($customerId != $order['customer_code']) {
                    $validMessage = 'Đơn trùng SDH nhưng không trùng Khách hàng.';
                    break;
                }
                if (array_key_exists('payment_type', $order) && $paymentType != $order['payment_type']) {
                    $validMessage = 'Đơn trùng SDH nhưng không trùng Hình thức thanh toán.';
                    break;
                }
                if (array_key_exists('payment_user_id', $order) && $paymentUserId != $order['payment_user_id']) {
                    $validMessage = 'Đơn trùng SDH nhưng không trùng Người chịu trách nhiệm thanh toán.';
                    break;
                }
                if (array_key_exists('vat', $order) && $vat != $order['vat']) {
                    $validMessage = 'Đơn trùng SDH nhưng không trùng VAT.';
                    break;
                }
            }
            if (!empty($validMessage)) {
                $value['warning'] = $validMessage;
            }
        }

        foreach ($data as &$row) {
            foreach ($orderCustomers as $orderCustomer) {
                if (!empty($orderCustomer['warning'])) {
                    foreach ($orderCustomer['orders'] as $order) {
                        if ($order['order_no'] == $row['order_no'] && empty($row['warning'])) {
                            $row['warning'][] = $orderCustomer['warning'];
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function doLocationExcelWithFormat($locations, $message)
    {
        $errorMessage = "";
        $size = count($locations);
        $provinceTitle = ucwords(trim(str_replace(array("tỉnh", "thành phố", "TP", "TP."), "", strtolower($locations[$size - 1]))));
        $districtTitle = ucwords(trim(str_replace(array("quận", "huyện", "thị xã", "thành phố", "TP", "TP."), "", strtolower($locations[$size - 2]))));
        $wardTitle = ucwords(trim(str_replace(array("xã", "phường"), "", strtolower($locations[$size - 3]))));
        $addressTitle = "";
        for ($i = 0; $i < $size - 3; $i++) {
            $addressTitle .= trim($locations[$i]);
            if ($i != $size - 4)
                $addressTitle .= ",";
        }

        $title = $addressTitle . ', ' . $wardTitle . ', ' . $districtTitle . ', ' . $provinceTitle;
        $province = $this->_provinceRepository->getProvince($provinceTitle);
        if ($province != null) {
            $district = $this->_districtRepository->getDistrict($province->province_id, $districtTitle);
            if ($district != null) {
                $warning = "";
                $ward = $this->_wardRepository->getWard($district->district_id, $wardTitle);
                $wardId = '';
                $coordinate = $district->location;
                if ($ward != null) {
                    $wardId = $ward->ward_id;
                    $coordinate = $ward->location;
                } else {
                    $warning = "Điểm " . $message . " chưa nhập đúng Xã,phường.";
                }

                if (empty($coordinate)) {
                    $errorMessage = "Điểm " . $message . " chưa có dữ liệu tọa độ trên hệ thống.";
                    return [$errorMessage];
                }
                //Convert tọa độ sang long
                $rexp = '/^(\-?\d+(?:\.\d+)?)(?:\D+(\d+)\D+(\d+)([NS]?))?[^\d\-]+(\-?\d+(?:\.\d+)?)(?:\D+(\d+)\D+(\d+)([EW]?))?$/i';
                preg_match($rexp, $coordinate, $matches);

                $latitude = AppConstant::DMS2Decimal((int)$matches[1], (int)$matches[2], (int)$matches[3]);
                $longitude = AppConstant::DMS2Decimal((int)$matches[5], (int)$matches[6], (int)$matches[7]);

                $address_auto_code = $province->province_id . ' - ' . $district->district_id . ' - ' . $wardId;
                $locationEntity = $this->_locationRepository->getLocation($address_auto_code, $addressTitle);

                if ($locationEntity == null) {
                    $locationEntity = $this->_locationRepository->findFirstOrNew([]);
                    $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location'), null, true);
                    if (empty($code)) {
                        $errorMessage = "Lỗi không sinh được mã địa điểm " . $message . " từ hệ thống.";
                        return [$errorMessage];
                    }
                    $locationEntity->code = $code;
                    $locationEntity->title = $title;
                    $locationEntity->full_address = $title;
                    $locationEntity->address = $addressTitle;
                    $locationEntity->province_id = $province->province_id;
                    $locationEntity->district_id = $district->district_id;
                    $locationEntity->ward_id = $wardId;
                    $locationEntity->address_auto_code = $address_auto_code;
                    $locationEntity->latitude = $latitude;
                    $locationEntity->longitude = $longitude;
                    $locationEntity->save();
                }
                return [$errorMessage, $warning, $locationEntity];
            } else {
                $errorMessage = "Điểm " . $message . " chưa nhập đúng Quận,huyện.";
                return [$errorMessage];
            }
        } else {
            $errorMessage = "Điểm " . $message . " chưa nhập đúng Tỉnh,thành phố.";
            return [$errorMessage];
        }
    }

    public static function processInputExcel($order, $data)
    {
        //Format date
        if ($order['status'] == config("constant.DANG_VAN_CHUYEN") || $order['status'] == config("constant.HOAN_THANH")) {
            $order['ETD_date_reality'] = empty($order['ETD_date_reality']) ? null : AppConstant::convertDate($order['ETD_date_reality'], 'Y-m-d');
        } else {
            $order['ETD_date_reality'] = null;
            $order['ETD_time_reality'] = null;
        }

        if ($order['status'] == config("constant.HOAN_THANH")) {
            $order['ETA_date_reality'] = empty($order['ETA_date_reality']) ? null : AppConstant::convertDate($order['ETA_date_reality'], 'Y-m-d');
        } else {
            $order['ETA_date_reality'] = null;
            $order['ETA_time_reality'] = null;
        }

        $order['ETD_date'] = empty($order['ETD_date']) ? null : AppConstant::convertDate($order['ETD_date'], 'Y-m-d');
        $order['ETA_date'] = empty($order['ETA_date']) ? null : AppConstant::convertDate($order['ETA_date'], 'Y-m-d');
        $order['order_date'] = empty($order['order_date']) ? null : AppConstant::convertDate($order['order_date'], 'Y-m-d');
        $order['date_collected_documents'] = empty($order['date_collected_documents']) ? null : AppConstant::convertDate($order['date_collected_documents'], 'Y-m-d');
        $order['time_collected_documents'] = empty($order['time_collected_documents']) ? null : $order['time_collected_documents'];
        $order['date_collected_documents_reality'] = empty($order['date_collected_documents_reality']) ? null : AppConstant::convertDate($order['date_collected_documents_reality'], 'Y-m-d');
        $order['time_collected_documents_reality'] = empty($order['time_collected_documents_reality']) ? null : $order['time_collected_documents_reality'];

        //Tính phí hoa hồng
        $commission_amount = 0;
        if (isset($order['commission_value']) && is_numeric($order['commission_value']) && isset($order['amount']) && is_numeric($order['amount'])) {
            if ($order['commission_type'] == config('constant.PHAN_TRAM_HOA_HONG')) {
                $commission_amount = $order['amount'] * ($order['commission_value'] / 100);
            } else if ($order['commission_type'] == config('constant.TONG_TIEN_HOA_HONG')) {
                $commission_amount = $order['commission_value'];
            }
            $order['commission_amount'] = $commission_amount;
        }

        //Tình trạng chứng từ
        if ($order['status_collected_documents'] != config("constant.DA_THU_DU") && !empty($order['date_collected_documents'])) {
            if (time() - strtotime($order['date_collected_documents'] . ' ' . $order['time_collected_documents']) > 0) {
                $order['status_collected_documents'] = config("constant.QUA_HAN");
            } else if (date('Y-m-d', strtotime(' today')) == date('Y-m-d', strtotime($order['date_collected_documents']))) {
                $order['status_collected_documents'] = config("constant.DEN_HAN_VAO_HOM_NAY");
            } else if (date('Y-m-d', strtotime(' +1 day')) == date('Y-m-d', strtotime($order['date_collected_documents']))) {
                $order['status_collected_documents'] = config("constant.DEN_HAN_VAO_HOM_SAU");
            }
        }

        //Cập nhật hạn thu chứng từ
        if ($order['status'] == config('constant.HOAN_THANH')
            && $data['location_arrival_limited_day'] && is_numeric($data['location_arrival_limited_day'])) {
            $dateCollected = date('Y-m-d', strtotime($order['ETA_date_reality'] . ' + ' . (int)$data['location_arrival_limited_day'] . ' days'));
            $timeCollected = $order['ETA_time_reality'];
            $order['date_collected_documents'] = $dateCollected;
            $order['time_collected_documents'] = $timeCollected;
        }

        return $order;
    }
}
