<script>
    let urlLocation = '{{route('location.combo-location')}}',
        locationDestinationId = '{{empty($entity->location_destination_id) ? 0 : $entity->location_destination_id}}',
        locationArrivalId = '{{empty($entity->location_arrival_id) ? 0 : $entity->location_arrival_id}}';

    let urlVehicle = '{{route('vehicle.combo-vehicle')}}',
        orderDropdownUri = '{{route('order.combo-order')}}',
        driverDropdownUri = '{{route('driver.combo-driver')}}',
        urlVehicleDriver = '{{route('driver.getVehicleDriver')}}',
        backendUri = '{{getBackendDomain()}}',
        costsUri = '{{route('quota.get-costs-by-quota')}}',
        quotaDropdownUri = '{{route('quota.combo-quota')}}',
        locationOrderUri = '{{route('route.combo-location')}}',
        calcCapacityUri = '{{ route('route.calcCapacity') }}';


    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [];
    var searchDriverExceptIds = JSON.parse('{{ $primaryDriverEntity['id'] == null ?'[]': '['.json_encode($primaryDriverEntity['id']).']' }}');
    var searchVehicleExceptIds = JSON.parse('{{ $vehicleEntity['id'] == null ?'[]': '['.json_encode($vehicleEntity['id']).']' }}');

    var searchOrderExceptIds = [];
    var obj = JSON.parse('{!! $orders == null ?'[]': json_encode($orders) !!}');
    for (var prop in obj) {
        searchOrderExceptIds.push(prop);
    }
    var searchQuotaExceptIds = JSON.parse('{{ $quotaEntity['id'] == null ?'[]': '['.json_encode($quotaEntity['id']).']' }}');

    @foreach($file_list as $file)
    existingFiles.push({
        name: '{{ $file->file_name }}',
        size: '{{ $file->size }}',
        type: '{{ $file->file_type }}',
        url: '{{ route('file.getImage', $file->file_id) }}',
        urlDownload: '{{ route('file.downloadFile', $file->file_id) }}',
        full_url: '{{ route('file.getImage', ['id' => $file->file_id, 'full' => true]) }}',
        id: '{{ $file->file_id }}'
    });
    @endforeach

