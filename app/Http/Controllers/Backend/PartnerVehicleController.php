<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\PartnerVehicleExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\PartnerVehicleImport;
use App\Model\Entities\GpsSyncLog;
use App\Model\Entities\Vehicle;
use App\Model\Entities\VehicleGroup;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\DriverRepository;
use App\Repositories\DriverVehicleRepository;
use App\Repositories\FileRepository;
use App\Repositories\GpsCompanyRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\PartnerVehicleRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleConfigFileRepository;
use App\Repositories\VehicleConfigSpecificationRepository;
use App\Repositories\VehicleFileRepository;
use App\Repositories\VehicleGroupRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleSpecificationRepository;
use App\Repositories\WardRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use SoapClient;
use stdClass;

/**
 * Class PartnerVehicleController
 * @package App\Http\Controllers\Backend
 */
class PartnerVehicleController extends BackendController
{
    /**
     * @var VehicleRepository
     *
     */
    protected $vehicleRepository;
    protected $vehicleFileRepository;
    protected $vehicleConfigFileRepository;
    protected $vehicleSpecificationRepository;
    protected $vehicleConfigSpecificationRepository;
    protected $vehicleGroupRepository;
    protected $provinceRepository;
    protected $districtRepository;
    protected $wardRepository;
    protected $fileRepository;
    protected $driverVehicleRepository;
    protected $driverRepository;
    protected $orderHistoryRepository;
    protected $orderRepository;
    protected $columnConfigRepository;
    protected $gpsCompanyRepository;
    protected $_templateRepository;
    protected $partnerRepository;

    /**
     * @return DriverVehicleRepository
     */
    public function getDriverVehicleRepository()
    {
        return $this->driverVehicleRepository;
    }

    /**
     * @param null $driverVehicleRepository
     */
    public function setDriverVehicleRepository($driverVehicleRepository): void
    {
        $this->driverVehicleRepository = $driverVehicleRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->driverRepository;
    }

    /**
     * @param null $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->driverRepository = $driverRepository;
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
     * @return VehicleFileRepository
     */
    public function getVehicleFileRepository(): VehicleFileRepository
    {
        return $this->vehicleFileRepository;
    }

    /**
     * @param VehicleFileRepository $vehicleFileRepository
     */
    public function setVehicleFileRepository(VehicleFileRepository $vehicleFileRepository): void
    {
        $this->vehicleFileRepository = $vehicleFileRepository;
    }

    /**
     * @return VehicleConfigFileRepository
     */
    public function getVehicleConfigFileRepository(): VehicleConfigFileRepository
    {
        return $this->vehicleConfigFileRepository;
    }

    /**
     * @param VehicleConfigFileRepository $vehicleConfigFileRepository
     */
    public function setVehicleConfigFileRepository(VehicleConfigFileRepository $vehicleConfigFileRepository): void
    {
        $this->vehicleConfigFileRepository = $vehicleConfigFileRepository;
    }

    /**
     * @return VehicleSpecificationRepository
     */
    public function getVehicleSpecificationRepository(): VehicleSpecificationRepository
    {
        return $this->vehicleSpecificationRepository;
    }

    /**
     * @param VehicleSpecificationRepository $vehicleSpecificationRepository
     */
    public function setVehicleSpecificationRepository(VehicleSpecificationRepository $vehicleSpecificationRepository): void
    {
        $this->vehicleSpecificationRepository = $vehicleSpecificationRepository;
    }

    /**
     * @return VehicleConfigSpecificationRepository
     */
    public function getVehicleConfigSpecificationRepository(): VehicleConfigSpecificationRepository
    {
        return $this->vehicleConfigSpecificationRepository;
    }

    /**
     * @param VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository
     */
    public function setVehicleConfigSpecificationRepository(VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository): void
    {
        $this->vehicleConfigSpecificationRepository = $vehicleConfigSpecificationRepository;
    }

    /**
     * @return VehicleGroupRepository
     */
    public function getVehicleGroupRepository(): VehicleGroupRepository
    {
        return $this->vehicleGroupRepository;
    }

