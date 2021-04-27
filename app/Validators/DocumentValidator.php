<?php

namespace App\Validators;

use App\Repositories\DriverRepository;
use App\Repositories\VehicleRepository;
use App\Validators\Base\BaseValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\Rule;

class DocumentValidator extends BaseValidator
{

    public function __construct(Factory $validator)
    {
        parent::__construct($validator);

    }

    protected function _getMessagesDefault()
    {
        return [
            '*.order_code.distinct' => trans('validation.distinct', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.required' => trans('validation.required', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.unique' => trans('validation.unique', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.order_code.exists' => trans('validation.exists', ['attribute' => trans('models.order.attributes.order_code')]),
            '*.time_collected_documents.date_format' => 'Giờ hạn thu chứng từ nhập sai hoặc không đúng định dạng',
            '*.date_collected_documents.date_format' => 'Ngày hạn thu chứng từ nhập sai hoặc không đúng định dạng',
            '*.time_collected_documents.date_format_reality' => 'Giờ thu chứng từ nhập sai hoặc không đúng định dạng',
            '*.date_collected_documents.date_format_reality' => 'Ngày thu chứng từ nhập sai hoặc không đúng định dạng',

        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
//            '*.order_code' => ['required',
//                Rule::exists('orders', 'order_code')->where(function ($query) {
//                    $query->where('del_flag', 0);
//                })
//            ],
            '*.date_collected_documents' => 'nullable|date_format:d-m-Y,d/m/Y',
            '*.time_collected_documents' => 'nullable||date_format:H:i',
            '*.date_collected_documents_reality' => 'nullable|date_format:d-m-Y,d/m/Y',
            '*.time_collected_documents_reality' => 'nullable||date_format:H:i',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }
}