<?php

namespace App\Events\Listeners\Base;

use Illuminate\Support\Facades\Event;

/**
 * Class Listeners
 * @package App\Events
 */
class Listeners
{
    /**
     * @param $eventName
     * @param $callback
     * @param $type
     */
    public function listen($eventName, $callback, $type)
    {
        $eventName = explode('.', $eventName);
        $last = array_pop($eventName);
        $tmpEventName = getEventName($type . '.' . $eventName[0]);
        unset($eventName[0]);
        $eventName = $type . '.' . $tmpEventName . '.' . implode('.', $eventName) . '.' . $last;
        $eventName = str_replace_first('..', '.', $eventName);
        if($type === getConstant('EVENT_MODEL_TYPE')){
            $eventName = str_replace('.*', ':*', $eventName);
        }
        $this->_listen($eventName, $callback);
    }

    /**
     * @param $eventName
     * @param $callback
     */
    protected function _listenControllerEvent($eventName, $callback)
    {
        $this->listen($eventName, $callback, getConstant('EVENT_CONTROLLER_TYPE'));
    }

    /**
     * @param $eventName
     * @param $callback
     */
    protected function _listenModelEvent($eventName, $callback)
    {
        $this->listen($eventName, $callback, getConstant('EVENT_MODEL_TYPE'));
    }

    /**
     * @param: $realName is the name of the event that Passed from event fire function
     * @param $eventName
     * @param $callback
     */
    protected function _listen($eventName, $callback)
    {
        Event::listen($eventName, function ($realName, $arg) use ($eventName, $callback) {
            $arg = strpos($eventName, '.*') !== false && isset($arg[1]) ? $arg[1] : $arg;
            return $callback($arg);
        });
    }
}