@extends('layouts.backend.layouts.main')
@push('after-css')
    <?php $cssFiles = [
        'nestable',
    ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
@endpush
@section('content')
    @php
        $deleteRoute = isset($deleteRoute) ? $deleteRoute : $routePrefix.'.destroy';
    @endphp
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs tabs-bordered">
                <li class="nav-item">
                    <a href="#payment" data-toggle="tab" aria-expanded="true" class="nav-link active">
                        Danh mục chi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#receipt" data-toggle="tab" aria-expanded="false" class="nav-link">
                        Danh mục thu
                    </a>
                </li>
                <a style=" position: absolute;top: 3px; right: 12px;" target="_blank"
                   href="{{getenv('HELP_DOMAIN','').trans('helps.'.$routeName)}}" data-toggle="tooltip"
                   data-placement="top" title="" data-original-title="{{trans('actions.help')}}">
                    <i class="fa fa-question-circle"></i>
                    {{--                    <img src="{{public_url('css/backend/img/help.png')}}" alt=""/>--}}
                </a>
            </ul>
            <div class="tab-content">
                <div id="payment" class="tab-pane fade show active">
                    <div class="card-box list-ajax">
                        <div class="form-inline m-b-20 justify-content-between">
                            <div class="row">
                                @php
                                    $create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
                                    $create_label = isset($create_label) ? $create_label : transb($routePrefix.'.create');
                                @endphp
                                <div class="btn-group flex-wrap" role="group" aria-label="Basic example">
                                    <a class="btn l-create" href="{{backUrl($create_route, array('type=2'))}}" >
                                        <i class="fa fa-plus" style="margin-right: 8px"></i>Thêm mới danh mục chi
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dd dd" id="payment_list" data-type = 2>
                            <ol class="dd-list">
                                @foreach($payment_categories as $category)
                                <li class="dd-item" data-id={{$category->id}}>
                                        <div class="dd-handle">
                                            @if($category->is_system == 0)
                                                <a href="{{backUrl('receipt-payment.edit', $category->id)}}">{{ $category->name }}</a>
                                            @else
                                                {{$category->name }}
                                            @endif
                                            <span>
                                                @if($category->is_display_driver == "1")
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa"></i>
                                                @endif
                                            </span>
                                            <div class="list-tag-column" style="display: inline">
                                                @foreach(explode('|', $category->amount) as $amount_item)
                                                    <span class="grid-tag">{{numberFormat($amount_item)}}</span>
                                                @endforeach
                                            @if($category->is_system == 0 && auth()->user()->can('delete receipt_payment'))
                                                <a class="delete-action" href="#del-confirm"
                                                   style="display:inline-block"
                                                   data-toggle="modal"
                                                   data-action="{{backUrl($deleteRoute,$category->id)}}">
                                                    <i class="fa fa-trash" aria-hidden="true"
                                                       title="{{trans('actions.destroy')}}"></i>
                                                </a>
                                            @endif
                                        </div>
                                        @if(count($category->children()->get()))
                                            @include('backend.receipt_payment.manageChild',['children' => $category->children()->get()])
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
                <div id="receipt" class="tab-pane fade show">
                    <div class="card-box list-ajax">
                        <div class="form-inline m-b-20 justify-content-between">
                            <div class="row">
                                @php
                                    $create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
                                    $create_label = isset($create_label) ? $create_label : transb($routePrefix.'.create');
                                @endphp
                                <div class="btn-group flex-wrap" role="group" aria-label="Basic example">
                                    <a class="btn l-create" href="{{backUrl($create_route, array('type=1'))}}">
                                        <i class="fa fa-plus"></i>&nbsp;Thêm mới danh mục thu
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dd dd" id="receipt_list" data-type = 1>
                            <ol class="dd-list">
                                @foreach($receipt_categories as $category)
                                    <li class="dd-item" data-id={{$category->id}}>
                                        <div class="dd-handle">
                                            @if($category->is_system == 0)
                                                <a href="{{backUrl('receipt-payment.edit', $category->id)}}">{{ $category->name }}</a>
                                            @else
                                                {{$category->name }}
                                            @endif
                                            <span>
                                                @if($category->is_display_driver == "1")
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa"></i>
                                                @endif
                                            </span>
                                            @if($category->is_system == 0)
                                                <a class="delete-action" href="#del-confirm"
                                                   style="display:inline-block"
                                                   data-toggle="modal"
                                                   data-action="{{backUrl($deleteRoute,$category->id)}}"><i
                                                            class="fa fa-trash" aria-hidden="true"
                                                            title="{{trans('actions.destroy')}}"></i></a>
                                            @endif
                                        </div>
                                        @if(count($category->children()->get()))
                                            @include('backend.receipt_payment.manageChild',['children' => $category->children()->get()])
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let saveOrderUrl = '{{route('receipt-payment.order')}}';
        </script>
    </div>
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/jquery.nestable'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush
