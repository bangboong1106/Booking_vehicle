<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

class OrderCustomerClientValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'client_id' => 'required',
            // 'list_goods' => 'required',
            "list_goods.*.goods_type_id"  => "required",
            "list_goods.*.quantity"  => "required|min:1",

        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            // 'list_goods.required' => trans('validation.required', ['attribute' => 'Thông tin hàng hoá']),
            'list_goods.*.goods_type_id.required' => trans('validation.required', ['attribute' => 'Loại hàng hoá']),
            'list_goods.*.quantity.min' => trans('validation.min', ['attribute' => 'Số lượng']),

        ];
    }
}
