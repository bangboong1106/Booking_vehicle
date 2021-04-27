@extends('layouts.frontend.layouts.auth')
@section('content')
    {{MyForm::open(['route'=>'frontend.register'])}}
    <div class="form-group">
        {{MyForm::label('name')}}
        {{MyForm::text('name')}}
    </div>
    <div class="form-group">
        {{MyForm::label('email')}}
        {{MyForm::text('email')}}
    </div>
    <div class="form-group">
        {{MyForm::label('password')}}
        {{MyForm::password('password')}}
    </div>
    <div class="form-group">
        {{MyForm::label('password_confirmation')}}
        {{MyForm::password('password_confirmation')}}
    </div>
    <div class="form-row">
        <div class="col-md-4 mb-3">
            {{MyForm::label('day')}}
            {{MyForm::dropDown('dob', null, $dob)}}
        </div>
        <div class="col-md-4 mb-3">
            {{MyForm::label('month')}}
            {{MyForm::dropDown('mob', null, $mob)}}
        </div>
        <div class="col-md-4 mb-3">
            {{MyForm::label('year')}}
            {{MyForm::text('year')}}
        </div>
    </div>
    <div class="form-group text-right m-t-20">
        <div class="col-xs-12">
            <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">Register</button>
        </div>
    </div>
    {!! MyForm::close() !!}
@endsection