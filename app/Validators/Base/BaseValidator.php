<?php

namespace App\Validators\Base;

use App\Model\Base\ModelSoftDelete;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use \Prettus\Validator\LaravelValidator;

/**
 * Class BaseValidator
 * @package App\Validator\Base
 */
class BaseValidator extends LaravelValidator
{
    /**
     *
     */
    const RULE_CREATE = 'create';
    /**
     *
     */
    const RULE_UPDATE = 'update';
    /**
     *
     */
    const RULE_DRAFT = 'draft';
    /**
     *
     */
    const RULE_SHOW = 'show';
    /**
     *
     */
    const RULE_DESTROY = 'destroy';
    /**
     *
     */
    const RULE_SEARCH = 'search';
    /**
     *
     */
    const RULE_IMPORT = 'import';
    /**
     *
     */
    const RULE_IMPORT_UPDATE = 'import_update';


    const RULE_CLIENT_API = 'client_api';


    /**
     *
     */
    const IMAGE_EXTENSION = 'img';

    /**
     *
     */
    const CSV_EXTENSION = 'csv';

    /**
     * @var null
     */
    protected $_model = null;
    /**
     * @var array
     */
    protected $rules = [];
    /**
     * @var array
     */
    protected $messages = [];
    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @param $data
     * @return bool
     */
    public function validateCreate($data)
    {
        $this->beforeValidateCreate($data);
        return $this->with($data)->passes(self::RULE_CREATE);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateDraft($data)
    {
        $this->beforeValidateDraft($data);
        return $this->with($data)->passes(self::RULE_DRAFT);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateUpdate($data)
    {
        $this->beforeValidateUpdate($data);
        return $this->with($data)->passes(self::RULE_UPDATE);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateShow($data)
    {
        $this->beforeValidateShow($data);
        return $this->with($data)->passes(self::RULE_SHOW);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateDestroy($data)
    {
        $this->beforeValidateDestroy($data);
        return $this->with($data)->passes(self::RULE_DESTROY);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateSearch($data)
    {
        $this->beforeValidateSearch($data);
        return $this->with($data)->passes(self::RULE_SEARCH);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateOther($data)
    {
        $rules = [
            'id' => 'required|max:1'
        ];
        $message = [];
        return $this->_addRules($rules, $message)->with($data)->passes();
    }

    /**
     * @param $data
     * @param bool $fromEditor
     * @return bool
     */
    public function validateImport($data, $fromEditor = false)
    {
        $this->beforeValidateImport($data, $fromEditor);
        return $this->with($data)->passes(self::RULE_IMPORT);
    }

    /**
     * @param $data
     * @param bool $fromEditor
     * @return bool
     */
    public function validateImportUpdate($data, $fromEditor = false)
    {
        $this->beforeValidateImportUpdate($data, $fromEditor);
        return $this->with($data)->passes(self::RULE_IMPORT_UPDATE);
    }

    public function validateClientApi($data)
    {
        $this->beforeValidateClientApi($data);
        return $this->with($data)->passes(self::RULE_CLIENT_API);
    }

    /**
     * @param string $type
     * @param null $size
     * @param array $ext
     * @return string
     */
    public function _fileRule($type = self::IMAGE_EXTENSION, $size = null, $ext = array())
    {
        $size = 1024 * 1024 * (empty($size) ? getConfig('file.' . $type . '.size') : $size);
        $ext = empty($ext) ? implode(',', getConfig('file.' . $type . '.ext')) : implode(',', $ext);
        $rule = 'max:' . $size . '|mimes:' . $ext;

        return $rule;
    }

    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return ['rules' => $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    protected function _buildClientApiRules()
    {
        return ['rules' => $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    /**
     * @return array
     */
    protected function _buildImportRules($fromEditor = false)
    {
        return ['rules' => $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    /**
     * @return array
     */
    protected function _buildImportUpdateRules($fromEditor = false)
    {
        return ['rules' => ['id' => 'required' . $this->_getExistInDbRule($this->getModel()->getTableName())] + $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    /**
     * @return array
     */
    protected function _buildUpdateRules()
    {
        return ['rules' => ['id' => 'required' . $this->_getExistInDbRule($this->getModel()->getTableName())] + $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    /**
     * @return array
     */
    protected function _buildDraftRules()
    {
        return ['rules' => $this->_getRulesDraft(), 'messages' => $this->_getMessagesDraft()];
    }

    /**
     * @return array
     */
    protected function _buildShowRules()
    {
        return [
            'rules' => $this->_buildRules(array(
                'id' => 'required' . $this->_getExistInDbRule(),
            ), false),
            'messages' => [
                'id.exists' => trans('validation.exists', ['attribute' => $this->getModel()->getModelName()]),
            ]
        ];
    }

    /**
     * @return array
     */
    protected function _buildDestroyRules()
    {
        return [
            'rules' => $this->_buildRules(array(
                'id' => 'required' . $this->_getExistInDbRule(),
            ), false),
            'messages' => [
                'id.exists' => trans('validation.exists', ['attribute' => $this->getModel()->getModelName()]),
            ]
        ];
    }

    /**
     * @return array
     */
    protected function _buildSearchRules()
    {
        return ['rules' => [], 'messages' => []];
    }

    /**
     * @param $data
     */
    public function beforeValidateCreate(&$data)
    {
        return $this->_build(self::RULE_CREATE, $data);
    }

    /**
     * @param $data
     * @param bool $fromEditor
     * @return void
     */
    public function beforeValidateImport(&$data, $fromEditor = false)
    {
        return $this->_build(self::RULE_IMPORT, $data, $fromEditor);
    }

    /**
     * @param $data
     * @param bool $fromEditor
     * @return void
     */
    public function beforeValidateImportUpdate(&$data, $fromEditor = false)
    {
        return $this->_build(self::RULE_IMPORT_UPDATE, $data, $fromEditor);
    }

    /**
     * @param $data
     */
    public function beforeValidateDraft(&$data)
    {
        return $this->_build(self::RULE_DRAFT, $data);
    }

    /**
     * @param $data
     */
    public function beforeValidateUpdate(&$data)
    {
        return $this->_build(self::RULE_UPDATE, $data);
    }

    public function beforeValidateClientApi(&$data)
    {
        return $this->_build(self::RULE_CLIENT_API, $data);
    }

    /**
     * @param $data
     */
    public function beforeValidateShow(&$data)
    {
        if (!is_array($data)) {
            $data = array('id' => $data);
        }
        return $this->_build(self::RULE_SHOW, $data);
    }

    /**
     * @param $data
     */
    public function beforeValidateDestroy(&$data)
    {
        if (!is_array($data)) {
            $data = array('id' => $data);
        }
        return $this->_build(self::RULE_DESTROY, $data);
    }

    /**
     * @param $data
     */
    public function beforeValidateSearch(&$data)
    {
        return $this->_build(self::RULE_SEARCH, $data);
    }

    /**
     * @return array
     */
    public function getClientRules()
    {
        return ['rules' => $this->_getRulesDefault(), 'messages' => $this->_getMessagesDefault()];
    }

    /**
     * @return array
     */
    public function getCreateRules()
    {
        return $this->_buildCreateRules();
    }

    /**
     * @return array
     */
    public function getUpdateRules()
    {
        return $this->_buildUpdateRules();
    }

    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function _getMessagesDraft()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function _getRulesDraft()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function _getMessagesDefault()
    {
        return array();
    }

    /**
     * @param array $rules
     * @param bool $mergeDefault
     * @return array
     */
    protected function _buildRules($rules = array(), $mergeDefault = true)
    {
        return $mergeDefault ? array_merge($this->_getRulesDefault(), $rules) : $rules;
    }

    /**
     * @param array $messages
     * @param bool $mergerDefault
     */
    protected function _setMessages($messages = array(), $mergerDefault = true)
    {
        $messagesX = $this->messages;
        if ($mergerDefault) {
            $messagesX = array_merge($this->messages, $this->_getMessagesDefault());
        }
        $this->messages = array_merge($messagesX, $messages);
    }

    /**
     * @param null $action
     * @return bool
     */
    public function passes($action = null)
    {
        $this->setData($this->data);
        $rules = $action ? $this->getRules($action) : $this->rules;
        $validator = $this->validator->make($this->data, $rules, $this->messages)->setAttributeNames($this->_getAttributeNames());

        if ($validator->fails()) {
            $this->errors = $validator->messages();
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function _getAttributeNames()
    {
        return (array)Lang::get('models.' . $this->getModel()->getAlias() . '.attributes');
    }

    /**
     * @return ModelSoftDelete
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param null $model
     * @return  $this;
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @param $type
     * @param array $data
     * @param bool $fromEditor
     */
    protected function _build($type, $data = [], $fromEditor = false)
    {
        $this->setData($data);
        $r = array();
        switch ($type) {
            case self::RULE_CREATE:
                $r = $this->_buildCreateRules();
                break;
            case self::RULE_DRAFT:
                $r = $this->_buildDraftRules();
                break;
            case self::RULE_UPDATE:
                $r = $this->_buildUpdateRules();
                break;
            case self::RULE_SHOW:
                $r = $this->_buildShowRules();
                break;
            case self::RULE_DESTROY:
                $r = $this->_buildDestroyRules();
                break;
            case self::RULE_SEARCH:
                $r = $this->_buildSearchRules();
                break;
            case self::RULE_IMPORT:
                $r = $this->_buildImportRules($fromEditor);
                break;
            case self::RULE_IMPORT_UPDATE:
                $r = $this->_buildImportUpdateRules($fromEditor);
                break;
            case self::RULE_CLIENT_API:
                $r = $this->_buildClientApiRules($fromEditor);
                break;
            default:
                $r = $this->_buildCreateRules();
                break;
        }
        $this->rules[$type] = isset($r['rules']) ? (array)$r['rules'] : array();
        $this->_setMessages(isset($r['messages']) ? (array)$r['messages'] : array());
    }

    /**
     * @param string $tableName
     * @param array $fields
     * @return string
     */
    protected function _getExistInDbRule($tableName = '', $fields = array('id'))
    {
        $fields = $this->_addDeleteScope($fields);
        $tableName = $tableName ? $tableName : $this->getModel()->getTableName();
        return '|exists:' . $tableName . ',' . $this->_implode(',', $fields);
    }

    protected function _implode($prefix, $params)
    {
        return join($prefix, array_map(function ($value) {
            return $value === null ? 'NULL' : $value;
        }, $params));
    }

    /**
     * @param string $tableName
     * @param array $fields
     * @return string
     */
    protected function _getUniqueInDbRule($tableName = '', $fields = array())
    {
        $fs = ['email', $this->getData('id'), 'id'];
        if (!empty($fields)) {
            $tmpField = Arr::first($fields);
            $tmpField = explode('.', $tmpField);
            $fs = [array_pop($tmpField)];
            unset($fields[0]);
            foreach ($fields as $field) {
                $fs[] = $this->getData($field);
                $tmpField = explode('.', $field);
                $fs[] = array_pop($tmpField);
            }
        }
        $fields = $this->_addDeleteScope($fs);
        $tableName = $tableName ? $tableName : $this->getModel()->getTableName();
        return '|unique:' . $tableName . ',' . $this->_implode(',', $fields);
    }

    /**
     * @param $array
     * @return string
     */
    protected function _getInArrayRule($array)
    {
        return '|in:' . $this->_implode(',', $array);
    }

    /**
     * @return string
     */
    protected function _getSmallIntegerRule()
    {
        return '|integer|max:32767';
    }

    protected function _addDeleteScope($fields = [])
    {
        if ($field = getDelFlagColumn()) {
            $fields[] = $field;
            $fields[] = getDelFlagColumn('active');
            return $fields;
        }
        if ($field = getDeletedAtColumn()) {
            $fields[] = $field;
            $fields[] = null;
            return $fields;
        }
        return $fields;
    }

    /**
     * @param $key
     * @param $default
     * @return array
     */
    public function getData($key = null, $default = null)
    {
        if ($key) {
            return Arr::get($this->_data, $key, $default);
        }
        return $this->_data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $data = $this->getData();
        return isset($data[$key]);
    }

    /**
     * @param array $rules
     * @param array $messages
     * @return $this
     */
    protected function _addRules($rules = array(), $messages = array())
    {
        $this->rules += $rules;
        $this->_setMessages($messages);
        return $this;
    }

    protected $_otherData = [];

    /**
     * @param null $key
     * @param null $default
     * @return array
     */
    public function getOtherData($key = null, $default = null)
    {
        if ($key) {
            return isset($this->_otherData[$key]) ? $this->_otherData[$key] : $default;
        }
        return $this->_otherData;
    }

    /**
     * @param array $otherData
     * @return $this;
     */
    public function setOtherData($otherData)
    {
        $this->_otherData += $otherData;
        return $this;
    }

    protected function _hasFileUpload($field)
    {
        if ($this->getData($field) instanceof UploadedFile) {
            return true;
        }

        return false;
    }

    protected function _toMb($value)
    {
        return $value * 1024;
    }
}
