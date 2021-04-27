<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class ProvinceValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'province_id' => 'required|max:5|unique:m_province,province_id,' . $this->getData('id'),
            'title' => 'required|max:255|unique:m_province,title,' . $this->getData('id')
        ];
        return $rules;
    }
}