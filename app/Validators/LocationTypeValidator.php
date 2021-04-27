<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class LocationTypeValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'title' => 'required|max:1000',
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/' . $this->_getUniqueInDbRule(false, ['code', 'id']),
            'customer_id' => 'required'
        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            'customer_id.required' => trans('validation.required', ['attribute' => trans('models.location_type.attributes.name_of_customer_id')]),
        ];
    }
}