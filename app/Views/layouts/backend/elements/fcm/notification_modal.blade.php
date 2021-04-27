<a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#"
   role="button" aria-haspopup="false" aria-expanded="false">
    <i class="fa fa-bell noti-icon"></i>
    <span class="badge badge-pink noti-icon-badge">{{ $notification->countUnread }}</span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg notification-list-header"
     aria-labelledby="Preview">
    @if(!empty($notification) && isset($notification))
        @foreach($notification->notification as $entity)
            <a href="{{route('notification-log.clickToNotificationItem', $entity->id)}}"
               class="{{ $entity->read_status > 0 ? "dropdown-item notify-item notify-item-read" : "dropdown-item notify-item notify-item-unread"}}">
                <div class="notify-icon notify-type-priority-2"><i class="mdi mdi-comment-account"></i></div>
                <p class="notify-details">{{ $entity->title }}
                    <small class="text-muted">{{ \Carbon\Carbon::parse( $entity->ins_date)->format('H:i:s d-m-Y') }}</small>
                </p>
            </a>
        @endforeach
        <a href="javascript:void(0);" class="dropdown-item notify-item notify-all" id="read-all-notify">
            <h5 class="font-16"><span class="badge badge-danger float-right">{{ $notification->countUnread }}</span>Đánh
                dấu đã đọc</h5>
        </a>
        <a class="dropdown-item notify-item" href="{{route('notification.index')}}"
           style="color: blue;cursor: pointer;">Xem tất cả</a>
    @else
        <a href="javascript:void(0);" class="dropdown-item notify-item notify-all">
            <h5 class="font-16"><span class="badge badge-danger float-right">0</span>Thu gọn</h5>
        </a>
    @endif

</div>