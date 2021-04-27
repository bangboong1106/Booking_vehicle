<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class VehicleGroupValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'id' => 'max:10|unique:m_vehicle_group,id,' . $this->getData('id'),
            'name' => 'required|max:255|unique:m_vehicle_group,name,' . $this->getData('id'),
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:m_vehicle_group,code,' . $this->getData('id'),
            'partner_id' => 'required'
        ];
        return $rules;
    }
}