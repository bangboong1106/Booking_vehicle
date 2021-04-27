<?php

namespace App\Http\Controllers\Backend;

use App\Common\GoogleConstant;
use App\Exports\LocationExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\LocationImport;
use App\Model\Entities\Location;
use App\Repositories\DistrictRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\WardRepository;
use App\Repositories\LocationTypeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Model\Entities\CustomerDefaultData;
use App\Repositories\CustomerRepository;
use Exception;
use Validator;

class LocationController extends BackendController
{
    protected $_wardRepository;
    protected $_districtRepository;
    protected $_provinceRepository;
    protected $_locationTypeRepository;
    protected $_locationGroupRepository;
    protected $customerRepository;

    /**
     * @return WardRepository
     */
    public function getWardRepository()
    {
        return $this->_wardRepository;
    }

    /**
     * @param mixed $wardRepository
     */
    public function setWardRepository($wardRepository)
    {
        $this->_wardRepository = $wardRepository;
    }

    /**
     * @return DistrictRepository
     */
    public function getDistrictRepository()
    {
        return $this->_districtRepository;
    }

    /**
     * @param mixed $districtRepository
     */
    public function setDistrictRepository($districtRepository)
    {
        $this->_districtRepository = $districtRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->_provinceRepository;
    }

    /**
     * @param mixed $provinceRepository
     */
    public function setProvinceRepository($provinceRepository)
    {
        $this->_provinceRepository = $provinceRepository;
    }

    /**
     * @return LocationTypeRepository
     */
    public function getLocationTypeRepository()
    {
        return $this->_locationTypeRepository;
    }

    /**
     * @param LocationTypeRepository $locationTypeRepository
     */
    public function setLocationTypeRepository($locationTypeRepository)
    {
        $this->_locationTypeRepository = $locationTypeRepository;
    }

    /**
     * @return LocationGroupRepository
     */
    public function getLocationGroupRepository()
    {
        return $this->_locationGroupRepository;
    }

    /**
     * Set the value of _locationGroupRepository
     *
     * @return  LocationGroupRepository
     */
    public function setLocationGroupRepository($locationGroupRepository)
    {
        $this->_locationGroupRepository = $locationGroupRepository;
    }

    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    public function setCustomerRepository($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * LocationController constructor.
     * @param WardRepository $wardRepository
     * @param DistrictRepository $districtRepository
     * @param ProvinceRepository $provinceRepository
     * @param LocationRepository $locationRepository
     * @param LocationTypeRepository $locationTypeRepository
     */
    public function __construct(
        WardRepository $wardRepository,
        DistrictRepository $districtRepository,
        ProvinceRepository $provinceRepository,
        LocationRepository $locationRepository,
        LocationTypeRepository $locationTypeRepository,
        LocationGroupRepository $locationGroupRepository,
        CustomerRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($locationRepository);
        $this->setBackUrlDefault('location.index');
        $this->setConfirmRoute('location.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.location.name'));

        $this->setWardRepository($wardRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setLocationTypeRepository($locationTypeRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->setCustomerRepository($customerRepository);
        $this->setMap(true);
        $this->setExcel(true);
        $this->setExcelUpdate(true);
    }

    public function _prepareIndex()
    {
        $this->setViewData([
            'urlTemplate' => route('location.exportTemplate')
        ]);
    }

    public function getDataForComboBox()
    {
        $customerId = Request::get('c_id', -1);

        $query = Location::select('*')
            ->where(function ($query) {
                $query->where('code', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('title', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('full_address', 'LIKE', '%' . request('q') . '%');
            });

        if ($customerId > 0) {
            $query = $query->where('customer_id', $customerId);
        }

        $query = $query->orderBy('code', 'asc')->with(['group'])->paginate(10);

        $data = $query->toArray();
        $items = [];
        foreach ($query as $location) {
            $groupName = empty($location->group) ? '' : $location->group->title;
            $groupId = empty($location->group) ? '' : $location->group->id;
            $items[] = [
                'id' => $location->id,
                'text' => '<div>' . $location->code . '</div>
                <div>' . $location->title . '</div>
                <div class="option-plaintext" data-group="' . $groupName . '" data-id="' . $groupId . '">' . $location->full_address . '</div>',
                'title' => $location->title
            ];
        }

        $response = [
            'items' => $items,
            'pagination' => $query->nextPageUrl() ? true : false
        ];

        return response()->json($response);
    }

    protected function _getParams()
    {
        $data = Request::all();

        foreach ($data as $key => &$value) {
            if ($key == 'limited_day') {
                $value = convertNumber($value);
            }
        }
        return $data;
    }

    protected function _findOrNewEntity($id = null, $clean = false, $getForUpdate = false)
    {
        $entity = parent::_findOrNewEntity($id, $clean, $getForUpdate);
        $entity->address_auto_code = $entity->province_id . ' - ' . $entity->district_id . ' - ' . $entity->ward_id;
        $fullAddress = [];
        empty($entity->address) ? null : $fullAddress[] = $entity->address;
        empty($entity->ward_id) ? null : $fullAddress[] = $this->getWardRepository()->search(['ward_id_eq' => $entity->ward_id])->first()->title;
        empty($entity->district_id) ? null : $fullAddress[] = $this->getDistrictRepository()->search(['district_id_eq' => $entity->district_id])->first()->title;
        empty($entity->province_id) ? null : $fullAddress[] = $this->getProvinceRepository()->search(['province_id_eq' => $entity->province_id])->first()->title;
        $entity->full_address = implode(', ', $fullAddress);

        return $entity;
    }

    protected function _prepareForm()
    {
        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
            'locationTypes' => [],
            'locationGroups' => [],
            'customers' => $this->getCustomerRepository()->search()->get()->pluck('full_name', 'id'),
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } elseif ($id == -1) {
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location'));
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    /**
     * @param $prepare
     * @return mixed|void
     */
    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }
        $entity = $this->getEntity();

        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->search()->get()->pluck('title', 'province_id'),
            'districtList' => empty($entity->province_id) ? []
                : $this->getDistrictRepository()->search(['province_id_eq' => $entity->province_id])->get()->pluck('title', 'district_id'),
            'wardList' => empty($entity->district_id) ? []
                : $this->getWardRepository()->search(['district_id_eq' => $entity->district_id])->get()->pluck('title', 'ward_id')
        ]);
    }

    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $this->_prepareSelected();
        return $parent;
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $this->_prepareSelected();
        return $parent;
    }

