<?php

namespace App\Helpers;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;

/**
 * Class ChannelWriter
 * @package App\Helpers
 */
class ChannelWriter
{
    /**
     * The Log channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug' => Logger::DEBUG,
        'info' => Logger::INFO,
        'notice' => Logger::NOTICE,
        'warning' => Logger::WARNING,
        'error' => Logger::ERROR,
        'critical' => Logger::CRITICAL,
        'alert' => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    /**
     * ChannelWriter constructor.
     */
    public function __construct()
    {
        $this->channels = [
            'info' => [
                'path' => getSystemConfig('log_info_filename', 'info'),
                'level' => Logger::INFO
            ],
            'api' => [
                'path' => getSystemConfig('log_api_filename', 'api'),
                'level' => Logger::INFO
            ],
            'error' => [
                'path' => getSystemConfig('log_error_filename', 'error'),
                'level' => Logger::ERROR
            ],
            'debug' => [
                'path' => getSystemConfig('log_debug_filename', 'debug'),
                'level' => Logger::DEBUG
            ],
            'warn' => [
                'path' => getSystemConfig('log_warning_filename', 'warning'),
                'level' => Logger::WARNING
            ]
        ];
    }

    /**
     * Write to log based on the given channel and log level set
     *
     * @param type $channel
     * @param type $message
     * @param array $context
     * @throws InvalidArgumentException
     */
    public function writeLog($channel, $level, $message, array $context = [])
    {
        if (is_array($message)) {
            $message = serialize($message);
        }
        //check channel exist
        if (!in_array($channel, array_keys($this->channels))) {
            throw new \InvalidArgumentException('Invalid channel used.');
        }

        //lazy load logger
        if (!isset($this->channels[$channel]['_instance'])) {
            //create instance
            $this->channels[$channel]['_instance'] = new Logger($channel);
            //add custom handler
            $this->channels[$channel]['_instance']->pushHandler($this->_getHandler($channel, $level));
        }

        //write out record
        $this->channels[$channel]['_instance']->{$level}($message, $context);
    }

    protected function _getHandler($channel, $level)
    {
        if (config('app.logs.default') == 'local') {
            return new ChannelStreamHandler(
                $channel,
                storage_path() . '/logs/' . date('Y-m-d') . DIRECTORY_SEPARATOR . $this->channels[$channel]['path'] . '-' . date('Y-m-d') . '.log',
                $this->channels[$channel]['level']
            );
        }
        $config = config('app.logs.cloud_watch');
        $sdkParams = [
            'region' => array_get($config, 'region'),
            'version' => 'latest',
            'credentials' => [
                'key' => array_get($config, 'key'),
                'secret' => array_get($config, 'secret'),
                'token' => array_get($config, 'token'), // token is optional
            ]
        ];

        // Instantiate AWS SDK CloudWatch Logs Client
        $client = new CloudWatchLogsClient($sdkParams);

        // Log group name, will be created if none
        $groupName = array_get($config, 'group_name');

        // Log stream name, will be created if none
        $streamName = $level;

        // Days to keep logs, 14 by default
        $retentionDays = array_get($config, 'retention_days');

        // Instantiate handler (tags are optional)
        return new CloudWatch($client, $groupName, $streamName, $retentionDays, 10000, [], $this->channels[$channel]['level']);

    }

    /**
     * @param $channel
     * @param $message
     * @param array $context
     */
    public function write($channel, $message, array $context = [])
    {
        //get method name for the associated level
        $level = array_flip($this->levels)[$this->channels[$channel]['level']];
        //write to log
        $this->writeLog($channel, $level, $message, $context);
    }

    //alert('event','Message');

    /**
     * @param $func
     * @param $params
     */
    function __call($func, $params)
    {
        if (in_array($func, array_keys($this->levels))) {
            return $this->writeLog($params[0], $func, $params[1], isset($params[2]) ? $params[2] : []);
        }
    }

}