<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;

/**
 * Class ColumnConfigValidator
 * @package App\Validators
 */
class LpCompanySupportValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
        ];
        return $rules;
    }

}