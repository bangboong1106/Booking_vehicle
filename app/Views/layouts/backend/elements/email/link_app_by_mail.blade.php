<h3><i><b>Kính chào anh/chị</b></i></h3>
<p>Chào mừng đến với phần mềm {{config('constant.APP_NAME')}}. Tải ngay ứng dụng tại: </p>
<h5>Danh sách ứng dụng hệ điều hành Android</h5>
<table style="
        width: 150px;
        table-layout: fixed;
        border-spacing: 0px;
        ">
    <thead>
    <tr>
        <td style=" width: 150px;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
            <b>Tên ứng dụng</b>
        </td>
        <td style="
             width: 500px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
            <b>Đường dẫn tải Ứng dụng</b>
        </td>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr style="border:1px solid;">
            <td style="border-top:1px solid #f3f3f3; width: 150px;padding: 10px 10px;">
                <b>{{$item->name}}</b>
            </td>
            <td style="border-top:1px solid #f3f3f3; width: 500px;padding: 10px 10px">
                {{"https://play.google.com/store/apps/details?id=" . $item->play_store_id}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


<h5>Danh sách ứng dụng hệ điều hành IOS</h5>
<table style="
        width: 100px;
        table-layout: fixed;
        border-spacing: 0px;
        ">
    <thead>
    <tr>
        <td style=" width: 150px;padding: 10px 10px; background-color: #f6f6f6;color: #007bff">
            <b>Tên ứng dụng</b>
        </td>
        <td style="
             width: 500px;
             padding: 10px 10px;
             background-color: #f6f6f6;
             color: #007bff;
             vertical-align: middle;
             text-align: center;
             ">
            <b>Đường dẫn tải Ứng dụng</b>
        </td>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr style="border:1px solid;">
            <td style="border-top:1px solid #f3f3f3; width: 150px;padding: 10px 10px;">
                <b>{{$item->name}}</b>
            </td>
            <td style="border-top:1px solid #f3f3f3; width: 500px;padding: 10px 10px">
                {{'https://apps.apple.com/app/id' . $item->app_store_id}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

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
