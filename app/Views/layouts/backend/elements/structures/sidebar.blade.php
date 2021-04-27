<style>
    #notification-wrap {
        height: 93vh;
        margin-top: 12px;
        margin-right: 10px;
        width: 360px;
        right: -90px;
        left: auto;

    }

    /*.dropdown-user {*/
    /*    height: 93vh;*/
    /*    margin-top: 12px;*/
    /*    margin-right: 2px;*/
    /*    width: 150px;*/
    /*    left: auto;*/
    /*    top: 25px !important;*/
    /*}*/

    #notification-wrap:after {
        position: absolute;
        content: '';
        width: 10px;
        height: 10px;
        bottom: 100%;
        right: 95px;
        margin-bottom: -5px;
        transform: rotate(45deg);
        background: white;
    }

    /*.dropdown-user:after {*/
    /*    position: absolute;*/
    /*    content: '';*/
    /*    width: 10px;*/
    /*    height: 10px;*/
    /*    bottom: 100%;*/
    /*    right: 22px;*/
    /*    margin-bottom: -5px;*/
    /*    transform: rotate(45deg);*/
    /*    background: white;*/
    /*}*/

    .notification-item {
        display: flex;
        height: 4vh;
        width: 100%;
        text-decoration: none;
        font-size: 90%;
        position: absolute;
        bottom: 8px;
        box-sizing: inherit;
        background: white;
    }

    .notification-item a {
        padding-bottom: 1px;
    }

    .loading-notify {
        position: absolute;
        justify-content: center;
        height: 50px;
        width: 50px;
        background-size: 60px;
        background-repeat: no-repeat;
        left: 40%;
        top: 40%;
        z-index: 100;
        border: 6px solid #f3f3f3;
        border-radius: 50%;
        border-top: 6px solid #3498db;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    #show_notification {
        position: relative;
        overflow: scroll;
        height: 80vh;
    }

    #show_notification::-webkit-scrollbar {
        display: none;
    }

    .notify-details {
        text-overflow: ellipsis;
        white-space: pre-line;
        overflow: hidden;
        padding-left: 5px;
    }


