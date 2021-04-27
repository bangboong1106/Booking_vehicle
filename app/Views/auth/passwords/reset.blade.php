@extends('layouts.backend.layouts.auth')
@section('content')
    @if (session('status'))
        <div id="success_msg">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @include('layouts.backend.elements.messages')
    <img class="logo-login" src="{{asset('css/backend/images/logo.png')}}" alt="xxx">
    <div class="form-group row">
        <div class="col-12">
            <div class="form-group">
                <form class="form-horizontal" role="form" method="POST" id="reset-form"
                      action="{{ route('backend.password.request') }}">
                    {{ csrf_field() }}

                    <input hidden="hidden" name="token" value="{{ $token }}"/>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <span class="control-label">Địa chỉ email</span>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="input-group border-primary-input">
                                    <div class="input-group-prepend">
                                        <div class="" style="width: 40px">
                                            <div class="icon-mail-reset"></div>
                                        </div>
                                    </div>
                                    <input class="form-control" id="email" name="email"
                                           placeholder="Nhập địa chỉ email"
                                           value="{{ $email }}" readonly>
                                </div>
                                <span id="email_error" style="color: red; font-size: 10px; margin-top: 5px; "></span>
                            </div>
                        </div>

                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <span class="control-label">Mật khẩu mới</span>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="input-group border-primary-input">
                                    <div class="input-group-prepend">
                                        <div class="" style="width: 40px; color: black">
                                            <div class="cls-icon-password icon-password"></div>
                                        </div>
                                    </div>
                                    <input class="form-control password" type="password" name="password"
                                           placeholder="Nhập mật khẩu" autocomplete="off" autofocus
                                           id="password">
                                    <span toggle="#password-field" id="password-field"
                                          class=" hide-password toggle-password"></span>
                                </div>
                                <span id="password_error" style="color: red; font-size: 10px; margin-top: 5px; "></span>

                            </div>
                        </div>

                    </div>
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <span class="control-label">Xác nhận mật khẩu mới</span>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="input-group border-primary-input">
                                    <div class="input-group-prepend">
                                        <div class="" style="width: 40px; color: black">
                                            <div class="cls-icon-password icon-password"></div>
                                        </div>
                                    </div>
                                    <input class="form-control password" type="password" name="password_confirmation"
                                           placeholder="Nhập mật khẩu mới"
                                           id="password_confirmation">
                                    <span toggle="#password-field" id="password_confirmation-field"
                                          class=" hide-password toggle-password"></span>
                                </div>
                                <span id="password_confirmation_error"
                                      style="color: red; font-size: 10px; margin-top: 5px; "></span>

                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button id="btn-reset-password"
                                    class="btn btn-primary btn-custom w-md waves-effect waves-light"
                                    type="submit">Xác nhận
                            </button>
                        </div>
                    </div>
                    <div class="form-group pull pull-center">
                        <a class="" href="{{ route('backend.login') }}">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        //Hien the span
        keyupLabel();

        function displayLabel(param) {
            param.addEventListener('keyup', function () {
                let lable = param.parentNode.parentNode.parentNode.parentNode.childNodes[1];
                if (lable != null) {
                    lable.style.display = 'block';
                }
            })
        }

        function hideLabel(param) {
            let lable = param.parentNode.parentNode.parentNode.childNodes[1];
            if (lable != null) {
                lable.style.display = 'none';
            }
        }

        function keyupLabel() {
            let password = document.getElementById('password');
            let password_confirm = document.getElementById('password_confirmation');
            let email = document.getElementById('email');
            if (email != null) {
                displayLabel(email);
            }
            if (password != null) {
                displayLabel(password);
            }
            if (password_confirm != null) {
                displayLabel(password_confirm);
            }

        }
    </script>
@endsection
