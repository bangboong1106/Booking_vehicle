@extends('layouts.backend.layouts.main')
@section('content')
<form action="">
    <input type="hidden" name="scroll_top">
</form>

<div class="container">
    <div class="col-md-12 p-0">
        <div class="row d-flex justify-content-center mt-2">
            <h5 class="text-center">{{ trans('models.get_started.header_page') }}</h5>
        </div>
        <div class="row">
            <div class="timeline timeline-single-column">
                <div class="timeline-item">
                    <div class="timeline-point d-none d-sm-block">
                        1
                    </div>
                    <div class="timeline-event">
                        <div class="timeline-body">
                            <div class="row">
                                <div class="col-8 col-md-9 float-left">
                                    <div class="timeline-heading">
                                        <h4>{{ trans('models.get_started.attributes.driver.create_driver') }}</h4>
                                    </div>
                                    <p>{{ trans('models.get_started.attributes.driver.text') }}</p>
                                    @include('backend.get_started._btn', [
                                        'import' => 'driver.index',
                                        'create' => 'driver.create',
                                    ])
                                </div>
                                <div class="col-4 col-md-3 float-left mt-2 mb-2 mt-md-0 mb-md-0">
                                    @include('backend.get_started._video',['url'=>'https://www.youtube.com/embed/UiJKrQPo9SY?playsinline=1&autoplay=1'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-point d-none d-sm-block">
                        2
                    </div>
                    <div class="timeline-event">
                        <div class="timeline-body">
                            <div class="row">
                                <div class="col-8 col-md-9 float-left">
                                    <div class="timeline-heading">
                                        <h4>{{ trans('models.get_started.attributes.vehicle.create_vehicle') }}</h4>
                                    </div>
                                    <p>{{ trans('models.get_started.attributes.vehicle.text') }}</p>
                                    @include('backend.get_started._btn', [
                                        'import' => 'vehicle.index',
                                        'create' => 'vehicle.create',
                                    ])
                                </div>
                                <div class="col-4 col-md-3 float-left mt-2 mb-2 mt-md-0 mb-md-0">
                                    @include('backend.get_started._video',['url'=>'https://www.youtube.com/embed/L7Y9DgnkBEc?autoplay=1&playsinline=1'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-point d-none d-sm-block">
                        3
                    </div>
                    <div class="timeline-event">
                        <div class="timeline-body">
                            <div class="row">
                                <div class="col-8 col-md-9 float-left">
                                    <div class="timeline-heading">
                                        <h4>{{ trans('models.get_started.attributes.order.create_order') }}</h4>
                                    </div>
                                    <p>{{ trans('models.get_started.attributes.order.text') }}</p>
                                    @include('backend.get_started._btn', [
                                        'import' => 'order.index',
                                        'create' => 'order.create',
                                    ])
                                </div>
                                <div class="col-4 col-md-3 float-left mt-2 mb-2 mt-md-0 mb-md-0">
                                    @include('backend.get_started._video',['url'=>'https://www.youtube.com/embed/CCMCvTUs_pc?autoplay=1&playsinline=1'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item" id="last-item">
                    <div class="timeline-point d-none d-sm-block">
                        4
                    </div>
                    <div class="timeline-event">
                        <div class="timeline-heading">
                            <h4>{{ trans('models.get_started.attributes.app.install_app') }}</h4>
                        </div>
                        <div class="timeline-body">
                            <div class="row">
                                <div class="d-none d-lg-block col-lg-3">
                                    <img src="{{asset('/css/backend/images/desktop_mockup.png')}}" width="100%"/>
                                </div>
                                <div class="col-12 col-lg-9">
                                    <p>Bộ ứng dụng hiện đã có trên 2 hệ điều hành: Android và IOS.</p>
                                    <p>Hãy quét mã QR bên dưới để tải ngay.</p>
                                </div>
                            </div>

                            <hr/>

                            <div class="row">
                                <div class="col-lg-3 d-none d-lg-block">
                                    <img src="{{asset('/css/backend/images/iphone.png')}}" width="100%"/>
                                </div>
                                <div class="col-12 col-lg-9">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <p class="text-center text-lg-left">Ứng dụng cho điện thoại, máy tính bảng cho từng bộ phận.</p>
                                        </div>
                                    </div>
                                    <div class="row pl-2 justify-content-md-center justify-content-lg-start">
                                        @foreach($orders as $order)
                                            @foreach($entities as $type => $item)
                                                @if ($item->id == $order)
                                                    @include('backend.get_started._qr_code_store', [
                                                        'title' => $item->name,
                                                        'id_app' => $item->id,
                                                        'play_store_url' => config('constant.PLAY_STORE_URL'). $item->play_store_id,
                                                        'app_store_url' => config('constant.APP_STORE_URL'). $item->app_store_id,
                                                    ])
                                                    @break
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                    <div class="row justify-content-md-center justify-content-lg-start">
                                        <div class="col-md-8 col-12">
                                            @include('backend.get_started._form')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.get_started._modal')

@endsection