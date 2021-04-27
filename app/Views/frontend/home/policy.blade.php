@extends('layouts.frontend.layouts.main')
@section('content')
    <link href="{{ public_url('css/frontend/autoload/policy.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <div class="wrapperContainer-layout">
        <div class="wrapper">
            <section>
                <div class="container policy clearfix">
                    <h1>Chính sách bảo mật của công ty {{config('constant.APP_COMPANY')}}</h1>
                    <div class="well">{{config('constant.APP_COMPANY')}} thu thập địa chỉ e-mail của người dùng gửi cho chúng tôi. Chúng tôi cũng
                        thu thập thông tin thông qua những trang người dùng truy cập, thông tin được cung cấp thông việc
                        khảo sát và đăng ký. Những thông tin này có thể chứa dữ liệu cá nhân của bạn bao gồm: địa chỉ,
                        số điện thoại, số thẻ tín dụng , vv.v {{config('constant.APP_COMPANY')}} cam đoan bảo vệ an toàn thông tin thẻ tín dụng của
                        bạn. Chúng tôi không được
                        phép tiết lộ thông tin cá nhân mà không có sự cho phép bằng văn bản. Tuy nhiên, một số thông tin
                        thu thập từ bạn và về bạn được sẽ được sử dụng để hỗ trợ việc cung cấp dịch vụ của chúng tôi với
                        bạn. Những thông tin chúng tôi thu thập không được chia sẻ với bạn, bán hoặc cho thuê, ngoại trừ
                        một số trường hợp sau đây:
                        <br>- {{config('constant.APP_COMPANY')}} có thể chia sẻ thông tin cá nhân để điều tra hoặc ngăn chặn các hoạt động bất hợp
                        pháp, nghi ngờ gian lận, các tình huống liên quan đến các mối đe dọa sự an toàn tính mạng lý của
                        bất kỳ người nào theo yêu cầu của luật pháp.
                        <br>- {{config('constant.APP_COMPANY')}} hợp tác với các công ty khác đại diện chúng tôi ở một số nhiệm vụ và có thể cần
                        phải chia sẻ thông tin của bạn với họ để cung cấp các sản phẩm và dịch vụ cho bạn. {{config('constant.APP_COMPANY')}} cũng
                        có thể chia sẻ thông tin của bạn để cung cấp sản phẩm hoặc dịch vụ mà bạn yêu cầu hoặc khi chúng
                        tôi có được sự cho phép của bạn.
                        <br>- Chúng tôi sẽ chuyển thông tin về bạn nếu {{config('constant.APP_COMPANY')}} được mua lại hoặc sáp nhập với công ty
                        khác. Trong trường hợp này, {{config('constant.APP_COMPANY')}} sẽ thông báo cho bạn bằng email hoặc bằng cách thông báo nổi
                        bật trên trang web của {{config('constant.APP_COMPANY')}} trước khi thông tin về bạn được chuyển giao và trở thành đối tượng
                        của một chính sách bảo mật khác.
                        <br>- Bảng tóm tắt này được cung cấp cho bạn vì lợi ích của bạn và không ràng buộc pháp lý. Xin
                        vui lòng đọc Điều khoản Dịch vụ một cách toàn diện để nắm rõ.
                    </div>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3>PHẦN 1</h3>
                            <h2>Chúng tôi sẽ làm gì với thông tin của bạn?</h2>
                            Thuật ngữ " Thông tin cá nhân " được sử dụng ở đây được định nghĩa là bất kỳ thông tin nào
                            có thể được sử dụng để nhận dạng, liên lạc hoặc xác định vị trí người mà thông tin đó liên
                            quan. Các thông tin cá nhân mà chúng tôi thu thập đều mang tính bảo mật cho cá nhân , và có
                            thể được sửa đổi theo thời gian.
                            Khi bạn đăng ký {{config('constant.APP_COMPANY')}} chúng tôi hỏi tên của bạn, tên công ty, địa chỉ email, địa chỉ thanh
                            toán, và thông tin thẻ tín dụng. Nếu bạn đăng ký tài khoản miễn phí thì không cần phải nhập
                            thẻ tín dụng. Tuy nhiên nếu bạn muốn kích hoạt quá trình thanh toán của bạn, bạn cần phải
                            cung cấp thông tin nhân thẻ tín dụng.
                            {{config('constant.APP_COMPANY')}} sử dụng thông tin chúng tôi thu thập cho các mục đích chung sau đây: các sản phẩm và
                            các dịch vụ cung cấp, thanh toán, xác định và xác thực, cải thiện dịch vụ , liên hệ, và
                            nghiên cứu.
                            Là một phần của quá trình mua và bán trên {{config('constant.APP_COMPANY')}}, bạn sẽ có được địa chỉ email và hoặc địa
                            chỉ vận chuyển của khách hàng. Bằng cách nhập vào Điều khoản người sử dụng của chúng tôi,
                            bạn đã đồng ý rằng, những thông tin liên quan đến người dùng khác mà bạn có được thông qua
                            {{config('constant.APP_COMPANY')}} hoặc thông qua một kênh truyền thông liên quan đến {{config('constant.APP_COMPANY')}} hoặc do Giao dịch {{config('constant.APP_COMPANY')}}
                            tạo điều kiện, {{config('constant.APP_COMPANY')}} sẽ cấp cho bạn một giấy phép sử dụng những thông tin đó chỉ trong mục
                            đích sử dụng có liên quan đến {{config('constant.APP_COMPANY')}}, mà không phải là những thông điệp thương mại khác.
                            {{config('constant.APP_COMPANY')}} không chấp nhận thư rác. Vì vậy, với những hạn chế nêu trên, bạn không được cấp phép
                            để thêm tên của những người đã mua một mặt hàng từ bạn, vào danh sách email của bạn ( email
                            hoặc thư vật lý) mà không cần sự đồng ý của họ.
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có nghĩa, </label>
                            Khi bạn đăng ký trên website, bạn cung cấp thông tin cá nhân mà chúng tôi thu thập và sử
                            dụng. Trong quá trình kiểm tra ra chúng tôi cũng thu thập thông tin thẻ tín dụng của bạn.
                            Bạn chỉ được phép sử dụng thông tin khách hàng {{config('constant.APP_COMPANY')}} cho với các hoạt động liên lạc liên
                            quan trừ khi họ cho phép bạn làm điều đó. Đừng gửi thư rác cho bất cứ ai!
                        </div>
                    </section>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3>PHẦN 2</h3>
                            <h2>Sự bảo mật</h2>
                            Bảo mật thông tin cá nhân của bạn là rất quan trọng với chúng tôi. Khi bạn nhập thông tin
                            nhạy cảm, như số thẻ tín dụng vào mẫu đăng ký của chúng tôi, chúng tôi mã hóa việc truyền
                            tải thông tin bằng cách sử dụng công nghệ mã hóa an toàn SSL - Secure Sockets Layer. Không
                            có phương pháp truyền qua Internet, hoặc phương pháp lưu trữ điện tử, là 100% an toàn. Vì
                            vậy, trong khi chúng tôi cố gắng sử dụng phương tiện mã hóa an toàn để bảo vệ thông tin cá
                            nhân của bạn, chúng tôi không thể đảm bảo an ninh tuyệt đối. Nếu bạn có bất kỳ câu hỏi về
                            bảo mật trên trang web của chúng tôi, bạn có thể gửi email cho chúng tôi tại hi@{{config('constant.APP_COMPANY')}}.com .
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có nghĩa,</label>
                            Chúng tôi mã hóa việc truyền tải thông tin theo chuẩn SSL. Bởi vì những thứ xảy ra, chúng
                            tôi không thể đảm bảo 100% bảo mật dữ liệu của bạn. Nếu bạn có thắc mắc email
                            {{config('constant.APP_EMAIL_SUPPORT')}}
                        </div>
                    </section>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3> PHẦN 3</h3>
                            <h2>Công bố</h2>
                            {{config('constant.APP_COMPANY')}} có thể sử dụng các nhà cung cấp dịch vụ bên thứ ba để cung cấp một số dịch vụ cho bạn
                            và chúng tôi có thể chia sẻ thông tin cá nhân với các nhà cung cấp dịch vụ đó. Chúng tôi yêu
                            cầu bất kỳ công ty mà chúng tôi có thể chia sẻ thông tin cá nhân bảo vệ dữ liệu một cách phù
                            hợp với chính sách này và hạn chế việc sử dụng các thông tin cá nhân như vậy để thực hiện
                            dịch vụ cho {{config('constant.APP_COMPANY')}}.
                            {{config('constant.APP_COMPANY')}} có thể tiết lộ thông tin cá nhân trong các trường hợp đặc biệt, chẳng hạn như để thực
                            hiện theo lệnh của tòa án yêu cầu chúng tôi làm như vậy hoặc khi hành động của bạn vi phạm
                            Điều khoản Dịch vụ .
                            Chúng tôi không bán hay cung cấp thông tin cá nhân cho các công ty khác để tiếp thị sản phẩm
                            hay dịch vụ của mình .
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có ý nghĩa</label>
                            Trong một số trường hợp , chúng tôi có thể tiết lộ thông tin cá nhân của bạn, như lệnh của
                            tòa án .
                        </div>
                    </section>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3> PHẦN 4</h3>
                            <h2> Lưu trữ dữ liệu khách hàng</h2>
                            {{config('constant.APP_COMPANY')}} sở hữu lưu trữ dữ liệu , cơ sở dữ liệu và tất cả các quyền đối với ứng dụng {{config('constant.APP_COMPANY')}}.
                            Tuy nhiên chúng tôi không yêu cầu sử dụng các quyền của dữ liệu của bạn. Bạn giữ lại tất cả
                            các quyền đối với dữ liệu của bạn và chúng tôi sẽ không bao giờ liên lạc với khách hàng của
                            bạn trực tiếp, hoặc sử dụng dữ liệu của bạn cho lợi thế kinh doanh của chúng tôi hoặc cạnh
                            tranh với bạn hoặc thị trường để khách hàng của bạn .
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có ý nghĩa</label>
                            Bạn sở hữu dữ liệu của bạn và chúng tôi sẽ tôn trọng điều đó. Chúng tôi sẽ không cố gắng
                            cạnh tranh với bạn hoặc viết thư cho khách hàng của bạn .
                        </div>
                    </section>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3> PHẦN 5</h3>
                            <h2>Cookies</h2>
                            Cookie là một lượng nhỏ dữ liệu , có thể bao gồm một định danh duy nhất vô danh. Cookie được
                            gửi tới trình duyệt của bạn từ một trang web và được lưu trữ trên ổ đĩa cứng của máy tính.
                            Mỗi máy tính có thể truy cập trang web của chúng tôi được phân công một cookie khác nhau.
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có nghĩa,</label>
                            Để xác định bạn trên hệ thống điện tử, một cookie sẽ được lưu trữ trên máy tính của bạn.
                            Chúng tôi có một công cụ " tiếp thị " hoạt động cho phép chúng tôi lưu ý khi bạn viếng thăm
                            trang web của chúng tôi và hiển thị quảng cáo có liên quan trên trang web của chúng tôi và
                            qua mạng Internet. Bạn luôn có thể chọn không tham gia .
                        </div>
                    </section>
                    <section>
                        <div class="col-sm-7 col-xs-12">
                            <h3>PHẦN 6</h3>
                            <h2>Thay đổi chính sách bảo mật </h2>
                            Chúng tôi có quyền thay đổi chính sách bảo mật này bất cứ lúc nào, vì vậy hãy xem xét nó
                            thường xuyên. Nếu chúng tôi có thay đổi quan trọng đối với chính sách này, chúng tôi sẽ
                            thông báo cho bạn đây hoặc bằng một thông báo trên trang chủ của chúng tôi để bạn nhận thức
                            được những gì chúng tôi thu thập thông tin, làm thế nào chúng ta sử dụng nó, và trong hoàn
                            cảnh nào, chúng tôi sẽ tiêt lộ nó, nếu có.
                        </div>
                        <div class="col-sm-5 col-xs-12">
                            <label>Điều đó có nghĩa, </label>
                            Chúng tôi có thể thay đổi chính sách bảo mật này . Nếu đó là một thay đổi lớn, chúng tôi sẽ
                            thông báo cho bạn, ngay tại đây.
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </div>
@endsection