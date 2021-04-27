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

class OrderClientValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            "list_goods.*.goods_type_id"  => "required",
        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            'list_goods.*.goods_type_id.required' => trans('validation.required', ['attribute' => 'Loại hàng hoá']),
        ];
    }
}
