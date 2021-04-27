<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class ReceiptPaymentValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'id' => 'max:10|unique:m_receipt_payment,id,' . $this->getData('id'),
            'name' => 'required|max:255|unique:m_receipt_payment,name,' . $this->getData('id')
        ];
        return $rules;
    }
}