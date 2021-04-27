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

class RoutesValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'name' => 'required',
            'route_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:routes,route_code,' . $this->getData('id'),
            'vehicle_id' => 'required|exists:vehicle,id',
            'driver_id' => 'required|exists:drivers,id',
        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.route_code.distinct' => trans('validation.distinct', ['attribute' => trans('models.route.attributes.route_code')]),
            '*.route_code.required' => trans('validation.required', ['attribute' => trans('models.route.attributes.route_code')]),
            '*.route_code.unique' => trans('validation.unique', ['attribute' => trans('models.route.attributes.route_code')]),
            '*.route_code.exists' => trans('validation.exists', ['attribute' => trans('models.route.attributes.route_code')])
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.route_code' => ['required',
                Rule::exists('routes', 'route_code')->where(function ($query) {
                    $query->where('del_flag', 0);
                })
            ],
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }
}