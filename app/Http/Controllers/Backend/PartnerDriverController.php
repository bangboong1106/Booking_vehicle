<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\PartnerDriverExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\PartnerDriverImport;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\DriverConfigFileRepository;
use App\Repositories\DriverRepository;
use App\Repositories\DriverFileRepository;
use App\Repositories\DriverVehicleRepository;
use App\Repositories\FileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PartnerDriverRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleTeamRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use stdClass;

/**
 * Class PartnerDriverController
 * @package App\Http\Controllers\Backend
 */
class PartnerDriverController extends BackendController
{
    /**
     * @var DriverRepository
     */
    protected $driverFileRepository;
    protected $driverConfigFileRepository;
    protected $adminUserRepository;
    protected $fileRepository;
    protected $vehicleTeamRepository;
    protected $vehicleRepository;
    protected $driverVehicleRepository;
    protected $orderHistoryRepository;
    protected $orderRepository;
    protected $columnConfigRepository;
    protected $_templateRepository;
    protected $partnerRepository;

    private $_vehicle_id;

    /**
     * @return null
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
     * @return VehicleTeamRepository
     */
    public function getVehicleTeamRepository(): VehicleTeamRepository
    {
        return $this->vehicleTeamRepository;
    }

    /**
     * @param VehicleTeamRepository $vehicleTeamRepository
     */
    public function setVehicleTeamRepository(VehicleTeamRepository $vehicleTeamRepository): void
    {
        $this->vehicleTeamRepository = $vehicleTeamRepository;
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
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function __construct(
        PartnerDriverRepository $partnerDriverRepository,
        DriverFileRepository $driverFileRepository,
        DriverConfigFileRepository $driverConfigFileRepository,
        AdminUserInfoRepository $adminUserRepository,
        FileRepository $fileRepository,
        VehicleTeamRepository $vehicleTeamRepository,
        VehicleRepository $vehicleRepository,
        DriverVehicleRepository $driverVehicleRepository,
        OrderHistoryRepository $orderHistoryRepository,
        OrderRepository $orderRepository,
        ColumnConfigRepository $columnConfigRepository,
        TemplateRepository $templateRepository,
        PartnerRepository $partnerRepository
    )
    {
        parent::__construct();
        $this->setRepository($partnerDriverRepository);
        $this->setBackUrlDefault('partner-driver.index');
        $this->setConfirmRoute('partner-driver.confirm');
        $this->setMenu('partner_driver');
        $this->setTitle(trans('models.driver.name'));

        $this->driverFileRepository = $driverFileRepository;
        $this->driverConfigFileRepository = $driverConfigFileRepository;
        $this->adminUserRepository = $adminUserRepository;
        $this->fileRepository = $fileRepository;
        $this->setVehicleTeamRepository($vehicleTeamRepository);
        $this->vehicleRepository = $vehicleRepository;
        $this->driverVehicleRepository = $driverVehicleRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->setTemplateRepository($templateRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setPartnerRepository($partnerRepository);

        $this->setExcel(true);
        $this->setAuditing(true);
        $this->setDeleted(true);
        $this->setExcelUpdate(true);
        $this->setViewData([
            'exampleName' => 'Danh_sach_tai_xe.xlsx',
            'urlTemplate' => route('driver.exportTemplate')
        ]);
    }

    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        $attributes = $this->_getFormData()->getAttributes();
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $this->_saveRelations($entity);
            $entity = $this->beforeSave($entity);

            $entity->save();
            $this->saveDriverFile($attributes, $entity->id);

            $this->fireEvent('after_store', $entity);
            // add new
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (\Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

    public function update($id)
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.update_failed'));
        }
        $attributes = $this->_getFormData()->getAttributes();
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);

            $this->_saveRelations($entity, 'update');
            $entity = $this->beforeSave($entity);

            $entity->save();
            // fire after save
            // fire before save relation

            $this->saveDriverFile($attributes, $entity->id);
            // fire after save relation
            $this->fireEvent('after_update', $entity);
            // add new
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (\Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
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

    public function saveDriverFile($attributes, $driver_id)
    {
        $driver_config_list = $this->driverConfigFileRepository->getAll();

        $this->driverFileRepository->deleteWhere([
            'driver_id' => $driver_id
        ]);

        foreach ($driver_config_list as $driver_config) {
            $driver_file = $attributes['driver_file'][$driver_config->id];

            if (isset($driver_file['file_id'])) {
                $file_id_list = explode(';', $driver_file['file_id']);

                foreach ($file_id_list as $file_id) {

                    $entity = $this->driverFileRepository->findFirstOrNew([]);

                    $entity->driver_id = $driver_id;
                    $entity->file_id = $file_id;
                    $entity->driver_config_file_id = $driver_config->id;
                    if ($driver_config->is_show_expired) {
                        $entity->expire_date = empty($driver_file['expire_date']) ? null : $driver_file['expire_date'];
                    }
                    if ($driver_config->is_show_register) {
                        $entity->register_date = empty($driver_file['register_date']) ? null : $driver_file['register_date'];
                    }

                    $entity->save();
                    app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->file_id, 'drivers');
                }
            }
        }
    }

    protected function beforeSave($entity)
    {
        $this->_vehicle_id = $entity->vehicle_id;
        $entity->partner_id = $this->getCurrentUser()->partner_id;

        return $entity;
    }

    public function _deleteRelations($entity)
    {
        // delete user
        if ($entity->user_id != null && $entity->user_id != 0) {
            $user = $this->adminUserRepository->find($entity->user_id);
            if (!is_null($user))
                $user->delete();
        }
        //delete driver_file
        $driverFiles = $this->driverFileRepository->getDriverFileWithDriverID($entity->id);
        if ($driverFiles != null) {
            foreach ($driverFiles as $driverFileEntity) {
                $fileEntity = $this->fileRepository->getFileWithID($driverFileEntity->file_id);
                if ($fileEntity != null)
                    $fileEntity->delete();
                $driverFileEntity->delete();
            }
        }

        //delete driver_vehicle
        $driverVehicles = $this->driverVehicleRepository->getItemsByDriverID($entity->id);
        if ($driverVehicles != null) {
            foreach ($driverVehicles as $entity) {
                $entity->delete();
            }
        }
    }

    protected function _prepareFormWithID($id)
    {
        //Get vehicle team list
        $vehicle_team_list = $this->getVehicleTeamRepository()->search([])->pluck('name', 'id');
        $this->setViewData(['vehicle_team_list' => $vehicle_team_list]);

        $listSex = array_keys(config('system.sex'));
        $sexs = [];

        foreach ($listSex as $sex) {
            $sexs[$sex] = trans('common.' . $sex);
        }
        $this->setViewData(['sexs' => $sexs]);

        $driver_licenses = config('system.driver_license');
        $this->setViewData(['driver_licenses' => $driver_licenses]);

        // Get driver config
        $attributes = $this->_getFormData()->getAttributes();

        $driver_config_list = $this->driverConfigFileRepository->getAll();
        $driver_file_list = [];
        if (array_key_exists('driver_file', $attributes)) {
            // Put lại data file cho form khi nhấn back
            foreach ($driver_config_list as $driver_config) {

                $driver_file = $attributes['driver_file'][$driver_config->id];
                $driver_files = null;
                if (!empty($driver_file['file_id'])) {
                    $file_id_list = explode(';', $driver_file['file_id']);
                    if (!empty($file_id_list)) {
                        foreach ($file_id_list as $file_id) {
                            $file = $this->fileRepository->getFileWithID($file_id);
                            if (!is_null($file)) {
                                $driverFileEntity = [
                                    'file_name' => $file->file_name,
                                    'size' => $file->size,
                                    'file_id' => $file_id,
                                    'register_date' => $driver_file['register_date'],
                                    'expire_date' => $driver_file['expire_date']
                                ];
                                $driver_files[] = $driverFileEntity;
                            } else {
                                unset($driver_files[$file_id]);
                            }
                        }
                    }
                }
                $driver_file_list[$driver_config->id] = collect($driver_files);
            }
        } else {
            foreach ($driver_config_list as $driver_config) {

                $driver_files = $this->driverFileRepository->getDriverFile($id, $driver_config->id);

                if ($driver_files) {
                    foreach ($driver_files as $key => $driver_file) {
                        $file = $this->fileRepository->getFileWithID($driver_file->file_id);
                        if (!is_null($file)) {
                            $driver_file['file_name'] = $file->file_name;
                            $driver_file['size'] = $file->size;
                        } else {
                            unset($driver_files[$key]);
                        }
                    }
                }
                $driver_file_list[$driver_config->id] = $driver_files;
            }
        }
        $attributes = $this->_getFormData(false);

        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_driver'));
            }
        }

