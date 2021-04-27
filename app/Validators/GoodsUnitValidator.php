<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class GoodsUnitValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'title' => 'required|max:255',
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:goods_unit,code,' . $this->getData('id'),
            'customer_id' => 'required'
        ];
        return $rules;
    }
    
    protected function _getMessagesDefault()
    {
        return [
            'customer_id.required' => trans('validation.required', ['attribute' => trans('models.goods_unit.attributes.name_of_customer_id')]),
        ];
    }
}