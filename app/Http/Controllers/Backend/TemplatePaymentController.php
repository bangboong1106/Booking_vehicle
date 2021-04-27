<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\File;
use App\Model\Entities\ReceiptPayment;
use App\Model\Entities\TemplatePaymentMapping;
use App\Repositories\TemplatePaymentMappingRepository;
use App\Repositories\TemplatePaymentRepository;
use App\Repositories\TemplateRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Input;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use Response;

class TemplatePaymentController extends BackendController
{

    protected $_fieldsSearch = ['id', 'title'];
    protected $_templatePaymentMappingRepos;

    /**
     * @return mixed
     */
    public function getTemplatePaymentMappingRepos()
    {
        return $this->_templatePaymentMappingRepos;
    }

    /**
     * @param mixed $templatePaymentMappingRepos
     */
    public function setTemplatePaymentMappingRepos($templatePaymentMappingRepos): void
    {
        $this->_templatePaymentMappingRepos = $templatePaymentMappingRepos;
    }

    public function __construct(TemplatePaymentRepository $templatePaymentRepository
        , TemplatePaymentMappingRepository $templatePaymentMappingRepository)
    {
        parent::__construct();
        $this->setRepository($templatePaymentRepository);
        $this->setTemplatePaymentMappingRepos($templatePaymentMappingRepository);
        $this->setBackUrlDefault('template-payment.index');
        $this->setConfirmRoute('template-payment.confirm');
        $this->setMenu('management');
        $this->setTitle(trans('models.template_payment.name'));

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
            $data = $this->_getFormData();
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity->save();
            //$this->_saveRelations($entity);

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
            $data = $this->_getFormData();
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity->save();
            // $this->_saveRelations($entity, 'edit');

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

    protected function _moveFileFromTmpToMedia(&$entity)
    {
        if (empty($entity->file_id)) {
            return;
        }
        app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->file_id, 'template_payment');
    }

    public function _deleteRelations($entity)
    {
        //Xóa bang anh xa
        $this->getTemplatePaymentMappingRepos()->deleteWhere([
            'template_payment_id' => $entity->id
        ]);
    }

    public function _processCreateRelation($data, $entity)
    {
        //Lưu bảng ánh xạ chi phí
        $entity->templatePaymentMappings = isset($data['templatePaymentMappings']) ? $data['templatePaymentMappings'] : [];
        $this->_saveTemplatePaymentMappings($entity);
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true)
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();
        $entity->templatePaymentMappings = $this->_prepareDataMappings($entity);

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => false,
        ]);
        $receiptPaymentList = ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true);
        $this->setViewData([
            'receiptPayments' => $receiptPaymentList
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        $entity->templatePaymentMappings = isset($entity->templatePaymentMappings) ? $entity->templatePaymentMappings->toArray() : [];

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => true
        ]);
        $receiptPaymentList = ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true);
        $this->setViewData([
            'receiptPayments' => $receiptPaymentList
        ]);
    }

    protected function _prepareEdit($id = null)
    {
        $attributes = $this->_getFormData(false);

        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $currentTemplatePaymentMappings = [];
        if (isset($attributes['templatePaymentMappings'])) {
            foreach ($entity->templatePaymentMappings as $templatePaymentMapping) {
                $currentTemplatePaymentMappings[$templatePaymentMapping['receipt_payment_id']] = $templatePaymentMapping['column_index'];
            }
        } else {
            $currentTemplatePaymentMappings = isset($entity->templatePaymentMappings) ? $entity->templatePaymentMappings->pluck('column_index', 'receipt_payment_id')->toArray() : [];
        }

        $receiptPaymentList = ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true);
        $this->setViewData([
            'receiptPayments' => $receiptPaymentList,
            'currentTemplatePaymentMappings' => $currentTemplatePaymentMappings
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
        $templatePaymentMappingModes = [];
        foreach ($data as $item) {
            $templatePaymentMappingModes[] = new TemplatePaymentMapping($item);
        }
        $entity->templatePaymentMappings()->delete();
        $entity->templatePaymentMappings()->saveMany($templatePaymentMappingModes);

    }

    public function _prepareDataMappings($entity)
    {
        $templatePaymentMappings = $entity->templatePaymentMappings;

        if (empty($templatePaymentMappings)) {
            return [];
        }
        $data = [];
        foreach ($templatePaymentMappings as $templatePaymentMapping) {
            if (!empty($templatePaymentMapping['receipt_payment_id']) && !empty($templatePaymentMapping['column_index']))
                $data[] = [
                    'template_payment_id' => $entity->id,
                    'receipt_payment_id' => $templatePaymentMapping['receipt_payment_id'],
                    'column_index' => strtoupper($templatePaymentMapping['column_index']),
                ];
        }

        return $data;
    }

}