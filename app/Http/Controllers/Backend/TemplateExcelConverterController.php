<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\TemplateExcelConverterMapping;
use App\Repositories\TemplateExcelConverterMappingRepository;
use App\Repositories\TemplateExcelConverterRepository;
use App\Repositories\ExcelColumnConfigRepository;

use Exception;
use Illuminate\Support\Facades\DB;

class TemplateExcelConverterController extends BackendController
{

    protected $_fieldsSearch = ['id', 'title'];
    protected $_templateExcelConverterMappingRepository;
    protected $_excelColumnConfigRepository;


    public function getTemplateExcelConvertertMappingRepository()
    {
        return $this->_templateExcelConverterMappingRepository;
    }

    public function setTemplateExcelConvertertMappingRepository($templateExcelConverterMappingRepository): void
    {
        $this->_templateExcelConverterMappingRepository = $templateExcelConverterMappingRepository;
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
        TemplateExcelConverterRepository $TemplateExcelConverterRepository,
        TemplateExcelConverterMappingRepository $templateExcelConverterMappingRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository
    ) {
        parent::__construct();
        $this->setRepository($TemplateExcelConverterRepository);
        $this->setTemplateExcelConvertertMappingRepository($templateExcelConverterMappingRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);

        $this->setBackUrlDefault('template-excel-converter.index');
        $this->setConfirmRoute('template-excel-converter.confirm');
        $this->setMenu('management');
        $this->setTitle(trans('models.template_excel_converter.name'));
    }

    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        DB::beginTransaction();
        try {
            $data = $this->_getFormData();
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $entity->save();

            $this->_processCreateRelation($data, $entity);
            // add new
            $this->fireEvent('after_store', $entity);
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (Exception $e) {
            logError($e);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

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
            $data = $this->_getFormData();
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity->save();

            $this->_processCreateRelation($data, $entity);
            DB::commit();
            $this->fireEvent('after_update', $entity);
            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (Exception $e) {
            logError($e);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }


    public function _deleteRelations($entity)
    {
        $this->getTemplateExcelConvertertMappingRepository()->where([
            'template_excel_converter_id' => $entity->id
        ])->forceDelete();
    }

    public function _processCreateRelation($data, $entity)
    {
        $entity->templateExcelConverterMappings = isset($data['templateExcelConverterMappings']) ? $data['templateExcelConverterMappings'] : [];
        $entity->templateExcelConverterSheets = isset($data['templateExcelConverterSheets']) ? $data['templateExcelConverterSheets'] : [];

        $this->_saveTemplatePaymentMappings($entity);
    }

    public function _prepareForm()
    {
        $excelColumnConfigMappings = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order')->excelColumnMappingConfigs;
        $this->setViewData([
            'excelColumnConfigMappings' => $excelColumnConfigMappings
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();
        $entity->templateExcelConverterMappings = $this->_prepareDataMappings($entity);

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => false,
        ]);
        $excelColumnConfigMappings = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order')->excelColumnMappingConfigs;
        $this->setViewData([
            'excelColumnConfigMappings' => $excelColumnConfigMappings
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);
        $entity->templateExcelConverterMappings = isset($entity->templateExcelConverterMappings) ? $entity->templateExcelConverterMappings->toArray() : [];
        $this->setEntity($entity);
        $this->setViewData([
            'show_history' => true
        ]);
        $excelColumnConfigMappings = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order')->excelColumnMappingConfigs;
        $this->setViewData([
            'excelColumnConfigMappings' => $excelColumnConfigMappings
        ]);
    }

    protected function _prepareEdit($id = null)
    {
        $attributes = $this->_getFormData(false);

        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $currentTemplateExcelConverterMappings = [];
        if (!isset($attributes['excelColumnConfigMappings'])) {
            $currentTemplateExcelConverterMappings = isset($entity->templateExcelConverterMappings) ? $entity->templateExcelConverterMappings : [];
        }

        $templateExcelConverterMappings = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order')->excelColumnMappingConfigs;
        $this->setViewData([
            'templateExcelConverterMappings' => $templateExcelConverterMappings,
            'currentTemplateExcelConverterMappings' => $currentTemplateExcelConverterMappings
        ]);
        $this->setEntity($entity);
        return $parent;
    }

    /**
     * Lưu bảng ánh xạ chi phí
     * @param $entity
     */
    protected function _saveTemplatePaymentMappings($entity)
    {
        $data = $this->_prepareDataMappings($entity);

        $templateExcelConverterMappingModes = [];
        foreach ($data as $item) {
            $templateExcelConverterMappingModes[] = new TemplateExcelConverterMapping($item);
        }
        $entity->templateExcelConverterMappings()->delete();
        $entity->templateExcelConverterMappings()->saveMany($templateExcelConverterMappingModes);
    }

    public function _prepareDataMappings($entity)
    {
        $templateExcelConverterMappings = $entity->templateExcelConverterMappings;

        if (empty($templateExcelConverterMappings)) {
            return [];
        }
        $data = [];
        foreach ($templateExcelConverterMappings as $templateExcelConverterMapping) {
            if (!empty($templateExcelConverterMapping['field'])) {
                $data[] = [
                    'field' => $templateExcelConverterMapping['field'],
                    'column_index' => null,
                    'formula' => $templateExcelConverterMapping['formula'],
                ];
            }
        }
        if (isset($entity->templateExcelConverterSheets)) {
            $templateExcelConverterSheets = is_array($entity->templateExcelConverterSheets) ? collect($entity->templateExcelConverterSheets) : $entity->templateExcelConverterSheets;
            foreach ($templateExcelConverterSheets as $templateExcelConverterSheet) {
                if (!empty($templateExcelConverterSheet['column_index'])) {
                    $data[] = [
                        'field' => null,
                        'column_index' => $templateExcelConverterSheet['column_index'],
                        'formula' => $templateExcelConverterSheet['formula'],
                    ];
                }
            }
        }
        return $data;
    }
}
