<?php

namespace App\Common;

use App\Http\Controllers\Api\ApiAppClient\AuthController;
use App\Model\Entities\Queue;
use DateTime;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AppConstant
{
    const UPLOAD_FILE_TYPE_IMAGE = 1;
    const UPLOAD_FILE_TYPE_DOCUMENT = 2;

    const ALERT_LOG_TYPE_ALL = 0;
    const ALERT_LOG_TYPE_SINGLE = 1;
    const ALERT_LOG_TYPE_GROUP = 2;

    const PLATFORM_TYPE_MOBILE = 1;
    const PLATFORM_TYPE_WEB = 2;

    const NOTIFICATION_TYPE_ALL = 0;
    const NOTIFICATION_TYPE_SINGLE = 1;

    const NOTIFICATION_UNREAD = 0;
    const NOTIFICATION_READ = 1;

    const NOTIFICATION_SCREEN_ORDER = 1;
    const NOTIFICATION_SCREEN_VEHICLE = 2;
    const NOTIFICATION_SCREEN_DRIVER = 3;
    const NOTIFICATION_SCREEN_CLIENT = 4;
    const NOTIFICATION_SCREEN_BOARD = 5;
    const NOTIFICATION_SCREEN_VEHICLE_REPAIR = 6;
    const NOTIFICATION_SCREEN_ROUTE = 7;
    const NOTIFICATION_SCREEN_ORDER_CUSTOMER = 8;

    const NOTIFICATION_TYPE_WEB_ADMIN = 1;
    const NOTIFICATION_TYPE_WEB_CLIENT = 2;

    const DATE_YMD = 'Y-m-d';
    const DATE_DMY = 'd-m-Y';


    public static function generateTitlePN($name = '')
    {
        if (!empty($name)) {
            return $name . ' thông báo';
        }

        return 'Trung tâm điều hành thông báo';
    }

    public static function isDatePattern($pattern, $date)
    {
        try {
            Carbon::createFromFormat($pattern, $date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function isDate2GreatDate1($date1, $date2)
    {
        $dateTime1 = new DateTime($date1);
        $dateTime2 = new DateTime($date2);
        if ($dateTime2 > $dateTime1)
            return true;
        return false;
    }

    public static function DMS2Decimal($degrees = 0, $minutes = 0, $seconds = 0, $direction = 'n')
    {
        //converts DMS coordinates to decimal
        //returns false on bad inputs, decimal on success

        //direction must be n, s, e or w, case-insensitive
        $d = strtolower($direction);
        $ok = array('n', 's', 'e', 'w');

        //degrees must be integer between 0 and 180
        if (!is_numeric($degrees) || $degrees < 0 || $degrees > 180) {
            $decimal = false;
        } //minutes must be integer or float between 0 and 59
        elseif (!is_numeric($minutes) || $minutes < 0 || $minutes > 59) {
            $decimal = false;
        } //seconds must be integer or float between 0 and 59
        elseif (!is_numeric($seconds) || $seconds < 0 || $seconds > 59) {
            $decimal = false;
        } elseif (!in_array($d, $ok)) {
            $decimal = false;
        } else {
            //inputs clean, calculate
            $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

            //reverse for south or west coordinates; north is assumed
            if ($d == 's' || $d == 'w') {
                $decimal *= -1;
            }
        }

        return $decimal;
    }

    public static function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function convertDate($date, $format = 'Y-m-d')
    {
        if (empty($date)) {
            return '';
        }
        $date = str_replace('/', '-', $date);
        $result = '';

        if (AppConstant::validateDate($date, $format)) {
            $result = $date;
        } else if (AppConstant::validateDate($date, 'd-m-Y')) {
            $d = DateTime::createFromFormat('d-m-Y', $date);
            $result = $d->format($format);
        } else if (AppConstant::validateDate($date, 'Y-m-d')) {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            $result = $d->format($format);
        } else if (AppConstant::validateDate($date, 'd-m-Y H:i')) {
            $d = DateTime::createFromFormat('d-m-Y  H:i', $date);
            $result = $d->format($format);
        } else if (AppConstant::validateDate($date, 'Y-m-d H:i')) {
            $d = DateTime::createFromFormat('Y-m-d H:i', $date);
            $result = $d->format($format);
        }
        return $result;
    }

    public static function convertTime($time, $format = 'H:i')
    {
        if (empty($time)) {
            return '';
        }
        $result = date($format, strtotime($time));

        return $result;
    }

    public static function getImagePath($path, $type)
    {
        $imagePath = '';
        if (!empty($path)) {
            $file_types = config('system.file_type');
            foreach ($file_types as $key => $value) {
                if (strrpos($value, $type)) {
                    switch ($key) {
                        case 1:
                            $imagePath = getenv('BASE_IMAGE_URL', '') . $path;
                            break;
                        case 2:
                            $imagePath = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/excel.png';
                            break;
                        case 3:
                            $imagePath = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/word.png';
                            break;
                        case 4:
                            $imagePath = getenv('BASE_IMAGE_URL', '') . 'css/backend/img/pdf.png';
                            break;
                        default:
                            $imagePath = getenv('BASE_IMAGE_URL', '') . $path;
                    }
                    break;
                }
            }
        }
        return $imagePath;
    }

    public static function isEmptyAndZero($value)
    {
        if (empty($value) || $value == 0 || $value == '0') {
            return true;
        }
        return false;
    }

    public static function getListGenerateSystemCodeConfig()
    {
        return include('GenerateSystemCodeConfig.php');
    }

    /** Lưu event vào hàng đợi
     * @param $object
     */
    public static function event($object)
    {
        $queue = new Queue();
        $queue->event = get_class($object);
        $queue->data = json_encode($object);
        $queue->attempts = 0;
        $queue->created_at = now();

        $queue->save();
    }

    /**
     * Xử lý event trong hàng đợi
     */
    public static function listen()
    {
        $eventListenerMap = include('EventListenerMap.php');
        $queues = Queue::orderBy('id', 'ASC')->lockForUpdate()->get();
        $eventData = null;
        if ($queues && count($queues) > 0)
            foreach ($queues as $queue) {
                //Cho chạy lại 2 lần nếu lỗi
                if ($queue->attempts >= 2)
                    continue;
                try {
                    $event = $queue->event;
                    $listener = $eventListenerMap[$event];
                    if ($listener) {
                        $eventData = json_decode($queue->data, true);
                        $result = app($listener)->handle($eventData, $queue);

                        //Xóa event
                        if ($result)
                            $queue->delete();
                    }
                } catch (Exception $e) {
                    logError($e . ' - Data :' . $queue);
                } catch (Throwable $e) {
                    logError($e . ' - Data :' . $queue);
                }
            }
    }

    /**
     * Escape values according to mysql.
     *
     * @param $fieldValue
     * @return array|string|string[]
     */
    public static function mysql_escape($fieldValue)
    {
        if (is_array($fieldValue)) {
            return array_map(__METHOD__, $fieldValue);
        }

        if (!empty($fieldValue) && is_string($fieldValue)) {
            return str_replace(
                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                $fieldValue
            );
        }

        return $fieldValue;
    }
}