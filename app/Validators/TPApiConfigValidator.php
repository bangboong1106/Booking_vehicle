<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class TPApiConfigValidator
 * @package App\Validator
 */
class TPApiConfigValidator extends BaseValidator
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