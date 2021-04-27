<div class="route-list container-fluid">
    <div class="row" style="margin: 8px">
        <div class="col-4">
            <label>Số đơn hàng</label><br/>
            {{ $entity->order_no }}
        </div>
        <div class="col-4">
            <label>Chủ hàng</label><br/>
            {{isset($entity->customer) ? $entity->customer->full_name : "-" }}
        </div>
        <div class="col-4">
            <label>Trạng thái</label><br />
            {!! $entity->getStatusOnList() !!}
        </div>
    </div>
    <div class="row" style="margin: 8px">
        <div class="col-4">
            <label>Tình trạng xuất hàng</label><br />
            {!! $entity->getStatusGoods() !!}
        </div>
        <div class="col-4">
            <label>Tổng dung tích</label><br />
            {{ $entity->volume }}
        </div>
        <div class="col-4">
            <label>Tổng khối lượng</label><br />
            {{ $entity->weight }}
        </div>
    </div>
    @if (count($orders) == 0)
        <div class="row">
            <div class="empty-box" style="top: 50% !important;">
                <span><i>Không thấy chuyến xe trong khoảng thời gian hiện tại</i></span>
            </div>
        </div>
    @else
        <div class="row" id="header" style="padding: 0 16px;">
            <div class="col-2 head">Mã đơn hàng vận tải</div>
            <div class="col-2 head">Đối tác vận tải</div>
            <div class="col-2 head">Xe</div>
            <div class="col-2 head">Trạng thái</div>
            <div class="col-2 head">Thời gian nhận hàng</div>
            <div class="col-2 head">Thời gian trả hàng</div>

        </div>
        <div class="row" id="body" style="padding: 0 16px 16px;">
            @foreach ($orders as $order)
                <div class="col-12 row detail">
                    <div class="col-2 content">
                        <a href="#" class="order-detail" data-id={{ $order->id }}
                            data-show-url="{{ route('order.show', $order->id) }}">
                            {{ $order->order_code }}
                        </a>
                    </div>
                    <div class="col-2 content">{{ $order->partner_name }}</div>
                    <div class="col-2 content">{{ $order->reg_no }}</div>
                    <?php
                    $statuses = config('system.order_status');
                    $status_partners = config('system.order_status_partner');

                    switch ($order->status) {
                        case config("constant.CHO_NHAN_HANG"):
                            $class = 'brown';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.DANG_VAN_CHUYEN"):
                            $class = 'blue';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.HOAN_THANH"):
                            $class = 'success';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.HUY"):
                            $class = 'dark';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.TAI_XE_XAC_NHAN"):
                            $class = 'stpink';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.SAN_SANG"):
                            $class = 'secondary';
                            $title = $statuses[$order->status];
                            break;
                        case config("constant.KHOI_TAO"):
                            $class = 'light';
                            $title = isset($status_partners[$order->status_partner]) ? $status_partners[$order->status_partner] : '';
                            break;
                    }
                    $status_string = '<span class="badge badge-' . $class . '">' . $title . '</span>';
                    ?>
                    <div class="col-2 content">{!! $status_string !!}</div>
                    <div class="col-2 content">
                        {{ \Carbon\Carbon::parse($order->ETD_date)->format('d-m-Y') . ' ' . \Carbon\Carbon::parse($order->ETD_time)->format('H:i') }}
                    </div>
                    <div class="col-2 content">
                        {{ \Carbon\Carbon::parse($order->ETA_date)->format('d-m-Y') . ' ' . \Carbon\Carbon::parse($order->ETA_time)->format('H:i') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<style>
    .route-list #header .head {
        font-weight: bold;
        border: 1px solid #e5e5e5;
        padding: 4px;
        text-align: center;
        background: #e5e5e5;
    }

    .route-list #body .detail {
        margin: 0;
        padding: 0;
    }

    .route-list #body .content {
        border: 1px solid #e5e5e5;
        padding: 8px;
    }

</style>
