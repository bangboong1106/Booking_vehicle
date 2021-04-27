<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\ClientExport;
use App\Exports\CustomersExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\ClientImport;
use App\Imports\CustomersImport;
use App\Model\Entities\Customer;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\WardRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class ClientController
 * @package App\Http\Controllers\Backend
 */
class ClientController extends BackendController
{
    protected $_wardRepository;
    protected $_districtRepository;
    protected $_provinceRepository;
    protected $_adminUserRepository;
    protected $columnConfigRepository;
    protected $_templateRepository;
    protected $_customerRepository;

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
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->columnConfigRepository = $columnConfigRepository;
    }

    /**
     * @param  $templateRepository
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
     * @return mixed
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param mixed $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * customerController constructor.
     * @param WardRepository $wardRepository
     * @param DistrictRepository $districtRepository
     * @param ProvinceRepository $provinceRepository
     * @param ClientRepository $clientRepository
     * @param AdminUserInfoRepository $adminUserRepository
     * @param ColumnConfigRepository $columnConfigRepository
     * @param TemplateRepository $templateRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        WardRepository $wardRepository,
        DistrictRepository $districtRepository,
        ProvinceRepository $provinceRepository,
        ClientRepository $clientRepository,
        AdminUserInfoRepository $adminUserRepository,
        ColumnConfigRepository $columnConfigRepository,
        TemplateRepository $templateRepository,
        CustomerRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($clientRepository);
        $this->setBackUrlDefault('client.index');
        $this->setConfirmRoute('client.confirm');
        $this->setMenu('customer');
        $this->setTitle(trans('models.client.name'));

        $this->setWardRepository($wardRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setAdminUserInfoRepository($adminUserRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setCustomerRepository($customerRepository);

        $this->setMap(true);
        $this->setExcel(true);
        $this->setAuditing(true);
        $this->setDeleted(true);
        $this->setExcelUpdate(true);
        $this->setViewData([
            'exampleName' => 'Danh_sach_khach_hang.xlsx',
            'urlTemplate' => route('client.exportTemplate')
        ]);
    }


    protected function _saveRelations($entity, $action = 'save')
    {
        if (in_array($action, ['delete', 'forceDelete', 'massDelete'])) {
            return true;
        }
        $relations = $entity->getRelations();
        foreach ($relations as $relationName => $relation) {
            if (is_null($relation)) {
                continue;
            }

            if ($action == 'update' && $relationName == 'adminUser' && empty($relation->password)) {
                unset($relation->password);
            }

            if (isCollection($relation)) {
                $relation->map(function ($item) use ($entity, $action, $relationName) {
                    $item->exists = (bool)$item->id;
                    $item->fill([$entity->$relationName()->getForeignKeyName() => $entity->id]);
                    call_user_func_array([$item, $action], []);
                    $this->_saveRelations($item, $action);
                });
                continue;
            }
            $relation->exists = (bool)$relation->id;
            $relation->fill([$entity->getForeignKey() => $entity->id]);
            call_user_func_array([$relation, $action], []);
            $entity->user_id = $relation->id;
            $entity->save();
            $this->_saveRelations($relation);
        }
    }

    public function _deleteRelations($entity)
    {
        // delete user
        $user = $this->getAdminUserInfoRepository()->search(['id' => $entity->user_id])->first();
        if ($user->count() !== 0) $user->delete();
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_customer'));
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
            'wardList' => [],
            'parentList' => $this->getCustomerRepository()->getGoodsOwnerList()->pluck('full_name', 'id')
        ]);
    }

    protected function _prepareShow($id)
    {
        $listCustomerGroup = $this->getCustomerRepository()->getCustomerGroups($id);
        $listCustomer = $this->getCustomerRepository()->getCustomerOfGoodsOwner($id);
        $this->setViewData([
            'show_history' => true,
            'listCustomerGroup' => $listCustomerGroup,
            'listCustomer' => $listCustomer
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
        $listSex = array_keys(config('system.sex'));
        $sexs = [];

        foreach ($listSex as $sex) {
            $sexs[$sex] = trans('common.' . $sex);
        }

        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('customer_code', $attributes)) {
            $code = $attributes['customer_code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_customer'));
            }
        }
        $this->setViewData([
            'sexs' => $sexs,
            'customer_code' => $code
        ]);
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = $this->_findOrNewEntity(null, false, true);
        return $this->_processInput($entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        return $this->_processInput($entity);
    }

    protected function _processInput($entity)
    {
        if (empty($entity->birth_date)) return $entity;
        $entity->birth_date = empty($entity->birth_date) ? null : AppConstant::convertDate($entity->birth_date, 'Y-m-d');
        return $entity;
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

        $clientExport = new ClientExport($this->getCustomerRepository(), $data);
        $update = Request::has('update') ? true : false;
        $parentList = $this->getCustomerRepository()->getGoodsOwnerList();
        return $clientExport->exportFromTemplate($update, $parentList);
    }

    public function _mappingDataImport($data, $update)
    {
        $numberCode = 0;
        $customerImport = new ClientImport();
        $listCustomerCode = [];
        foreach ($data as &$customer) {
            $customer = $customerImport->map($customer);
            if ($customer != null && empty($customer['customer_code'])) $numberCode++;
            if ($update) {
                $listCustomerCode[] = $customer['customer_code'];
            }
        }

        $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_customer'), $numberCode);
        $i = 0;
        $listCustomer = $update ? $this->getRepository()->search(['customer_code_in' => $listCustomerCode])->get() : null;

        $parentMap = $this->getCustomerRepository()->getGoodsOwnerList()->pluck('id', 'customer_code')->toArray();
        foreach ($data as &$item) {
            if ($item != null) {
                if (empty($item['customer_code'])) {
                    $item['customer_code'] = $systemCodeList[$i];
                    $i++;
                }

                $item['parent_id'] = isset($parentMap[$item['parent_code']]) ? $parentMap[$item['parent_code']] : null;
                $item['customer_type'] = config('constant.KHACH_HANG');

                $item['importable'] = true;
                $item['failures'] = [];
            }

            if ($update) {
                $customerItem = $listCustomer->where('customer_code', $item['customer_code'])->first();
                if (isset($customerItem)) {
                    $item['id'] = $customerItem->id;
                    $item['user_id'] = $customerItem->user_id;
                    $item['adminUser']['id'] = $customerItem->user_id;
                }
            }
        }
        return $data;
    }

    protected function _processDataForImport($entity, $data)
    {
        return $this->_processInput($entity);
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 10/4/2020
    public function exportCustomTemplate()
    {
        $ids = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $ids);
        $datas = [];
        foreach ($arr as $item) {
            $data = $this->getRepository()->getExportByID($item);
            $datas[] = [
                'id' => $item,
                'name' => $data->{'customer_code'},
                'data' => $data
            ];
        }

        $dataExport = new TemplateExport(
            $this->getTemplateRepository(),
            $datas
        );
        return $dataExport->exportCustomTemplate($templateId);
    }

    public function getClientForCombobox(){
        $q = Request::get('q');
        $customerId = Request::get('customer_id', -1);

        $data = $this->getRepository()->getClientByCustomerId($customerId, $q);
        return response()->json($data);
    }
}
