<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class TypeShipValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'title' => 'required|max:255',
        ];
    }

    
    protected function _getMessagesDefault()
    {
        return [
            '*.title.required' => trans('validation.required', ['attribute' => trans('models.typeship.attributes.title')]),
            '*.title.max' => trans('validation.max.string', ['attribute' => trans('models.typeship.attributes.title')]),
        ];
    }
}