<div class="route-list container">
    <div class="row">
        <div class="col-12">
            <p><i>Bạn vui lòng lựa chọn 1 trong 2 tuỳ chọn bên dưới để tiến hành xử lý đơn hàng <b><span
                            class="order-title"></span></b></i></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="choose-radio" id="radio-create-route" checked
                    value="1">
                <label class="form-check-label" for="radio-create-route" style="margin-bottom: 8px;">
                    Nếu bạn muốn tạo chuyến xe mới vui lòng chọn tài xế
                </label>
                <div>
                    <select class="select2 select-driver" id="select-driver-for-route">
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {!! $driver->id == $default_driver->id ? 'selected' : ''
                                !!}>{{ $driver->full_name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 16px;">
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="choose-radio" id="radio-merge-route" value="2">
                <label class="form-check-label" for="radio-merge-route" style="margin-bottom: 16px;">
                    Nếu bạn muốn ghép chuyến vui lòng chọn từ danh sách chuyến của xe <b><span class="vehicle-title"></span></b> có sẵn ở dưới
                </label>
            </div>
        </div>
    </div>
    @if (count($routes) == 0)
        <div class="row">
            <div class="empty-box" style="top: 50% !important;">
                <span><i>Không thấy chuyến xe của xe <span class="vehicle-title"></span> trong khoảng thời gian hiện tại</i></span>
            </div>
        </div>
    @else
        <div class="row" id="header">
            <div class="col-2 head">Mã chuyến</div>
            <div class="col-3 head">Tên chuyến</div>
            <div class="col-2 head">Tài xế</div>
            <div class="head" style="width: 12.499999995%">TG nhận hàng</div>
            <div class="head" style="width: 12.499999995%">TG trả hàng</div>
            <div class="col-2 head">Tỷ lệ loading</div>
        </div>
        <div class="row" id="body">
            @foreach ($routes as $route)
                <div class="col-12 row detail">
                    <div class="col-2 content">
                        <div class="form-check">
                            <input class="form-check-input route-item" type="radio" name="radios"
                                id="route-{{ $route->id }}" value="{{ $route->id }}" disabled>
                            <label class="form-check-label" for="route-{{ $route->id }}">
                                {{ $route->route_code }}
                            </label>
                        </div>
                    </div>
                    <div class="col-3 content">{{ $route->name }}</div>
                    <div class="col-2 content">{{ $route->driver_name }}</div>
                    <div class="content" style="width: 12.499999995%">
                        {{ $route->getDateTime('ETD_date', 'd-m-Y') . ' ' . $route->getDateTime('ETD_time', 'H:i') }}
                    </div>
                    <div class="content" style="width: 12.499999995%">
                        {{ $route->getDateTime('ETA_date', 'd-m-Y') . ' ' . $route->getDateTime('ETA_time', 'H:i') }}
                    </div>
                    <div class="col-2 content">
                        <?php
                        $weight = $route->capacity_weight_ratio == null ? 0 : $route->capacity_weight_ratio;
                        $volume = $route->capacity_volume_ratio == null ? 0 : $route->capacity_volume_ratio;
                        ?>
                        <div>
                            <label for="file">Tải trọng ({{ (number_format($weight, 2, "," , ".")) }})</label>
                            <progress max=100 value={{ $weight }}>{{ number_format($weight, 2, "," , ".") }}</progress>
                            <label for="file">Thể tích ({{ (number_format($volume,2, "," , ".")) }})</label>
                            <progress max=100 value={{ $volume }}>{{ number_format($volume, 2, "," , ".") }}</progress>
                        </div>
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
