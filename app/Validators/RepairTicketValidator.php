<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;


class RepairTicketValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:repair_ticket,code,' . $this->getData('id'),
        ];
        return $rules;
    }

    public function _buildImportRules($fromEditor = false)
    {
        $rules = [
            '*.code' => 'required|distinct' . $this->_getUniqueInDbRule(false, ['code', 'id']),
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.code' => ['required',
                Rule::exists('order_customer', 'code')->where(function ($query) {
                    $query->where('del_flag', 0);
                })
            ],
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.code.distinct' => trans('validation.distinct', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.required' => trans('validation.required', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.exists' => trans('validation.exists', ['attribute' => trans('models.order_customer.attributes.code')]),
        ];
    }

}