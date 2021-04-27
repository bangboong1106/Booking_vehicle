<style>
    .notify-icon {

    }

    .notify-avatar {
        float: left;
        height: 36px;
        width: 36px;
        line-height: 36px;
        text-align: center;
        margin-right: 10px;
        border-radius: 50%;
        font-size: 30px;
        background: #5b80bb;
    }
    .notify-content {
        font-size: 12px;
        white-space: pre-wrap;
        opacity: 0.6;
        margin-top: -15px;
    }
</style>

@if(!empty($notification) && isset($notification))
        @foreach($notification->notification as $entity)
            <a href="{{route('notification-log.clickToNotificationItem', $entity->id)}}"
               class="{{ $entity->read_status > 0 ? "dropdown-item notify-item notify-item-read" : "dropdown-item notify-item notify-item-unread"}}">
                <div class="notify-avatar"><i class="mdi mdi-comment-account"><img src="{{public_url('favicon.png')}}" alt="" style="width: 36px;
    height: 36px;
    margin-bottom: 12px;"></i></div>
                <p class="notify-details">{{ $entity->title }}
                    <small class="text-muted">{{ \Carbon\Carbon::parse( $entity->ins_date)->format('H:i:s d-m-Y') }}</small>
                </p>
                <div class="notify-content">{{ $entity->content }}</div>
            </a>
        @endforeach
{{--    <a href="javascript:void(0);" class="dropdown-item notify-item notify-all" id="read-all-notify">--}}
{{--        <h5 class="font-16"><span class="badge badge-danger float-right">{{ $notification->countUnread }}</span>Đánh--}}
{{--            dấu đã đọc</h5>--}}
{{--    </a>--}}
@else
    <a href="javascript:void(0);" class="dropdown-item notify-item notify-all">
        <h5 class="font-16"><span class="badge badge-danger float-right">0</span>Thu gọn</h5>
    </a>
    @endif
</div>
    <span hidden id="count">{{ $notification->countUnread }}</span>

<script>
    $(document).ready(function () {
        countNotification();

        function countNotification() {
            let count = $('#count').text();
            $('#count_notifications').html(count);
            $('.noti-icon-badge').html(count);
        }
    })
</script>
