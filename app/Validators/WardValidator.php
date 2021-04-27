<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class WardValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'ward_id' => 'required|max:5|unique:m_ward,ward_id,' . $this->getData('id'),
            'title' => 'required|max:255|unique:m_ward,title,' . $this->getData('id')
        ];
        return $rules;
    }
}