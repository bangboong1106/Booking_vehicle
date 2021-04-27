<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->


@include('layouts.frontend.elements.structures.head')

<body class="home-one homepage">

<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<!--- PRELOADER -->
<div class="preeloader">
    <div class="preloader-spinner"></div>
</div>

<!--SCROLL TO TOP-->
{{--<a href="#home" class="scrolltotop"><i class="fa fa-long-arrow-up"></i></a>--}}
<div class="wrapperContainer-layout">
    <header class="mainHeader-hrv">
        <nav class="navbar navbarmain-hrv">
            <div class="container-menu container-fluid">
                <div class="navbar-header">
                    <div class="slicknav_icon visible-xs visible-sm">
                        <a href="javascript:void(0)" id="showmenu-mobile" class="slicknav_btn slicknav_collapsed">
                            <span class="nav-burger"><span class="slicknav_icon-bar"></span></span>
                        </a>
                    </div>
                    <a class="navbar-brand" href="{{route("home")}}">
                        <img class="img-brand" src="{{ public_url('css/backend/images/McLean-logo-1.png') }}"/>
                    </a>
                </div>
                <!-- version 1.1 -->
                <div class="navbar-hrvcollapse navHeader" id="navHeader">
                    <div class="clearfix wrapbox-navbar">
                        <ul class="nav navbar-nav mainmenu-hrv">
                            <li class=""><a href="{{route("about")}}">Giới thiệu</a></li>
                            <li class=""><a href="https://www.vietnamworks.com/onelog-kv" target="_blank">Tuyển dụng</a>
                            </li>
                            <li class=""><a href="{{route("support")}}">Dùng thử miễn phí</a></li>
                            <li class=""><a href="{{route("covid")}}">COVID-19</a><span class="siHot"></span></li>
                            <li class="add-order"><a href="/main#/anonymous-order"><span >Tạo mới đơn hàng</span></a></li>

                        </ul>
                        <ul class="nav navbar-nav navbar-right navbar-button">
                            <li><a class="hot-line" href="tel:{{config('constant.APP_HOTLINE')}}">Hot line: {{config('constant.APP_HOTLINE')}}</a></li>
                            <li class="scroll"><a href="/main" class="btn-hrvmenu menu-regis btn-hover-vertical">Đăng
                                    nhập</a></li>
                        </ul>
                    </div>
                </div>
                <!-- end desk -->
            </div>
        </nav><!-- End navbar -->
    </header>
    @yield('content')
