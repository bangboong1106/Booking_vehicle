<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class OrderHistoryValidator
 * @package App\Validator
 */
class OrderHistoryValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return [
            'rules' => $this->_buildRules([
            ])
        ];
    }
}