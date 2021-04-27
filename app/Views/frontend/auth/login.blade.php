@extends('layouts.frontend.layouts.auth')
@section('content')
    {{MyForm::open(['route'=>'frontend.login'])}}
    @include('layouts.frontend.elements.messages')
    <div class="form-group row">
        <div class="col-12">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></div>
                </div>
                <input class="form-control" type="text" required="" id="username" name="username" placeholder="Username"
                       value="{{Request::old('username')}}">
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-bullseye" aria-hidden="true"></i></div>
                </div>
                <input class="form-control" type="password" name="password" required="" placeholder="Password">
            </div>
        </div>
    </div>
    <div class="form-group text-right m-t-20">
        <div class="col-xs-12">
            <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">Log In
            </button>
        </div>
    </div>
    {!! MyForm::hidden('return_url') !!}
    {!! MyForm::close() !!}
@stop