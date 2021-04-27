<head>
    <!--====== USEFULL META ======-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Công ty TNHH {{config('constant.APP_COMPANY')}}"/>
    <meta name="keywords" content="{{config('constant.APP_NAME')}}, xe tải, vận chuyển hàng"/>
    <meta property="og:site_name" content="{{config('constant.APP_NAME')}}"/>
    <meta property="og:title" content="{{config('constant.APP_NAME')}}"/>
    <meta property="og:image" content="https://ceta.vn/css/frontend/img/covid/lp2.png">

    <!--====== TITLE TAG ======-->
    <title>{{config('constant.APP_NAME')}}</title>

    <!--====== FAVICON ICON =======-->
    <link rel="shortcut icon" type="image/ico" href="{{public_url('favicon.png')}}"/>

    <link href="{{ public_url('css/frontend/fonts/brandontext-font.min.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <link href="{{ public_url('css/frontend/ceta/font-awesome.min.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <link href="{{ public_url('css/frontend/ceta/home.css') }}" rel="stylesheet" type="text/css" media="all">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link href="{{ public_url('css/frontend/ceta/slicknav-v1.5.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ public_url('css/frontend/ceta/global-home-v1.2.css') }}" rel="stylesheet" type="text/css"
          media="all">
    <script src="{{ public_url('css/frontend/ceta/jquery-1.11.3.min.js') }}" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <style>
        body, html {
            font-size: 75%;
            margin: 0;
            padding: 0;
        }

    </style>

    @include('layouts.frontend.elements.autoload.head_autoload')
</head>