    /**
     * @param VehicleGroupRepository $vehicleGroupRepository
     */
    public function setVehicleGroupRepository(VehicleGroupRepository $vehicleGroupRepository): void
    {
        $this->vehicleGroupRepository = $vehicleGroupRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository(): ProvinceRepository
    {
        return $this->provinceRepository;
    }

    /**
     * @param ProvinceRepository $provinceRepository
     */
    public function setProvinceRepository(ProvinceRepository $provinceRepository): void
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @return DistrictRepository
     */
    public function getDistrictRepository(): DistrictRepository
    {
        return $this->districtRepository;
    }

    /**
     * @param DistrictRepository $districtRepository
     */
    public function setDistrictRepository(DistrictRepository $districtRepository): void
    {
        $this->districtRepository = $districtRepository;
    }

    /**
     * @return WardRepository
     */
    public function getWardRepository(): WardRepository
    {
        return $this->wardRepository;
    }

    /**
     * @param WardRepository $wardRepository
     */
    public function setWardRepository(WardRepository $wardRepository): void
    {
        $this->wardRepository = $wardRepository;
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

    /**
     * VehicleController constructor.
     * @param PartnerVehicleRepository $partnerVehicleRepository
     * @param VehicleFileRepository $vehicleFileRepository
     * @param VehicleConfigFileRepository $vehicleConfigFileRepository
     * @param VehicleGroupRepository $vehicleGroupRepository
     * @param ProvinceRepository $provinceRepository
     * @param DistrictRepository $districtRepository
     * @param WardRepository $wardRepository
     * @param VehicleSpecificationRepository $vehicleSpecificationRepository
     * @param VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository
     * @param FileRepository $fileRepository
     * @param DriverVehicleRepository $driverVehicleRepository
     * @param DriverRepository $driverRepository
     * @param OrderHistoryRepository $orderHistoryRepository
     * @param OrderRepository $orderRepository
     * @param ColumnConfigRepository $columnConfigRepository
     * @param GpsCompanyRepository $gpsCompanyRepository
     * @param TemplateRepository $templateRepository
     * @param PartnerRepository $partnerRepository
     */
    public function __construct(
        PartnerVehicleRepository $partnerVehicleRepository,
        VehicleFileRepository $vehicleFileRepository,
        VehicleConfigFileRepository $vehicleConfigFileRepository,
        VehicleGroupRepository $vehicleGroupRepository,
        ProvinceRepository $provinceRepository,
        DistrictRepository $districtRepository,
        WardRepository $wardRepository,
        VehicleSpecificationRepository $vehicleSpecificationRepository,
        VehicleConfigSpecificationRepository $vehicleConfigSpecificationRepository,
        FileRepository $fileRepository,
        DriverVehicleRepository $driverVehicleRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        OrderRepository $orderRepository,
        ColumnConfigRepository $columnConfigRepository,
        GpsCompanyRepository $gpsCompanyRepository,
        TemplateRepository $templateRepository,
        PartnerRepository $partnerRepository
    )
    {
        parent::__construct();
        $this->setRepository($partnerVehicleRepository);
        $this->setBackUrlDefault('partner-vehicle.index');
        $this->setConfirmRoute('partner-vehicle.confirm');
        $this->setMenu('partner_vehicle');
        $this->setTitle(trans('models.vehicle.name'));

        $this->setVehicleFileRepository($vehicleFileRepository);
        $this->setVehicleConfigFileRepository($vehicleConfigFileRepository);
        $this->setVehicleSpecificationRepository($vehicleSpecificationRepository);
        $this->setVehicleConfigSpecificationRepository($vehicleConfigSpecificationRepository);
        $this->setVehicleGroupRepository($vehicleGroupRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setWardRepository($wardRepository);
        $this->fileRepository = $fileRepository;
        $this->setDriverVehicleRepository($driverVehicleRepository);
        $this->setDriverRepository($driverRepository);
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->gpsCompanyRepository = $gpsCompanyRepository;
        $this->setTemplateRepository($templateRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setPartnerRepository($partnerRepository);

        $this->setMap(true);
        $this->setExcel(true);
        $this->setViewData([
            'exampleName' => 'Danh_sach_xe.xlsx',
            'urlTemplate' => route('vehicle.exportTemplate')
        ]);
        $this->setMap(true);
        $this->setAuditing(true);
        $this->setDeleted(true);
        $this->setExcelUpdate(true);
    }

    public function _prepareIndex()
    {
        $userId = $this->getCurrentUser()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_vehicle'));
        $gps_company_list = $this->gpsCompanyRepository->search()->get()->pluck('name', 'id');
        $this->setViewData([
            'vehicle_groups' => VehicleGroup::getNestedList('name', 'id', ''),
            'drivers' => $this->getDriverRepository()->search()->pluck('full_name', 'id')->toArray(),
            'gps_company_list' => $gps_company_list,
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
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
            $entity = $this->beforeSave($entity);

            $entity->save();
            $this->saveVehicleFile($attributes, $entity->id);
            $this->saveVehicleSpecification($attributes, $entity->id);
            $this->saveDriverVehicle($attributes, $entity);
            $this->_saveRelations($entity);

            $this->fireEvent('after_store', $entity);
            // add new
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (Exception $e) {
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
            $entity = $this->beforeSave($entity);
            $entity->save();

            // fire before save relation
            $this->saveVehicleFile($attributes, $entity->id);
            $this->saveVehicleSpecification($attributes, $entity->id);
            $this->saveDriverVehicle($attributes, $entity);
            $this->_saveRelations($entity);

            // fire after save relation
            $this->fireEvent('after_update', $entity);
            // add new
            DB::commit();

            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }

    public function beforeSave($entity)
    {
        if (isset($entity->reg_no)) {
            $entity->vehicle_plate = str_replace(array("-", " ", "."), "", $entity->reg_no);
        }

        $entity->partner_id = $this->getCurrentUser()->partner_id;
        return $entity;
    }

    public function saveVehicleFile($attributes, $vehicle_id)
    {
        $vehicle_config_list = $this->getVehicleConfigFileRepository()->getAll();

        $this->getVehicleFileRepository()->deleteWhere([
            'vehicle_id' => $vehicle_id
        ]);

        foreach ($vehicle_config_list as $vehicle_config) {

            $vehicle_file = $attributes['vehicle_file'][$vehicle_config->id];

            if (isset($vehicle_file['file_id'])) {
                $file_id_list = explode(';', $vehicle_file['file_id']);

                foreach ($file_id_list as $file_id) {

                    $entity = $this->getVehicleFileRepository()->findFirstOrNew([]);

                    $entity->vehicle_id = $vehicle_id;
                    $entity->file_id = $file_id;
                    $entity->vehicle_config_file_id = $vehicle_config->id;
                    $entity->note = empty($vehicle_file['note']) ? null : $vehicle_file['note'];
                    if ($vehicle_config->is_show_expired) {
                        $entity->expire_date = empty($vehicle_file['expire_date']) ? null : $vehicle_file['expire_date'];
                    }
                    if ($vehicle_config->is_show_register) {
                        $entity->register_date = empty($vehicle_file['register_date']) ? null : $vehicle_file['register_date'];
                    }

                    $entity->save();
                    app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->file_id, 'vehicles');
                }
            } else if (!empty($vehicle_file['note'])) {
                $entity = $this->getVehicleFileRepository()->findFirstOrNew([]);
                $entity->vehicle_id = $vehicle_id;
                $entity->note = empty($vehicle_file['note']) ? null : $vehicle_file['note'];
                if ($vehicle_config->is_show_expired) {
                    $entity->expire_date = empty($vehicle_file['expire_date']) ? null : $vehicle_file['expire_date'];
                }
                if ($vehicle_config->is_show_register) {
                    $entity->register_date = empty($vehicle_file['register_date']) ? null : $vehicle_file['register_date'];
                }

                $entity->save();
            }
        }
    }

    public function saveVehicleSpecification($attributes, $vehicle_id)
    {
        $vehicle_config_list = $this->getVehicleConfigSpecificationRepository()->getAll();
        foreach ($vehicle_config_list as $vehicle_config) {
            $vehicle_specification = $attributes['vehicle_specification'][$vehicle_config->id];
            if (isset($vehicle_specification['id'])) {
                $entity = $this->getVehicleSpecificationRepository()->findFirstOrNew(['id' => $vehicle_specification['id']]);
            } else {
                $entity = $this->getVehicleSpecificationRepository()->findFirstOrNew([]);
            }
            if (isset($vehicle_specification['value'])) {
                $entity->vehicle_id = $vehicle_id;
                $entity->vehicle_config_specification_id = $vehicle_config->id;
                $entity->value = $vehicle_specification['value'];
                $entity->save();
            } else if (isset($entity->id)) {
                // Xoa specification trong edit
                $entity->value = null;
                $entity->save();
            }
        }
    }

    public function saveDriverVehicle($attributes, $entity)
    {
        $entity->drivers()->sync($attributes['listDriver']);
    }

    public function _deleteRelations($entity)
    {
        //delete vehicle file
        $vehicleFiles = $this->getVehicleFileRepository()->getVehicleFileWithVehicleID($entity->id);
        if ($vehicleFiles != null) {
            foreach ($vehicleFiles as $vehicleFileEntity) {
                $fileEntity = $this->fileRepository->getFileWithID($vehicleFileEntity->file_id);
                if ($fileEntity != null)
                    $fileEntity->delete();
                $vehicleFileEntity->delete();
            }
        }
        // delete vehicle specification
        $vehicleSpecifications = $this->getVehicleSpecificationRepository()->getVehicleSpecificationWithVehicleID($entity->id);
        foreach ($vehicleSpecifications as $entitySpecification) {
            $entitySpecification->delete();
        }
        //delete driver_vehicle
        $driverVehicles = $this->driverVehicleRepository->getItemsByVehicleID($entity->id);
        if ($driverVehicles != null) {
            foreach ($driverVehicles as $entity) {
                $entity->delete();
            }
        }
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
        $entity->repair_date = empty($entity->repair_date) ? null : AppConstant::convertDate($entity->repair_date, 'Y-m-d');

        return $entity;
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return $prepare;
        }

        /** @var Vehicle $entity */
        $entity = $this->getEntity();
        $this->_getSelected($entity);
        return $prepare;
    }

    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $entity = $this->getEntity();
        /** @var Vehicle $entity */
        $this->_getSelected($entity);
        return $parent;
    }

    protected function _getSelected($entity)
    {
        // Get vehicle config list , vehicle file list
        $attributes = $this->_getFormData()->getAttributes();

        $vehicle_config_file_list = $this->getVehicleConfigFileRepository()->getAll();
        $vehicle_file_list = [];
        if (array_key_exists('vehicle_file', $attributes)) {
            // Put lại data file cho form khi nhấn back
            foreach ($vehicle_config_file_list as $vehicle_config_file) {
                $vehicle_file = $attributes['vehicle_file'][$vehicle_config_file->id];
                $vehicle_files = null;
                if (!empty($vehicle_file['file_id'])) {
                    $file_id_list = explode(';', $vehicle_file['file_id']);
                    if (!empty($file_id_list)) {
                        foreach ($file_id_list as $file_id) {
                            $file = $this->fileRepository->getFileWithID($file_id);
                            if (!is_null($file)) {
                                $vehicleFileEntity = [
                                    'file_name' => $file->file_name,
                                    'size' => $file->size,
                                    'file_id' => $file_id,
                                    'note' => $vehicle_file['note'],
                                    'register_date' => $vehicle_file['register_date'],
                                    'expire_date' => $vehicle_file['expire_date']
                                ];
                                $vehicle_files[] = $vehicleFileEntity;
                            } else {
                                unset($vehicle_files[$file_id]);
                            }
                        }
                    }
                } else if (!empty($vehicle_file['note'])) {
                    $vehicleFileEntity = [
                        'file_name' => "",
                        'size' => "",
                        'file_id' => null,
                        'note' => $vehicle_file['note'],
                        'register_date' => $vehicle_file['register_date'],
                        'expire_date' => $vehicle_file['expire_date']
                    ];
                    $vehicle_files[] = $vehicleFileEntity;
                }
                $vehicle_file_list[$vehicle_config_file->id] = collect($vehicle_files);
            }
        } else {
            foreach ($vehicle_config_file_list as $vehicle_config_file) {
                $vehicle_files = $this->getVehicleFileRepository()->getVehicleFile(
                    !empty($entity->id) ? $entity->id : '-1',
                    $vehicle_config_file->id
                );
                if ($vehicle_files) {
                    foreach ($vehicle_files as $key => $vehicle_file) {
                        if (!empty($vehicle_file->file_id)) {
                            $file = $this->fileRepository->getFileWithID($vehicle_file->file_id);
                            if (!is_null($file)) {
                                $vehicle_file['file_name'] = $file->file_name;
                                $vehicle_file['size'] = $file->size;
                            } else {
                                unset($vehicle_files[$key]);
                            }
                        }
                    }
                }
                $vehicle_file_list[$vehicle_config_file->id] = $vehicle_files;
            }
        }

        $vehicle_config_specification_list = $this->getVehicleConfigSpecificationRepository()->getAll();
        $vehicle_specification_list = [];
        if (array_key_exists('vehicle_specification', $attributes)) {
            $vehicle_specification_list = $attributes['vehicle_specification'];
        } else {
            foreach ($vehicle_config_specification_list as $vehicle_config_specification) {
                $vehicleSpecificationEnity = $this->getVehicleSpecificationRepository()->getVehicleSpecification($entity->id, $vehicle_config_specification->id);
                $vehicle_specification = null;
                if ($vehicleSpecificationEnity != null) {
                    $vehicle_specification['id'] = $vehicleSpecificationEnity->id;
                    $vehicle_specification['value'] = $vehicleSpecificationEnity->value;
                }
                $vehicle_specification_list[$vehicle_config_specification->id] = $vehicle_specification;
            }
        }

        $this->setViewData([
            'vehicle_config_file_list' => $vehicle_config_file_list,
            'vehicle_file_list' => $vehicle_file_list,
            'vehicle_config_specification_list' => $vehicle_config_specification_list,
            'vehicle_specification_list' => $vehicle_specification_list,
            'status_list' => config('system.vehicle_status'),
            'active_list' => config('system.vehicle_active'),
            'type_list' => config('system.vehicle_type'),
        ]);
    }

    protected function _prepareForm()
    {
        $user = $this->getCurrentUser();
        $this->setViewData([
            'vehicleGroupList' => $this->getVehicleGroupRepository()->getNestedList('name', 'id', ''),
            'gpsCompanyList' => $this->gpsCompanyRepository->search()->get()->pluck('name', 'id'),
            'driverList' => $this->getDriverRepository()->getAvailableDriversForUser($user->id, $user->partner_id)->pluck('full_name', 'id'),
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
            'partnerId' => $user->partner_id
        ]);
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $currentListDriver = $entity->drivers->pluck('id')->toArray();
        $entity->listDriver = empty($entity->listDriver) ? $currentListDriver : $entity->listDriver;

        $this->setViewData([
            'currentListDriver' => $currentListDriver
        ]);
        $this->setEntity($entity);
        return $parent;
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $attributes = $this->_getFormData()->getAttributes();

        $vehicle_config_file_list = $this->getVehicleConfigFileRepository()->getAll();
        $vehicle_file_list = [];

        if (array_key_exists('vehicle_file', $attributes))
            $vehicle_file_list = $attributes['vehicle_file'];

        $this->setViewData(['vehicle_config_file_list' => $vehicle_config_file_list]);
        $this->setViewData(['vehicle_file_list' => $vehicle_file_list]);

        $vehicle_config_specification_list = $this->getVehicleConfigSpecificationRepository()->getAll();

        $vehicle_specification_list = [];
        if (array_key_exists('vehicle_specification', $attributes))
            $vehicle_specification_list = $attributes['vehicle_specification'];

        $this->setViewData(['vehicle_config_specification_list' => $vehicle_config_specification_list]);
        $this->setViewData(['vehicle_specification_list' => $vehicle_specification_list]);

        // Driver List
        $this->_prepareForm();
        $entity = $this->getEntity();
        $entity->listDriver = empty($attributes['listDriver']) ? [] :
            $this->getDriverRepository()->search(['id_in' => $attributes['listDriver']])->get()->pluck('full_name', 'id');

        $this->setViewData([
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $vehicle_config_file_list = $this->getVehicleConfigFileRepository()->getAll();
        $vehicle_file_list = [];
        foreach ($vehicle_config_file_list as $vehicle_config_file) {
            $vehicle_files = $this->getVehicleFileRepository()->getVehicleFile($id, $vehicle_config_file->id);
            if ($vehicle_files) {
                foreach ($vehicle_files as $key => $vehicle_file) {
                    if (!empty($vehicle_file->file_id)) {
                        $file = $this->fileRepository->getFileWithID($vehicle_file->file_id);
                        if (!is_null($file)) {
                            $vehicle_file['file_name'] = $file->file_name;
                            $vehicle_file['size'] = $file->size;
                        } else {
                            unset($vehicle_files[$key]);
                        }
                    }
                }
            }
            $vehicle_file_list[$vehicle_config_file->id]['file_id'] = $vehicle_files->pluck('file_id')->implode(';');
            $vehicle_file_list[$vehicle_config_file->id]['note'] = $vehicle_files->first()['note'];
            $vehicle_file_list[$vehicle_config_file->id]['register_date'] = $vehicle_files->first()['register_date'];
            $vehicle_file_list[$vehicle_config_file->id]['expire_date'] = $vehicle_files->first()['expire_date'];
        }

        $vehicle_config_specification_list = $this->getVehicleConfigSpecificationRepository()->getAll();
        $vehicle_specification_list = [];
        foreach ($vehicle_config_specification_list as $vehicle_config_specification) {
            $vehicleSpecificationEnity = $this->getVehicleSpecificationRepository()->getVehicleSpecification($id, $vehicle_config_specification->id);
            $vehicle_specification = null;
            if ($vehicleSpecificationEnity != null) {
                $vehicle_specification['id'] = $vehicleSpecificationEnity->id;
                $vehicle_specification['value'] = $vehicleSpecificationEnity->value;
            }
            $vehicle_specification_list[$vehicle_config_specification->id] = $vehicle_specification;
        }

        // Driver List
        $entity = $this->getEntity();
        $entity->listDriver = $entity->drivers->pluck('full_name', 'id');

        $this->setViewData([
            'vehicle_config_file_list' => $vehicle_config_file_list,
            'vehicle_file_list' => $vehicle_file_list,
            'vehicle_config_specification_list' => $vehicle_config_specification_list,
            'vehicle_specification_list' => $vehicle_specification_list,
            'show_history' => true,
        ]);
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

        $vehicleExport = new PartnerVehicleExport(
            $this->getRepository(),
            $this->getDriverVehicleRepository(),
            $this->getDriverRepository(),
            $data
        );
        $update = Request::has('update') ? true : false;

        $currentUser = $this->getCurrentUser();
        $dataVehicleGroup = $this->getVehicleGroupRepository()->all(['code', 'name']);
        $dataDriver = $this->getDriverRepository()->getAvailableDriversForUser($currentUser->id, $currentUser->partner_id);
        $dataGpsCompany = $this->gpsCompanyRepository->all(['id', 'name']);
        return $vehicleExport->exportFileTemplate($dataVehicleGroup, $dataDriver, $dataGpsCompany, $update);
    }

    public function export()
    {
        $vehicleExport = new PartnerVehicleExport($this->getRepository(), $this->getDriverVehicleRepository(), $this->getDriverRepository(), $this->_getDataIndex(false));
        return $vehicleExport->exportFromTemplate();
    }

    protected function _mappingDataImport($data, $update)
    {
        $currentUser = $this->getCurrentUser();
        $vehicleImport = new PartnerVehicleImport();
        $drivers = $this->getDriverRepository()->search(['partner_id','=',$currentUser->partner_id])->pluck('id', 'code');
        $vehicleGroups = $this->getVehicleGroupRepository()->search()->pluck('id', 'code');
        $listRegNo = [];

        if (!empty($data)) {
            foreach ($data as $key => &$row) {
                $row = $vehicleImport->map($row);
                if (empty($row)) {
                    unset($data[$key]);
                    continue;
                }

                $row['importable'] = true;
                $row['failures'] = [];

                if ($update) {
                    $listRegNo[] = $row['reg_no'];
                }

                $row['group_id'] = isset($vehicleGroups[$row['group_code']]) ? $vehicleGroups[$row['group_code']] : 0;

                if (empty($row['driver_codes'])) continue;
                foreach ($row['driver_codes'] as $driverCode) {
                    isset($drivers[$driverCode]) ? $row['driver_ids'][] = $drivers[$driverCode] : null;
                }
            }
        }

        $listIds = $update ? $this->getRepository()->search(['reg_no_in' => $listRegNo])->get()->pluck('reg_no', 'id')->toArray() : [];

        foreach ($data as &$item) {
            $item['id'] = !empty($item['reg_no']) && in_array($item['reg_no'], $listIds) ?
                array_search($item['reg_no'], $listIds) : null;
        }

        return $data;
    }

    protected function _processFileImport()
    {
        try {
            $backendExcel = session(self::SESSION_EXCEL, array());
            $currentController = $this->getCurrentControllerName();
            $data = $backendExcel[$currentController];
            $update = $backendExcel[$currentController . '_type'];

            DB::beginTransaction();

            $ignoreCount = 0;
            $total = count($data);
            foreach ($data as $item) {
                if (!$item['importable']) {
                    $ignoreCount++;
                    continue;
                }
                $entity = $this->getRepository()->findFirstOrNew($item);
                $entity = $this->_processInput($entity);
                // Thêm bước lấy chuẩn biển số xe
                $entity = $this->beforeSave($entity);

                $entity->save();
                $this->_saveRelations($entity);
                if (!empty($item['driver_ids'])) {
                    $drivers = $entity->drivers->pluck('id', 'code')->toArray();
                    $res = array_unique(array_merge($drivers, $item['driver_ids']));
                    $entity->drivers()->sync($res);
                }
            }
            DB::commit();

            unset($backendExcel[$currentController]);
            unset($backendExcel[$currentController . '_type']);
            session([self::SESSION_EXCEL => $backendExcel]);

            $file = request()->file;
            if (!empty($file)) {
                app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $update, $this->getTitle());
            }

            $this->setViewData([
                'total' => $total,
                'done' => $total - $ignoreCount,
            ]);

            return [
                'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
                'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
            ];
        } catch (Exception $e) {
            logError($e);
            DB::rollBack();
        }
        return null;
    }

    public function getVehicleGpsHistory()
    {
        try {
            $data = $this->getGpsHistory(Request::all());

            $this->setData([
                'error_code' => '100',
                'data' => $data
            ]);
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e);
            $this->setData([
                'error_code' => '101',
            ]);
            return $this->renderJson();
        }
    }

    public function getGpsHistory($request)
    {
        $result = [];

        $from_date = isset($request['from_date']) ? $request['from_date'] : date('d-m-Y', (strtotime('-1 day', strtotime(now()))));
        $from_time = isset($request['from_time']) ? $request['from_time'] : date('H:i');
        $to_date = isset($request['to_date']) ? $request['to_date'] : date('d-m-Y');
        $to_time = isset($request['to_time']) ? $request['to_time'] : date('H:i');

        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = date('Y-m-d', strtotime($to_date));

        $gpsSyncLog = new GpsSyncLog();
        try {
            $vehicleId = $request['vehicle_id'];
            if (isset($vehicleId)) {
                $vehicle = $this->getRepository()->find($vehicleId);
                if (isset($vehicle)) {
                    // Binh Anh
                    if ($vehicle->gps_company_id == 1) {
                        // Initialize WS with the WSDL
                        $client = new SoapClient(env('GPS_BINH_ANH_WEB_SERVICE_WSDL', 'http://gps4.binhanh.com.vn/WebServices/BinhAnh.asmx?wsdl'));
                        $params = array(
                            'xnCode' => env('GPS_BINH_ANH_THANH_DAT_USER', '7213'),
                            'key' => env('GPS_BINH_ANH_THANH_DAT_KEY', 'pUrgARkgRakh4ZBAJqRdHCPKBTGMtf3KZdjU2fUA'),
                            'vehiclePlate' => $request['vehicle_plate'],
                            'fromDate' => date('c', strtotime($from_date . 'T' . $from_time)),
                            'toDate' => date('c', strtotime($to_date . 'T' . $to_time))
                        );
                        $gpsSyncLog->request = json_encode(['request' => $params]);
                        $response = $client->__soapCall(env('GPS_BINH_ANH_THANH_DAT_VEHICLE_FUNCTION_NAME', 'GetRouteByXNCodeWithAddress'), array($params));
                        $gpsSyncLog->response = json_encode(['response' => $response]);

                        if (isset($response)) {
                            $data = $response->GetRouteByXNCodeWithAddressResult;
                            if (isset($data)) {
                                $gpsSyncLog->error_message = $data->MessageResult;

                                if (strcasecmp('Success!', $gpsSyncLog->error_message) == 0) {
                                    $gpsSyncLog->response = '';
                                    $result = $this->processGPSBinhAnh($data->Routes);
                                }
                            }
                        }
                    } else if ($vehicle->gps_company_id == 2) {
                        // Vietmaps
                        if (isset($vehicle->gps_id)) {
                            $client = new \GuzzleHttp\Client();
                            $df = date('YmdHis', strtotime($from_date . $from_time));
                            $dt = date('YmdHis', strtotime($to_date . $to_time));
                            $request = $client->get('https://client-api.quanlyxe.vn/v3/tracking/getvehiclehistory?id=' . $vehicle->gps_id . '&from=' . $df . '&to=' . $dt . '&apikey=' . env('GPS_VIETMAPS_API_KEY'));
                            $response = $request->getBody();
                            $gpsSyncLog->request = json_encode(['request' => 'https://client-api.quanlyxe.vn/v3/tracking/getvehiclehistory?id=' . $vehicle->gps_id . '&fromTicks=' . $df . '&toTicks=' . $dt . '&apikey=' . env('GPS_VIETMAPS_API_KEY')]);

                            if ($response != null) {
                                $content = $response->getContents();
                                $gpsSyncLog->response = $content;
                                $data = json_decode($content);
                                if (!empty($data)) {
                                    $result = $this->processGPSVietMaps($data->Data);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            $gpsSyncLog->error_code = 'Exception';
            $gpsSyncLog->error_message = $exception->getMessage();
        }
        $gpsSyncLog->type_request = 'VEHICLE';
        $gpsSyncLog->save();

        return $result;
    }

    public function processGPSBinhAnh($data)
    {
        if (isset($data) && isset($data->BARoute)) {
            $baRoute = $data->BARoute;
            if (isset($baRoute)) {
                $index = 0;
                $result = [];
                foreach ($baRoute as $route) {
                    $result[] = [
                        $route->Address . '</br>' . date('d-m-Y H:i:s', strtotime($route->LocalTime)),
                        $route->Latitude,
                        $route->Longitude,
                        $index++
                    ];
                }
                return $result;
            }
        }
        return [];
    }

    public function processGPSVietMaps($data)
    {
        if (isset($data)) {
            $index = 0;
            $result = [];
            foreach ($data as $route) {
                $result[] = [
                    $route->Address . '</br>' . date('d-m-Y H:i:s', strtotime($route->SysTime)),
                    $route->Y,
                    $route->X,
                    $index++
                ];
            }
            return $result;
        }
        return [];
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

    /**
     * @param Vehicle $entity
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        if (typeOf($entity) === 'Vehicle') {
            $entity->drivers()->sync($entity->listDriver);
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
        $template = $this->getTemplateRepository()->getTemplateByTemplateId($templateId);

        $results = [];
        $data = $this->getRepository()->getExportByIDs($arr, $parameter, $template);
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->{'id'},
                'name' => $item->{'reg_no'},
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
