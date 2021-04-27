<?php

namespace App\Model\Presenters;

use App\Helpers\Facades\CustomStorage;
use Illuminate\Support\Carbon;

/**
 * Trait Base
 * @package App\Model\Presenters
 */
trait Base
{
    protected $urlAttributes = [];

    /**
     * @return array
     */
    public function getUrlAttributes()
    {
        return $this->urlAttributes;
    }

    /**
     * @param array $urlAttributes
     */
    public function setUrlAttributes($urlAttributes)
    {
        $this->urlAttributes = $urlAttributes;
    }

    /**
     * @param string $field
     * @param array $options
     * @return string
     */
    public function getTmpFile($field = '', $options = [
        'name' => 'img',
        'class' => 'tmp-file',
        'height' => '250px'
    ])
    {
        $o = '';
        $options['src'] = $this->tmp_file ? $this->getFileUrl(array_get($this->tmp_file, $field)) : $this->getFileUrl($this->$field);
        foreach ($options as $key => $option) {
            $o .= $key . '="' . $option . '" ';
        }
        $html = '<img ' . $o . '/>';
        return $html;
    }

    /**
     * @param string $field
     * @param array $options
     * @return string
     */
    public function getImgView($field = '', $options = [
        'refresh' => false,
        'class' => 'img',
        'height' => '150px'
    ])
    {
        $o = '';
        $options['class'] = isset($options['class']) ? $options['class'] : 'img';
        $options['src'] = $this->getFileUrl($this->$field);
        if (isset($options['refresh']) && $options['refresh']) {
            $options['src'] .= '?v=' . time();
        }
        foreach ($options as $key => $option) {
            $o .= $key . '="' . $option . '" ';
        }
        $html = '<img ' . $o . '/>';
        return $html;
    }


    /**
     * @param $fileName
     * @return mixed
     */
    public function getFileUrl($fileName)
    {
        return CustomStorage::url($fileName);
    }


    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->{getDelFlagColumn()} == $this->getDelFlagValue(true);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getCreatedAtValue($format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($this->{getCreatedAtColumn()})->format($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getUpdatedAtValue($format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($this->{getUpdatedAtColumn()})->format($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getDeletedAtValue($format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($this->{getDeletedAtColumn()})->format($format);
    }

    /**
     * @return mixed
     */
    public function getDeletedByValue()
    {
        return $this->{getDeletedByColumn()};
    }

    /**
     * @return mixed
     */
    public function getUpdatedByValue()
    {
        return $this->{getUpdatedByColumn()};
    }

    /**
     * @return mixed
     */
    public function getCreatedByValue()
    {
        return $this->{getCreatedByColumn()};
    }

    // override base
    protected function getArrayableItems(array $values)
    {
        if (count($this->getUrlAttributes()) <= 0) {
            return parent::getArrayableItems($values);
        }

        foreach ($this->getUrlAttributes() as $attribute) {
            if (!array_key_exists($attribute, $values)) {
                continue;
            }
            $values[$attribute] = $this->getFileUrl($values[$attribute]);
        }
        return parent::getArrayableItems($values);
    }

    public function trans($attribute, $value = '')
    {
        //@todo translate
        return $value;
    }
}