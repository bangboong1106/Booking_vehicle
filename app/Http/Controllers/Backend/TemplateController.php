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
use App\Model\Entities\GoodsType;
use App\Model\Entities\ReceiptPayment;
use App\Repositories\TemplateRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Input;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use Response;

class TemplateController extends BackendController
{

    protected $_fieldsSearch = ['id', 'title'];

    public function __construct(TemplateRepository $templateRepository)
    {
        parent::__construct();
        $this->setRepository($templateRepository);
        $this->setBackUrlDefault('template.index');
        $this->setConfirmRoute('template.confirm');
        $this->setMenu('management');
        $this->setTitle(trans('models.template.name'));
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'goodsList' => GoodsType::where('del_flag', '=', 0)->orderBy('title')->get(['id', 'title as name']),
            'costList' => ReceiptPayment::where('del_flag', '=', 0)->orderBy('sort_order')->where('type', '=', 2)->get(['id', 'name'])

        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData()->getAttributes();
        $type = 0;
        if (array_key_exists('list_item', $attributes)) {
            $type  = $attributes['type'];
            $selectedList = $attributes['list_item'];
        } else {
            $entity = $this->getRepository()->getTemplateByTemplateId($id);
            if ($entity != null) {
                $type  = $entity->type;
                if (!empty($entity->list_item)) {
                    $selectedList = explode(',',  $entity->list_item);
                }
            }
        }
        if (isset($selectedList) && !empty($selectedList)) {
            if ($type == 1) {
                $this->setViewData([
                    'selectedList' => GoodsType::whereIn('id', $selectedList)->get(['id', 'title as name']),

                ]);
            }
            if ($type == 3 || $type == 7) {
                $this->setViewData([
                    'selectedList' => ReceiptPayment::whereIn('id', $selectedList)->get(['id', 'name']),

                ]);
            }
        }
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $entity = $this->getRepository()->getTemplateByTemplateId($id);
        if ($entity != null) {
            $type  = $entity->type;
            if (!empty($entity->list_item)) {
                $selectedList = explode(',',  $entity->list_item);
            }
        }
        if (isset($selectedList) && !empty($selectedList)) {
            if ($type == 1) {
                $this->setViewData([
                    'selectedList' => GoodsType::whereIn('id', $selectedList)->get(['id', 'title as name']),

                ]);
            }
            if ($type == 3 || $type == 7) {
                $this->setViewData([
                    'selectedList' => ReceiptPayment::whereIn('id', $selectedList)->get(['id', 'name']),

                ]);
            }
        }
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $attributes = $this->_getFormData()->getAttributes();
        if (array_key_exists('list_item', $attributes)) {
            $type  = $attributes["type"];
            if (!empty($attributes["list_item"])) {
                $selectedList = explode(',',  $attributes["list_item"]);
            }
        }
        if (isset($selectedList) && !empty($selectedList)) {
            if ($type == 1) {
                $this->setViewData([
                    'selectedList' => GoodsType::whereIn('id', $selectedList)->get(['id', 'title as name']),

                ]);
            }
            if ($type == 3 || $type == 7) {
                $this->setViewData([
                    'selectedList' => ReceiptPayment::whereIn('id', $selectedList)->get(['id', 'name']),

                ]);
            }
        }
    }

    protected function _moveFileFromTmpToMedia(&$entity)
    {
        if (empty($entity->file_id)) {
            return;
        }
        app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->file_id, 'templates');
    }

    //Hiển thị danh sách mẫu in
    //CreatedBy nlhoang 4/4/2020
    public function printCustom()
    {
        $ids = Request::get('ids');
        $type = Request::get('type', config('constant.ORDER'));
        if ($ids == '') {
            return json_encode([
                'error' => 'Lỗi không có id'
            ]);
        }
        $isParam = ($type == config('constant.VEHICLE') || $type == config('constant.DRIVER') || $type == config('constant.DRIVER'));
        $templateList = $this->getRepository()->getTemplateByUserId(Auth::User()->id, $type);
        $this->setViewData([
            'templateList' => $templateList,
            'ids' => $ids,
            'type' => $type,
            'isParam' => $isParam
        ]);
        $html = [
            'content' => $this->render('layouts.backend.elements.template._template')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }


    // Tải danh sách trường trộn
    //CreatedBy nlhoang 05/04/2020
    public function mergeTemplate($type)
    {
        $objWriter = null;
        $objPHPExcel = null;
        try {


            $templateLayouts = $this->getRepository()->getTemplateLayoutByType($type);

            $fileTemplatePath = public_path('file/DanhSachTruongTron.xlsx');
            $fileType = PHPExcel_IOFactory::identify($fileTemplatePath);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($fileTemplatePath);

            $sheet = $objPHPExcel->setActiveSheetIndex(0);

            $index = 14;
            foreach ($templateLayouts as $templateLayout) {
                $sheet->setCellValue('A' . ($index), $templateLayout->display_name);
                $sheet->setCellValue('B' . ($index), $templateLayout->merge_name);
                $index++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if (!is_dir(public_path('file/export'))) {
                mkdir(public_path('file/export'));
            }

            $templateTypes = config('system.template_type');
            $templateType = array_key_exists($type, $templateTypes) ? $templateTypes[$type] : '';
            $templateExportType = vietnameseToLatin($templateType);
            $exportFilePath = 'danh-sach-truong-tron-' . $templateExportType . '_' . Carbon::now()->format('d-m-Y') . '.xlsx';
            $filePath = public_path('file/export/' . $exportFilePath);

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $objWriter->save($filePath);
            $nameFile = $exportFilePath;

            return Response::download($filePath, $nameFile, []);
        } catch (\PHPExcel_Reader_Exception $e) {
            logError($e);
        } catch (\PHPExcel_Exception $e) {
            logError($e);
        } catch (\Exception $e) {
            logError($e);
        } finally {
            if (isset($objPHPExcel)) {
                $objPHPExcel->disconnectWorksheets(); // Good to disconnect
                $objPHPExcel->garbageCollect(); // Add this too
            }
            if (isset($objWriter) && isset($objPHPExcel)) {
                unset($objWriter, $objPHPExcel);
            }
        }
    }
}