        $create_account_flag = true;
        if (array_key_exists('create_account', $attributes)) {
            $create_account_flag = $attributes['create_account'] == 1 ? true : false;
        } else if ($id != -1) {
            $entity = $this->getRepository()->getItemById($id);
            if ($entity != null && $entity->user_id != 0)
                $create_account_flag = true;
        }
        $this->setViewData([
            'driver_config_list' => $driver_config_list,
            'driver_file_list' => $driver_file_list,
            'code' => $code,
            'create_account_flag' => $create_account_flag
        ]);
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_driver'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $attributes = $this->_getFormData()->getAttributes();
        $driver_config_list = $this->driverConfigFileRepository->getAll();
        $driver_file_list = [];
        if (array_key_exists('driver_file', $attributes))
            $driver_file_list = $attributes['driver_file'];
        $this->setViewData([
            'driver_config_list' => $driver_config_list,
            'driver_file_list' => $driver_file_list,
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $driver_config_list = $this->driverConfigFileRepository->getAll();
        $driver_file_list = [];
        foreach ($driver_config_list as $driver_config) {
            $driver_files = $this->driverFileRepository->getDriverFile($id, $driver_config->id);
            if ($driver_files) {
                foreach ($driver_files as $key => $driver_file) {
                    $file = $this->fileRepository->getFileWithID($driver_file->file_id);
                    if (!is_null($file)) {
                        $driver_file['file_name'] = $file->file_name;
                        $driver_file['size'] = $file->size;
                    } else {
                        unset($driver_files[$key]);
                    }
                }
            }
            $driver_file_list[$driver_config->id]['file_id'] = $driver_files->pluck('file_id')->implode(';');
            $driver_file_list[$driver_config->id]['register_date'] = $driver_files->first()['register_date'];
            $driver_file_list[$driver_config->id]['expire_date'] = $driver_files->first()['expire_date'];
        }

        $this->setViewData([
            'driver_config_list' => $driver_config_list,
            'driver_file_list' => $driver_file_list,
            'show_history' => true,

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
        $entity->birth_date = empty($entity->birth_date) ? null : AppConstant::convertDate($entity->birth_date, 'Y-m-d');
        $entity->work_date = empty($entity->work_date) ? null : AppConstant::convertDate($entity->work_date, 'Y-m-d');

        return $entity;
    }

    protected function _processDataForImport($entity, $data)
    {
        $entity = $this->_processInput($entity);
        // TODO: workaround loi ko luu user_id o driver
        $userId = $entity->tryGet('adminUser')->id;
        if (isset($userId) && $entity->user_id == 0) {
            $entity->user_id = $userId;
        }

        $entity->partner_id = $this->getCurrentUser()->partner_id;
        $entity->save();

        $vehicleTeamIds = $this->getVehicleTeamRepository()->getIDsByCodes($data['vehicle_team_codes']);
        if (!empty($vehicleTeamIds)) {
            $entity->vehicleTeams()->sync($vehicleTeamIds);
        }

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

        $vehicleTeams = $this->getVehicleTeamRepository()->all(['code', 'name']);
        $customerExport = new PartnerDriverExport($this->getRepository(), $data);
        $update = Request::has('update') ? true : false;
        return $customerExport->exportFromTemplate($update, $vehicleTeams);
    }

    public function export()
    {
        $driverExport = new PartnerDriverExport($this->getRepository(), $this->_getDataIndex(false));
        return $driverExport->exportFromTemplate();
    }

    public function _mappingDataImport($data, $update)
    {
        $numberCode = 0;
        $driverImport = new PartnerDriverImport();
        $listDriverCode = [];
        foreach ($data as &$driver) {
            $driver = $driverImport->map($driver);
            if ($driver != null && empty($driver['code'])) $numberCode++;
            if ($update) {
                $listDriverCode[] = $driver['code'];
            }
        }

        $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_driver'), $numberCode);
        $i = 0;
        $listDriver = $update ? $this->getRepository()->search(['code_in' => $listDriverCode])->get() : null;

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
                $driverItem = $listDriver->where('code', $item['code'])->first();
                if (isset($driverItem)) {
                    $item['id'] = $driverItem->id;
                    $item['user_id'] = $driverItem->user_id;
                    $item['adminUser']['id'] = $driverItem->user_id;
                }
            }
        }
        return $data;
    }

    public function generateCodeForExcels($data)
    {
        $numberCode = 0;
        $driverImport = new PartnerDriverImport();
        foreach ($data as &$driver) {
            $driver = $driverImport->map($driver);
            if ($driver != null && empty($driver['code'])) $numberCode++;
        }

        $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_driver'), $numberCode);
        $i = 0;
        $results = [];
        foreach ($data as $item) {
            if ($item != null) {
                if (empty($item['code'])) {
                    $item['code'] = $systemCodeList[$i];
                    $i++;
                }
                $results[] = $item;
            }
        }
        return $results;
    }

    protected function _processQuickSave($id, $field, $value)
    {
        $entity = $this->getRepository()->getItemById($id);
        if ($entity != null) {
            $entity->$field = $value;
            $entity = $this->_processInput($entity);
            $entity->save();
        }
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 10/4/2020
    public function exportCustomTemplate()
    {
        $ids = Request::get('ids');
        $templateId = Request::get('templateId');
        $startDate = Request::get('startDate');
        $endDate = Request::get('endDate');
        $parameter = new stdClass();
        $parameter->startDate = $startDate;
        $parameter->endDate = $endDate;
        $arr = explode(",", $ids);

        $results = [];
        $template = $this->getTemplateRepository()->getTemplateByTemplateId($templateId);

        $data = $this->getRepository()->getExportByIDs($arr, $parameter, $template);
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->{'id'},
                'name' => $item->{'code'},
                'data' => $item
            ];
        }
        $dataExport = new TemplateExport(
            $this->getTemplateRepository(),
            $results
        );
        return $dataExport->exportCustomTemplate($templateId);
    }
}
