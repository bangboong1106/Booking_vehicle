<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class ActivityLogValidator
 * @package App\Validator
 */
class ActivityLogValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [];
    }
}