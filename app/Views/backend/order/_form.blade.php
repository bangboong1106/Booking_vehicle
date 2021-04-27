@php
    $today = (new DateTime());
    $tomorrow = new DateTime('tomorrow');
    $isETAHide = true;
    $isETDHide = true;
    if($entity->status == config("constant.DANG_VAN_CHUYEN") ||
        $entity->status == config("constant.HOAN_THANH") ){
        $isETDHide = false;
    }
    if($entity->status == config("constant.HOAN_THANH") ){
        $isETAHide = false;
    }
@endphp

<script>
    var primaryDriverExceptIds = JSON.parse('{{  $primaryDriver == null ?'[]': '['.json_encode( $primaryDriver->id).']' }}');
    var secondaryDriverExceptIds = JSON.parse('{{ $secondaryDriver == null ?'[]': '['.json_encode($secondaryDriver->id).']' }}');
    var searchVehicleExceptIds = JSON.parse('{{ empty($vehicle) ?'[]': '['.json_encode($vehicle->id).']' }}');
    var searchRouteExceptIds = JSON.parse('{{ empty($currentRoute) ?'[]': '['.json_encode($currentRoute->id).']' }}');

</script>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, [
            'route' => [empty($formAdvance) ? 'order.valid' : 'order.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation,
            'autocomplete' => 'off',
            'class' => 'no-convert',
        ])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#order_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{ trans('models.order.attributes.communication') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#order_file" data-toggle="tab" aria-expanded="false" class="nav-link">
                                {{trans('models.order.attributes.files_info')}}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="order_info">
                            @include('backend.order._order_info')
                            @include('backend.order._ship_info')
                            @include('backend.order._driver_goods_info')
                        </div>
                        <div class="tab-pane fade" id="order_file">
                            @foreach($order_status_file_list as $order_status)
                                <div class="card-box">
                                    <h6 class="m-t-0 m-b-10 header-title">{!! $order_status['name'] !!}</h6>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <div class="dropzone-outer previewsContainer_{{$order_status['id']}} ">
                                            </div>
                                            <div class="dropzone"
                                                 id={{$order_status['id']}} data-id="{{$order_status['id']}}"
                                                 data-file_type=""></div>
                                            {!! MyForm::hidden('order_file['.$order_status['id'].'][file_id]',
                                           $order_file_list[$order_status['id']] == null?"":   $order_file_list[$order_status['id']]->pluck('file_id')->implode(';'),
                                            ['id' => $order_status['id'].'_file_id']) !!}
                                        </div>
                                        @if($order_status['id']!= config("constant.KHOI_TAO"))
                                            <div class="form-group col-md-12">
                                                {!! MyForm::label(trans('models.order.attributes.reason')) !!}
                                                {!! MyForm::text('order_file['.$order_status['id'].'][reason]',
                                                $order_file_list[$order_status['id']] == null?"":  $order_file_list[$order_status['id']]->first()['reason'],
                                                ['placeholder'=>$entity->tA('reason')]) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="m-t-20"></div>
                        @include('layouts.backend.elements._submit_form_button')
                    </div>
                </div>
            </div>
            {!! MyForm::close() !!}
        </div>
    </div>
</div>
@include('layouts.backend.elements.search._vehicle_search', ['location' => true])
@include('layouts.backend.elements.search._contact_search')
@include('layouts.backend.elements.search._routes_search')
@include('layouts.backend.elements.search._quota_search')
@include('layouts.backend.elements.search._goods_search')
@include('layouts.backend.elements.search._location_search')

@include('layouts.backend.elements.search._driver_search',
 ['modal' => 'primary_driver_modal',
 'table'=>'table_primary_drivers',
 'button'=> 'btn-primary-driver'])

@include('layouts.backend.elements.search._driver_search',
 ['modal' => 'secondary_driver_modal',
 'table'=>'table_secondary_drivers',
 'button'=> 'btn-secondary-driver'])

@if(empty($formAdvance))
    @include('layouts.backend.elements._map')
@endif

@include('backend.order._default_data')

<?php
$jsFiles = [
    'autoload/object-select2',
    'autoload/customer',
    'vendor/lib/locationObject'
];
?>
{!! loadFiles($jsFiles, $area, 'js') !!}

<script>
            @if(empty($formAdvance))
    var urlLocation = '{{route('location.combo-location')}}',
        locationDestinationId = '{{empty($entity->location_destination_id) ? 0 : $entity->location_destination_id}}',
        locationArrivalId = '{{empty($entity->location_arrival_id) ? 0 : $entity->location_arrival_id}}',
        driverDropdownUri = '{{route('driver.combo-driver')}}',
        backendUri = '{{getBackendDomain()}}',
        urlVehicle = '{{route('vehicle.combo-vehicle')}}',
        urlVehicleDriver = '{{route('driver.getVehicleDriver')}}',
        urlCodeConfig = '{{route('system-code-config.getCodeConfig')}}',
        urlCode = '{{route('system-code.getCode')}}',
        routeDropdownUri = '{{route('route.combo-route')}}',
        quotaDropdownUri = '{{route('quota.combo-quota')}}',
        comboCustomerUri = '{{route('customer.combo-customer')}}';
        urlDefaultData = '{{route('customer-default-data.default')}}';
            @endif

    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [];

    var urlSuggestion = '{{ route('order.suggestionLocation') }}',
        urlCalcCapacity = '{{ route('route.calcCapacity') }}';

    @foreach($order_status_file_list as $order_status)
    @if($order_file_list[$order_status['id']] != null)
    @foreach($order_file_list[$order_status['id']] as $order_file)
    @if(isset($order_file['file_id']) && !empty($order_file['file_id']))
    existingFiles.push({
        name: '{{ $order_file['file_name'] }}',
        size: '{{ $order_file['size'] }}',
        type: 'image/jpeg',
        url: '{{ route('file.getImage',  $order_file['file_id']) }}',
        urlDownload: '{{ route('file.downloadFile',$order_file['file_id']) }}',
        full_url: '{{ route('file.getImage', ['id' =>  $order_file['file_id'], 'full' => true]) }}',
        id: '{{  $order_file['file_id'] }}',
        order_status_id: '{{$order_status['id']}}'
    });
    @endif
    @endforeach
    @endif
    @endforeach
</script>
<div class="modal" id="preview-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4>&nbsp;</h4>
            </div>
            <div class="modal-body">
                <img src="" id="preview" width="100%">
            </div>
        </div>
    </div>
</div>