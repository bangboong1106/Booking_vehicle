<h3><i><b>Kính chào anh/chị {{$request['customer_name']}}</b></i></h3>
<p>Cảm ơn anh/chị đã đặt hàng tại website {{$url}}.<br/>
    Đơn hàng quý khách đã được gửi thành công đến hệ thống xử lý chúng tôi.</p>

<p>Dưới đây là chi tiết đơn hàng <b> {{$orderCode}}</b>({{$currentDay->format('d-m-Y H:i')}}) của anh/chị</p>
<div style="width: 100%; display: inline-flex">
    <div style="width: 33.3%"><b>Tên khách hàng : </b> {{$request['customer_name']}}</div>
    <div style="width: 33.3%"><b>SĐT : </b> {{$request['customer_mobile_no']}}</div>
    <div style="width: 33.3%"><b>Email : </b> {{$request['email']}}</div>
</div>
<div style="width: 100%; display: inline-flex">
    <div style="width: 66.6%"><b>Địa chỉ nhận hàng : </b> {{$request['ETD_location']['full_address']}}</div>
    <div style="width: 33.3%"><b>Thời gian nhận hàng
            : </b> {{ \Carbon\Carbon::parse($request['ETD_date'])->format('d-m-Y') .' '.\Carbon\Carbon::parse($request['ETD_time'])->format('H:i') }}
    </div>
</div>
<div style="width: 100%; display: inline-flex">
    <div style="width: 66.6%"><b>Địa chỉ trả hàng : </b> {{$request['ETA_location']['full_address']}}</div>
    <div style="width: 33.3%"><b>Thời gian trả hàng
            : </b> {{ \Carbon\Carbon::parse($request['ETA_date'])->format('d-m-Y') .' '.\Carbon\Carbon::parse($request['ETA_time'])->format('H:i') }}
    </div>
</div>
<div style="width: 100%; display: inline-flex">
    <div style="width: 33.3%"><b>Khoảng cách : </b> {{$request['distance'].' km'}}</div>
    <div style="width: 33.3%"><b>Tổng trọng lượng : </b> {{$request['weight'].' kg'}}</div>
    <div style="width: 33.3%"><b>Tổng dung tích : </b> {{$request['volume'].' m³'}}</div>
</div>
<div>
    <h4><b>Thông tin yêu cầu xe</b></h4>
    <table style="
            width: 50%;
            table-layout: fixed;
            border-spacing: 0px;
            ">
        <thead>
        <tr>
            <td style=" width: 60%;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
                <b>Chủng loại xe</b>
            </td>
            <td style="
                width: 40%;
                padding: 10px 10px;
                background-color: #f6f6f6;
                color: #007bff;
                vertical-align: middle;
                text-align: right;
                ">
                <b>Số lượng</b>
            </td>
        </tr>
        </thead>
        <tbody>
        @foreach($request['listVehicleGroup'] as $item)
            <tr style="border:1px solid;">
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;">
                    {{$item['VehicleGroupName']}}
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;">
                    {{$item['RouteNumber']}}
                </td>
            </tr>
        @endforeach
    </table>
</div>
<h5><i>Xin lưu ý: Thông báo email này là thông báo email tự động. Xin vui lòng đừng trả lời tin nhắn này</i></h5>
<h5><b>Cảm ơn anh/chị đã sử dụng dịch vụ của chúng tôi</b></h5>
<br/>
<p>==============================================<================================/p>
<h3><b>{{config('constant.APP_NAME')}} JSC</b></h3>
<div style="display: inline-flex">
    <div>
        <img src="https://ceta.vn/logo.png" style="width: 200px;">
    </div>
    <div style="margin-left: 50px">
        <p style="margin: 0;margin-bottom: 20px;">
            Địa chỉ: {{config('constant.APP_ADDRESS')}}<br>
            SĐT liên hệ: <a href="tel:{{config('constant.APP_HOTLINE')}}">{{config('constant.APP_HOTLINE')}}</a><br>
            Email: <a href="mailto:{{config('constant.APP_EMAIL_SUPPORT')}}">{{config('constant.APP_EMAIL_SUPPORT')}}</a><br>
            Web: <a href="{{config('constant.APP_WEB')}}">{{config('constant.APP_WEB')}}</a><br>
        </p>
    </div>
</div>


