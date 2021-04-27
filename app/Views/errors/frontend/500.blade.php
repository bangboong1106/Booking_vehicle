@extends('layouts.frontend.layouts.error')
@section('content')
    <div class="alert alert-danger alert-dismissible" role="alert">
        <!-- flash message -->
        <div>{{trans('messages.failed')}}</div>
    </div>
@endsection