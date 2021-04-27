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
                <div class="form-group">
                    <div class="col-md-4">
                        {!! MyForm::label('partner_id', 'Đối tác vận tải', [], false) !!}
                        {!! MyForm::dropDown('partner_id',null, $partnerList, false ,
                        ['id'=> 'partner_id', 'data-default' => route('vehicle-group.getVehicleGroups')]) !!}
                    </div>
                </div>
                <div class="custom-dd dd" id="vehicle_group">
                    @include('backend.vehicle_group.item_list')
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