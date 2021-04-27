@extends('layouts.frontend.layouts.main')
@section('content')
    <link href="{{ public_url('css/frontend/autoload/about.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <main>
        {{--<section class="section intro">--}}
        {{--<div class="container">--}}
        {{--<h1>Lịch sử hình thành công ty</h1>--}}
        {{--</div>--}}
        {{--</section>--}}

        <section class="timeline">
            <ol>
                <li>
                    <div>
                        <time>2016</time>
                        Ý tưởng phát triển một nền tảng công nghệ giúp các doanh nghiệp logistic tại Việt Nam
                    </div>
                </li>
                <li>
                    <div>
                        <time>05/2017</time>
                        Tìm kiếm những người cùng chung khát khao mong ước giúp đỡ nền logistic Việt
                    </div>
                </li>
                <li>
                    <div>
                        <time>08/2017</time>
                        Dự án bắt đầu với đội ngũ LTV có trình độ và giàu kinh nghiệm được tư vấn bởi những các chuyên
                        gia trong ngành logistic
                    </div>
                </li>
                <li>
                    <div>
                        <time>2018</time>
                        Nền tảng {{config('constant.APP_NAME')}} được hình thành theo đúng tiến độ của dự án. {{config('constant.APP_COMPANY')}} được thành thập theo CGN Đầu
                        tư do Sở KHDT Hà Nội Cấp
                    </div>
                </li>
                <li>
                    <div>
                        <time>01/2019</time>
                        Chính thức ra mắt nền tảng công nghệ {{config('constant.APP_NAME')}}
                    </div>
                </li>
                <li>
                    <div>
                        <time>2020</time>
                        Không ngừng phát triển để trở thành một công ty chuyên cung cấp các giải pháp công nghệ cao
                        trong ngành logistic
                    </div>
                </li>
            </ol>

            <div class="arrows">
                <button class="arrow arrow__prev disabled" disabled>
                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/arrow_prev.svg"
                         alt="prev timeline arrow">
                </button>
                <button class="arrow arrow__next">
                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/arrow_next.svg"
                         alt="next timeline arrow">
                </button>
            </div>
        </section>
    </main>
    <script src="https://hammerjs.github.io/dist/hammer.min.js" type="text/javascript"></script>
    <script src="{{ public_url('js/frontend/autoload/about.js') }}" type="text/javascript"></script>
@endsection