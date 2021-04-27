<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

/**
 * Class Sorting
 * @package App\Helpers
 */
class Sorting
{
    protected static $_field = '';

    /**
     * @return string
     */
    public static function getField()
    {
        return self::$_field;
    }

    /**
     * @param string $field
     */
    public static function setField($field)
    {
        self::$_field = $field;
    }


    /**
     * @param $field
     * @param $text
     * @param $hasData
     * @param array $attrs
     * @return string
     */
    static public function thLink($field, $text = null, $attrs = array())
    {
        $text = self::getText($field, $text);
        // $hasData = getViewData('entities')->total();
        $hasData = true;
        $r = $hasData ? '<th' . self::build($field, $text, $attrs, false) . '</th>' : '<th>' . $text . '</th>';
        return $r;
    }

    /**
     * @param $field
     * @param $text
     * @param $attrs
     * @param bool $a
     * @return string
     */
    public static function build($field, $text, $attrs, $a = false)
    {
        self::setField($field);
        $link = self::_collectUrl($field);
        $class = static::_getClass();
        $newAttrs = [
            'class' => isset($attrs['class']) ? $attrs['class'] . ' ' . $class : $class,
            'data-sort' => $field
        ];
        $a ? $newAttrs['href'] = $link : null;
        $attrs = static::attributes(array_merge($attrs, $newAttrs));
        $r = $attrs . ' >' . $text;
        return $r;
    }

    /**
     * @param $field
     * @param $text
     * @param array $attrs
     * @return string
     */
    static public function aLink($field, $text = null, $attrs = array())
    {
        $text = self::getText($field, $text);
        $hasData = true;
        $r = $hasData ? '<a' . self::build($field, $text, $attrs, true) . '</a>' : $text;
        return $r;
    }

    public static function getText($field, $text)
    {
        return is_null($text) ? getViewData('model')->getAttributeName($field) : $text;
    }

    /**
     * @return mixed
     */
    static public function _getType()
    {
        $field = self::getField();
        return Request::get('sort_field') == $field ? Request::get('sort_type', 'desc') : '';
    }

    /**
     * @return string
     */
    static public function _getNewType()
    {
        return strtolower(self::_getType()) == 'desc' ? 'asc' : 'desc';
    }

    /**
     * @return string
     */
    static public function _getClass()
    {
        $field = self::getField();
        $dataIndex = getViewData('dataIndex');
        if (isset($dataIndex['sort_field']) && $dataIndex['sort_field'] == $field)
        {
            return 'table-sorting sorting '.(empty($dataIndex['sort_type']) ? 'sorting_asc':'sorting_'.$dataIndex['sort_type']);
        }
        return 'table-sorting sorting';
    }

    /**
     * @param $field
     * @return string
     */
    static public function _collectUrl($field)
    {
        $query = Request::query();
        $query['sort_type'] = self::_getNewType();
        $query['sort_field'] = $field;
        $current = url()->current();
        return $current . '?' . http_build_query($query);
    }

    static protected function _attributeElement($key, $value)
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numerics.
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key != 'value') {
            return $value ? $key : '';
        }

        if (!is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
    }

    static public function attributes($attributes)
    {
        $html = [];

        foreach ((array)$attributes as $key => $value) {
            $element = static::_attributeElement($key, $value);

            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
}