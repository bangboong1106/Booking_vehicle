<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Support\Facades\Auth;

use App\Common\AppConstant;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\RepairTicket;
use App\Repositories\AccessoryRepository;
use App\Repositories\DriverRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ExcelColumnConfigRepository;
use App\Repositories\RepairTicketRepository;
use App\Repositories\ColumnConfigRepository;
use App\Imports\RepairTicketImport;
use App\Exports\RepairTicketExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class ContractController
 * @package App\Http\Controllers\Backend
 */
class RepairTicketController extends BackendController
{
    protected $_columnConfigRepository;
    protected $_accessoryRepository;
    protected $_driverRepository;
    protected $_vehicleRepository;
    protected $_excelColumnConfigRepository;

    public function getColumnConfigRepository()
    {
        return $this->_columnConfigRepository;
    }

    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->_columnConfigRepository = $columnConfigRepository;
    }

    public function getAccessoryRepository()
    {
        return $this->_accessoryRepository;
    }

    public function setAccessoryRepository($accessoryRepository): void
    {
        $this->_accessoryRepository = $accessoryRepository;
    }

    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    public function getExcelColumnConfigRepository()
    {
        return $this->_excelColumnConfigRepository;
    }

    public function setExcelColumnConfigRepository($excelColumnConfigRepository): void
    {
        $this->_excelColumnConfigRepository = $excelColumnConfigRepository;
    }

    public function __construct(
        RepairTicketRepository $repairTicketRepository,
        AccessoryRepository $accessoryRepository,
        ColumnConfigRepository $columnConfigRepository,
        DriverRepository $driverRepository,
        VehicleRepository $vehicleRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository
    )
    {
        parent::__construct();
        $this->setRepository($repairTicketRepository);
        $this->setAccessoryRepository($accessoryRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setDriverRepository($driverRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);

        $this->setBackUrlDefault('repair-ticket.index');
        $this->setConfirmRoute('repair-ticket.confirm');
        $this->setMenu('vehicles');
        $this->setTitle(trans('models.repair_ticket.name'));
        $this->setExcel(true);
        $this->setExcelUpdate(true);
        $this->setViewData([
            'urlTemplate' => route('repair-ticket.exportTemplate'),
        ]);
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_repair_ticket'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'accessories' => $this->getAccessoryRepository()->all(['id', 'name'])->sortBy('name'),

        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $entity = $this->getEntity();
        $entity->name_of_vehicle_id = $entity->vehicle->reg_no;
        $entity->name_of_driver_id = $entity->driver->full_name;
        foreach ($entity->items as $item) {
            $item->name_of_accessory_id = $item->accessory->name;
        }
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } elseif ($id == -1) {
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_repair_ticket'));
        }
        $this->setViewData([
            'code' => $code
        ]);
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = $this->_findOrNewEntity($id, false, true);
        return $this->_processInputData($entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        return $this->_processInputData($entity);
    }

    public function _processInputData($entity)
    {
        $entity->repair_date = empty($entity->repair_date) ? null : AppConstant::convertDate($entity->repair_date, 'Y-m-d');
        foreach ($entity->items as $item) {
            $item->next_repair_date = empty($item->next_repair_date) ? null : AppConstant::convertDate($item->next_repair_date, 'Y-m-d');
        }
        return $entity;
    }

    public function exportTemplate()
    {
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);

        if (isset($ids)) {
            $sort_field = array_key_exists('sort_field', $data) ? $data["sort_field"] : 'repair_ticket.id';
            $sort_type = array_key_exists('sort_type', $data) ? $data["sort_type"] : 'desc';
            $data = [];
            $data['repair_ticket|id_in'] = explode(',', $ids);
            $data["sort_field"] = $sort_field;
            $data["sort_type"] = $sort_type;
        }

        $update = Request::has('update') ? true : false;

        $export = new RepairTicketExport(
            $this->getRepository(),
            $this->getAccessoryRepository(),
            $this->getDriverRepository(),
            $this->getVehicleRepository(),
            $this->getExcelColumnConfigRepository(),
            $data
        );
        $export->is_update = $update;
        $export->excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'repair_ticket');
        $currentUser = $this->getCurrentUser();
        return $export->exportFileTemplate($currentUser->id);
    }

    protected function _processDataImport($update = false)
    {
        $data = json_decode(request()->get('data'));

        $vehicleDriverList = $this->getVehicleRepository()->getVehicleAndDriverList();
        $itemImport = new RepairTicketImport($vehicleDriverList);

        $excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'repair_ticket');

        foreach ($data as $key => &$row) {
            $row = $itemImport->map($row, $excelColumnConfig, $data);

            $row['importable'] = true;
            $row['failures'] = [];
            $row['warning'] = [];
        }

        $data = $itemImport->processItemImport($data);

        if ($update == true) {
            $this->getRepository()->getValidator()->validateImportUpdate($data);
        } else {
            $this->getRepository()->getValidator()->validateImport($data);
        }
        $errors = $this->getRepository()->getValidator()->errorsBag();

        foreach ($data as $key => &$row) {
            if (!empty($errors))
                foreach ($errors->get($key . '.*') as $message) {
                    $row['failures'][] = Arr::get($message, 0);
                }

            if (empty($row['failures'])) continue;
            $row['importable'] = false;
            $driverList[] = $row['primary_driver'];
            $driverList[] = $row['secondary_driver'];
        }

        $currentController = $this->getCurrentControllerName();
        $backendExcel = session(self::SESSION_EXCEL, []);
        $backendExcel[$currentController] = $data;
        $backendExcel[$currentController . '_type'] = $update;
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'excelColumnMappingConfigs' => $excelColumnConfig->excelColumnMappingConfigs,
            'entities' => $data,
        ]);
        return [
            'content' => $this->render('backend.repair_ticket.import')->render(),
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
        ];
    }

    protected function _processFileImport()
    {
        $backendExcel = session(self::SESSION_EXCEL, array());
        $currentController = $this->getCurrentControllerName();
        $dataList = $backendExcel[$currentController];
        $update = $backendExcel[$currentController . '_type'];

        $ignoreCount = 0;
        $total = 0;
        try {
            DB::beginTransaction();

            $accessories = $this->getAccessoryRepository()->search()->get();
            $drivers = $this->getDriverRepository()->search()->pluck('id', 'code');
            $vehicles = $this->getVehicleRepository()->search()->pluck('id', 'reg_no');

            $accessoryList = [];
            foreach ($accessories as $item) {
                $name = mb_strtoupper(Str::slug(str_replace("-", "", $item->name)));
                $accessoryList[$name] = $item;
            }

            $total = count($dataList);
            foreach ($dataList as $data) {
                if (!$data['importable']) {
                    $ignoreCount++;
                    continue;
                }

                //Luu item
                $repairTicketItems = [];
                foreach ($data['items'] as $item) {
                    $accessory = $accessoryList[mb_strtoupper(Str::slug(str_replace("-", "", trim($item['accessory_code']))))];
                    if (isset($accessory)) {
                        $repairTicketItems[] = [
                            'accessory_id' => $accessory->id,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'amount' => $item['amount'],
                            'next_repair_date' => isset($item['next_repair_date']) ? AppConstant::convertDate($item['next_repair_date'], 'Y-m-d') : null,
                            'next_repair_distance' => $item['next_repair_distance']
                        ];
                    }
                }

                if ($update) {
                    $entity = $this->getRepository()->getItemByCode($data['code']);
                    $data['id'] = $entity->id;
                }
                $data['vehicle_id'] = isset($vehicles[$data['vehicle']]) ? $vehicles[$data['vehicle']] : 0;
                $data['driver_id'] = isset($drivers[$data['driver']]) ? $drivers[$data['driver']] : 0;
                $entity = $this->getRepository()->findFirstOrNew($data);
                $entity = $this->_processInputData($entity);
                $entity->save();

                $entity->repairTicketItems()->detach();
                $entity->repairTicketItems()->sync($repairTicketItems);

            }

            DB::commit();
        } catch (Exception $e) {
            logError($e);
            $ignoreCount = $total;
            DB::rollBack();
        }

        $file = request()->file;
        if (!empty($file)) {
            app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $update, $this->getTitle());
        }

        unset($backendExcel[$currentController]);
        unset($backendExcel[$currentController . '_type']);
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'total' => $total,
            'done' => $total - $ignoreCount,
        ]);

        return [
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
            'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
        ];
    }
}
