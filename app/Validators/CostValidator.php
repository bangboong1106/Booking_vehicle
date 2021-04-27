<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class CostValidator
 * @package App\Validator
 */
class CostValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [];
        return $rules;
    }

    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return [];
    }
}