<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\CustomersExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\CustomersImport;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\Customer;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\DriverRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleTeamRepository;
use App\Repositories\WardRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class PartnerController
 * @package App\Http\Controllers\Backend
 */
class PartnerController extends BackendController
{
    protected $_wardRepository;
    protected $_districtRepository;
    protected $_provinceRepository;
    protected $_adminUserRepository;
    protected $columnConfigRepository;
    protected $_templateRepository;
    protected $_driverRepository;
    protected $_vehicleRepository;
    protected $_vehicleTeamRepository;

    /**
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return AdminUserInfoRepository
     */
    public function getAdminUserInfoRepository()
    {
        return $this->_adminUserRepository;
    }

    /**
     * @param mixed $adminUserRepository
     */
    public function setAdminUserInfoRepository($adminUserRepository)
    {
        $this->_adminUserRepository = $adminUserRepository;
    }

    /**
     * @return ColumnConfigRepository
     */
    public function getColumnConfigRepository()
    {
        return $this->columnConfigRepository;
    }

    /**
     * @param $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository)
    {
        $this->columnConfigRepository = $columnConfigRepository;
    }

    /**
     * @param TemplateRepository $templateRepository
     */
    public function setTemplateRepository($templateRepository)
    {
        $this->_templateRepository = $templateRepository;
    }

    /**
     * @return TemplateRepository
     */
    public function getTemplateRepository()
    {
        return $this->_templateRepository;
    }

    /**
     * @param DriverRepository $driverRepository
     */
    public function setDriverRepository($driverRepository)
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param VehicleRepository $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository)
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param VehicleTeamRepository $vehicleTeamRepository
     */
    public function setVehicleTeamRepository($vehicleTeamRepository)
    {
        $this->_vehicleTeamRepository = $vehicleTeamRepository;
    }

    /**
     * @return VehicleTeamRepository
     */
    public function getVehicleTeamRepository()
    {
        return $this->_vehicleTeamRepository;
    }

    /**
     * customerController constructor.
     * @param WardRepository $wardRepository
     * @param DistrictRepository $districtRepository
     * @param ProvinceRepository $provinceRepository
     * @param PartnerRepository $partnerRepository
     * @param AdminUserInfoRepository $adminUserRepository
     * @param ColumnConfigRepository $columnConfigRepository
     * @param TemplateRepository $templateRepository
     */
    public function __construct(
        WardRepository $wardRepository,
        DistrictRepository $districtRepository,
        ProvinceRepository $provinceRepository,
        PartnerRepository $partnerRepository,
        AdminUserInfoRepository $adminUserRepository,
        ColumnConfigRepository $columnConfigRepository,
        TemplateRepository $templateRepository,
        DriverRepository $driverRepository,
        VehicleRepository $vehicleRepository,
        VehicleTeamRepository $vehicleTeamRepository
    )
    {
        parent::__construct();
        $this->setRepository($partnerRepository);
        $this->setBackUrlDefault('partner.index');
        $this->setConfirmRoute('partner.confirm');
        $this->setMenu('partner');
        $this->setTitle(trans('models.partner.name'));

        $this->setWardRepository($wardRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setAdminUserInfoRepository($adminUserRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setDriverRepository($driverRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setVehicleTeamRepository($vehicleTeamRepository);

        $this->setAuditing(true);
        $this->setDeleted(false);
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_partner'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    protected function _prepareForm()
    {
        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => []
        ]);
    }

    protected function _prepareShow($id)
    {
        $drivers = $this->getDriverRepository()->getDriverByPartnerId($id);
        $vehicles = $this->getVehicleRepository()->getVehicleByPartnerId($id);
        $vehicleTeam = $this->getVehicleTeamRepository()->getVehicleTeamByPartnerId($id);

        $this->setViewData([
            'show_history' => true,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'vehicleTeam' => $vehicleTeam
        ]);
        $entity = $this->getRepository()->findWithRelation($id);
        return $this->setEntity($entity);
    }

    protected function _prepareConfirm()
    {
        $this->setEntity($this->_getFormData());
        $this->setViewData([
            'show_history' => false,
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
            'provinceList' => $this->getProvinceRepository()->orderBy('title', 'asc')->get()->pluck('title', 'province_id'),
            'districtList' => empty($entity->province_id) ? []
                : $this->getDistrictRepository()->search(['province_id_eq' => $entity->province_id])->get()->pluck('title', 'district_id'),
            'wardList' => empty($entity->district_id) ? []
                : $this->getWardRepository()->search(['district_id_eq' => $entity->district_id])->get()->pluck('title', 'ward_id')
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_partner'));
            }
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    protected function _processQuickSave($id, $field, $value)
    {
        $entity = $this->getRepository()->findFirstOrNew(['id' => $id]);
        if ($entity != null) {
            $entity->$field = $value;
            $entity->save();
        }
    }

    public function getDataForComboBox()
    {
        $all = Request::get('all');
        $q = Request::get('q');
        $customer_id = Request::get('c_id', -1);
        $query = $this->getRepository()->getItemsForComboBox($all, $q, $customer_id);
        return response()->json(['items' => $query->toArray()['data'], 'pagination' => $query->nextPageUrl() ? true : false]);
    }

}
