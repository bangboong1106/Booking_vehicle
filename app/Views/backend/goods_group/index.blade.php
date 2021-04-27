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
            <div class="card-box list-ajax">
                @include('layouts.backend.elements._index_head')
                <div class="custom-dd dd" id="vehicle_group">
                    <ol class="dd-list">
                        @foreach($categories as $category)
                            <li class="dd-item no-drag">
                                <div class="dd-handle no-drag">
                                    <a href="{{backUrl('goods-group.edit', $category->id)}}">{{ $category->name }}</a>
                                    @can('delete vehicle_group')
                                    <a class="delete-action" href="#del-confirm"
                                       style="display:inline-block"
                                       data-toggle="modal"
                                       data-action="{{backUrl($deleteRoute,$category->id)}}">
                                        <i class="fa fa-trash" aria-hidden="true"
                                           title="{{trans('actions.destroy')}}"></i></a>
                                    @endcan
                                </div>
                                @if(count($category->children()->get()))
                                    @include('backend.vehicle_group.manageChild',['children' => $category->children()->get()])
                                @endif

                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
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