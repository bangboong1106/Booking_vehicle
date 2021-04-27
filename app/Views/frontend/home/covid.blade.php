@extends('layouts.frontend.layouts.main')
@section('content')
    <link href="{{ public_url('css/frontend/autoload/index.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <link href="{{ public_url('css/frontend/autoload/covid.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <style>
        .covid-banner {
            background-image: url("{{public_url('/css/frontend/img/covid/lp2.png')}}");
            background-size: cover
        }

        .protect-block {
            background-image: url("{{public_url('/css/frontend/img/covid/covid-section2.png')}}");
            background-size: cover;
            height: 420px;
        }

        .home_pro_product {
            background-image: url("{{public_url('/css/frontend/img/covid/covid-section4.png')}}");
            background-size: cover;
        }

        .home_brand_trusted {
            background-image: url("{{public_url('/css/frontend/img/covid/covid-section5.png')}}");
            background-size: cover;
            height: 760px;
        }


        @media (max-width: 768px) {
            .covid-banner {
                background-image: url('{{public_url('/css/frontend/img/covid/lp2_mb.png')}}') !important;
                background-position: center;
            }

            section.home_mainBanner {
                padding: 0px;
                overflow: hidden;
                height: 450px;
            }
        }


    </style>
    <div class="wrapperContainer-layout">
        <section class="home_mainBanner">
            <div class="container-fluid">
                <div class="row covid-banner">
                    <div class="col-md-6 col-sm-12 col-xs-12"></div>
                    <div class="col-md-6 col-sm-12 col-xs-12 slide-content">
                        <div class="wrapper_header">
                            <p class="header">ĐỒNG HÀNH CÙNG<br/>DOANH NGHIỆP VIỆT<br/>VƯỢT QUA <span
                                        style="color: #57c6da;">COVID-19</span></p>
                            <hr style="height: 3px;width: 90px;border:none;background-color: #58c5da;">
                            <p class="tagline">Miễn phí một tháng dịch vụ</p>
                            <p class="tagline">Hỗ trợ tới 60% chi phí</p>
                            <p class="tagline">Đào tạo triển khai trực tuyến nhanh gọn</p>
                            <a class="btn_more_header btn-hover-vertical register-trial" href="#home_brand_support">NHẬN TƯ VẤN NGAY</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="protect-block">
            <div class="container-fluid covid-news">

                <div class="row protect-block_body">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="wrapper_header">
                            <p class="span-white">
                                Tình hình <b>COVID-19</b> diễn ra phức tạp khiến các doanh nghiệp logistics bị ảnh
                                hưởng không hề nhỏ.
                                Tuy nhiên hãy <b>tận dụng cơ hội</b> này để thực hiện chuyển đổi số, cải thiện bộ máy nội
                                bộ. Sử
                                dụng phần mềm tự động hóa hoạt động doanh nghiệp vận tải.
                            </p>
                            <p class="span-white">
                                Là giải pháp đồng hành cùng Doanh nghiệp vận tải, <b>{{config('constant.APP_NAME')}}-Giải pháp Logistics ưu việt</b>
                                luôn sẵn sáng hỗ trợ doanh nghiệp trong mùa dịch
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row protect-block_footer">
                    <a class="btn btn-blue" href="#home_brand_support">NHẬN TƯ VẤN NGAY</a>

                </div>
            </div>
        </section>
        <section class="covid-section3">
            <div class="row">
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 no-padding reason-title">
                        <div id="content-home" class="r-title-2">BẢO VỆ DOANH NGHIỆP MÙA DỊCH
                        </div>
                    </div>
                    <div class="col-md-12 no-padding reason-item-list">
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/covid/covid-section3-supprt.png')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/covid/covid-section3-support.png')}}">
                            {{--                            <p class="reason-item-title span-white">Tư vấn, hướng dẫn online</p>--}}

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/covid/covid-section3-free.png')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/covid/covid-section3-free.png')}}">
                            {{--                            <p class="reason-item-title span-white">Miễn phí tháng đầu tiên sử dụng</p>--}}

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/covid/covid-section3-percent.png')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/covid/covid-section3-percent.png')}}">
                            {{--                            <p class="reason-item-title span-white">Hỗ trợ 60% chi phí doanh nghiệp</p>--}}

                        </div>
                    </div>
                </div>
            </div>
            <div class="row protect-block_footer">
                <a class="btn btn-blue" href="#home_brand_support">NHẬN TƯ VẤN NGAY</a>

            </div>
        </section>
        <section class="home_pro_product" style="padding: 50px;">
            <div class="row">
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 no-padding reason-title">
                        <div id="content-home" class="r-title-2 span-white">{{config('constant.APP_NAME')}} ĐÃ ĐỒNG HÀNH CÙNG<br/> 100+ DOANH
                            NGHIỆP VẬN TẢI
                        </div>
                    </div>
                    <div class="col-md-12 no-padding reason-item-list">
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_DapUngDayDuNghiepVu_white.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_DapUngDayDuNghiepVu_white.svg')}}">
                            <p class="reason-item-title span-white">Đáp ứng đầy đủ nghiệp vụ quản trị & điều hành</p>

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_TangKhaNangGiamSat_white.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_TangKhaNangGiamSat_white.svg')}}">
                            <p class="reason-item-title span-white">Tăng khả năng giám sát</p>

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_CongCuPhanTichBaoCao_white.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_CongCuPhanTichBaoCao_white.svg')}}">
                            <p class="reason-item-title span-white">Công cụ phân tích & báo cáo hữu ích</p>

                        </div>
                    </div>
                    <div class="col-md-12 no-padding reason-item-list">
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_GiaoDienTrucQuanKhoaHoc_white.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_GiaoDienTrucQuanKhoaHoc_white.svg')}}">
                            <p class="reason-item-title span-white">Giao diện trực quan & khoa học</p>

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_TichHopDeDang_white.svg')}}"
                                 alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_TichHopDeDang_white.svg')}}">
                            <p class="reason-item-title span-white">Tích hợp và triển khai dễ dàng, nhanh chóng</p>

                        </div>
                        <div class="col-md-4 reason-item">
                            <img data-src="{{public_url('css/frontend/img/m_ChiPhiThap_white.svg')}}" alt="{{config('constant.APP_COMPANY')}}"
                                 class="img-responsive lazy-load"
                                 src="{{public_url('css/frontend/img/m_ChiPhiThap_white.svg')}}">
                            <p class="reason-item-title span-white">Chi phí thấp - Giá trị cao</p>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row protect-block_footer">
                <a class="btn btn-blue" href="#home_brand_support">NHẬN TƯ VẤN NGAY</a>

            </div>
        </section>
        <section class="home_brand_support" id="home_brand_support"
                 style='background-image: url("{{public_url('/css/frontend/img/banner/landingpage.png')}}")'>
            <div class="home_brand_support_wrap">
                <header class="paralell-lienhe">
                    <div class="col-md-12 clearfix">
                        <h3 class="bold" style="text-align: center">CÙNG {{config('constant.APP_NAME')}} VƯỢT QUA MÙA DỊCH</h3>
                        <h5 class="bold" style="text-align: center">Hot line: {{config('constant.APP_HOTLINE')}} || {{config('constant.APP_EMAIL')}}</h5>
                    </div>
                </header>
                <div class="wrapper-contact" id="contact-onelog">
                    <section class="container-fluid">
                        <form accept-charset="UTF-8" class="contact-form" method="post"
                              id="contactid">
                            <input name="form_type" type="hidden" value="contact">
                            <input name="utf8" type="hidden" value="✓">
                            <div class="form-group row" id="form-lienhe">
                                <div class="col-sm-12 form-group">
                                    <input placeholder="Tên công ty" required="" type="text"
                                           title="Vui lòng nhập tên công ty" class="form-control mt-10"
                                           name="companyName">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <input placeholder="Họ &amp; Tên" type="text" required=""
                                           title="Vui lòng nhập họ và tên" name="fullName"
                                           class="name-contact form-control mt-10"/></div>
                                <div class="col-sm-6 form-group">
                                    <input placeholder="Số điện thoại" type="tel" name="phone" required=""
                                           title="Vui lòng nhập số điện thoại"
                                           class="tel-contact form-control mt-10">
                                </div>
                                <div class="col-sm-12 form-group">
                                    <input placeholder="Địa chỉ email" type="email" name="email" required=""
                                           title="Vui lòng nhập email" class="email-contact form-control mt-10">
                                </div>
                                <div class="col-sm-12 form-group">
                                    <textarea placeholder="Nội dung cần liên hệ"
                                              class="content-contact body-contact form-control" name="remark"
                                              required="required" title="Vui lòng nhập nội dung liên hệ"
                                              rows="8"></textarea>
                                </div>

                                <input type="hidden" name="contact[body]">
                            </div>
                            <div class="form-group info-submit">
                                <div class="info-mess hidden">Cảm ơn bạn đã quan tâm tới dịch vụ của chúng
                                    tôi.
                                    Chúng tôi sẽ hồi đáp thông tin cho bạn trong thời gian sớm nhất.
                                </div>
                                <button class="btn btn-primary btn-send btn-contact btn-contact-form">
                                    <span>NHẬN TƯ VẤN</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

        </section>

        <script>
            $(document).ready(function () {
                if ($(window).width() < 769) {
                    $('.contact-form .col-left').removeClass('col-sm-6');
                    $('.contact-form .col-right').removeClass('col-sm-5 col-sm-offset-1').css('padding', '0');
                    $('.social-contact').css('padding', '0');
                }
                ;
                $(document).on('click', '.btn-contact-form', function (e) {
                    if ($(this).closest('.contact-form')[0].checkValidity() == true) {
                        e.preventDefault();
                        var form = $(this).parents('form');

                        var data = {
                            companyName: form.find('input[name=companyName]').val(),
                            fullName: form.find('input[name=fullName]').val(),
                            email: form.find('input[name=email]').val(),
                            phone: form.find('input[name=phone]').val(),
                            remark: form.find('[name=remark]').val()
                        };
                        $.ajax({
                            url: '/api/signUpCompany',
                            dataType: 'json',
                            type: 'POST',
                            data: data,
                            success: function (data) {
                                $('.info-mess').removeClass('hidden');
                                form.find("input[type=text],input[type=email],input[type=tel], textarea").val("");
                            }
                        });
                    }
                });
            });
        </script>
    </div>
@endsection