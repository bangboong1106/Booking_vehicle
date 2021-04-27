<?php namespace App\Validators\Base;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Validator;

/**
 * Class CustomValidator
 * @package App\Validator
 */
class CustomValidator extends Validator
{
    protected $dependentRules = [
        'RequiredWith', 'RequiredWithAll', 'RequiredWithout', 'RequiredWithoutAll',
        'RequiredIf', 'RequiredUnless', 'Confirmed', 'Same', 'Different', 'Unique',
        'Before', 'After', 'BeforeOrEqual', 'AfterOrEqual', 'Gt', 'Lt', 'Gte', 'Lte',
        'Exists'
    ];
    /**
     * @var array
     */
    private $_customMessages = [
        'greater_than' => 'The :attribute format is invalid !',
        'greater_than_equal' => 'The :attribute format is invalid !',
        'greater_than_equal_for_time_stamp' => 'The :attribute format is invalid !',
        'after_if_e_t_a_date' => 'Giờ trả hàng phải lớn hơn giờ nhận hàng',
        'after_if_e_t_a_date_reality' => 'Giờ trả hàng thực tế phải lớn hơn giờ nhận hàng thực tế',
        'check_customer' => 'Khách hàng là bắt buộc',
    ];

    /**
     * CustomValidator constructor.
     * @param Translator $translator
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     */
    public function __construct($translator, $data, $rules,
                                $messages = array(), $customAttributes = array())
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
        $this->_setCustomMessages();
    }

    /**
     * Setup custom error messages
     */
    protected function _setCustomMessages()
    {
        $this->setCustomMessages($this->_customMessages);
    }

    /**
     * Custom validator method
     */
    public function validateGreaterThan($attribute, $value, $parameters)
    {
        $data = $this->getData();
        $other = $data[$parameters[0]];
        return isset($other) and intval($value) > intval($other);
    }

    /**
     * Custom validator method
     */
    public function validateGreaterThanField($attribute, $value, $parameters, $validator)
    {
        $min_field = $parameters[0];
        $data = $validator->getData();
        $min_value = Arr::get($data, $min_field);
        return $value > $min_value;
    }

    /**
     * Custom validator method
     */
    public function validateGreaterThanOrEqualField($attribute, $value, $parameters, $validator)
    {
        $min_field = $parameters[0];
        $data = $validator->getData();
        $min_value = Arr::get($data, $min_field);
        return $value >= $min_value;
    }

    /**
     * Custom validator method
     */
    public function validateGreaterThanOrEqualTimeField($attribute, $value, $parameters, $validator)
    {
        $min_field = $parameters[0];
        $data = $validator->getData();
        $min_value = Arr::get($data, $min_field);
        strlen($value) == 4 ? $value = '0' . $value : null;
        strlen($min_value) == 4 ? $min_value = '0' . $min_value : null;
        return $value >= $min_value;
    }

    /**
     * Custom validator method
     */
    public function validateLessThanField($attribute, $value, $parameters, $validator)
    {
        $min_field = $parameters[0];
        $data = $validator->getData();
        $min_value = Arr::get($data, $min_field);
        return $value < $min_value;
    }

    /**
     * Custom validator method
     */
    public function validateLessOrEqualThanField($attribute, $value, $parameters, $validator)
    {
        $min_field = $parameters[0];
        $data = $validator->getData();
        $min_value = Arr::get($data, $min_field);
        return $value <= $min_value;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateGreaterThanEqual($attribute, $value, $parameters)
    {
        $data = $this->getData();
        $other = $data[$parameters[0]];
        if (intval($value) == intval($other)) {
            return true;
        }
        return isset($other) and intval($value) > intval($other);
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateGreaterThanEqualForTimeStamp($attribute, $value, $parameters)
    {
        $value = date('Y-m-d H:i:s', strtotime($value));
        $other = date('Y-m-d H:i:s', strtotime($parameters[0]));

        if ($value == $other) {
            return true;
        }
        return $value > $other;
    }

    protected function replaceGreaterThanField($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getDisplayableAttribute($parameters[0]), $message);
    }

    protected function replaceGreaterThanOrEqualField($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getDisplayableAttribute($parameters[0]), $message);
    }

    protected function replaceGreaterThanOrEqualTimeField($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getDisplayableAttribute($parameters[0]), $message);
    }

    protected function replaceGreaterThanOrEqual($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getDisplayableAttribute($parameters[0]), $message);
    }

    protected function replaceGreaterThanEqualForTimeStamp($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->getDisplayableAttribute($parameters[0]), $message);
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateKatakana($attribute, $value, $parameters)
    {
        $result = preg_match('/^([゠ァアィイゥウェエォオカガキギクグケゲコゴサザシジスズセゼソゾタダチヂッツヅテデトドナニヌネノハバパヒビピフブプヘベペホボポマミムメモャヤュユョヨラリルレロヮワヰヱヲンヴヵヶヷヸヹヺ・ーヽヾヿ]+)$/u', $value, $matches);
        return $result ? true : false;
    }
    /*
     * Validate that an attribute matches a date format.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @return bool
     */
    public function validateDateFormat($attribute, $value, $parameters)
    {
        $date = ['Y-m-d H:i:s' => getConstant('DEFAULT_DATE_TIME_VALUE'), 'Y-m-d' => getConstant('DEFAULT_DATE_VALUE'), 'H:i:s' => getConstant('DEFAULT_TIME_VALUE')];
        $vFormat = isset($parameters[0]) ? $parameters[0] : '';
        if (!$vFormat) {
            return parent::validateDateFormat($attribute, $value, $parameters);
        }
        foreach ($date as $format => $item) {
            if ($format === $vFormat && $item === $value) {
                return true;
            }
        }
        return parent::validateDateFormat($attribute, $value, $parameters);
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function validateArrayRequired($attribute, $value)
    {
        if (!is_array($value) && empty($value) && '0' !== (string)$value) {
            return false;
        }
        if (is_array($value)) {
            $value = array_filter($value);
            if (empty($value)) {
                return false;
            }
            foreach ($value as $item) {
                if (!$this->validateArrayRequired($attribute, $item)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validatePhone($attribute, $value, $parameters)
    {
        $regex = "/^\d{9,11}$/";
        if(strpos($value, '+') !== false){
            $regex = "/^\+?(?:[0-9] ?){9,11}[0-9]$/";
        }
        return preg_match($regex, $value);
    }

    /**
     * @param $attribute
     * @param $values
     * @param $parameters
     * @return bool
     */
    public function validateRequiredOne($attribute, $values, $parameters)
    {
        if (empty($parameters) || !is_array($parameters)) {
            return false;
        }
        $field = array_shift($parameters);
        foreach ($values as $index => $item) {
            if (isset($item[$field]) && !empty($item[$field])) {
                return true;
            }
            if (is_array($item)) {
                foreach ($item as $idx => $child) {
                    if (isset($child[$field]) && !empty($child[$field])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function validateAfterIfETADate($attribute, $values, $parameters)
    {
        $index = Arr::first(explode('.', $attribute), null, 0);
        $ETDDate = Arr::get($this->data, str_replace('*', $index,$parameters[0]));
        $ETADate = Arr::get($this->data, str_replace('*', $index,$parameters[1]));
        $ETDTime = Arr::get($this->data, str_replace('*', $index,$parameters[2]));

        if (empty($ETDDate) || empty($ETADate)) {
            return true;
        }

        if (empty($ETDTime) || empty($values)) {
            return true;
        }

        if ($ETDDate !== $ETADate) {
            return true;
        }

        if (strtotime($values) > strtotime($ETDTime)) {
            return true;
        }

        return false;
    }

    public function validateAfterIfETADateReality($attribute, $values, $parameters)
    {
        $index = Arr::first(explode('.', $attribute), null, 0);
        $ETDDate = Arr::get($this->data, str_replace('*', $index,$parameters[0]));
        $ETADate = Arr::get($this->data, str_replace('*', $index,$parameters[1]));
        $ETDTime = Arr::get($this->data, str_replace('*', $index,$parameters[2]));

        if (empty($ETDDate) || empty($ETADate)) {
            return true;
        }

        if (empty($ETDTime) || empty($values)) {
            return true;
        }

        if ($ETDDate !== $ETADate) {
            return true;
        }

        if (strtotime($values) > strtotime($ETDTime)) {
            return true;
        }

        return false;
    }

    public function validateCheckCustomer($attribute, $values, $parameters)
    {
        print_r('sodisoid');die;
        $status = Arr::get($this->data, $parameters[0]);
        $customerName = Arr::get($this->data, $parameters[1]);

        if (isset($status) && $status === config('constant.KHOI_TAO')) {
            return true;
        }

        if (!empty($customerName) && empty($values)) {
            return true;
        }

        return false;
    }

    protected function getExtraConditionsCustom(array $segments)
    {
        $extra = [];

        $count = count($segments);

        for ($i = 0; $i < $count; $i += 2) {
            if (preg_match('/\[(.*)\]/', $segments[$i + 1], $matches)) {
                $segments[$i + 1] = $this->getValue($matches[1]);
            }
            $extra[$segments[$i]] = $segments[$i + 1];
        }

        return $extra;
    }
}