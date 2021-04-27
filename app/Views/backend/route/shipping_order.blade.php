<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LỆNH VẬN CHUYỂN</title>
    <style>
        body {
            font-family: 'Dejavu Sans';
        }

        .row {
            display: flex;
            width: 100%;
        }
        .table {
            width: 100%;
        }
        .container {
            padding: 5%;
        }
        .col-4 {
            width: 100%;
            flex: 0 0 33.3333%;
            max-width: 33.3333%;
        }
        .col-6 {
            width: 100%;
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col-8 {
            width: 100%;
            flex: 0 0 66.6667%;
            max-width: 66.6667%;
        }
        .col-12 {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }
        .text-center {
            text-align: center
        }
        .title {
            padding: 5%;
        }
        .footer {
            padding: 2%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="table">
                <tr>
                    <td class="col-4">
                        <span><b>CÔNG TY CỔ PHẦN {{config('constant.APP_COMPANY')}}</b></span><br />
                        <span>Số {{ $entity->route_code }}/LĐX-{{config('constant.APP_COMPANY')}}</span>
                    </td>
                    <td class="col-8 text-center">
                        <span><b>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</b></span><br />
                        <span>Độc lập - Tự do - Hạnh phúc </span>
                        <hr style="width: 50%"/>
                    </td>
                </tr>
            </table>
        </div>
        <div class="title">
            <div class="row">
                <div class="col-12 text-center">
                    <span><b>LỆNH VẬN CHUYỂN</b></span>
                </div>
            </div>
        </div>
        <div class="body">
            <div class="wrap-container">
                <div class="row">
                    <div class="col-12">
                        <span><b>1. Thông tin đơn vị</b></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Đơn vị vận tải</i>: {{$companyName}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Địa chỉ</i>: {{$companyAddress}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Số điện thoại</i>: {{$companyMobileNo}}
                    </div>
                </div>
            </div>
            <div class="wrap-container">
                <div class="row">
                    <div class="col-12">
                        <span><b>2. Thông tin lái xe</b></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Họ và tên</i>: {{!empty($entity->driver) ? $entity->driver->full_name : ''}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Giấy phép lái xe số</i>: {{!empty($entity->driver) ?$entity->driver->identity_no: ''}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Số điện thoại</i>: {{!empty($entity->driver) ?$entity->driver->mobile_no: ''}}
                    </div>
                </div>
            </div>
            <div class="wrap-container">
                <?php $order = empty($entity->orders->first()) ? null : $entity->orders->first()?>
                <?php $customer = empty($order) ? null : $order->customer?>

                <div class="row">
                    <div class="col-12">
                        <span><b>3. Thông tin chủ hàng</b></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Tên đơn vị/cá nhân</i>: {{ empty($customer) ? '' : $customer->full_name}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Địa chỉ</i>: {{ empty($customer) ? '' : $customer->address}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Số điện thoại</i>: {{ empty($customer) ? '' : $customer->mobile_no}}
                    </div>
                </div>
            </div>
            <div class="wrap-container">
                <div class="row">
                    <div class="col-12">
                        <span><b>4. Thông tin hợp đồng</b></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <i>Hợp đồng nội bộ giữa đơn vị vận tải và công ty khách hàng</i>
                    </div>
                </div>
            </div>
            <div class="wrap-container">
                <div class="row">
                    <div class="col-12">
                        <span><b>5. Lộ trình di chuyển</b></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ empty($entity->locationDestination) ? "" : $entity->locationDestination->title}} - 
                        {{empty($entity->locationArrival) ? "" : $entity->locationArrival->title}}
                    </div>
                </div>
            </div>
            <div class="wrap-container">
                <div class="row">
                    <div class="col-12">
                        <span><b>6. Thông tin về rơ moóc, sơ mi rơ moóc (nếu có)</b></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <table class="table">
                <tr>
                    <td class="col-6">
                    </td>
                    <td class="col-6 text-center">
                    <span><b>Hà Nội, ngày {{date_create()->format('d-m-Y')}}</b></span>
                    <br/>
                    @if(!empty($companyStampPath))
                        <img src="{{ $companyStampPath }}" style="width: 200px; height: 200px">
                    @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