</div>
<footer class="mainFooter-hrv media-rich-hararvan--nopopup ">
    <div class="footer-hrv-linklists">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-3 col-xs-12 widget-linklists widget-footer-mb">
                    <h3 class="title-footer togged-footer">Giới thiệu</h3>
                    <ul class="list-unstyled list-footerlink footer-collapse">
                        <li><a href="/about">Giới thiệu</a></li>
                        <li>Tin tức</li>
                        <li>Thư viện</li>
                        <li><a href="">Tuyển dụng</a></li>
                        <li><a href="/support">Liên hệ</a></li>
                        <li>Chính sách bảo mật</li>
                    </ul>
                </div>
                {{--<div class="col-lg-3 col-sm-3 col-xs-12 widget-linklists widget-footer-mb">--}}
                {{--<h3 class="title-footer togged-footer"> Hỗ trợ</h3>--}}
                {{--<ul class="list-unstyled list-footerlink footer-collapse">--}}
                {{--<li><a href="http://1log.online">Tài liệu hướng dẫn sử dụng</a></li>--}}
                {{--</ul>--}}
                {{--</div>--}}
                <div class="col-lg-3 col-sm-3 col-xs-12 widget-linklists widget-footer-mb widget-border-b0">
                    <div class="widget-hrv-appstore">
                        <h3 class="title-footer togged-footer">TẢI ỨNG DỤNG <span>{{config('constant.APP_NAME')}} DRIVER</span></h3>
                        <ul class="list-unstyled navbar-appstore footer-collapse">
                            <li>
                                <a href="https://play.google.com/store/apps/details?id=com.onelog.elogistics.main"
                                   target="_blank" rel="nofollow">
                                    <svg class="svg-appstore-ggplay" xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 135 40">
                                        <defs>
                                            <lineargradient id="fterlinear-gradient" x1="31.09" y1="20" x2="6.91"
                                                            y2="20" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#ffdf00"></stop>
                                                <stop offset="0.41" stop-color="#fbbc0e"></stop>
                                                <stop offset="0.78" stop-color="#f9a418"></stop>
                                                <stop offset="1" stop-color="#f89b1c"></stop>
                                            </lineargradient>
                                            <lineargradient id="fterlinear-gradient-2" x1="24.81" y1="22.29"
                                                            x2="2.07" y2="45.03" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#ee4447"></stop>
                                                <stop offset="1" stop-color="#c5166c"></stop>
                                            </lineargradient>
                                            <lineargradient id="fterlinear-gradient-3" x1="1.97" y1="-5.13"
                                                            x2="20.54" y2="13.43" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#269e6f"></stop>
                                                <stop offset="0.05" stop-color="#2ba06f"></stop>
                                                <stop offset="0.47" stop-color="#53b26b"></stop>
                                                <stop offset="0.8" stop-color="#6bbd69"></stop>
                                                <stop offset="1" stop-color="#74c168"></stop>
                                            </lineargradient>
                                        </defs>
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="Layer_1-2" data-name="Layer 1">
                                                <rect width="135" height="40" rx="5" ry="5"></rect>
                                                <path class="fter-ggplay-1"
                                                      d="M130,.8A4.2,4.2,0,0,1,134.2,5V35a4.2,4.2,0,0,1-4.2,4.2H5A4.2,4.2,0,0,1,.8,35V5A4.2,4.2,0,0,1,5,.8H130m0-.8H5A5,5,0,0,0,0,5V35a5,5,0,0,0,5,5H130a5,5,0,0,0,5-5V5a5,5,0,0,0-5-5Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M47.42,10.24a2.71,2.71,0,0,1-.75,2,2.91,2.91,0,0,1-2.2.89A3.09,3.09,0,0,1,41.35,10a3.09,3.09,0,0,1,3.12-3.13,3.1,3.1,0,0,1,1.23.25,2.48,2.48,0,0,1,.94.67l-.53.53a2,2,0,0,0-1.64-.71A2.32,2.32,0,0,0,42.14,10a2.36,2.36,0,0,0,4,1.73,1.89,1.89,0,0,0,.5-1.22H44.47V9.79h2.91A2.54,2.54,0,0,1,47.42,10.24Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M52,7.74H49.3v1.9h2.46v.72H49.3v1.9H52V13h-3.5V7H52Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M55.28,13h-.77V7.74H52.83V7H57v.74H55.28Z"></path>
                                                <path class="fter-ggplay-2" d="M59.94,13V7h.77v6Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M64.13,13h-.77V7.74H61.68V7H65.8v.74H64.13Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M73.61,12.22a3.12,3.12,0,0,1-4.4,0A3.07,3.07,0,0,1,68.33,10a3.07,3.07,0,0,1,.88-2.22,3.1,3.1,0,0,1,4.4,0A3.07,3.07,0,0,1,74.49,10,3.07,3.07,0,0,1,73.61,12.22Zm-3.83-.5a2.31,2.31,0,0,0,3.26,0A2.35,2.35,0,0,0,73.71,10,2.35,2.35,0,0,0,73,8.28a2.31,2.31,0,0,0-3.26,0A2.35,2.35,0,0,0,69.11,10,2.35,2.35,0,0,0,69.78,11.72Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M75.58,13V7h.94l2.92,4.67h0l0-1.16V7h.77v6h-.8L76.35,8.11h0l0,1.16V13Z"></path>
                                                <path class="fter-ggplay-3"
                                                      d="M68.14,21.75A4.25,4.25,0,1,0,72.41,26,4.19,4.19,0,0,0,68.14,21.75Zm0,6.83A2.58,2.58,0,1,1,70.54,26,2.46,2.46,0,0,1,68.14,28.58Zm-9.31-6.83A4.25,4.25,0,1,0,63.09,26,4.19,4.19,0,0,0,58.82,21.75Zm0,6.83A2.58,2.58,0,1,1,61.22,26,2.46,2.46,0,0,1,58.82,28.58ZM47.74,23.06v1.8h4.32a3.77,3.77,0,0,1-1,2.27,4.42,4.42,0,0,1-3.33,1.32,4.8,4.8,0,0,1,0-9.6A4.6,4.6,0,0,1,51,20.14l1.27-1.27A6.29,6.29,0,0,0,47.74,17a6.61,6.61,0,1,0,0,13.21,6,6,0,0,0,4.61-1.85,6,6,0,0,0,1.56-4.22,5.87,5.87,0,0,0-.1-1.13Zm45.31,1.4a4,4,0,0,0-3.64-2.71,4,4,0,0,0-4,4.25,4.16,4.16,0,0,0,4.22,4.25,4.23,4.23,0,0,0,3.54-1.88l-1.45-1a2.43,2.43,0,0,1-2.09,1.18,2.16,2.16,0,0,1-2.06-1.29l5.69-2.35Zm-5.8,1.42a2.33,2.33,0,0,1,2.22-2.48,1.65,1.65,0,0,1,1.58.9ZM82.63,30H84.5V17.5H82.63Zm-3.06-7.3H79.5a3,3,0,0,0-2.24-1,4.26,4.26,0,0,0,0,8.51,2.9,2.9,0,0,0,2.24-1h.06v.61c0,1.63-.87,2.5-2.27,2.5a2.35,2.35,0,0,1-2.14-1.51l-1.63.68a4.05,4.05,0,0,0,3.77,2.51c2.19,0,4-1.29,4-4.43V22H79.57Zm-2.14,5.88a2.59,2.59,0,0,1,0-5.16A2.4,2.4,0,0,1,79.7,26,2.38,2.38,0,0,1,77.42,28.58ZM101.81,17.5H97.33V30H99.2V25.26h2.61a3.89,3.89,0,1,0,0-7.76Zm0,6H99.2V19.24h2.65a2.14,2.14,0,1,1,0,4.29Zm11.53-1.8a3.5,3.5,0,0,0-3.33,1.91l1.66.69a1.77,1.77,0,0,1,1.7-.92,1.8,1.8,0,0,1,2,1.61v.13a4.13,4.13,0,0,0-1.95-.48c-1.79,0-3.6,1-3.6,2.81a2.89,2.89,0,0,0,3.1,2.75A2.63,2.63,0,0,0,115.32,29h.06v1h1.8V25.19C117.18,23,115.52,21.73,113.39,21.73Zm-.23,6.85c-.61,0-1.46-.31-1.46-1.06,0-1,1.06-1.33,2-1.33a3.32,3.32,0,0,1,1.7.42A2.26,2.26,0,0,1,113.16,28.58ZM123.74,22l-2.14,5.42h-.06L119.32,22h-2l3.33,7.58-1.9,4.21h1.95L125.82,22Zm-16.81,8h1.87V17.5h-1.87Z"></path>
                                                <path class="fter-ggplay-4"
                                                      d="M10.44,7.55A2,2,0,0,0,10,9V31a2,2,0,0,0,.46,1.4l.07.07L22.89,20.15v-.29L10.51,7.48Z"></path>
                                                <path class="fter-ggplay-5"
                                                      d="M27,24.27l-4.13-4.13v-.29L27,15.73l.09.05L32,18.56c1.4.79,1.4,2.09,0,2.89l-4.89,2.78Z"></path>
                                                <path class="Graphic-Style-2"
                                                      d="M27.11,24.22,22.89,20,10.44,32.45a1.63,1.63,0,0,0,2.08.06l14.6-8.29"></path>
                                                <path class="fter-ggplay-6"
                                                      d="M27.11,15.78,12.51,7.49a1.63,1.63,0,0,0-2.08.06L22.89,20Z"></path>
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://apps.apple.com/vn/app/driver-app/id1455773807" target="_blank"
                                   rel="nofollow">
                                    <svg class="svg-appstore-apple" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 119.66 40">
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="Layer_1-2" data-name="Layer 1">
                                                <path class="fter-apple-1"
                                                      d="M110.13,0H7.52a13.21,13.21,0,0,0-2,.18,6.67,6.67,0,0,0-1.9.63A6.44,6.44,0,0,0,2,2,6.26,6.26,0,0,0,.82,3.62a6.6,6.6,0,0,0-.62,1.9,13,13,0,0,0-.18,2c0,.31,0,.61,0,.92V31.56c0,.31,0,.61,0,.92a13,13,0,0,0,.18,2,6.59,6.59,0,0,0,.63,1.9A6.21,6.21,0,0,0,2,38a6.27,6.27,0,0,0,1.62,1.18,6.7,6.7,0,0,0,1.9.63,13.45,13.45,0,0,0,2,.18H112.14a13.28,13.28,0,0,0,2-.18,6.8,6.8,0,0,0,1.91-.63A6.28,6.28,0,0,0,117.67,38a6.39,6.39,0,0,0,1.18-1.61,6.6,6.6,0,0,0,.62-1.9,13.51,13.51,0,0,0,.19-2c0-.31,0-.61,0-.92s0-.72,0-1.09V9.54c0-.37,0-.73,0-1.09s0-.61,0-.92a13.51,13.51,0,0,0-.19-2,6.62,6.62,0,0,0-.62-1.9A6.47,6.47,0,0,0,116,.82a6.77,6.77,0,0,0-1.91-.63,13,13,0,0,0-2-.18h-2Z"></path>
                                                <path d="M8.44,39.13h-.9A12.69,12.69,0,0,1,5.67,39,5.88,5.88,0,0,1,4,38.4a5.41,5.41,0,0,1-1.4-1,5.32,5.32,0,0,1-1-1.4,5.72,5.72,0,0,1-.54-1.66,12.41,12.41,0,0,1-.17-1.87c0-.21,0-.91,0-.91V8.44s0-.69,0-.89a12.37,12.37,0,0,1,.17-1.87A5.76,5.76,0,0,1,1.6,4a5.37,5.37,0,0,1,1-1.4A5.57,5.57,0,0,1,4,1.6a5.82,5.82,0,0,1,1.65-.54A12.59,12.59,0,0,1,7.54.89H112.13a12.38,12.38,0,0,1,1.86.16,5.94,5.94,0,0,1,1.67.55A5.59,5.59,0,0,1,118.07,4a5.76,5.76,0,0,1,.54,1.65,13,13,0,0,1,.17,1.89c0,.28,0,.59,0,.89s0,.73,0,1.09V30.46c0,.36,0,.72,0,1.08s0,.62,0,.93a12.73,12.73,0,0,1-.17,1.85,5.74,5.74,0,0,1-.54,1.67,5.48,5.48,0,0,1-1,1.39,5.41,5.41,0,0,1-1.4,1A5.86,5.86,0,0,1,114,39a12.54,12.54,0,0,1-1.87.16H8.44Z"></path>
                                                <g id="_Group_" data-name=" Group ">
                                                    <g id="_Group_2" data-name=" Group 2">
                                                        <g id="_Group_3" data-name=" Group 3">
                                                            <path id="_Path_" data-name=" Path "
                                                                  class="fter-apple-2"
                                                                  d="M24.77,20.3a5,5,0,0,1,2.36-4.15,5.07,5.07,0,0,0-4-2.16c-1.68-.18-3.31,1-4.16,1s-2.19-1-3.61-1a5.32,5.32,0,0,0-4.47,2.73C9,20.11,10.4,25,12.25,27.74c.93,1.33,2,2.81,3.43,2.75s1.91-.88,3.58-.88,2.14.88,3.59.85,2.43-1.33,3.32-2.67a11,11,0,0,0,1.52-3.09A4.78,4.78,0,0,1,24.77,20.3Z"></path>
                                                            <path id="_Path_2" data-name=" Path 2"
                                                                  class="fter-apple-2"
                                                                  d="M22,12.21a4.87,4.87,0,0,0,1.11-3.49,5,5,0,0,0-3.21,1.66,4.64,4.64,0,0,0-1.14,3.36A4.1,4.1,0,0,0,22,12.21Z"></path>
                                                        </g>
                                                    </g>
                                                    <path class="fter-apple-2"
                                                          d="M42.3,27.14H37.57L36.43,30.5h-2l4.48-12.42H41L45.48,30.5h-2Zm-4.24-1.55h3.75L40,20.14h-.05Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M55.16,26c0,2.81-1.51,4.62-3.78,4.62A3.07,3.07,0,0,1,48.53,29h0v4.48H46.63v-12h1.8v1.51h0a3.21,3.21,0,0,1,2.88-1.6C53.65,21.35,55.16,23.16,55.16,26Zm-1.91,0c0-1.83-.95-3-2.39-3s-2.37,1.23-2.37,3,1,3,2.38,3S53.25,27.82,53.25,26Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M65.12,26c0,2.81-1.51,4.62-3.78,4.62A3.07,3.07,0,0,1,58.5,29h0v4.48H56.6v-12h1.8v1.51h0a3.21,3.21,0,0,1,2.88-1.6C63.61,21.35,65.12,23.16,65.12,26Zm-1.91,0c0-1.83-.95-3-2.39-3s-2.37,1.23-2.37,3,1,3,2.38,3,2.39-1.2,2.39-3Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M71.71,27c.14,1.23,1.33,2,3,2s2.69-.81,2.69-1.92-.68-1.54-2.29-1.94l-1.61-.39c-2.28-.55-3.34-1.62-3.34-3.35,0-2.14,1.87-3.61,4.52-3.61s4.42,1.47,4.48,3.61H77.26c-.11-1.24-1.14-2-2.63-2s-2.52.76-2.52,1.86c0,.88.65,1.39,2.25,1.79l1.37.34c2.55.6,3.61,1.63,3.61,3.44,0,2.32-1.85,3.78-4.79,3.78-2.75,0-4.61-1.42-4.73-3.67Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M83.35,19.3v2.14h1.72v1.47H83.35v5c0,.78.34,1.14,1.1,1.14a5.81,5.81,0,0,0,.61,0v1.46a5.1,5.1,0,0,1-1,.09c-1.83,0-2.55-.69-2.55-2.44V22.91H80.16V21.44h1.32V19.3Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M86.07,26c0-2.85,1.68-4.64,4.29-4.64s4.29,1.79,4.29,4.64S93,30.61,90.36,30.61,86.07,28.83,86.07,26Zm6.7,0c0-2-.9-3.11-2.4-3.11S88,24,88,26s.89,3.11,2.4,3.11,2.4-1.14,2.4-3.11Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M96.19,21.44H98V23h0a2.16,2.16,0,0,1,2.18-1.64,2.87,2.87,0,0,1,.64.07v1.74A2.6,2.6,0,0,0,100,23,1.87,1.87,0,0,0,98,25.13V30.5H96.19Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M109.38,27.84c-.25,1.64-1.85,2.77-3.9,2.77-2.63,0-4.27-1.76-4.27-4.6s1.64-4.68,4.19-4.68,4.08,1.72,4.08,4.47v.64h-6.39v.11a2.36,2.36,0,0,0,2.14,2.56h.29a2,2,0,0,0,2.09-1.27Zm-6.28-2.7h4.53a2.18,2.18,0,0,0-2.05-2.29h-.17a2.29,2.29,0,0,0-2.31,2.28S103.1,25.13,103.1,25.13Z"></path>
                                                </g>
                                                <g id="_Group_4" data-name=" Group 4">
                                                    <path class="fter-apple-2"
                                                          d="M37.83,8.73a2.64,2.64,0,0,1,2.81,3c0,1.91-1,3-2.81,3H35.67v-6ZM36.6,13.85h1.13a1.88,1.88,0,0,0,2-2.15,1.88,1.88,0,0,0-2-2.13H36.6Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M41.68,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S43,14,43.81,14,45,13.42,45,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M51.57,14.7h-.92l-.93-3.32h-.07l-.93,3.32h-.91l-1.24-4.5h.9l.81,3.44h.07l.93-3.44h.85L51,13.63h.07l.8-3.44h.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M53.85,10.19h.86v.72h.07a1.35,1.35,0,0,1,1.34-.8,1.46,1.46,0,0,1,1.56,1.67V14.7h-.89V12c0-.72-.31-1.08-1-1.08a1,1,0,0,0-1.08,1.14V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2" d="M59.09,8.44H60V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M61.22,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S62.57,14,63.34,14,64.55,13.42,64.55,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M66.4,13.42c0-.81.6-1.28,1.67-1.34L69.3,12v-.39c0-.48-.31-.74-.92-.74s-.84.18-.94.5h-.86c.09-.77.82-1.27,1.84-1.27s1.77.56,1.77,1.51V14.7h-.86v-.63h-.07a1.51,1.51,0,0,1-1.35.71,1.36,1.36,0,0,1-1.49-1.21S66.4,13.47,66.4,13.42ZM69.3,13v-.38l-1.1.07c-.62,0-.9.25-.9.65s.35.64.83.64a1.06,1.06,0,0,0,1.16-.95Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M71.35,12.44c0-1.42.73-2.32,1.87-2.32a1.48,1.48,0,0,1,1.38.79h.07V8.44h.89V14.7H74.7V14h-.07a1.56,1.56,0,0,1-1.41.79C72.07,14.77,71.35,13.87,71.35,12.44Zm.92,0c0,1,.45,1.53,1.2,1.53s1.21-.58,1.21-1.53-.47-1.53-1.21-1.53-1.2.58-1.2,1.53Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M79.23,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S80.58,14,81.36,14,82.56,13.42,82.56,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M84.67,10.19h.86v.72h.07a1.35,1.35,0,0,1,1.34-.8,1.46,1.46,0,0,1,1.56,1.67V14.7h-.89V12c0-.72-.31-1.08-1-1.08a1,1,0,0,0-1.08,1.14V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M93.52,9.07v1.14h1V11h-1v2.32c0,.47.19.68.64.68l.34,0v.74a2.92,2.92,0,0,1-.48,0c-1,0-1.38-.35-1.38-1.22V11h-.71v-.75h.71V9.07Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M95.7,8.44h.88v2.48h.07A1.39,1.39,0,0,1,98,10.11a1.48,1.48,0,0,1,1.55,1.68V14.7h-.89V12c0-.72-.33-1.08-1-1.08a1.05,1.05,0,0,0-1.13,1.14V14.7H95.7Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M104.76,13.48a1.83,1.83,0,0,1-2,1.3,2,2,0,0,1-2.08-2.32,2.08,2.08,0,0,1,1.78-2.33l.29,0c1.25,0,2,.86,2,2.27v.31h-3.18v0A1.19,1.19,0,0,0,102.72,14h.11a1.08,1.08,0,0,0,1.07-.55ZM101.64,12h2.27a1.09,1.09,0,0,0-1-1.16h-.11A1.15,1.15,0,0,0,101.64,12s0,0,0,0Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="widget-hrv-social">
                        <h3 class="title-footer togged-footer">KẾT NỐI VỚI {{config('constant.APP_NAME')}}</h3>
                        <ul class="list-unstyled navbar-social footer-collapse">
                            <li>
                                <a href="" target="_blank"
                                   rel="nofollow">
                                    <img alt="social facebook mau" title="social facebook"
                                         src="{{ public_url('css/frontend/ceta/fb-mau.svg') }}">
                                    <img alt="social facebook den" title="social facebook"
                                         src="{{ public_url('css/frontend/ceta/fb-den.svg') }}">
                                </a>
                            </li>
                            <li>
                                <a href="" target="_blank"
                                   rel="nofollow">
                                    <img alt="social youtube mau" title="social youtube"
                                         src="{{ public_url('css/frontend/ceta/yt-mau.svg') }}">
                                    <img alt="social youtube den" title="social youtube"
                                         src="{{ public_url('css/frontend/ceta/yt-den.svg') }}">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-3 col-xs-12 widget-linklists widget-footer-mb widget-border-b0">
                    <div class="widget-hrv-appstore">
                        <h3 class="title-footer togged-footer">TẢI ỨNG DỤNG <span>{{config('constant.APP_NAME')}} MANAGEMENT</span></h3>
                        <ul class="list-unstyled navbar-appstore footer-collapse">
                            <li>
                                <a href="https://play.google.com/store/apps/details?id=com.onelog.dashboardapp"
                                   target="_blank" rel="nofollow">
                                    <svg class="svg-appstore-ggplay" xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 135 40">
                                        <defs>
                                            <lineargradient id="fterlinear-gradient" x1="31.09" y1="20" x2="6.91"
                                                            y2="20" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#ffdf00"></stop>
                                                <stop offset="0.41" stop-color="#fbbc0e"></stop>
                                                <stop offset="0.78" stop-color="#f9a418"></stop>
                                                <stop offset="1" stop-color="#f89b1c"></stop>
                                            </lineargradient>
                                            <lineargradient id="fterlinear-gradient-2" x1="24.81" y1="22.29"
                                                            x2="2.07" y2="45.03" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#ee4447"></stop>
                                                <stop offset="1" stop-color="#c5166c"></stop>
                                            </lineargradient>
                                            <lineargradient id="fterlinear-gradient-3" x1="1.97" y1="-5.13"
                                                            x2="20.54" y2="13.43" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#269e6f"></stop>
                                                <stop offset="0.05" stop-color="#2ba06f"></stop>
                                                <stop offset="0.47" stop-color="#53b26b"></stop>
                                                <stop offset="0.8" stop-color="#6bbd69"></stop>
                                                <stop offset="1" stop-color="#74c168"></stop>
                                            </lineargradient>
                                        </defs>
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="Layer_1-2" data-name="Layer 1">
                                                <rect width="135" height="40" rx="5" ry="5"></rect>
                                                <path class="fter-ggplay-1"
                                                      d="M130,.8A4.2,4.2,0,0,1,134.2,5V35a4.2,4.2,0,0,1-4.2,4.2H5A4.2,4.2,0,0,1,.8,35V5A4.2,4.2,0,0,1,5,.8H130m0-.8H5A5,5,0,0,0,0,5V35a5,5,0,0,0,5,5H130a5,5,0,0,0,5-5V5a5,5,0,0,0-5-5Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M47.42,10.24a2.71,2.71,0,0,1-.75,2,2.91,2.91,0,0,1-2.2.89A3.09,3.09,0,0,1,41.35,10a3.09,3.09,0,0,1,3.12-3.13,3.1,3.1,0,0,1,1.23.25,2.48,2.48,0,0,1,.94.67l-.53.53a2,2,0,0,0-1.64-.71A2.32,2.32,0,0,0,42.14,10a2.36,2.36,0,0,0,4,1.73,1.89,1.89,0,0,0,.5-1.22H44.47V9.79h2.91A2.54,2.54,0,0,1,47.42,10.24Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M52,7.74H49.3v1.9h2.46v.72H49.3v1.9H52V13h-3.5V7H52Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M55.28,13h-.77V7.74H52.83V7H57v.74H55.28Z"></path>
                                                <path class="fter-ggplay-2" d="M59.94,13V7h.77v6Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M64.13,13h-.77V7.74H61.68V7H65.8v.74H64.13Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M73.61,12.22a3.12,3.12,0,0,1-4.4,0A3.07,3.07,0,0,1,68.33,10a3.07,3.07,0,0,1,.88-2.22,3.1,3.1,0,0,1,4.4,0A3.07,3.07,0,0,1,74.49,10,3.07,3.07,0,0,1,73.61,12.22Zm-3.83-.5a2.31,2.31,0,0,0,3.26,0A2.35,2.35,0,0,0,73.71,10,2.35,2.35,0,0,0,73,8.28a2.31,2.31,0,0,0-3.26,0A2.35,2.35,0,0,0,69.11,10,2.35,2.35,0,0,0,69.78,11.72Z"></path>
                                                <path class="fter-ggplay-2"
                                                      d="M75.58,13V7h.94l2.92,4.67h0l0-1.16V7h.77v6h-.8L76.35,8.11h0l0,1.16V13Z"></path>
                                                <path class="fter-ggplay-3"
                                                      d="M68.14,21.75A4.25,4.25,0,1,0,72.41,26,4.19,4.19,0,0,0,68.14,21.75Zm0,6.83A2.58,2.58,0,1,1,70.54,26,2.46,2.46,0,0,1,68.14,28.58Zm-9.31-6.83A4.25,4.25,0,1,0,63.09,26,4.19,4.19,0,0,0,58.82,21.75Zm0,6.83A2.58,2.58,0,1,1,61.22,26,2.46,2.46,0,0,1,58.82,28.58ZM47.74,23.06v1.8h4.32a3.77,3.77,0,0,1-1,2.27,4.42,4.42,0,0,1-3.33,1.32,4.8,4.8,0,0,1,0-9.6A4.6,4.6,0,0,1,51,20.14l1.27-1.27A6.29,6.29,0,0,0,47.74,17a6.61,6.61,0,1,0,0,13.21,6,6,0,0,0,4.61-1.85,6,6,0,0,0,1.56-4.22,5.87,5.87,0,0,0-.1-1.13Zm45.31,1.4a4,4,0,0,0-3.64-2.71,4,4,0,0,0-4,4.25,4.16,4.16,0,0,0,4.22,4.25,4.23,4.23,0,0,0,3.54-1.88l-1.45-1a2.43,2.43,0,0,1-2.09,1.18,2.16,2.16,0,0,1-2.06-1.29l5.69-2.35Zm-5.8,1.42a2.33,2.33,0,0,1,2.22-2.48,1.65,1.65,0,0,1,1.58.9ZM82.63,30H84.5V17.5H82.63Zm-3.06-7.3H79.5a3,3,0,0,0-2.24-1,4.26,4.26,0,0,0,0,8.51,2.9,2.9,0,0,0,2.24-1h.06v.61c0,1.63-.87,2.5-2.27,2.5a2.35,2.35,0,0,1-2.14-1.51l-1.63.68a4.05,4.05,0,0,0,3.77,2.51c2.19,0,4-1.29,4-4.43V22H79.57Zm-2.14,5.88a2.59,2.59,0,0,1,0-5.16A2.4,2.4,0,0,1,79.7,26,2.38,2.38,0,0,1,77.42,28.58ZM101.81,17.5H97.33V30H99.2V25.26h2.61a3.89,3.89,0,1,0,0-7.76Zm0,6H99.2V19.24h2.65a2.14,2.14,0,1,1,0,4.29Zm11.53-1.8a3.5,3.5,0,0,0-3.33,1.91l1.66.69a1.77,1.77,0,0,1,1.7-.92,1.8,1.8,0,0,1,2,1.61v.13a4.13,4.13,0,0,0-1.95-.48c-1.79,0-3.6,1-3.6,2.81a2.89,2.89,0,0,0,3.1,2.75A2.63,2.63,0,0,0,115.32,29h.06v1h1.8V25.19C117.18,23,115.52,21.73,113.39,21.73Zm-.23,6.85c-.61,0-1.46-.31-1.46-1.06,0-1,1.06-1.33,2-1.33a3.32,3.32,0,0,1,1.7.42A2.26,2.26,0,0,1,113.16,28.58ZM123.74,22l-2.14,5.42h-.06L119.32,22h-2l3.33,7.58-1.9,4.21h1.95L125.82,22Zm-16.81,8h1.87V17.5h-1.87Z"></path>
                                                <path class="fter-ggplay-4"
                                                      d="M10.44,7.55A2,2,0,0,0,10,9V31a2,2,0,0,0,.46,1.4l.07.07L22.89,20.15v-.29L10.51,7.48Z"></path>
                                                <path class="fter-ggplay-5"
                                                      d="M27,24.27l-4.13-4.13v-.29L27,15.73l.09.05L32,18.56c1.4.79,1.4,2.09,0,2.89l-4.89,2.78Z"></path>
                                                <path class="Graphic-Style-2"
                                                      d="M27.11,24.22,22.89,20,10.44,32.45a1.63,1.63,0,0,0,2.08.06l14.6-8.29"></path>
                                                <path class="fter-ggplay-6"
                                                      d="M27.11,15.78,12.51,7.49a1.63,1.63,0,0,0-2.08.06L22.89,20Z"></path>
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://apps.apple.com/us/app/ceta-management/id1479315037" target="_blank"
                                   rel="nofollow">
                                    <svg class="svg-appstore-apple" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 119.66 40">
                                        <g id="Layer_2" data-name="Layer 2">
                                            <g id="Layer_1-2" data-name="Layer 1">
                                                <path class="fter-apple-1"
                                                      d="M110.13,0H7.52a13.21,13.21,0,0,0-2,.18,6.67,6.67,0,0,0-1.9.63A6.44,6.44,0,0,0,2,2,6.26,6.26,0,0,0,.82,3.62a6.6,6.6,0,0,0-.62,1.9,13,13,0,0,0-.18,2c0,.31,0,.61,0,.92V31.56c0,.31,0,.61,0,.92a13,13,0,0,0,.18,2,6.59,6.59,0,0,0,.63,1.9A6.21,6.21,0,0,0,2,38a6.27,6.27,0,0,0,1.62,1.18,6.7,6.7,0,0,0,1.9.63,13.45,13.45,0,0,0,2,.18H112.14a13.28,13.28,0,0,0,2-.18,6.8,6.8,0,0,0,1.91-.63A6.28,6.28,0,0,0,117.67,38a6.39,6.39,0,0,0,1.18-1.61,6.6,6.6,0,0,0,.62-1.9,13.51,13.51,0,0,0,.19-2c0-.31,0-.61,0-.92s0-.72,0-1.09V9.54c0-.37,0-.73,0-1.09s0-.61,0-.92a13.51,13.51,0,0,0-.19-2,6.62,6.62,0,0,0-.62-1.9A6.47,6.47,0,0,0,116,.82a6.77,6.77,0,0,0-1.91-.63,13,13,0,0,0-2-.18h-2Z"></path>
                                                <path d="M8.44,39.13h-.9A12.69,12.69,0,0,1,5.67,39,5.88,5.88,0,0,1,4,38.4a5.41,5.41,0,0,1-1.4-1,5.32,5.32,0,0,1-1-1.4,5.72,5.72,0,0,1-.54-1.66,12.41,12.41,0,0,1-.17-1.87c0-.21,0-.91,0-.91V8.44s0-.69,0-.89a12.37,12.37,0,0,1,.17-1.87A5.76,5.76,0,0,1,1.6,4a5.37,5.37,0,0,1,1-1.4A5.57,5.57,0,0,1,4,1.6a5.82,5.82,0,0,1,1.65-.54A12.59,12.59,0,0,1,7.54.89H112.13a12.38,12.38,0,0,1,1.86.16,5.94,5.94,0,0,1,1.67.55A5.59,5.59,0,0,1,118.07,4a5.76,5.76,0,0,1,.54,1.65,13,13,0,0,1,.17,1.89c0,.28,0,.59,0,.89s0,.73,0,1.09V30.46c0,.36,0,.72,0,1.08s0,.62,0,.93a12.73,12.73,0,0,1-.17,1.85,5.74,5.74,0,0,1-.54,1.67,5.48,5.48,0,0,1-1,1.39,5.41,5.41,0,0,1-1.4,1A5.86,5.86,0,0,1,114,39a12.54,12.54,0,0,1-1.87.16H8.44Z"></path>
                                                <g id="_Group_" data-name=" Group ">
                                                    <g id="_Group_2" data-name=" Group 2">
                                                        <g id="_Group_3" data-name=" Group 3">
                                                            <path id="_Path_" data-name=" Path "
                                                                  class="fter-apple-2"
                                                                  d="M24.77,20.3a5,5,0,0,1,2.36-4.15,5.07,5.07,0,0,0-4-2.16c-1.68-.18-3.31,1-4.16,1s-2.19-1-3.61-1a5.32,5.32,0,0,0-4.47,2.73C9,20.11,10.4,25,12.25,27.74c.93,1.33,2,2.81,3.43,2.75s1.91-.88,3.58-.88,2.14.88,3.59.85,2.43-1.33,3.32-2.67a11,11,0,0,0,1.52-3.09A4.78,4.78,0,0,1,24.77,20.3Z"></path>
                                                            <path id="_Path_2" data-name=" Path 2"
                                                                  class="fter-apple-2"
                                                                  d="M22,12.21a4.87,4.87,0,0,0,1.11-3.49,5,5,0,0,0-3.21,1.66,4.64,4.64,0,0,0-1.14,3.36A4.1,4.1,0,0,0,22,12.21Z"></path>
                                                        </g>
                                                    </g>
                                                    <path class="fter-apple-2"
                                                          d="M42.3,27.14H37.57L36.43,30.5h-2l4.48-12.42H41L45.48,30.5h-2Zm-4.24-1.55h3.75L40,20.14h-.05Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M55.16,26c0,2.81-1.51,4.62-3.78,4.62A3.07,3.07,0,0,1,48.53,29h0v4.48H46.63v-12h1.8v1.51h0a3.21,3.21,0,0,1,2.88-1.6C53.65,21.35,55.16,23.16,55.16,26Zm-1.91,0c0-1.83-.95-3-2.39-3s-2.37,1.23-2.37,3,1,3,2.38,3S53.25,27.82,53.25,26Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M65.12,26c0,2.81-1.51,4.62-3.78,4.62A3.07,3.07,0,0,1,58.5,29h0v4.48H56.6v-12h1.8v1.51h0a3.21,3.21,0,0,1,2.88-1.6C63.61,21.35,65.12,23.16,65.12,26Zm-1.91,0c0-1.83-.95-3-2.39-3s-2.37,1.23-2.37,3,1,3,2.38,3,2.39-1.2,2.39-3Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M71.71,27c.14,1.23,1.33,2,3,2s2.69-.81,2.69-1.92-.68-1.54-2.29-1.94l-1.61-.39c-2.28-.55-3.34-1.62-3.34-3.35,0-2.14,1.87-3.61,4.52-3.61s4.42,1.47,4.48,3.61H77.26c-.11-1.24-1.14-2-2.63-2s-2.52.76-2.52,1.86c0,.88.65,1.39,2.25,1.79l1.37.34c2.55.6,3.61,1.63,3.61,3.44,0,2.32-1.85,3.78-4.79,3.78-2.75,0-4.61-1.42-4.73-3.67Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M83.35,19.3v2.14h1.72v1.47H83.35v5c0,.78.34,1.14,1.1,1.14a5.81,5.81,0,0,0,.61,0v1.46a5.1,5.1,0,0,1-1,.09c-1.83,0-2.55-.69-2.55-2.44V22.91H80.16V21.44h1.32V19.3Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M86.07,26c0-2.85,1.68-4.64,4.29-4.64s4.29,1.79,4.29,4.64S93,30.61,90.36,30.61,86.07,28.83,86.07,26Zm6.7,0c0-2-.9-3.11-2.4-3.11S88,24,88,26s.89,3.11,2.4,3.11,2.4-1.14,2.4-3.11Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M96.19,21.44H98V23h0a2.16,2.16,0,0,1,2.18-1.64,2.87,2.87,0,0,1,.64.07v1.74A2.6,2.6,0,0,0,100,23,1.87,1.87,0,0,0,98,25.13V30.5H96.19Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M109.38,27.84c-.25,1.64-1.85,2.77-3.9,2.77-2.63,0-4.27-1.76-4.27-4.6s1.64-4.68,4.19-4.68,4.08,1.72,4.08,4.47v.64h-6.39v.11a2.36,2.36,0,0,0,2.14,2.56h.29a2,2,0,0,0,2.09-1.27Zm-6.28-2.7h4.53a2.18,2.18,0,0,0-2.05-2.29h-.17a2.29,2.29,0,0,0-2.31,2.28S103.1,25.13,103.1,25.13Z"></path>
                                                </g>
                                                <g id="_Group_4" data-name=" Group 4">
                                                    <path class="fter-apple-2"
                                                          d="M37.83,8.73a2.64,2.64,0,0,1,2.81,3c0,1.91-1,3-2.81,3H35.67v-6ZM36.6,13.85h1.13a1.88,1.88,0,0,0,2-2.15,1.88,1.88,0,0,0-2-2.13H36.6Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M41.68,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S43,14,43.81,14,45,13.42,45,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M51.57,14.7h-.92l-.93-3.32h-.07l-.93,3.32h-.91l-1.24-4.5h.9l.81,3.44h.07l.93-3.44h.85L51,13.63h.07l.8-3.44h.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M53.85,10.19h.86v.72h.07a1.35,1.35,0,0,1,1.34-.8,1.46,1.46,0,0,1,1.56,1.67V14.7h-.89V12c0-.72-.31-1.08-1-1.08a1,1,0,0,0-1.08,1.14V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2" d="M59.09,8.44H60V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M61.22,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S62.57,14,63.34,14,64.55,13.42,64.55,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M66.4,13.42c0-.81.6-1.28,1.67-1.34L69.3,12v-.39c0-.48-.31-.74-.92-.74s-.84.18-.94.5h-.86c.09-.77.82-1.27,1.84-1.27s1.77.56,1.77,1.51V14.7h-.86v-.63h-.07a1.51,1.51,0,0,1-1.35.71,1.36,1.36,0,0,1-1.49-1.21S66.4,13.47,66.4,13.42ZM69.3,13v-.38l-1.1.07c-.62,0-.9.25-.9.65s.35.64.83.64a1.06,1.06,0,0,0,1.16-.95Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M71.35,12.44c0-1.42.73-2.32,1.87-2.32a1.48,1.48,0,0,1,1.38.79h.07V8.44h.89V14.7H74.7V14h-.07a1.56,1.56,0,0,1-1.41.79C72.07,14.77,71.35,13.87,71.35,12.44Zm.92,0c0,1,.45,1.53,1.2,1.53s1.21-.58,1.21-1.53-.47-1.53-1.21-1.53-1.2.58-1.2,1.53Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M79.23,12.44a2.13,2.13,0,1,1,4.25,0,2.13,2.13,0,1,1-4.25,0Zm3.33,0c0-1-.44-1.55-1.21-1.55s-1.21.57-1.21,1.55S80.58,14,81.36,14,82.56,13.42,82.56,12.44Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M84.67,10.19h.86v.72h.07a1.35,1.35,0,0,1,1.34-.8,1.46,1.46,0,0,1,1.56,1.67V14.7h-.89V12c0-.72-.31-1.08-1-1.08a1,1,0,0,0-1.08,1.14V14.7h-.89Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M93.52,9.07v1.14h1V11h-1v2.32c0,.47.19.68.64.68l.34,0v.74a2.92,2.92,0,0,1-.48,0c-1,0-1.38-.35-1.38-1.22V11h-.71v-.75h.71V9.07Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M95.7,8.44h.88v2.48h.07A1.39,1.39,0,0,1,98,10.11a1.48,1.48,0,0,1,1.55,1.68V14.7h-.89V12c0-.72-.33-1.08-1-1.08a1.05,1.05,0,0,0-1.13,1.14V14.7H95.7Z"></path>
                                                    <path class="fter-apple-2"
                                                          d="M104.76,13.48a1.83,1.83,0,0,1-2,1.3,2,2,0,0,1-2.08-2.32,2.08,2.08,0,0,1,1.78-2.33l.29,0c1.25,0,2,.86,2,2.27v.31h-3.18v0A1.19,1.19,0,0,0,102.72,14h.11a1.08,1.08,0,0,0,1.07-.55ZM101.64,12h2.27a1.09,1.09,0,0,0-1-1.16h-.11A1.15,1.15,0,0,0,101.64,12s0,0,0,0Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-hrv-address">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 widget-address widget-address-hcm">
                    <p class="title-address">Văn phòng Hà Nội</p>
                    <ul class="list-unstyled list-address">
                        <li class="street-address">{{config('constant.APP_ADDRESS')}}
                        </li>
                        <li class="tel">Tổng đài hỗ trợ: <a class="fter-txtbold" href="tel:{{config('constant.APP_HOTLINE')}}">{{config('constant.APP_HOTLINE')}}</a>
                        </li>
                        {{--<li class="tel">Số hỗ trợ ngoài giờ: <a class="fter-txtbold" href="tel:0988.999.999">0988--}}
                        {{--999 999</a></li>--}}
                    </ul>
                </div>
                {{-- <div class="col-md-6 col-sm-6 col-xs-12 widget-address widget-address-hn">
                    <p class="title-address">Văn phòng Hồ Chí Minh</p>
                    <ul class="list-unstyled list-address">
                        <li class="street-address">Tầng 10 DreamPlex, 21 đường Nguyễn Trung Ngạn, Phường Bến Nghé, Quận
                            1, Tp. Hồ Chí Minh.
                        </li>
                        <li class="tel">Tổng đài hỗ trợ: <a class="fter-txtbold" href="tel:{{config('constant.APP_HOTLINE')}}">{{config('constant.APP_HOTLINE')}}</a>
                        </li>
                        <li class="tel">Số hỗ trợ ngoài giờ: <a class="fter-txtbold" href="tel:0989 888 888">0989
                        888 888</a></li>
                    </ul>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="footer-hrv-bottom">
        <div class="container">
            <ul class="list-unstyled">
                <li>Email: <a class="fter-txtbold" href="mailto:{{config('constant.APP_EMAIL_SUPPORT')}}"
                              target="_top">{{config('constant.APP_EMAIL_SUPPORT')}}</a>
                </li>
                <li>Hotline: <a class="fter-txtbold" href="tel:{{config('constant.APP_HOTLINE')}}">{{config('constant.APP_HOTLINE')}}</a></li>
            </ul>
        </div>
    </div>
</footer>
@include('layouts.frontend.elements.structures.footer_js')
@stack('scripts')
@include('layouts.frontend.elements.autoload.footer_autoload')

<!--====== SCRIPTS JS ======-->

</body>

</html>
