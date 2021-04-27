<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Log;

class OrderJob extends Command
{
    protected $signature = 'order:jobs';

    protected $description = 'Order Job check status to notify';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = Auth::User()->id;

        $orders = app('App\Http\Controllers\Backend\OrderController')->getExpiredItem();
        if ($orders != null) {
            foreach ($orders as $order) {
                $title = "Đơn hàng " . $order->order_code . " sắp hết hạn";
                $message = "Ngày nhận hàng : " . $order->ETD_date . "\n Ngày trả hàng : " . $order->ETA_date;
                app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationWeb($userId, $title, $message, 1, 1);
            }
        }
    }
}