    protected function _prepareDuplicate($id = null)
    {
        $parent = parent::_prepareDuplicate($id);
        $this->_prepareSelected();
        return $parent;
    }

    protected function _prepareSelected()
    {
        $entity = $this->getEntity();

        if (!empty($entity->province_id)) {
            $this->setViewData([
                'districtList' => $this->getDistrictRepository()->where('province_id', $entity->province_id)
                    ->orderBy('title', 'asc')
                    ->pluck('title', 'district_id'),
            ]);
        }
        if (!empty($entity->province_id) && !empty($entity->district_id)) {
            $this->setViewData([
                'wardList' => $this->getWardRepository()->where('district_id', $entity->district_id)
                    ->orderBy('title', 'asc')
                    ->pluck('title', 'ward_id'),
            ]);
        }
    }

    public function exportTemplate()
    {
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);
        if (isset($ids)) {
            $sort_field = array_key_exists('sort_field', $data) ? $data["sort_field"] : 'id';
            $sort_type = array_key_exists('sort_type', $data) ? $data["sort_type"] : 'desc';
            $data = [];
            $data['id_in'] = explode(',', $ids);
            $data["sort_field"] = $sort_field;
            $data["sort_type"] = $sort_type;
        }

        $quotaExport = new LocationExport($this->getRepository(), $this->getCustomerRepository(), $data);
        $update = Request::has('update') ? true : false;
        return $quotaExport->exportFromTemplate($update);
    }

    public function _mappingDataImport($data, $update)
    {
        $numberCode = 0;

        $locationImport = new LocationImport();
        $listLocationCode = [];

        foreach ($data as &$location) {
            $location = $locationImport->map($location);
            if ($location != null && empty($location['code'])) $numberCode++;
            if ($update) {
                $listLocationCode[] = $location['code'];
            }
        }

        $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_location'), $numberCode);
        $i = 0;
        $listLocation = $update ? $this->getRepository()->search(['code_in' => $listLocationCode])->get() : null;

        $dataCustomerList = $this->getCustomerRepository()->getGoodsOwnerList()->pluck('id', 'customer_code')->toArray();

        foreach ($data as &$item) {
            if ($item != null) {
                if (empty($item['code'])) {
                    $item['code'] = $systemCodeList[$i];
                    $i++;
                }
                $item['importable'] = true;
                $item['failures'] = [];
            }

            if ($update) {
                $locationItem = $listLocation->where('code', $item['code'])->first();
                if (isset($locationItem)) {
                    $item['id'] = $locationItem->id;
                }
            }
            if (isset($dataCustomerList[$item['customer_code']])) {
                $item['customer_id'] = $dataCustomerList[$item['customer_code']];
            }
        }

        return $data;
    }

    protected function _processDataForImport($entity, $data)
    {
        if (empty($entity->province_id) && empty($entity->district_id) && empty($entity->ward_id)) {
            $entity = $this->_processLocation($entity);
        }

        if (empty($entity->latitude) && isset($entity->address) && isset($entity->ward_text)
            && isset($entity->district_text) && isset($entity->province_text)) {
            $address = $entity->address . ', ' . $entity->ward_text . ', ' . $entity->district_text . ', ' . $entity->province_text;
            $client = new Client();
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) .
                '&sensor=false&key=' . env('GOOGLE_MAP_API_KEY', '');
            $result = (string)$client->post($url, ['verify' => false])->getBody();
            $json = json_decode($result);
            if (!empty($results)) {
                $entity->latitude = $json->results[0]->geometry->location->lat;
                $entity->longitude = $json->results[0]->geometry->location->lng;
            }
        }

        // Thêm full_address
        if (isset($entity->address_auto_code) && empty($entity->address_auto_code)) {
            $entity->address_auto_code = $entity->province_id . ' - ' . $entity->district_id . ' - ' . $entity->ward_id;
        }

        if (empty($entity->full_address)) {
            $fullAddress = [];
            empty($entity->address) ? null : $fullAddress[] = $entity->address;
            empty($entity->ward_id) ? null : $fullAddress[] = $this->getWardRepository()->search(['ward_id_eq' => $entity->ward_id])->first()->title;
            empty($entity->district_id) ? null : $fullAddress[] = $this->getDistrictRepository()->search(['district_id_eq' => $entity->district_id])->first()->title;
            empty($entity->province_id) ? null : $fullAddress[] = $this->getProvinceRepository()->search(['province_id_eq' => $entity->province_id])->first()->title;
            $entity->full_address = implode(', ', $fullAddress);
        }

        return $entity;
    }

    protected function _processLocation($entity)
    {
        $locations = explode(",", $entity->address);
        $size = count($locations);
        // TODO: Tạm fix, nếu nhập dữ liệu địa chỉ ko đúng định dạng thì sẽ tạo địa chỉ mới
        if ($size < 4) {
            $location = $this->getRepository()->findAddress(empty($entity->address) ? $entity->title : $entity->address);
            if (!isset($location)) return $entity;
            return $location;
        } else {
            $entity = $this->_doLocationExcelWithFormat($entity);
        }
        return $entity;
    }

    protected function _doLocationExcelWithFormat($entity)
    {
        $locations = explode(",", $entity->address);
        $size = count($locations);
        $provinceTitle = ucwords(trim(str_replace(array("tỉnh", "thành phố", "TP"), "", strtolower($locations[$size - 1]))));
        $districtTitle = ucwords(trim(str_replace(array("quận", "huyện", "thị xã", "thành phố", "TP"), "", strtolower($locations[$size - 2]))));
        $wardTitle = ucwords(trim(str_replace(array("xã", "phường"), "", strtolower($locations[$size - 3]))));
        $addressTitle = "";
        for ($i = 0; $i < $size - 3; $i++) {
            $addressTitle .= trim($locations[$i]);
            if ($i != $size - 4)
                $addressTitle .= ",";
        }

        $title = $addressTitle . ', ' . $wardTitle . ', ' . $districtTitle . ', ' . $provinceTitle;
        $province = $this->getProvinceRepository()->getProvince($provinceTitle);
        if ($province != null) {
            $district = $this->getDistrictRepository()->getDistrict($province->province_id, $districtTitle);
            if ($district != null) {
                $warning = "";
                $ward = $this->getWardRepository()->getWard($district->district_id, $wardTitle);
                $wardId = '';
                $coordinate = $district->location;
                if ($ward != null) {
                    $wardId = $ward->ward_id;
                    $coordinate = empty($ward->location) ? $coordinate : $ward->location;
                }

                //Convert tọa độ sang long
                $googleConstant = new GoogleConstant(env('GOOGLE_MAP_API_KEY', ''));
                $latLong = $googleConstant->convertDMSToLatLong($coordinate);

                $latitude = !empty($latLong) ? $latLong['latitude'] : '';
                $longitude = !empty($latLong) ? $latLong['longitude'] : '';

                $address_auto_code = $province->province_id . ' - ' . $district->district_id . ' - ' . $wardId;
                $locationEntity = $this->getRepository()->getLocation($address_auto_code, $addressTitle);

                if ($locationEntity == null) {
                    $entity->full_address = $title;
                    $entity->address = $addressTitle;
                    $entity->province_id = $province->province_id;
                    $entity->district_id = $district->district_id;
                    $entity->ward_id = $wardId;
                    $entity->address_auto_code = $address_auto_code;
                    $entity->latitude = $latitude;
                    $entity->longitude = $longitude;
                } else {
                    return $locationEntity;
                }
            }
        }

        return $entity;
    }

    public function destroy($id, $action = 'delete')
    {
        $issetLocation = CustomerDefaultData::where('del_flag', 0)
                    ->where('location_destination_ids', $id)
                    ->orWhere('location_arrival_ids', $id)
                    ->get()->toArray();

        if (count($issetLocation) == 0) {
            return parent::destroy($id, $action = 'delete');
        }

        return $this->_backToStart()->withErrors(trans('messages.delete_location_failed'));
    }
}
