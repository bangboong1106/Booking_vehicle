<?php

namespace App\Helpers;


class Translator extends \Illuminate\Translation\Translator
{
    /**
     * Get the translation for the given key.
     *
     * @param  string $key
     * @param  array $replace
     * @param  string|null $locale
     * @param  bool $fallback
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ? $locale : getCurrentLangCode();
        return parent::get($key, $replace, $locale, $fallback);
    }
}
