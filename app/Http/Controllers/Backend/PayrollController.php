<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\PayrollFormula;
use App\Model\Entities\VehicleGroup;
use App\Repositories\CustomerGroupRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\PayrollCustomerGroupRepository;
use App\Repositories\PayrollRepository;
use App\Repositories\ColumnConfigRepository;

use Exception;
use Illuminate\Http\RedirectResponse;
use Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends BackendController
{
    protected $_customerGroupRepository;
    protected $_locationGroupRepository;
    protected $_payrollCustomerGroupRepository;

    /**
     * @return mixed
     */
    public function getCustomerGroupRepository()
    {
        return $this->_customerGroupRepository;
    }

    /**
     * @param mixed $customerGroupRepository
     */
    public function setCustomerGroupRepository($customerGroupRepository): void
    {
        $this->_customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @return mixed
     */
    public function getLocationGroupRepository()
    {
        return $this->_locationGroupRepository;
    }

    /**
     * @param mixed $locationGroupRepository
     */
    public function setLocationGroupRepository($locationGroupRepository): void
    {
        $this->_locationGroupRepository = $locationGroupRepository;
    }

    /**
     * @return mixed
     */
    public function getPayrollCustomerGroupRepository()
    {
        return $this->_payrollCustomerGroupRepository;
    }

    /**
     * @param mixed $payrollCustomerGroupRepository
     */
    public function setPayrollCustomerGroupRepository($payrollCustomerGroupRepository): void
    {
        $this->_payrollCustomerGroupRepository = $payrollCustomerGroupRepository;
    }

    /**
     * @return ColumnConfigRepository
     */
    public function getColumnConfigRepository()
    {
        return $this->_columnConfigRepository;
    }

    /**
     * @param $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->_columnConfigRepository = $columnConfigRepository;
    }

    public function __construct(
        PayrollRepository $payrollRepository,
        PayrollCustomerGroupRepository $payrollCustomerGroupRepository,
        CustomerGroupRepository $customerGroupRepository,
        LocationGroupRepository $locationGroupRepository,
        ColumnConfigRepository $columnConfigRepository

    )
    {
        parent::__construct();

        $this->setRepository($payrollRepository);
        $this->setPayrollCustomerGroupRepository($payrollCustomerGroupRepository);
        $this->setCustomerGroupRepository($customerGroupRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->setColumnConfigRepository($columnConfigRepository);

        $this->setBackUrlDefault('payroll.index');
        $this->setConfirmRoute('payroll.confirm');
        $this->setMenu('quota');
        $this->setTitle(trans('models.payroll.name'));
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_payroll'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity->save();
            // $this->_saveRelations($entity);

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($data, $entity);
            // add new
            $this->fireEvent('after_store', $entity);
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function update($id)
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.update_failed'));
        }
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity->save();
            // $this->_saveRelations($entity, 'edit');

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($data, $entity);
            DB::commit();
            $this->fireEvent('after_update', $entity);
            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }

    public function _deleteRelations($entity)
    {
        //Xóa danh sách nhóm khách hàng
        $this->getPayrollCustomerGroupRepository()->deleteWhere([
            'payroll_id' => $entity->id
        ]);
        //Xóa danh sách công thức
        $entity->formular()->delete();

    }

    public function _processCreateRelation($data, $entity)
    {
        //Lưu danh sách nhóm KH
        $entity->customerGroups = isset($data['customerGroups']) ? $data['customerGroups'] : [];
        $this->_saveCustomerGroups($entity);

        //Lưu danh sách công thức
        $entity->formulas = isset($data['formulas']) ? $data['formulas'] : [];
        $this->_saveFormulas($entity);
    }

    public function _prepareForm()
    {
        //Get group vehicle list
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', true);

        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
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
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_payroll'));
            }
        }

        if (isset($attributes['customerGroups'])) {
            $currentListCustomerGroup = $attributes['customerGroups'];
        } else {
            $entity = $this->getRepository()->getPayrollWithID($id);
            $currentListCustomerGroup = isset($entity->customerGroups) ? $entity->customerGroups->pluck('id')->toArray() : [];
        }

        $this->setViewData([
            'code' => $code,
            'currentListCustomerGroup' => $currentListCustomerGroup
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();

        $entity->formulas = $this->_prepareDataFormulas($entity);

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => false,
        ]);
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', false);
        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        $entity->customerGroups = isset($entity->customerGroups) ? $entity->customerGroups->pluck('id')->toArray() : [];
        $entity->formulas = isset($entity->formulas) ? $entity->formulas->toArray() : [];

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => true
        ]);
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', false);
        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $entity->formulas = $this->_prepareDataFormulas($entity);

        $this->setEntity($entity);
        return $parent;
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

    public function _processInput($entity)
    {
        $entity->date_from = empty($entity->date_from) ? null : AppConstant::convertDate($entity->date_from, 'Y-m-d');
        $entity->date_to = empty($entity->date_to) ? null : AppConstant::convertDate($entity->date_to, 'Y-m-d');
        $entity->isApplyAll = empty($entity->isApplyAll) ? 0 : $entity->isApplyAll;
        $entity->isDefault = empty($entity->isDefault) ? 0 : $entity->isDefault;
        return $entity;
    }

    /**
     * Lưu nhóm khách hàng
     * @param $entity
     */
    protected function _saveCustomerGroups($entity)
    {
        if ($entity->isApplyAll == 1) {
            $this->getPayrollCustomerGroupRepository()->deleteWhere([
                'payroll_id' => $entity->id
            ]);
        } else {
            $customerGroups = $entity->customerGroups;

            if (empty($customerGroups)) {
                return;
            }
            $data = [];
            foreach ($customerGroups as $customerGroupId) {
                if (!empty($entity->id) && !empty($customerGroupId))
                    $data[] = [
                        'payroll_id' => $entity->id,
                        'customer_group_id' => $customerGroupId
                    ];
            }
            $entity->customerGroups()->sync($data);
        }
    }

    /**
     * Lưu danh sách công thức
     * @param $entity
     */
    protected function _saveFormulas($entity)
    {
        $data = $this->_prepareDataFormulas($entity);

        $formulaModels = [];
        foreach ($data as $item) {
            $formulaModels[] = new PayrollFormula($item);
        }
        $entity->formulas()->delete();
        $entity->formulas()->saveMany($formulaModels);
    }

    public function _prepareDataFormulas($entity)
    {
        $formulas = $entity->formulas;

        if (empty($formulas)) {
            return [];
        }
        $data = [];
        foreach ($formulas as $item) {
            $data[] = [
                'payroll_id' => $entity->id,
                'vehicle_group_id' => isset($item['vehicle_group_id']) ? $item['vehicle_group_id'] : null,
                'location_group_destination_id' => isset($item['location_group_destination_id']) ? $item['location_group_destination_id'] : null,
                'location_group_arrival_id' => isset($item['location_group_arrival_id']) ? $item['location_group_arrival_id'] : null,
                'price' => $item['price'],
            ];

        }

        return $data;
    }

    /**
     * Lấy dữ liệu select2
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataForComboBox(Request $request)
    {

        $query = $this->getRepository()->getPayrolls($request);
        return response()->json(
            [
                'items' => $query->toArray()['data'],
                'pagination' => $query->nextPageUrl() ? true : false
            ]
        );
    }
}
