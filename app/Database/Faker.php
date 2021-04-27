<?php

namespace App\Database;

use Faker\Generator;

class Faker extends Generator
{
    const LANG_JA = 'ja';
    const LANG_EN = 'en_US';
    const LANG_KR = 'ko';
    const LANG_CN_HAN = 'zh_CN';
    const LANG_CN_KAN = 'zh_TW';
    const LANG_TL = 'th_TH';
    const LANG_NON = '';

    public $lang = 'ja';

    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    public function getFormatter($formatter)
    {
        $lang = '_' . $this->lang;
        $newFormatter = $formatter . $lang;
        if (isset($this->formatters[$newFormatter])) {
            return $this->formatters[$newFormatter];
        }

        foreach ($this->providers as $provider) {
            $className = $this->_getClassFileName($provider);
            if (strpos($className, $this->lang) !== false && method_exists($provider, $formatter)) {
                $this->formatters[$newFormatter] = array($provider, $formatter);
                return $this->formatters[$newFormatter];
            }
        }
        throw new \InvalidArgumentException(sprintf('Unknown formatter "%s"', $newFormatter));
    }

    protected function _getClassFileName($className){
        $reflector = new \ReflectionClass($className);
        return $reflector->getFileName();
    }
}