</style>
<div class="topbar">
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{route('board.index')}}" class="logo logo-with-brand"><img src="{{asset('css/backend/images/McLean-logo-2.png')}}" style="height: 42px;"></a>
            <a href="{{route('board.index')}}" class="logo logo-no-brand"><img src="{{asset('css/backend/images/McLean-logo-3.png')}}" style="height: 43px;"></a>
                {{-- <span>CETA<span style="font-size: 14px; text-transform: none"> by {{config('constant.APP_COMPANY')}}</span></span></a> --}}
                {{-- <span>{{config('constant.APP_NAME')}}</span></a> --}}
        </div>
    </div>
    <nav class="navbar-custom">
        <ul class="list-inline float-right mb-0">
            <li class="full-search list-inline-item hide-phone" id="fullsearch-item-inline">
                <input type="text" class="form-control" aria-label="Search" id="fullsearch" placeholder="Tìm kiếm">
                <a href="" class="icon-search"><i class="fa fa-search"></i></a>
            </li>
            <li class="list-inline-item add-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="fa fa-plus-square-o noti-icon" style="font-size: 24px;vertical-align: middle;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" role="menu" id="AddNewItemMenu"
                     style="overflow-y:visible; max-height: none;">
                   {{-- @if ($routePrefix != 'order')
                        @can('add order')
                            <a href="javascript:void(0)" class="dropdown-item notify-item in-entity-modal quick-add"
                               data-model="order" data-url="{{route('order.advance')}}">
                                <i class="fa fa-server font-16"></i>{{trans('models.common.add_order')}}
                            </a>
                        @endcan
                    @endif--}}
                   {{-- @can('add route')
                        <a class="dropdown-item notify-item in-entity-modal" data-action="Create"
                           data-entity-type="Route"
                           href="{{route('route.create')}}">
                            <i class="fa fa-barcode font-16"></i>{{trans('models.common.add_route')}}
                        </a>
                    @endcan--}}
                    @can('add quota')
                        <a class="dropdown-item notify-item in-entity-modal" data-action="Create"
                           data-entity-type="Quota"
                           href="{{route('quota.create')}}">
                            <i class="fa fa-google-wallet font-16"></i>{{trans('models.common.add_quota')}}
                        </a>
                    @endcan
                    @can('add customer')
                        <a class="dropdown-item notify-item in-entity-modal" data-action="Create"
                           data-entity-type="Customer"
                           href="{{route('customer.create')}}">
                            <i class="fa fa-address-book font-16"></i>{{trans('models.common.add_customer')}}
                        </a>
                    @endcan
                    @can('add driver')
                        <a class="in-entity-modal dropdown-item notify-item" data-action="Create"
                           data-entity-type="Driver"
                           href="{{route('driver.create')}}">
                            <i class="fa fa-drivers-license font-16"></i>{{trans('models.common.add_driver')}}
                        </a>
                    @endcan
                    @can('add vehicle')
                        <a class="in-entity-modal dropdown-item notify-item" data-action="Create"
                           data-entity-type="Vehicle"
                           href="{{route('vehicle.create')}}">
                            <i class="fa fa-truck font-16"></i>{{trans('models.common.add_vehicle')}}
                        </a>
                    @endcan
                </div>
            </li>
            <li class="list-inline-item dropdown notification-list notification-list-header">
                <span id="notification-link" data-url="{{route('notification-log.getNotification')}}"
                      style="cursor: pointer">
                    <i class="fa fa-bell noti-icon"></i>
                    <span class="badge badge-pink noti-icon-badge" style="top: 8px">{{ $countUnread }}</span>
                </span>
                <a id="notification-hyperlink" class="nav-link dropdown-toggle arrow-none waves-light waves-effect"
                   data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false"></a>
                <div id="notification-wrap"
                     class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg notification-list-header"
                     aria-labelledby="Preview">

                    <span class="dropdown-item notify-item" style="border-bottom: 1px lightgray solid"><strong>Thông báo<span
                                    id="count_notifications" class="badge badge-danger"
                                    style="margin-left: 3px"></span></strong></span>
                    <div class="loading-notify"></div>
                    <div id="show_notification"></div>
                    <div class="notification-item">
                        <a class="dropdown-item" href="{{route('notification.index')}}"
                           style="color: blue;cursor: pointer;">Xem tất cả</a>
                        <a class="dropdown-item" href="javascript:void(0);"
                           style="color: blue;cursor: pointer;" id="read-all-notify">Đánh dấu đã đọc</a>
                    </div>
                </div>
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a id="profile-hyperlink" class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#"
                   role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <img src="{{ route('file.getImage',
                        ['id' => empty(backendGuard()->user()->avatar_id) ? '00000000-0000-0000-0000-000000000000' :backendGuard()->user()->avatar_id,
                         'width' => 100, 'height' => 100]) }}"
                         alt="user" class="rounded-circle">
                    {{--                    @else--}}
                    {{--                        <i class="fa fa-user-o" aria-hidden="true"></i>--}}
                    {{--                    @endif--}}
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown dropdown-user" aria-labelledby="Preview" id="dropdown-profile">
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow">
                            <small>
                                <b><span class="user-name">{!! backendGuard()->user()->full_name !!}</span></b>
                            </small>
                        </h5>
                    </div>
                    <a href="{{backUrl('admin.profile')}}" class="dropdown-item notify-item">
                        <i class="fa fa-user"></i> <span>{{trans('auth.profile')}}</span>
                    </a>
                    @if (\Auth::user()->role == 'admin')
                        <a href="{{route('get-started.index')}}" class="dropdown-item notify-item">
                            <i class="fa fa-info-circle" aria-hidden="true"></i> <span>{{trans('models.get_started.name')}}</span>
                        </a>
                    @else 
                        <a href="{{route('partner-get-started.index')}}" class="dropdown-item notify-item">
                            <i class="fa fa-info-circle" aria-hidden="true"></i> <span>{{trans('models.get_started.name')}}</span>
                        </a>
                    @endif
                    <a href="{{route('backend.logout')}}" class="dropdown-item notify-item">
                        <i class="fa fa-sign-out" aria-hidden="true"></i> <span>{{trans('auth.logout')}}</span>
                    </a>
                </div>
            </li>
        </ul>

        <ul class="list-inline menu-left mb-0">
            <li class="float-left ms-hover">
                <button class="button-menu-mobile open-left waves-light waves-effect">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </button>
            </li>
            @can('view order')
                <li class="float-left mce-menu-shortcut hide-phone menubar-left-order-dash">
                    <a href="{{route("order.index")}}" class="waves-light waves-effect">Đơn hàng</a>
                </li>
            @endcan
            @can('view dashboard')
                <li class="float-left mce-menu-shortcut hide-phone menubar-left-order-dash">
                    <a href="{{route("order-board.index")}}" class="waves-light waves-effect">Vận hành</a>
                </li>
            @endcan
            @can('view report')
                <li class="float-left mce-menu-shortcut hide-phone" id="menubar-left-report">
                    <a href="{{route("report.index")}}" class="waves-light waves-effect">Báo cáo</a>
                </li>
            @endcan
            <li class="float-left mce-menu-shortcut hide-phone">
                <a href="{{route('journey.index')}}" class="waves-light waves-effect">
                    {{ trans('models.journey.attributes.map') }}
                </a>
            </li>
        </ul>
    </nav>
</div>
<script>
    let urlFullSearch = '{{route('quicksearch.full-search')}}',
        urlSaveColumnConfig = '{{route('column-config.saveColumnConfig')}}';
</script>