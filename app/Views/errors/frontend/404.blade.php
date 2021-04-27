@extends('layouts.frontend.layouts.error')
@section('content')
    <div class="alert alert-danger alert-dismissible" role="alert">
        <!-- flash message -->
        <div>{{trans('messages.url_not_found')}}</div>
    </div>
@endsection