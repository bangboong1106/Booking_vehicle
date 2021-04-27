<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AlertLogValidator
 * @package App\Validator
 */
class ReportScheduleValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'email' => 'required',
            'report_type' => 'required'
        ];
        return $rules;
    }
}