</script>
<div class="row">
    <div class="col-12">
        <input type="hidden" id="route_id" value="{{$entity->id}}">
        {!! MyForm::model($entity, ['route' => ['route.valid', $entity->id]])!!}

        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#route_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{ trans('models.order.attributes.communication') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#route_file" data-toggle="tab" aria-expanded="false" class="nav-link">
                                {{trans('models.order.attributes.files_info')}}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="route_info">
                            {!! MyForm::hidden('locations', $locations, ['id'=>'locations']) !!}
                            {!! MyForm::hidden('costs', $costsJson, ['id'=>'costs']) !!}
                            <div class="content-body">
                                <div class="card-header" role="tab" id="headingInformation">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                           aria-controls="collapseInformation" class="collapse-expand">
                                            {{trans('models.order.attributes.information')}}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseInformation" class="collapse show" role="tabpanel"
                                     aria-labelledby="headingOne"
                                     style="">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                {!! MyForm::label('route_code', $entity->tA('route_code')  . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('route_code', $entity->route_code != null ? $entity->route_code : $code, ['placeholder'=>$entity->tA('route_code')]) !!}

                                            </div>
                                            <div class="col-md-6">
                                                {!! MyForm::label('name', $entity->tA('name')  . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                                            </div>
                                        </div>
                                        <div class="advanced form-group row">
                                            <div class="col-md-6">
                                                {!! MyForm::label('vehicle', $entity->tA('vehicle')  . ' <span class="text-danger">*</span>', [], false) !!}
                                                <div class="input-group select2-bootstrap-prepend">
                                                    <select class="select2 select-vehicle" id="vehicle_id"
                                                            name="vehicle_id">
                                                        <option value="{{$vehicleEntity['id']}}" selected="selected"
                                                                title="{{$vehicleEntity['reg_no']}}">{{$vehicleEntity['reg_no']}}</option>
                                                    </select>
                                                    <span class="input-group-addon vehicle-search" id="vehicle-search">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-truck"></i>
                                                    </div>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {!! MyForm::label('primary_driver', $entity->tA('primary_driver')  . ' <span class="text-danger">*</span>', [], false) !!}
                                                <div class="input-group select2-bootstrap-prepend">
                                                    <select class="select2 select-driver" id="driver_id"
                                                            name="driver_id">
                                                        <option value="{{$primaryDriverEntity['id']}}"
                                                                selected="selected"
                                                                title="{{$primaryDriverEntity['name']}}">{{$primaryDriverEntity['name']}}</option>
                                                    </select>
                                                    <span class="input-group-addon driver-search"
                                                          id="tprimary-driver-search">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-id-card"></i>
                                                    </div>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="advanced form-group row">
                                            <div class="col-md-6">
                                                <label for="order_ids">{{ $entity->tA('order')}}</label>
                                                <div class="input-group select2-bootstrap-prepend">
                                                    <select class="select2 select-order" id="order_id"
                                                            name="order_ids[]"
                                                            multiple='multiple' {{ empty($entity->id) ? 'disabled' : ''  }}>
                                                        @if($orders)
                                                            @foreach($orders as $key => $order_code)
                                                                <option value="{{$key}}" selected="selected"
                                                                        title="{{$order_code}}">{{$order_code}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <span class="input-group-addon  order-search" data-type="multiple">
                                                        <div class="input-group-text bg-transparent">
                                                            <i class="fa fa-barcode {{ empty($entity->id) ? 'pointer' : ''  }}"
                                                               id="order-search"
                                                            ></i>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 warning-message">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header" role="tab" id="headingRoute">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseRoute" aria-expanded="true"
                                           aria-controls="collapseInformation" class="collapse-expand">
                                            Thông tin lộ trình <i class="fa"></i>

                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseRoute" class="collapse show" role="tabpanel"
                                     aria-labelledby="headingOne"
                                     style="">
                                    <div class="card-body">
                                        <div class="advanced form-group row">
                                            <div class="col-md-2">
                                                <label>Lộ trình chuyến xe</label>
                                            </div>
                                        </div>
                                        <div class=" form-group row">
                                            <div class="timeline location">
                                                <article class="timeline-item">
                                                    <div class="timeline-desk">
                                                        <div class="panel">
                                                            <div class="panel-body">
                                                                <span class="arrow"></span>
                                                                <span class="timeline-icon"></span>
                                                                <div class="destination row">
                                                                    <div class="col-md-6">
                                                                        <label>Địa điểm nhận hàng</label>
                                                                        <div class="input-group with-button-add">
                                                                            <select class="select2 select-location"
                                                                                    name="destination_location_0"
                                                                                    id="destination_location_0">
                                                                            </select>
                                                                            <div class="input-group-append">
                                                                                <button class="btn btn-third quick-add"
                                                                                        type="button"
                                                                                        data-model="location"
                                                                                        data-url="{{route('location.advance')}}">
                                                                                    <i class="fa fa-plus"
                                                                                       aria-hidden="true"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>Thời gian nhận hàng</label>
                                                                        <div>
                                                                            <input name="destination_location_date_0"
                                                                                   id="destination_location_date_0"
                                                                                   type="text"
                                                                                   class="form-control datepicker"
                                                                                   placeholder="Ngày">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label>.</label>
                                                                        <div>
                                                                            <input name="destination_location_time_0"
                                                                                   id="destination_location_time_0"
                                                                                   type="text"
                                                                                   class="form-control timepicker"
                                                                                   placeholder="Giờ">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1 delete-timeline">
                                                                        <span class="delete-timeline-item">X</span>
                                                                    </div>
                                                                </div>
                                                                <hr/>
                                                                <div class="arrival row">
                                                                    <div class="col-md-6">
                                                                        <label>Địa điểm trả hàng</label>
                                                                        <div class="input-group with-button-add">
                                                                            <select class="select2 select-location"
                                                                                    name="arrival_location_0"
                                                                                    id="arrival_location_0">
                                                                            </select>
                                                                            <div class="input-group-append">
                                                                                <button class="btn btn-third quick-add"
                                                                                        type="button"
                                                                                        data-model="location"
                                                                                        data-url="{{route('location.advance')}}">
                                                                                    <i class="fa fa-plus"
                                                                                       aria-hidden="true"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>Thời gian trả hàng</label>
                                                                        <div>
                                                                            <input name="arrival_location_date_0"
                                                                                   id="arrival_location_date_0"
                                                                                   type="text"
                                                                                   class="form-control datepicker"
                                                                                   placeholder="Ngày">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label>.</label>
                                                                        <div>
                                                                            <input name="arrival_location_time_0"
                                                                                   id="arrival_location_time_0"
                                                                                   type="text"
                                                                                   class="form-control timepicker"
                                                                                   placeholder="Giờ">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                                <article class="timeline-item add-route">
                                                    <div class="add-plus text-left ">
                                                        <div class="time-show">
                                                <span class="fa-stack fa-lg">
                                                <i class="fa fa-circle fa-stack-2x"></i>
                                                <strong class="fa-stack-1x text-white">+</strong>
                                            </span>
                                                        </div>

                                                    </div>
                                                </article>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-5">
                                                {!! MyForm::label('location_destination_id', $entity->tA('location_destination_id'), [], false) !!}
                                                <select class="select2 select-location">
                                                    @if(isset($locationDestination))
                                                        <option value="{{$locationDestination['id']}}"
                                                                selected="selected"
                                                                title="{{$locationDestination['title']}}">{{$locationDestination['title']}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('ETD', $entity->tA('ETD'), [], false) !!}<br/>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        {!! MyForm::text('ETD_time', isset($entity->ETD_time) ? $entity->ETD_time : $ETD_time, ['id'=>'ETD_time', 'placeholder'=>$entity->tA('ETD_time'), 'class'=>'timepicker']) !!}
                                                    </div>
                                                    <div class="col-md-8">
                                                        {!! MyForm::text('ETD_date', isset($entity->ETD_date) ? $entity->ETD_date : $ETD_date, ['id'=>'ETD_date','placeholder'=>$entity->tA('ETD_date'), 'class'=>'datepicker']) !!}
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class=" form-group row">
                                            <div class="col-md-5">
                                                {!! MyForm::label('location_arrival_id', $entity->tA('location_arrival_id'), [], false) !!}
                                                <select class="select2 select-location">
                                                    @if(isset($locationArrival))
                                                        <option value="{{$locationArrival['id']}}"
                                                                selected="selected"
                                                                title="{{$locationArrival['title']}}">{{$locationArrival['title']}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('ETA', $entity->tA('ETA'), [], false) !!}<br/>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        {!! MyForm::text('ETA_time', isset($entity->ETA_time) ? $entity->ETA_time : $ETA_time, ['id'=>'ETA_time','placeholder'=>$entity->tA('ETA_time'),'class'=>'timepicker']) !!}
                                                    </div>
                                                    <div class="col-md-8">
                                                        {!! MyForm::text('ETA_date', isset($entity->ETA_date) ? $entity->ETA_date : $ETA_date, ['id'=>'ETA_date','placeholder'=>$entity->tA('ETA_date'), 'class'=>'datepicker']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @can('edit cost')
                                    <div class="card-header" role="tab" id="headingCost">
                                        <h5 class="mb-0 mt-0 font-16">
                                            <a data-toggle="collapse" href="#collapseCost" aria-expanded="true"
                                               aria-controls="collapseCost" class="collapse-expand">
                                                Thông tin chi phí
                                                <i class="fa"></i>

                                            </a>
                                        </h5>
                                    </div>
                                    <div id="collapseCost" class="collapse show" role="tabpanel"
                                         aria-labelledby="headingOne"
                                         style="">
                                        <div class="card-body">
                                            <div class="advanced form-group row">
                                                <div class="col-md-2">
                                                    <label>Bảng định mức chi phí</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <select class="select2 select-quota" id="quota_id"
                                                                name="quota_id">
                                                            <option value="{{$quotaEntity['id']}}" selected="selected"
                                                                    title="{{$quotaEntity['name']}}">{{$quotaEntity['name']}}</option>
                                                        </select>
                                                        <span class="input-group-addon route-quota quota-search"
                                                              id="quota-search">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-search"></i>
                                                    </div>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table class="table table-bordered table-hover table-cost">
                                                    <thead id="head_content">
                                                    <tr class="active">
                                                        <td style="font-size: 14px; font-weight: bold;">
                                                            Diễn giải
                                                        </td>
                                                        <td style="width: 200px; font-size: 14px; font-weight: bold;"
                                                            class="text-right">Số tiền (VND)
                                                        </td>
                                                    </tr>
                                                    <tbody id="body_content">
                                                    @if($costs)
                                                        @foreach($costs as $key => $cost)
                                                            <tr class="cost-item">
                                                                <td>
                                                <span class="receipt-payment" type="text" id="receipt-payment_0">
                                                    {{$cost['receipt_payment_name']}}
                                                </span>
                                                                    <input type="hidden"
                                                                           name="listCost[{{$key}}][receipt_payment_name]"
                                                                           value="{{$cost['receipt_payment_name']}}">
                                                                </td>
                                                                <td>
                                                                    <div class="pull-right">
                                                    <span class="currency" name="amount_0" type="text" id="amount_0">
                                                        {!! numberFormat($cost['amount_admin']) !!}
                                                    </span>
                                                                    </div>
                                                                    <input type="hidden"
                                                                           name="listCost[{{$key}}][receipt_payment_id]"
                                                                           value="{{$cost['receipt_payment_id']}}">
                                                                    <input type="hidden"
                                                                           name="listCost[{{$key}}][amount_admin]"
                                                                           value="{{$cost['amount_admin']}}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif

                                                    <tr class="d-none cost-default">
                                                        <td>
                                                    <span class="receipt-payment" type="text"
                                                          id="receipt-payment_0"></span>
                                                            <input type="hidden" class="rp-name rp-item"
                                                                   data-field="receipt_payment_name">
                                                        </td>
                                                        <td>
                                                            <div class="pull-right">
                                                        <span class="currency" name="amount_0" type="text"
                                                              id="amount_0"></span>
                                                            </div>
                                                            <input type="hidden" class="rp-id rp-item"
                                                                   data-field="receipt_payment_id">
                                                            <input type="hidden" class="rp-amount rp-item"
                                                                   data-field="amount_admin">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                            <div class="total row" style="margin-top: 8px">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-8 font-14">
                                                            <b>Tổng chi phí theo định mức</b>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="pull-right"><b
                                                                        id="total_cost_view">{{$total_cost ? numberFormat($total_cost) : 0}}</b>
                                                            </div>
                                                            {!! MyForm::hidden('total_cost',$total_cost ? $total_cost : 0, ['id'=>'total_cost']) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="tab-pane fade" id="route_file">
                            <div class="card-box">
                                <div class="form-group  row">
                                    <div class="col-md-12">
                                        <div class="dropzone-outer previewsContainer"></div>
                                        <div class="dropzone" id="route"></div>
                                        {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! MyForm::label('route_note', $entity->tA('route_note'), [], false) !!}
                                        {!! MyForm::text('route_note', $entity->route_note, ['placeholder'=>$entity->tA('note')]) !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-t-20"></div>
                        @include('layouts.backend.elements._submit_form_button')
                    </div>
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>

@include('layouts.backend.elements.search._vehicle_search')
@include('layouts.backend.elements.search._driver_search')
@include('layouts.backend.elements.search._order_search')
@include('layouts.backend.elements.search._quota_search')

{{--Cảnh báo không được Xóa--}}
<div class="modal fade" id="warning-delete" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cảnh báo<span></span></h4>
            </div>
            <div class="modal-body">
                Lộ trình chuyến xe luôn phải có điểm đầu và điểm kết thúc.<br>
                Bạn không được phép xóa!
            </div>
            <div class="modal-footer">
                <button id="close-warning-delete" type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
<script type="text/javascript"
        src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>

<?php
$searchJsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}
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
