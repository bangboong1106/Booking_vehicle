<a href="{{route('notification-log.clickToNotificationItem', $notification->actionId)}}">
    <div class="notification-box-item-icon notify-type-priority-2"><i class="mdi mdi-comment-account"></i></div>
    <p class="notification-box-item-title">{{ $notification->title }}
        <small class="notification-box-item-message">{{ $notification->message }}</small>
    </p>
</a>
<span class="notification-box-item-close" onclick="$('.notification-box-item').fadeOut()">x</span>