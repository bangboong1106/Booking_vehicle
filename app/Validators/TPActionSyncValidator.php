<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class TPActionSyncValidator
 * @package App\Validator
 */
class TPActionSyncValidator extends BaseValidator
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