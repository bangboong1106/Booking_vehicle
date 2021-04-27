@extends('layouts.frontend.layouts.main')
@section('content')
    <link href="{{ public_url('css/frontend/autoload/index.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <div class="wrapperContainer-layout">
        <section class="home_mainBanner background-banner">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                    <div class="item active">
                        <div class="row slide-image1">
                            <div class="col-md-6 col-sm-12 col-xs-12"></div>
                            <div class="col-md-6 col-sm-12 col-xs-12 slide-content">
                                <div class="wrapper_header">
                                    <p class="header">GIẢI PHÁP QUẢN i <br/> VẬN TẢI HÀNG ĐẦU</p>
                                    <hr style="height: 5px;width: 150px;border:none;background-color: #58c5da;">
                                    <p class="tagline">Quản lý ưu việt - Giao diện tinh gọn - Chi phí hợp lý</p>
                                    <p class="tagline">Tăng cường vị thế của Doanh Nghiệp đối với khách hàng</p>
                                    <a class="scroll btn_more_header btn-hover-vertical more-info">Tìm hiểu thêm</a>
                                    <a class="btn_more_header btn-hover-vertical register-trial"
                                       href="{{route("support")}}">Dùng
                                        thử miễn phí</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="row slide-image2">
                            <div class="col-md-6 col-sm-12 col-xs-12"></div>
                            <div class="col-md-6 col-sm-12 col-xs-12 slide-content">
                                <div class="wrapper_header">
                                    <p class="header">ĐỒNG HÀNH CÙNG<br/>DOANH NGHIỆP VIỆT<br/>VƯỢT QUA <span
                                                style="color: #57c6da;">COVID-19</span></p>
                                    <hr style="height: 5px;width: 150px;border:none;background-color: #58c5da;">
                                    <p class="tagline">Miễn phí một tháng dịch vụ</p>
                                    <p class="tagline">Hỗ trợ tới 60% chi phí</p>
                                    <p class="tagline">Đào tạo triển khai trực tuyến nhanh gọn</p>
                                    <a class="btn_more_header btn-hover-vertical register-trial" href="{{route("covid")}}">Thông tin chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>
        <section class="home_pro_product" style="padding: 50px;">
            <div class="row">
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 no-padding reason-title">
                        <div id="content-home" class="r-title-2">Các Doanh nghiệp vận tải đã tin tưởng lựa chọn {{config('constant.APP_NAME')}}
                            vì
                        </div>
                    </div>
                    <div class="col-md-12 no-padding reason-item-list">
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_DapUngDayDuNghiepVu.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_DapUngDayDuNghiepVu.svg')}}">
                            <p class="reason-item-title">Đáp ứng đầy đủ nghiệp vụ quản trị & điều hành</p>
                            <p class="reason-item-detail">Thuật toán tối ưu giúp tự động hoá nghiệp vụ điều hành vận
                                tải.
                                Rút ngắn thời gian thao tác.
                                Giảm sai sót do con người.</p>
                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_TangKhaNangGiamSat.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_TangKhaNangGiamSat.svg')}}">
                            <p class="reason-item-title">Tăng khả năng giám sát</p>
                            <p class="reason-item-detail">Giám sát theo thời gian thực, mọi lúc, mọi nơi.
                                Cảnh báo và nhắc nhở trước mọi mốc thời điểm trong quá trình vận tải</p>
                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_CongCuPhanTichBaoCao.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_CongCuPhanTichBaoCao.svg')}}">
                            <p class="reason-item-title">Công cụ phân tích & báo cáo hữu ích</p>
                            <p class="reason-item-detail">Bảng phân tích dữ liệu tự động giúp chủ doanh nghiệp đánh giá
                                chất lượng dịch vụ, thống kê chi phí và doanh thu tại bất cứ nơi đâu</p>
                        </div>
                    </div>
                    <div class="col-md-12 no-padding reason-item-list">
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_GiaoDienTrucQuanKhoaHoc.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_GiaoDienTrucQuanKhoaHoc.svg')}}">
                            <p class="reason-item-title">Giao diện trực quan & khoa học</p>
                            <p class="reason-item-detail">{{config('constant.APP_NAME')}} được tối ưu riêng cho từng đối tượng (chủ doanh nghiệp,
                                chủ hàng, điều hành, lái xe) trên nền tảng đám mây.
                                Tương thích mọi thiết bị (Mobile & PC)</p>
                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_TichHopDeDang.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_TichHopDeDang.svg')}}">
                            <p class="reason-item-title">Tích hợp và triển khai dễ dàng, nhanh chóng</p>
                            <p class="reason-item-detail">Bắt đầu tích hợp và hoàn thiện triển khai trong vòng 48h. Đáp
                                ứng nhu cầu của Doanh Nghiệp trên cả nước</p>
                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_ChiPhiThap.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_ChiPhiThap.svg')}}">
                            <p class="reason-item-title">Chi phí thấp - Giá trị cao</p>
                            <p class="reason-item-detail">Nhiều gói dịch vụ đa dạng. Phù hợp với mọi doanh nghiệp vận
                                tải trên cả nước</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="section-guide" class="section section--light section-guide"
                 style="padding-top: 50px;padding-bottom: 0px;background-color: #fafafa;">

            <div class="container">
                <header>
                    <h2 class="title-grouphome text-center">Tính năng nổi bật của {{config('constant.APP_NAME')}}?</h2>
                    <p class="reason-item-detail text-center">Giám sát và điều hành mọi lúc mọi nơi trên cùng một nền
                        tảng </p>
                </header>
                <div class="row">
                    <div class="col-xs-12 col-md-10 col-md-offset-1">
                        <div class="carousel carousel--mobileview carousel-fade slide clearfix" data-ride="carousel"
                             id="carousel--mobileview--how-it-works-grabtaxi">
                            <!-- Indicators -->
                            <ol class="carousel-indicators steps--4 clearfix">
                                <li class="" data-slide-to="0"
                                    data-target="#carousel--mobileview--how-it-works-grabtaxi" data-step="1"
                                    data-original-title="" title="">
                                    <h4 class="reason-item-title">1. Khởi tạo, phân công và thông báo hàng trăm đơn hàng
                                        với 1 click</h4>
                                    <p class="reason-item-detail">Các thao tác đơn giản và thuận tiện. <br/>
                                        Trên phần mềm bảng điều khiển và biểu mẫu excel</p>
                                </li>
                                <li data-slide-to="1" data-target="#carousel--mobileview--how-it-works-grabtaxi"
                                    data-step="2" data-original-title="" title="" class="">
                                    <h4 class="reason-item-title">Tài xế nhận thông báo liền tay</h4>
                                    <p class="reason-item-detail">Các bác tài dễ dàng tương tác với điều hành qua ứng
                                        dụng trực tuyến</p>
                                </li>
                                <li data-slide-to="2" data-target="#carousel--mobileview--how-it-works-grabtaxi"
                                    data-step="3" data-original-title="" title="" class="">
                                    <h4 class="reason-item-title">Theo dõi và giám sát tiến trình thực hiện</h4>
                                    <p class="reason-item-detail">Hệ thống hóa giám sát toàn diện tiến trình thực hiện
                                        đơn hàng. Tận dụng hiệu quả năng lực vận chuyển của đội xe.</p>
                                </li>
                                </li>
                                <li data-slide-to="3" data-target="#carousel--mobileview--how-it-works-grabtaxi"
                                    data-step="4" data-original-title="" title="">
                                    <h4 class="reason-item-title">Hoàn thành. Tự động báo cáo và cảnh báo.</h4>
                                    <p class="reason-item-detail">Giảm thiểu tối đa rủi ro, sai sót do con người.
                                        Phiên bản Apps dành riêng cho Nhà Quản trị và Web dành cho Chủ hàng.</p>
                                </li>
                            </ol><!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <div class="item">
                                    <img src="{{public_url('css/frontend/img/process/step1.jpeg')}}">
                                </div><!-- /item -->
                                <div class="item">
                                    <img src="{{public_url('css/frontend/img/process/step1.jpeg')}}">
                                </div><!-- /item -->
                                <div class="item active">
                                    <img src="{{public_url('css/frontend/img/process/step1.jpeg')}}">
                                </div><!-- /item -->
                                <div class="item">
                                    <img src="{{public_url('css/frontend/img/process/step1.jpeg')}}">
                                </div><!-- /item -->
                            </div><!-- /carousel-inner -->
                            <div class="carousel-bg"></div>
                        </div><!-- /carousel -->

                    </div>
                </div><!-- /row -->

            </div><!-- /container -->
        </section>
        <section class="home_brand_trusted">
            <div class="container">
                <h2 class="title-grouphome text-center">Tin dùng {{config('constant.APP_NAME')}} để trở thành Doanh nghiệp hiện đại và tiên phong
                </h2>
                <div class="row listing_brand_logo flex_container_brand">
                    <div class="customer-logo">
                        <a href="#">
                            <img src="{{ public_url('css/frontend/img/company/thanhdat_logo.png') }}"/>
                        </a>
                    </div>
                    <div class="customer-logo">
                        <a href="#">
                            <img src="{{ public_url('css/frontend/img/company/neco_logo.png') }}"/>
                        </a>
                    </div>
                    <div class="customer-logo">
                        <a href="#">
                            <img src="{{ public_url('css/frontend/img/company/hongducexpress.png') }}"/>
                        </a>
                    </div>
                    <div class="customer-logo">
                        <a href="#">
                            <img src="{{ public_url('css/frontend/img/company/tandat_logo.png') }}"/>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection