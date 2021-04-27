<?php

namespace App\Http\Controllers\Backend\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class MailResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public static $toMailCallback;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        DB::table('admin_users')
            ->where('email', $notifiable->email)
            ->update(['remember_token' => $this->token]);

        return (new MailMessage)
//            ->view('backend.auth.email-template-reset-pw', ['data' => $notifiable])
            ->markdown('backend.auth.email-template-reset-pw', ['data' => $notifiable])
            ->priority(1)
            ->subject("Đổi mật khẩu E-log")
            ->greeting("Chào bạn,")
            ->line('Email này là để đổi mật khẩu, chún  g tôi đã nhận được một yêu cầu từ tài khoản của bạn.')
            ->action('Đổi Mật Khẩu', url('password/reset' , $this->token))
            ->line('Nếu bạn không muốn thay đổi nó có thể bỏ qua email này.');

    }

}
