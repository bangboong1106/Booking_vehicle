@extends('layouts.backend.layouts.auth')
<title>Đăng nhập - {{config('constant.APP_NAME')}} </title>
@section('content')
    <div class="container-logo-login">
        <img class="logo-login" src="{{asset('css/backend/images/' . $logo)}}" alt="xxx">
    </div>
    {{MyForm::open(['route'=>'backend.login', 'id' => 'login-form'])}}
    @include('layouts.backend.elements.messages')
    @if (session('status'))
        <div id="success_msg">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-group row">
        <div class="col-12">
            <div class="input-group border-primary-input">
                <div class="input-group-prepend">
                    <div class="" style="width: 40px">
                        <div class="cls-icon-user icon-user"></div>
                    </div>
                </div>
                <input class="form-control" type="text" id="username" name="username" placeholder="Nhập tên đăng nhập"
                       autofocus
                       value="{{Request::old('username')}}">
            </div>
            <span id="username_error" style="color: red; font-size: 10px; margin-top: 5px; "></span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <div class="input-group border-primary-input">
                <div class="input-group-prepend">
                    <div class="" style="width: 40px; color: black">
                        <div class="cls-icon-password icon-password"></div>
                    </div>
                </div>
                <input class="form-control password" type="password" name="password" placeholder="Nhập mật khẩu"
                       id="password">
                <span toggle="#password-field" id="password-field" class=" hide-password toggle-password"></span>
            </div>
            <span id="password_error" style="color: red; font-size: 10px; margin-top: 5px; "></span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <button id="login-submit" class="btn btn-primary btn-custom w-md waves-effect waves-light"
                    type="submit">{{trans('auth.login')}}</button>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <a id="back-main-page" class="btn btn-custom w-md waves-effect waves-light text-decoration-none"
                    href="{{ route('home') }}" style="padding: 0.375rem 0">Quay lại trang chủ</a>
        </div>
    </div>
    <div class="form-group pull pull-left login-full-width">
        <a class="login-float-left" href="{{ route('backend.password.request') }}">Quên mật khẩu?</a>
    </div>
    {!! MyForm::hidden('return_url', isset($returnUrl) ? $returnUrl : null) !!}
    {!! MyForm::close() !!}
    <div class="lable-time">
        <b>Thời gian làm việc</b><br>
        Thứ 2 - Chủ nhật từ 8:00 đến 22:00
    </div>
@stop
<script>

</script>
