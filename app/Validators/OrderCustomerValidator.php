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

class OrderCustomerValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            // 'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:order_customer,code,' . $this->getData('id'),
            'order_no' => 'distinct' . $this->_getUniqueInDbRule(false, ['order_no', 'id']),
            'customer_id' => 'required',
            'location_destination_id' => 'required',
            'location_arrival_id' => 'required',
            'ETD_date' => 'required',
            'ETD_time' => 'required',
            'ETA_date' => 'required|after_or_equal:ETD_date',
            'ETA_time' => 'after_if_ETA_date:ETD_date,ETA_date,ETD_time|required',
        ];
        return $rules;
    }

    protected function _buildClientApiRules()
    {
        $rules = [
            'customer_id' => 'required'
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        $rules = [
            '*.code' => 'required|distinct' . $this->_getUniqueInDbRule(false, ['code', 'id']),
            '*.order_no' => 'distinct' . $this->_getUniqueInDbRule(false, ['order_no', 'id']),
            '*.customer_code' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.code' => [
                'required',
                Rule::exists('order_customer', 'code')->where(function ($query) {
                    $query->where('del_flag', 0);
                })
            ],
            '*.customer_code' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'location_destination_id.required' => trans('validation.required', ['attribute' => trans('models.order_customer.attributes.location_destination')]),
            'location_arrival_id.required' => trans('validation.required', ['attribute' => trans('models.order_customer.attributes.location_arrival')]),

            '*.code.distinct' => trans('validation.distinct', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.required' => trans('validation.required', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.code.exists' => trans('validation.exists', ['attribute' => trans('models.order_customer.attributes.code')]),
            '*.order_no.distinct' => trans('validation.distinct', ['attribute' => trans('models.order_customer.attributes.order_no')]),
            '*.order_no.unique' => trans('validation.unique', ['attribute' => trans('models.order_customer.attributes.order_no')]),
            '*.customer_code.required' => trans('validation.required', ['attribute' => trans('models.order_customer.attributes.customer_code')]),
        ];
    }
}
