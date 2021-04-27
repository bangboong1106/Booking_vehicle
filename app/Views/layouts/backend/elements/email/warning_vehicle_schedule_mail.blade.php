<h3><i><b>Kính gửi Quý khách hàng,</b></i></h3>
<p>Phần mềm {{config('constant.APP_NAME')}} kính gửi Anh/Chị bảng thông kê số lượng xe hết hạn giấy phép ngày và xe đến hạn bảo
    dưỡng {{ $date }}</p>
@if(!empty($data) && sizeof($data) > 0)
    <h5>Danh sách xe hết hạn giấy phép</h5>
    <table style="
        width: 100px;
        table-layout: fixed;
        border-spacing: 0px;
        ">
        <thead>
        <tr>
            <td style=" width: 100px;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
                <b>Xe</b>
            </td>
            <td style="
             width: 140px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Loại giấy tờ</b>
            </td>
            <td style="
             width: 100px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Ngày hết hạn</b>
            </td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr style="border:1px solid;">
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;">
                    <b>{{$item['reg_no']}}</b>
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 140px;padding: 10px 10px;text-align: center;">
                    {{$item['file_name']}}
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;text-align: center;">
                    {{$item['expire_date']}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@if(!empty($dataRepair) && sizeof($dataRepair) > 0)
    <h5>Danh sách xe đến hạn bảo dưỡng</h5>
    <table style="
        width: 100px;
        table-layout: fixed;
        border-spacing: 0px;
        ">
        <thead>
        <tr>
            <td style=" width: 100px;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
                <b>Xe</b>
            </td>
            <td style="
             width: 140px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Số km bảo dưỡng</b>
            </td>
            <td style="
             width: 100px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Ngày bảo dưỡng gần nhất</b>
            </td>
            <td style="
             width: 250px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Số km đã chạy <br/>(Tính từ ngày bảo dưỡng gần nhất)</b>
            </td>
        </tr>
        </thead>
        <tbody>
        @foreach($dataRepair as $item)
            <tr style="border:1px solid;">
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;">
                    <b>{{$item['reg_no']}}</b>
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 140px;padding: 10px 10px;text-align: center;">
                    {{numberFormat($item['repair_distance'])}}
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;text-align: center;">
                    {{ \Carbon\Carbon::parse($item['repair_date'])->format('d-m-Y')}}
                </td>
                <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;text-align: center;">
                    {{numberFormat($item['distance'])}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@if(!empty($dataAccessoryRepair) && sizeof($dataAccessoryRepair) > 0)
    <h5>Danh sách phụ tùng của xe đến hạn bảo dưỡng</h5>
    <table style="
        width: 100px;
        table-layout: fixed;
        border-spacing: 0px;
        ">
        <thead>
        <tr>
            <td style=" width: 100px;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
                <b>Xe</b>
            </td>
            <td style="
             width: 140px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Phụ tùng</b>
            </td>
            <td style="
             width: 100px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
                <b>Ngày bảo dưỡng tiếp theo</b>
            </td>
        </tr>
        </thead>
        <tbody>
        @foreach($dataAccessoryRepair as $reg_no=>$accessoryRepair)
            @foreach($accessoryRepair as $i => $item)
                <tr style="border:1px solid;">
                    <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;">
                        <b>{{$i == 0 ? $reg_no : ""}}</b>
                    </td>
                    <td style="border-top:1px solid #f3f3f3; width: 140px;padding: 10px 10px;text-align: center;">
                        {{$item['accessory_name']}}
                    </td>
                    <td style="border-top:1px solid #f3f3f3; width: 100px;padding: 10px 10px;text-align: center;">
                        {{ \Carbon\Carbon::parse($item['next_repair_date'])->format('d-m-Y')}}
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endif
<h3><b>Chân thành cảm ơn.</b></h3>
<br/>
<p>==============================================</p>
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


