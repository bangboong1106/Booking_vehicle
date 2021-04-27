<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class DistrictValidator extends BaseValidator
{

    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'district_id' => 'required|max:5|unique:m_district,district_id,' . $this->getData('id'),
            'title' => 'required|max:255|unique:m_district,title,' . $this->getData('id')
        ];
        return $rules;
    }

}