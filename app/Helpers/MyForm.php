<?php

namespace App\Helpers;

use App\Model\Base\ModelSoftDelete;
use Baum\Extensions\Eloquent\Model;
use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Helpers\Facades\CustomStorage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class MyForm
 * @package App\Helpers
 */
class MyForm extends FormBuilder
{
    protected $_errors = [];

    protected $_documentIcon = 'data:image/png;base64,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';
    protected $_hasValidateMsg = false;

    /**
     * @return null
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @param null $entity
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
    }

    /**
     * @var null
     */
    protected $_entity = null;
    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @param $key
     * @param array $default
     * @return array
     */
    public function getData($key, $default = [])
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : $default;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data += $data;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $name = $this->_getName($key);
        return isset($this->_data[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _getName($name)
    {
        $name = explode('[', $name);
        return $name[0];
    }

    /**
     * @param mixed $entity
     * @param array $options
     * @return string
     */
    public function model($entity, array $options = [])
    {
        $this->setEntity($entity);
        $html = $this->open($options); // TODO: Change the autogenerated stub
        $actionMethod = Route::getCurrentRoute()->getActionMethod();
        $html .= $actionMethod !== 'duplicate' ? '<input type="hidden" name="id" value="' . $entity->id . '">' . keepBack() :
            '<input type="hidden" name="id" value="0">' . keepBack();
        return $html;
    }

    /**log
     * @param array $options
     * @return HtmlString
     */
    public function delete($options = [
        'del_form',
        'id' => 'del_form',
        'class' => 'del-form',
        'method' => 'DELETE'
    ])
    {
        return $this->open($options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function massDelete($options = [
        'mass_del_form',
        'id' => 'mass_del_form',
        'class' => 'mass-del-form',
        'method' => 'DELETE'
    ])
    {
        return $this->open($options) . ' <input type="hidden" name="id" id="mass_destroy_id"/>'; // TODO: Change the autogenerated stub
    }

    /**
     * @param array $options
     * @return HtmlString
     */
    public function import($options = [
        'import_form',
        'id' => 'import_form',
        'class' => 'import-form form-horizontal',
        'method' => 'POST',
    ])
    {
        $nameRoute = Route::getCurrentRoute()->getName();
        $compliedRoute = explode('.', $nameRoute);
        $controllerAlias = reset($compliedRoute);
        $options['route'] = $controllerAlias.'.import';
        return $this->open($options);
    }

    /**
     * @param array $options
     * @return HtmlString
     */
    public function open(array $options = [
        'class' => 'form-horizontal',
    ])
    {
        if (!empty($options['validation'])) {
            $errors = $options['validation'];
            $this->_errors = is_array($errors) ? $errors : [];

            unset($options['validation']);
        } elseif (Session()->has('validation')) {
            $errors = Session()->get('validation');
            $this->_errors = is_array($errors) ? $errors : [];
        }
        $options = $this->_addDefaultOpenClass($options, getSystemConfig('form_open_class_default'));
        $options = $this->_addDefaultShowLoading($options);
        $options = $this->_addEnctype($options);
        return parent::open($options); // TODO: Change the autogenerated stub
    }

    /**
     * @param string $name
     * @param array $options
     * @return string
     */
    public function upload($name, $options = [])
    {
        /** @var ModelSoftDelete $entity */
        $entity = $this->getEntity();
        $options['class'] = isset($options['class']) ? $options['class'] : 'input-image';
        $options['id'] = 'uploadFile-' . $name;
        $input = '<input type="hidden" name="_file_name[' . $name . ']" value="' . $name . '">';
        $input .= '<div id="preview-file-' . $name . '" class="pt-2">';
        if (!empty($entity->tmp_file)) {
            $fileInfo = pathinfo(Arr::get($entity->tmp_file, $name));
            $ext = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
            if (in_array($ext, getConfig('file.default.image.ext'))) {
                $input .= $entity->getTmpFile($name);
            } else {
                $input .= $entity->getDefaultFileView($this->_documentIcon, $name);
            }
            $input .= '<input type="hidden" name="tmp_file[' . $name . ']" value="' . array_get($entity->tmp_file, $name) . '">';
            $input .= '<input type="hidden" name="' . $name . '" value="' . $entity->$name . '">';
        } else {
            if ($entity->id) {
                $input .= '<input type="hidden" class="input-from-db" name="' . $name . '" value="' . $entity->$name . '">';
            }
            if ($entity->$name) {
                $fileInfo = pathinfo($entity->$name);
                $ext = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
                $input .= '<input type="hidden" name="' . $name . '" value="' . $entity->$name . '">';
                if (in_array($ext, getConfig('file.default.image.ext'))) {
                    $input .= $entity->getTmpFile($name);
                } else {
                    $input .= $entity->getDefaultFileView($this->_documentIcon, $name);
                }
            }
        }
        $input .= '</div>';


        $options['ext'] = implode(',', $options['ext']);
        $options['size'] = implode(',', $options['size']);


        $options['data-label'] = $entity->getAttributeName($name);
        $this->_setValidateFileMsg($input);
        return parent::file($name, $options) . $input;
    }

    protected function _setValidateFileMsg(&$input)
    {
        if ($this->_hasValidateMsg) {
            return;
        }
        $validateMsg = Lang::get('validation');
        $msg = [
            'min' => $validateMsg['min']['file'],
            'max' => $validateMsg['max']['file'],
            'mimes' => $validateMsg['mimes'],
        ];
        $input .= '<script> var validateFileMsg = ' . json_encode($msg) . '</script>';
        $input .= '<script>var documentIcon = "' . $this->_documentIcon . '"</script>';
        $this->_hasValidateMsg = true;
    }

    /**
     * @param $entity
     * @param string $routePrefix
     * @param array $options
     * @return string
     */
    public function confirm($entity, $routePrefix = '', array $options = [])
    {
        $options = $this->_addDefaultRoute($options, $entity, $routePrefix);
        $this->setEntity($entity);
        return $this->open($options) . keepBack(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $name
     * @param int $value
     * @param $checkedList
     * @param $checkField
     * @param array $options
     * @return string
     */
    public function checkboxMulti($name, $value = 1, $checkedList, $checkField, $options = [])
    {
        $checkBox = '';
        if (!$this->has($name)) {
            if ($checkField) {
                $checkedList = $checkedList->pluck($checkField)->toArray();
            }
            $nameTmp = $this->_getName($name);
            $this->setData([$nameTmp => $checkedList]);
            if ($this->getEntity() && $this->getEntity()->id) {
                $checkBox = parent::checkbox($nameTmp . '[0]', 0, true, ['checked' => true, 'class' => 'hide']);
            }

        }

        $checkedList = $this->getData($this->_getName($name), []);
        if (in_array($value, (array)$checkedList)) {
            $options['checked'] = 'checked';
            return $checkBox . parent::checkbox($name, $value, true, $options); // TODO: Change the autogenerated stub
        }
        return $checkBox . parent::checkbox($name, $value, null, $options); // TODO: Change the autogenerated stub
    }

    public function query($options = ['method' => 'GET'])
    {
        return $this->open($options);
    }

    public function dropDown($name, $selected = null, $list = [], $allowNullOption = true,  array $selectAttributes = [], array $optionsAttributes = [])
    {
        isCollection($list) ? $list = $list->toArray() : null;
        if (!$allowNullOption) {
            $allowNullOption = $list;
        } else {
            $allowNullOption = is_array($allowNullOption) ? $allowNullOption : ['' => trans('common.default_option')];
        }
        $allowNullOption += $list;
        return parent::select($name, $allowNullOption, $selected, $this->_addDefaultClass($selectAttributes), $optionsAttributes);
    }

    /**
     * @param string $type
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        $options = $this->_addDefaultClass($options);

        $this->type = $type;

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $id = $this->getIdAttribute($name, $options);

        if (!in_array($type, $this->skipValueTypes)) {
            $value = $this->getValueAttribute($name, $value);
        }

        $merge = compact('type', 'value', 'id');
        $options = array_merge($options, $merge);

        $feedback = '';
        $convertedName = $this->_convertFieldName($name);
        if (array_key_exists($convertedName, Arr::get($this->_errors, 'inValid', []))) {
            $listError = Arr::get($this->_errors, 'inValid', []);
            $options['class'] .= ' is-invalid';
            $feedback = '<span id="'.$name.'-error" class="invalid-feedback">' . Arr::get($listError, $convertedName) . '</span>';
        }

        return $this->toHtmlString('<input' . $this->html->attributes($options) . '>' . $feedback);
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString|string
     */
    public function textarea($name, $value = null, $options = [])
    {
        $whiteSpaces = ' <input type="hidden" name="_white_space_[]" value="' . $name . '"> ';
        return $whiteSpaces . parent::textarea($name, $value, $this->_addDefaultClass($options)); // TODO: Change the autogenerated stub
    }

    protected function _getShowError($name)
    {
        if (!$this->_errors) {
            return '';
        }
        $name = str_replace('[', '.', str_replace(']', '', $name));
        $errors = $this->_errors;
        $error = isset($errors[$name]) ? Arr::get($errors[$name], 0) : '';
        if (!$error) {
            return '';
        }
        return $error;
    }

    protected function _wrapError($errors = [])
    {
        if (empty($errors)) {
            return '';
        }

        $result = "";
        foreach ($errors as $err) {
            $result .= '<li>' . $err . '</li>';
        }
        return '<div class="i-alert i-alert-danger"><ul>' . $result . '</ul></div>';
    }

    public function errors($names)
    {
        $errorList = [];
        if (is_array($names)) {
            foreach ($names as $name) {
                empty($this->_getShowError($name)) ? null : $errorList[] = $this->_getShowError($name);
            }
        } else {
            empty($this->_getShowError($names)) ? null : $errorList[] = $this->_getShowError($names);
        }
        return $this->_wrapError($errorList);
    }

    /**
     * @param $options
     * @param bool $showLoading
     * @return mixed
     */
    protected function _addDefaultShowLoading($options, $showLoading = true)
    {
        !isset($options['show-loading']) ? $options['show-loading'] = (string)$showLoading : null;
        return $options;
    }

    /**
     * @param $options
     * @return mixed
     */
    protected function _addEnctype($options)
    {
        !isset($options['enctype']) ? $options['enctype'] = "multipart/form-data" : null;
        return $options;
    }

    /**
     * @param $options
     * @param null $class
     * @return mixed
     */
    protected function _addDefaultClass($options, $class = null)
    {
        $ignoreDefaultClass = isset($options['defaultClass']) && !$options['defaultClass'];
        if ($ignoreDefaultClass) {
            return $options;
        }
        empty($class) ? $class = getSystemConfig('form_class_default') : null;
        if (isset($options['class'])) {
            $pos = strpos($options['class'], $class);
            false === $pos ? $options['class'] .= getSystemConfig('form_class_default') : null;
        } else {
            $options['class'] = $class;
        }

        return $options;
    }

    /**
     * @param $options
     * @param null $class
     * @return mixed
     */
    protected function _addDefaultOpenClass($options, $class = null)
    {
        $ignoreDefaultClass = isset($options['defaultClass']) && !$options['defaultClass'];
        if ($ignoreDefaultClass) {
            return $options;
        }
        empty($class) ? $class = getSystemConfig('form_open_class_default') : null;
        if (isset($options['class'])) {
            $pos = strpos($options['class'], $class);
            false === $pos ? $options['class'] .= getSystemConfig('form_open_class_default') : null;
        } else {
            $options['class'] = $class;
        }

        return $options;
    }

    /**
     * @param $options
     * @param Model $entity
     * @param string $route
     * @return mixed
     */
    protected function _addDefaultRoute($options, $entity, $route = '')
    {
        if (isset($options['route']) || !$route || empty($entity)) {
            return $options;
        }
        $options['route'] = array(
            $entity->id ? (!empty($entity->getAttributes()['duplicate']) ? $route . '.store' : $route . '.update') : $route . '.store',
            $entity->id
        );

        if (isset($options['method'])) {
            return $options;
        }

        $options['method'] = $entity->id ? (!empty($entity->getAttributes()['duplicate']) ? 'POST' : 'PUT') : 'POST';
        return $options;
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function text($name, $value = null, $options = [])
    {
        $ignoreDefaultClass = isset($options['defaultClass']) && !$options['defaultClass'];
        return parent::text($name, $value, $ignoreDefaultClass ? $options : $this->_addDefaultClass($options));
    }

    /**
     * @param $name
     * @param array $options
     * @param string $relation
     * @return string
     */
    public function comment($name, $options = [], $relation = '')
    {
        // class default
        $classDefault = 'update - comment btn btn - info btn - xs ';
        $classDefault .= isset($options['class']) ? $options['class'] : '';
        // id
        $id = $this->getEntity()->id;
        !$this->getEntity()->$name ? $classDefault .= ' btn - warning' : '';
        $html = ' < a class="' . $classDefault . '" data - relation = "' . $relation . '" data - name = "' . $name . '" data - id = "' . $id . '" ><span
                    class="ls-icon ls-icon-file" aria-hidden = "true" ></span ></a > ';

        return isShow() ? $html : '';
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return HtmlString
     */
    public function checkable($type, $name, $value, $checked, $options)
    {
        if ($type == 'checkbox') {
            return parent::checkable($type, $name, $value, $checked, $options);
        }
        $this->type = $type;
        $isChecked = false;
        if ($value == $checked) {
            $isChecked = true;
        }
        $checked = $this->getCheckedState($type, $name, $value, $checked);
        if (($checked || (string)$checked !== '') && ($value == $checked || $isChecked)) {
            $options['checked'] = 'checked';
        }

        return $this->input($type, $name, $value, $options);
    }

    public function close()
    {
        Session()->forget('validation');
        return parent::close();
    }

    protected function _convertFieldName($name)
    {
        if (!Str::contains($name,'[')) {
            return $name;
        }

        $tmp = str_replace('[', '.', $name);
        $tmp = str_replace(']', '', $tmp);
        return $tmp;
    }

    public function error($name)
    {
        $convertedName = $this->_convertFieldName($name);
        if (array_key_exists($convertedName, Arr::get($this->_errors, 'inValid', []))) {
            $listError = Arr::get($this->_errors, 'inValid', []);
            return '<span id="'.$name.'-error" class="invalid-feedback">' . Arr::get($listError, $convertedName) . '</span>';
        }
        return '';
    }

    public function label($name, $value = null, $options = [], $escape_html = true)
    {
        $convertedName = $this->_convertFieldName($name);
        if (array_key_exists($convertedName, Arr::get($this->_errors, 'inValid', []))) {
            $options['class'] = 'is-invalid';
        }
        return parent::label($name, $value, $options, $escape_html);
    }
}