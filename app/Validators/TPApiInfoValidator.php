<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class TPApiInfoValidator
 * @package App\Validator
 */
class TPApiInfoValidator extends BaseValidator
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