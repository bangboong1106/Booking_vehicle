<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class PartnerVehicleTeamValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'id' => 'max:10|unique:vehicle_team,id,' . $this->getData('id'),
            'name' => 'required|max:255|unique:vehicle_team,name,' . $this->getData('id'),
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:vehicle_team,code,' . $this->getData('id'),
        ];
        return $rules;
    }

}