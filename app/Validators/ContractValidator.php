<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;

/**
 * Class customerInfo
 * @package App\Validator
 */
class ContractValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'contract_no' => 'required|unique:contracts,contract_no,'. $this->getData('id').',id,del_flag,0',
            'issue_date' => 'date_format:d-m-Y',
            'issue_date' => 'required_unless:status,1',
            'status' => 'no_js_validation',
        ];
        return $rules;
    }

}