<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

/**
 * Class PartnerValidator
 * @package App\Validator
 */
class PartnerValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:partner,code,' . $this->getData('id'),
            'mobile_no' => 'required|max:256|unique:customer,mobile_no,' . $this->getData('id') . ',id,del_flag,0',
            'full_name' => 'required'
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        $rules = [
            '*.code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.mobile_no' => 'required|max:256|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.full_name' => 'required'
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|distinct|exists:customer,customer_code',
            '*.mobile_no' => 'required|max:256|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.full_name' => 'required',
            '*.delegate' => 'required_if:*.type,' . config("constant.CORPORATE_CUSTOMERS"),
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'delegate.required_if' => trans('validation.required',['attribute' => trans('models.customer.attributes.delegate')]),

            '*.code.distinct' => trans('validation.distinct', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.code.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.code.max' => trans('validation.max.string', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.code.regex' => trans('validation.regex', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.customer.attributes.customer_code')]),

            '*.mobile_no.distinct' => trans('validation.distinct', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.max' => trans('validation.max.string', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.unique' => trans('validation.unique', ['attribute' => trans('models.customer.attributes.mobile_no')]),

            '*.full_name.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.full_name')]),

        ];
    }
}