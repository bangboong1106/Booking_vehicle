@extends('layouts.backend.layouts.auth')
@section('content')
    <div class="form-group row">
        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('backend.password.request') }}">
                {{ csrf_field() }}

                <input hidden="hidden" name="token" value="{{ $token }}"/>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <span class="control-label">Địa chỉ email</span>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="input-group border-primary-input">
                                <div class="input-group-prepend">
                                    <div class="" style="width: 30px">
                                        <div class="icon-mail-reset"></div>
                                    </div>
                                </div>
                                <input class="form-control" type="email" id="email" name="email" placeholder="Email"
                                       value="{{ $email or old('email')}}">
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <span class="control-label">Mật khẩu</span>
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
                    @if ($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label for="password_confirmation" class="col-md-8 control-label">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-bullseye" aria-hidden="true"></i></div>
                        </div>
                        <input id="password_confirmation" type="password" class="form-control"
                               name="password_confirmation" required>

                    </div>
                    @if ($errors->has('password_confirmation'))
                        <p class="help-block">
                            {{ $errors->first('password_confirmation') }}
                        </p>
                    @endif
                </div>
                <div class="reset-password-optileon">
                    <div class="form-group pull-left">
                        <a class="" href="{{ route('backend.login') }}">Đăng nhập</a>
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-primary btn-custom btn-right" type="submit">Xác nhận
                        </button>
                    </div>
                </div>
            </form>
        </div>
@endsection
