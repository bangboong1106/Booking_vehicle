<?php

namespace App\Common;

/**
 * StatusCodes provides named constants for
 * HTTP protocol status codes. Written for the
 * Recess Framework (http://www.recessframework.com/)
 *
 * @author Kris Jordan
 * @license MIT
 * @package recess.http
 */
class HttpCode
{

    private static $messages = array(
        0 => 'Thành công',
        -1 => 'Có lỗi xảy ra trong quá trình xử lý',
        1 => 'Thông tin không đúng định dạng',
        2 => 'Thông tin đăng nhập không chính xác',
        3 => 'Xác thực request không hợp lệ',
        4 => 'Thông tin gửi lên không hợp lệ'
    );

    public static function httpHeaderFor($code)
    {
        return 'HTTP/1.1 ' . self::$messages[$code];
    }


    public static function getMessageForCode($code)
    {
        if (array_key_exists($code, self::$messages)) {
            return self::$messages[$code];
        } else {
            $code;
        }
    }

    public static function isError($code)
    {
        return is_numeric($code) && $code >= self::EC_APPLICATION_ERROR;
    }

    /*
        Custom error code - error message
    */
    const EC_OK = 0;
    const EC_APPLICATION_ERROR = -1;
    const EC_BAD_REQUEST = 1;
    const EC_LOGIN_FAILED = 2;
    const EC_UNAUTHENTICATED = 3;
    const EC_UPLOAD_FILE_ERROR = 4;
    const EC_UPLOAD_FILE_BAD_REQUEST = 4;
    const EC_APPLICATION_WARNING = 5;

    const EM_OLD_PASSWORD_NOT_MATCH = "Mật khẩu cũ không chính xác";
    const EM_NEW_PASSWORD_DUPLICATE = "Mật khẩu mới không được trùng với mật khẩu cũ";
    const EM_UPLOAD_FILE_ERROR = "Tải file lên không thành công";
    const INTERNAL_SERVER_ERROR = "Server Error";
}
