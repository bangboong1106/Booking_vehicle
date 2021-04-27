@extends('layouts.frontend.layouts.main')
@section('content')
    <link href="{{ public_url('css/frontend/autoload/support.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <div class="wrapperContainer-layout">
        <header class="paralell-lienhe">
            <div class="col-md-12 clearfix">
                <h1 class="bold">Liên hệ {{config('constant.APP_NAME')}} để dùng thử miễn phí</h1>
            </div>
        </header>
        <div class="wrapper-contact" id="contact-onelog">
            <section class="container">
                <div class="col-md-12 col-sm-12 col-xs-12 clearfix">
                    <h3 class="title-contact">Thông tin</h3>
                    <div class="col-left col-sm-6 col-xs-6">
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
                                           title="Vui lòng nhập số điện thoại" class="tel-contact form-control mt-10">
                                </div>
                                <div class="col-sm-12 form-group">
                                    <input placeholder="Địa chỉ email" type="email" name="email" required=""
                                           title="Vui lòng nhập email" class="email-contact form-control mt-10">
                                </div>
                                <div class="col-sm-12 form-group">
                                    <div class="wrapper-select select_expand">
                                        <span class="title-select" data-option="">Chọn nhu cầu liên hệ:</span>
                                        <div class="list-content-options">
                                            <ul class="listing-options" name="type">
                                                <li data-value=1><span>Tư vấn dịch vụ &amp; Báo giá</span>
                                                </li>
                                                <li data-value=2><span>Hỗ trợ dịch vụ</span></li>
                                                <li data-value=3>
                                                    <span>Báo chí &amp; Truyền thông</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <textarea placeholder="Nội dung cần liên hệ"
                                              class="content-contact body-contact form-control" name="remark"
                                              required="required" title="Vui lòng nhập nội dung liên hệ"
                                              rows="8"></textarea>
                                </div>

                                <input type="hidden" name="contact[body]">
                            </div>
                            <div class="form-group">
                                <div class="info-mess mb-40 hidden">Cảm ơn bạn đã quan tâm tới dịch vụ của chúng tôi.
                                    Chúng tôi sẽ hồi đáp thông tin cho bạn trong thời gian sớm nhất.
                                </div>
                                <button class="btn btn-primary btn-send btn-contact btn-contact-form"><span>GỬI THÔNG TIN</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-right col-sm-6 col-xs-6">
                        <div class="info-contact">
                            <ul>
                                <li class="address">
                                    <label>Văn phòng Hà Nội:</label>
                                    <p><strong>Địa chỉ :</strong>Số 96, Hoàng Ngân, Trung Hoà, Cầu Giấy, Tp. Hà
                                        Nội</p>
                                </li>
                            </ul>
                        </div>
                        <div class="social-contact">
                            <h3>Kết nối với {{config('constant.APP_NAME')}} trên mạng xã hội</h3>
                            <a href="" target="_blank"
                               class="facebook"></a>
                            <a href="" target="_blank"
                               class="youtube"></a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="clear"></div>
        <script>
            $(document).ready(function () {
                if ($(window).width() < 769) {
                    $('.contact-form .col-left').removeClass('col-sm-6');
                    $('.contact-form .col-right').removeClass('col-sm-5 col-sm-offset-1').css('padding', '0');
                    $('.social-contact').css('padding', '0');
                }
                ;
                /* dropdown */
                $('.select_expand').click(function () {
                    $('.wrapper-select').removeClass('requid');
                    $(this).toggleClass("open_select");
                    $(this).find('.list-content-options').slideToggle();
                });
                $('.listing-options li').on('click', function () {
                    $('.listing-options li').removeClass('active');
                    $(this).addClass('active');
                    //	var data = $(this).attr("data-option");
                    $('.select_expand').find('.title-select').text($(this).text()).addClass('txt-active').attr('data-option', $(this).attr('data-value'));

                });
                $(document).on('click', '.btn-contact-form', function (e) {
                    if ($(this).closest('.contact-form')[0].checkValidity() == true) {
                        e.preventDefault();
                        if ($('.title-select').attr("data-option").length > 0) {
                            var form = $(this).parents('form');
                            var type = form.find('.title-select').attr("data-option");

                            var data = {
                                companyName: form.find('input[name=companyName]').val(),
                                fullName: form.find('input[name=fullName]').val(),
                                email: form.find('input[name=email]').val(),
                                phone: form.find('input[name=phone]').val(),
                                type: type,
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
                                    form.find('.title-select').attr("data-option", '');
                                }
                            });
                        } else {
                            $('.wrapper-select').addClass('requid');
                        }
                    }
                });
            });
        </script>
    </div>
@endsection