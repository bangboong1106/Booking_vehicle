<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class PriceQuoteCustomerGroupValidator extends BaseValidator
{
    /** Default create
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