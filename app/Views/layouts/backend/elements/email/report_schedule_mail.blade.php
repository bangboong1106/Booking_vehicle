<h3><i><b>Kính gửi Quý khách hàng,</b></i></h3>
<p>Công ty OneLog xin gửi Anh/Chị báo cáo đặt lịch thông qua giải pháp CETA của quý doanh nghiệp ngày {{ $date }}</p>
@if($fileInteractive !=null)
    <h5>Bảng thống kê việc tương tác quản trị vận tải. Vui lòng xem file : CETA_ThongKe_TuongTac_VanTai_{{$date}}
        .xlsx</h5>
@endif
@if($fileVehiclePerformance !=null)
    <h5>Báo cáo năng suất và chất lượng của xe. Vui lòng xem file : CETA_BaoCaoNangSuat_DinhKy_{{$date}}.xlsx</h5>
@endif
@if($fileCustomerRevenueAndCost !=null)
    <h5>Báo cáo doanh thu , chi phí theo xe. Vui lòng xem file : CETA_BaoCao_DoanhThu_ChiPhi_KH_{{$date}}.xlsx</h5>
@endif
@if($fileVehicleTeam !=null)
    <h5>Báo cáo hoạt động theo đội xe và tài xế. Vui lòng xem file : CETA_BaoCao_HoatDong_DoiXe_{{$date}}.xlsx</h5>
@endif
@if($fileInteractiveDriver !=null)
    <h5>Thống kê tương tác tài xế. Vui lòng xem file : CETA_ThongKe_TuongTac_TaiXe.xlsx</h5>
@endif
<h3><b>Chân thành cảm ơn.</b></h3>
<br/>
<p>==============================================</p>
<h3><b>CETA BY ONELOG JSC</b></h3>
<div style="display: inline-flex">
    <div>
        <img src="https://ceta.vn/logo.png" style="width: 200px;">
    </div>
    <div style="margin-left: 50px">
        <p style="margin: 0;margin-bottom: 20px;">
            Add: No 96 Hoang Ngan Str., Cau Giay Dist., Hanoi, Vietnam<br>
            Mobile: <a href="tel:02473009559">02473009559</a><br>
            Email: <a href="mailto:hotro@onelog.com.vn">hotro@onelog.com.vn</a><br>
            Web: <a href="https://onelog.com.vn">https://onelog.com.vn</a><br>
            Domain: <a
                    href="{{$domain}}">{{$domain}}</a><br>
        </p>
    </div>
</div>