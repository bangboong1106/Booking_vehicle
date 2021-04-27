<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ReceiptPaymentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use JsValidator;
use App\Common\HttpCode;
use App\Model\Entities\ReceiptPayment;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class ReceiptPaymentController
 * @package App\Http\Controllers\Backend
 */
class ReceiptPaymentController extends BackendController
{
    /**
     * ReceiptPaymentController constructor.
     * @param ReceiptPaymentRepository $ReceiptPaymentRepository
     */
    public function __construct(ReceiptPaymentRepository $ReceiptPaymentRepository)
    {
        parent::__construct();
        $this->setRepository($ReceiptPaymentRepository);
        $this->setBackUrlDefault('receipt-payment.index');
        $this->setConfirmRoute('receipt-payment.confirm');
        $this->setMenu('category');
        $this->setTitle(trans('models.receipt_payment.name'));
    }

    public function index()
    {
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $model = $this->getRepository()->getModel();
        $receipt_categories = $model::where($model->getParentColumnName(), '=', null)
            ->where($model->getScopedColumns()[0], '=', 1)
            ->orderBy('sort_order')
            // ->orderBy('is_system', 'DESC')
            // ->orderBy('name')
            ->get();

        $this->setViewData(['receipt_categories' => $receipt_categories]);

        $payment_categories = $model::where($model->getParentColumnName(), '=', null)
            ->where($model->getScopedColumns()[0], '=', 2)
            ->orderBy('sort_order')
            // ->orderBy('is_system', 'DESC')
            // ->orderBy('name')
            ->get();


        $this->setViewData(['payment_categories' => $payment_categories]);
        return $this->render();
    }

    public function beforeStore(&$entity)
    {
        $entity->amount = implode('|', $entity->amount_list);
    }

    public function beforeUpdate(&$entity)
    {
        $entity->amount = implode('|', $entity->amount_list);
    }

    protected function _prepareForm()
    {
        $scope = Request::get('type', null);
        $model = $this->getRepository()->getModel();


        if (!empty($scope)) {
            $receipt_payment_list = $model::getScopedNestedList('name', 'id', '-', $scope);

            $this->setViewData([
                'receipt_payment_list' => $receipt_payment_list,
                'type' => $scope,
            ]);
        }

    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }

        $type = $prepare->getEntity()->type;
        $model = $this->getRepository()->getModel();

        $receipt_payment_list = $model::getScopedNestedList('name', 'id', '-', $type);

        $this->setViewData(['receipt_payment_list' => $receipt_payment_list]);
        $this->setViewData(['type' => $type]);

        $amount_list =  explode('|', $prepare->getEntity()->amount);
        $this->setViewData([
            'amount_list' => $amount_list
        ]);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $model = $this->getRepository()->getModel();
        $parent_name = $model::find($this->getEntity()->parent_id);

        $entity = $this->getEntity();
        if (empty($parent_name)) {
            $this->setViewData(['parent_name' => '']);
        } else {
            $this->setViewData(['parent_name' => $parent_name->name]);
        }
    }

    //Xử lý cập nhật node bằng Baum
    protected function _updateNestedSet($entity)
    {
        $pid = $entity->getParentId();
        $name = $entity->name;
        $amount = $entity->amount;
        $is_display_driver = $entity->is_display_driver;

        if (empty($pid)) {
            $entity->makeRoot();
            $entity->name = $name;
            $entity->amount = $amount;
            $entity->is_display_driver = $is_display_driver;

        } else if ($pid !== FALSE) {
            $entity->makeChildOf($pid);
        }
        $entity->save();
    }

    // Sắp xếp danh mục thu chi
    // CreatedBy nlhoang 14/07/2020
    public function order(Request $request)
    {
        $entity = $request->all();
        $items = $entity['list'];
        DB::beginTransaction();
        try {
            $order = 1;
            foreach ($items as $key => $item) {
                ReceiptPayment::where('id', $item["id"])->update(array('sort_order' => $order));
                $order++;
            }
            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
            ]);
        } catch (Exception $exception) {
            DB::rollback();
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }
}
