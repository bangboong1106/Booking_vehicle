@extends('layouts.backend.layouts.auth')
@section('content')
    @if ($errors->has('email'))
        <div id="error_msg">
            <div class="alert alert-danger">
                <ul>
                    <li>{{ $errors->first('email') }}</li>
                </ul>
            </div>
        </div>
        </span>
    @endif
    @if (session('status'))
        <div id="success_msg">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    <img class="logo-login" src="{{asset('css/backend/images/logo.png')}}" alt="xxx">
    <div class="form-group row">
        <div class="col-12">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <form class="form-horizontal" role="form" method="POST" id="get-email-form" style="padding-top: 40px;"
                      action="{{ route('backend.password.email') }}">
                    {{ csrf_field() }}
                                        <span class=" control-label">Địa chỉ email</span>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="input-group {{ $errors->has('email') ? ' border-error-input' : 'border-primary-input' }}">
                                <div class="input-group-prepend">
                                    <div class="" style="width: 40px">
                                        <div class="icon-mail-reset"></div>
                                    </div>
                                </div>
                                <input class="form-control" type="email" id="email" name="email"
                                       placeholder="Nhập địa chỉ email" autofocus
                                       value="{{old('email')}}">
                            </div>
                            <span id="email_error" style="color: red; font-size: 10px; margin-top: 5px; "></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button id="btn-get-email" class="btn btn-primary btn-custom w-md waves-effect waves-light"
                                    type="submit">Reset</button>
                        </div>
                    </div>
                    <div class="form-group pull pull-left">
                        <a class="" href="{{ route('backend.login') }}">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>

    </script>
@endsection