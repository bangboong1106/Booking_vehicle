<?php

namespace App\Events;

/**
 * Trait BaseEvent
 * @package App\Events
 */
trait BaseEvent
{
    protected $eventSuffix = '';

    protected $eventPrefix = '';

    /**
     * @return string
     */
    public function getEventPrefix(): string
    {
        return $this->eventPrefix;
    }

    /**
     * @param string $eventPrefix
     */
    public function setEventPrefix(string $eventPrefix)
    {
        $this->eventPrefix = $eventPrefix;
    }

    /**
     * @return string
     */
    public function getEventSuffix(): string
    {
        return $this->eventSuffix;
    }

    /**
     * @param string $eventSuffix
     */
    public function setEventSuffix(string $eventSuffix)
    {
        $this->eventSuffix = $eventSuffix;
    }

    /**
     * @param $name
     * @param $data
     */
    public function fireEvent($name, &$data)
    {
        $nameC = camel_case($name);
        if (method_exists($this, $nameC)) {
            $this->{$nameC}($data);
        }
        $name = $this->getEventName($name);
        $realName = $this->eventPrefix . '.' . $name . '.' . $this->eventSuffix;
        $r = event($realName, [$realName, $data], true);
        if ($r) {
            $data = $r;
        }
    }

    public function getEventName($name)
    {
        return getEventName($name);
    }
}