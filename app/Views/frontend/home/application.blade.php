<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! isset($title) ? sprintf("%s | ", $title) : ''!!}{{config('constant.APP_NAME')}}</title>
    <meta name="description" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{public_url('favicon.png')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    @include('layouts.backend.elements.structures.font_kanit')
    @stack('before-css')
    <?php

    $cssFiles = [
        'autoload/application',
    ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'frontend') !!}
    @stack('after-css')

</head>
<body class="auth widescreen">
<div class="logo-image"></div>
<div id="header">
    <a id="logo-link">
        <div class="page-content-logo">

        </div>
    </a>
</div>
<div class="login-form">
    <div class="container">
        <div class="row mb-0 mb-sm-3">
            <div class="col">
                <div class="container-logo-login">
                    <img class="logo-login" src="{{asset('css/backend/images/McLean-logo-1.png')}}" alt="mclean">
                </div>
                <h4 class="text-center">Hãy chọn phiên bản:</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 pb-3 pb-sm-0">
                <div class="card">
                    <div class="card-body">
                        <div class="logo-app">
                            <img src="{{asset('css/frontend/img/cart.svg')}}"  alt="">
                        </div>
                        <h4 class="card-title text-center">Client</h4>
                        <p class="card-text text-center">Phiên bản dành cho Khách hàng - Chủ hàng.</p>
                        <a href="{{route('home.main')}}" class="btn btn-primary" style="width: 100%">Đăng nhập</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 pb-3 pb-sm-0">
                <div class="card">
                    <div class="card-body">
                        <div class="logo-app">
                            <img src="{{asset('css/frontend/img/graph.svg')}}"  alt="">
                        </div>
                        <h4 class="card-title text-center">Admin</h4>
                        <p class="card-text text-center">Phiên bản dành cho Quản trị viên - Đối tác vận tải.</p>
                        <a href="{{route('backend.login')}}" class="btn btn-primary" style="width: 100%">Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer-contact-content">
    <div class="container" style="text-align: center">
        <div id="copy-right">
            <span> Copyright © 2018 - <script>document.write(new Date().getFullYear())</script> {{config('constant.APP_NAME')}} | <a
                href="{{config('constant.APP_WEB')}}" style="color:white;" target="_blank">{{config('constant.APP_WEB')}}</a>

            </span>
        </div>
    </div>
</div>
{{--@stack('scripts')--}}
</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>