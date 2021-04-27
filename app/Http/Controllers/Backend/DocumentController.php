<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\DocumentExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\DocumentImport;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TemplateRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Exception;

/**
 * Class DocumentController
 * @package App\Http\Controllers\Backend
 */
class DocumentController extends BackendController
{
    protected $_orderRepository;
    protected $_templateRepository;
    protected $_columnConfigRepository;


    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    public function getOrderRepository()
    {
        return $this->_orderRepository;
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
        DocumentRepository $documentRepository,
        OrderRepository $orderRepository,
        TemplateRepository $templateRepository,
        ColumnConfigRepository $columnConfigRepository
    ) {
        parent::__construct();
        $this->setRepository($documentRepository);
        $this->setOrderRepository($orderRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setColumnConfigRepository($columnConfigRepository);


        $this->setBackUrlDefault('document.index');
        $this->setMenu('order');
        $this->setTitle(trans('models.document.name'));
        $this->setExcel(false);
        $this->setExcelUpdate(false);
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_document'));
        $this->setViewData([
            'statuses' => config('system.order_status'),
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUpdate()
    {
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);

        if (isset($ids)) {
            $sort_field = array_key_exists('sort_field', $data) ? $data["sort_field"] : 'id';
            $sort_type =  array_key_exists('sort_type', $data) ? $data["sort_type"] : 'desc';
            $data = [];
            $data['id_in'] = explode(',', $ids);
            $data["sort_field"] = $sort_field;
            $data["sort_type"] = $sort_type;
        }

        $documentExport = new DocumentExport($this->getRepository(), $data);
        return $documentExport->exportFileTemplateUpdate();
    }

    protected function _processDataImport($update = false)
    {
        $data = json_decode(request()->get('data'));
        $orderImport = new DocumentImport();

        foreach ($data as $key => &$row) {
            $row = $orderImport->map($row);
            $row['importable'] = true;
            $row['failures'] = [];
        }

        $this->getRepository()->getValidator()->validateImportUpdate($data);
        $errors = $this->getRepository()->getValidator()->errorsBag();

        foreach ($data as $key => &$row) {
            $order = $this->getOrderRepository()->getOrdersByOrderCode($row['order_code']);
            if ($order && $order->is_lock == 1) {
                $row['importable'] = false;
                $row['failures'][] = 'Đơn hàng đang được khoá. Bạn không được phép cập nhật';
                $row['error']['order_code'] = 'Đơn hàng đang được khoá. Bạn không được phép cập nhật';
            }
            if (!empty($errors)) {
                foreach ($errors->get($key . '.*') as $message) {
                    $row['failures'][] = Arr::get($message, 0);
                }
            }
            if (empty($row['failures'])) {
                continue;
            }
            $row['importable'] = false;
        }
        $currentController = $this->getCurrentControllerName();
        $backendExcel = session(self::SESSION_EXCEL, []);
        $backendExcel[$currentController] = $data;
        $backendExcel[$currentController . '_type'] = $update;
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'entities' => $data
        ]);
        $html = [
            'content' => $this->render('backend.document.import')->render(),
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
        ];
        return $html;
    }

    protected function _processFileImport()
    {
        $backendExcel = session(self::SESSION_EXCEL, array());
        $currentController = $this->getCurrentControllerName();
        $dataList = $backendExcel[$currentController];
        $update = $backendExcel[$currentController . '_type'];

        $ignoreCount = 0;
        $total = count($dataList);
        try {
            DB::beginTransaction();

            foreach ($dataList as $data) {
                if (!$data['importable']) {
                    $ignoreCount++;
                    continue;
                }

                // Lấy order code. Nếu ko có order code thì lấy danh sách đơn hàng theo số đơn hàng
                $entities = $this->getOrderRepository()->getOrderDocumentByCode($data['order_code'], $data['order_no']);

                if (isset($entities) && sizeof($entities) > 0) {
                    foreach ($entities as $entity) {
                        $entity->is_collected_documents = $data['is_collected_documents'];
                        $entity->status_collected_documents = $data['status_collected_documents'];
                        if (!empty($data['date_collected_documents']))
                            $entity->date_collected_documents = AppConstant::convertDate($data['date_collected_documents'], 'Y-m-d');
                        else
                            $entity->date_collected_documents = null;
                        $entity->time_collected_documents = $data['time_collected_documents'];
                        $entity->time_collected_documents_reality = $data['time_collected_documents_reality'];
                        if (!empty($data['date_collected_documents_reality']))
                            $entity->date_collected_documents_reality = AppConstant::convertDate($data['date_collected_documents_reality'], 'Y-m-d');
                        else
                            $entity->date_collected_documents_reality = null;
                        $entity->num_of_document_page = $data['num_of_document_page'];
                        $entity->document_type = $data['document_type'];
                        $entity->document_note = $data['document_note'];

                        $entity = $this->calcStatusDocument($entity);
                        $entity->save();
                    }
                } else {
                    // TODO: Cảnh báo lỗi
                }
            }

            DB::commit();
        } catch (Exception $e) {
            logError($e);
            $ignoreCount = $total;
            DB::rollBack();
        }

        unset($backendExcel[$currentController]);
        unset($backendExcel[$currentController . '_type']);
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'total' => $total,
            'done' => $total - $ignoreCount,
        ]);

        $html = [
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
            'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
        ];
        return $html;
    }

    public function calcStatusDocument($order)
    {
        if ($order->status_collected_documents != config("constant.DA_THU_DU") && !empty($order->date_collected_documents)) {
            if (time() - strtotime($order->date_collected_documents . ' ' . $order->time_collected_documents) > 0) {
                $order->status_collected_documents = config("constant.QUA_HAN");
                return $order;
            }
            if (date('Y-m-d', strtotime(' today')) == date('Y-m-d', strtotime($order->date_collected_documents))) {
                $order->status_collected_documents = config("constant.DEN_HAN_VAO_HOM_NAY");
                return $order;
            }
            if (date('Y-m-d', strtotime(' +1 day')) == date('Y-m-d', strtotime($order->date_collected_documents))) {
                $order->status_collected_documents = config("constant.DEN_HAN_VAO_HOM_SAU");
                return $order;
            }
        }
        return $order;
    }


    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 10/4/2020
    public function exportCustomTemplate()
    {
        $orderIds = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $orderIds);
        $results = [];
        $data = $this->getRepository()->getExportByIDs($arr);
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->{'order_id'},
                'name' => $item->{'order_code'},
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
