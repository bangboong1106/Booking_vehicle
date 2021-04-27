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

class PayrollValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:payroll,code,' . $this->getData('id'),
            'name' => 'required',
        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.code.distinct' => trans('validation.distinct', ['attribute' => trans('models.price_quote.attributes.code')]),
            '*.code.required' => trans('validation.required', ['attribute' => trans('models.price_quote.attributes.code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.price_quote.attributes.code')]),
            '*.code.exists' => trans('validation.exists', ['attribute' => trans('models.price_quote.attributes.code')])
        ];
    }

}