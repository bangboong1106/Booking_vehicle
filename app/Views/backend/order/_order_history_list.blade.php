@if(!isset($order_history_list) || $order_history_list->isEmpty())
    <div class="empty-box"></div>
    <p class="text-center"><i>Không có lịch sử</i></p>
@else
    <div class="timeline">
        <article class="timeline-item alt">
            <div class="text-right">
                <div class="time-show first">
                    <a href="#" class="btn btn-danger w-lg">Bắt đầu</a>
                </div>
            </div>
        </article>
        @foreach($order_history_list as $indexKey => $order_history)
            @if($indexKey%2==0)
                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon"></span>
                                <h4> Trạng thái:
                                    @if($order_history->order_status == config("constant.TAI_XE_XAC_NHAN"))
                                        <span class="status bg-stpink text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.CHO_NHAN_HANG"))
                                        <span class="status bg-brown text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.DANG_VAN_CHUYEN"))
                                        <span class="status bg-blue text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.HOAN_THANH"))
                                        <span class="status bg-success text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.HUY"))
                                        <span class="status bg-dark text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @else
                                        <span class="status bg-secondary text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @endif

                                </h4>
                                <p class="timeline-date text-muted"><i class="fa fa-clock-o"></i>
                                    {{ \Carbon\Carbon::parse( $order_history->ins_date)->format('H:i:s d-m-Y') }}
                                </p>
                                <p><i class="fa fa-map-marker"></i>
                                    <a target="_blank"
                                       href="https://www.google.com/maps/search/?api=1&query={!!
                               urlencode($order_history->current_location) !!}">
                                        {{ $order_history->current_location }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </article>
            @else
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon"></span>
                                <h4> Trạng thái:
                                    @if($order_history->order_status == config("constant.TAI_XE_XAC_NHAN"))
                                        <span class="status bg-stpink text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.CHO_NHAN_HANG"))
                                        <span class="status bg-brown text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.DANG_VAN_CHUYEN"))
                                        <span class="status bg-blue text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.HOAN_THANH"))
                                        <span class="status bg-success text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @elseif($order_history->order_status == config("constant.HUY"))
                                        <span class="status bg-dark text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @else
                                        <span class="status bg-secondary text-white"> {{$order_history->getOrderStatus()}}
                                    </span>
                                    @endif

                                </h4>
                                <p class="timeline-date text-muted"><i class="fa fa-clock-o"></i>
                                    {{ \Carbon\Carbon::parse( $order_history->ins_date)->format('H:i:s d-m-Y') }}
                                </p>
                                <p><i class="fa fa-map-marker"></i>
                                    <a target="_blank"
                                       href="https://www.google.com/maps/search/?api=1&query={!!
                               urlencode($order_history->current_location) !!}">
                                        {{ $order_history->current_location }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </article>
            @endif
        @endforeach
        <article class="timeline-item alt">
            <div class="text-right">
                <div class="time-show">
                    <a href="#" class="btn btn-success w-lg">Kết thúc</a>
                </div>
            </div>
        </article>
    </div>
    <div class="map-detail">
        <p id="map-detail-info"><b>Tổng số km: <span class="totalChild" id="totalChild">0</span></b></p>
        <div class="mapChild" id="mapChild" style="height: 400px"></div>
    </div>
@endif
@push('scripts')
    <?php
    $jsFiles = [
        'autoload/order-detail'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush
