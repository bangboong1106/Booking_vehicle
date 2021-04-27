<?php
$selectedDrivers = isset($entity->listDriver) ? $entity->listDriver : $entity->drivers;
?>
<script>
    let driverDropdownUri = '{{route('driver.combo-driver')}}';
    let vehicleGroupDropdownUri = '{{route('vehicle-group.combo-vehicle-group')}}';
    let backendUri = '{{getBackendDomain()}}';
    var searchDriverExceptIds = JSON.parse('{{ $selectedDrivers == null ? '[]' : json_encode($selectedDrivers) }}');
    var is_first_time = true;
    var is_create = @json(Request::is('*/create') ? true : false);
</script>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['vehicle.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#vehicle_main_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{trans('models.vehicle.attributes.information')}}
                            </a>
                        </li>
                        @if(!$vehicle_config_file_list->isEmpty())
                            <li class="nav-item">
                                <a href="#vehicle_file" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    {{trans('models.vehicle.attributes.files_info')}}
                                </a>
                            </li>
                        @endif
                        @if(!empty($vehicle_config_specification_list))
                            <li class="nav-item">
                                <a href="#vehicle_specification" data-toggle="tab" aria-expanded="false"
                                   class="nav-link">
                                    {{trans('models.vehicle.attributes.information_expand')}}
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content content-body">
                        <div class="tab-pane fade show active" id="vehicle_main_info">
                            {!! MyForm::hidden('id', isset($isDuplicate) ? '' : $entity->id) !!}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('reg_no', $entity->tA('reg_no'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {!! MyForm::text('reg_no', $entity->reg_no, ['placeholder'=>$entity->tA('reg_no')]) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::label('partner_id', $entity->tA('partner_id'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {!! MyForm::dropDown('partner_id', $entity->partner_id, $partnerList, true, [ 'class' => 'select2 minimal', 'id'=>'partner_id']) !!}

                                    @if(Request::is('*/edit') && $entity->partner_id !== null)
                                        <input type="hidden" name="partner_id" value={{$entity->partner_id}}>
                                    @endif
                                </div>
                            </div>
                            <div class="advanced form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('group_id', $entity->tA('group_id'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {{-- {!! MyForm::dropDown('group_id', $entity->group_id, $vehicleGroupList, true, [ 'class' => 'select2 minimal']) !!} --}}

                                    <select class="select2 select-vehicle-group with-border" id="group_id"
                                            name="group_id" {{empty($entity->group_id) && $entity->group_id != null  ? '' : 'disabled'}} style="border-right: 1px solid #cfcfcf">
                                            @if (isset($vehicleGroupList[$entity->group_id]))
                                                <option value="{{$entity->group_id}}" selected="selected"
                                                    title="{{$vehicleGroupList[$entity->group_id]}}">{{$vehicleGroupList[$entity->group_id]}}</option>
                                            @endif
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    {!! MyForm::label('group_id', $entity->tA('driver'). ' <span class="text-danger">*</span>', [], false) !!}
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-driver" id="listDriver[]"
                                                name="listDriver[]" multiple='multiple' {{empty($entity->drivers) ? '' : 'disabled'}}>
                                            @foreach($entity->drivers as $driver)
                                                <option value="{{$driver->id}}" selected="selected"
                                                        title="{{$driver->full_name}}">{{$driver->full_name}}</option>
                                            @endforeach
                                        </select>

                                        <span class="input-group-addon driver-search" data-type="multiple" data-all="1">
                                            <div class="input-group-text bg-transparent">
                                                    <i class="fa fa-id-card"></i>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('gps_company_id', $entity->tA('gps_company_id')) !!}
                                    {!! MyForm::dropDown('gps_company_id', $entity->gps_company_id, $gpsCompanyList, true, [ 'class' => 'select2 minimal']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::hidden('latitude', $entity->latitude ,['id'=>'latitude']) !!}
                                    {!! MyForm::hidden('longitude', $entity->longitude,['id'=>'longitude']) !!}
                                    {!! MyForm::label('current_location', $entity->tA('current_location'), [], false) !!}
                                    <div class="input-group group-address {{empty($entity->current_location) ? 'not-address' : ''}}">
                                        <input name="current_location" id="current_location" type="text"
                                               class="form-control"
                                               aria-label="Recipient's username" aria-describedby="basic-addon2"
                                               readonly="readonly" value="{{ $entity->current_location}}">
                                        <div class="input-group-append">
                                            <button id="input_location" type="button"
                                                    class="btn btn-primary waves-effect waves-light"
                                                    data-toggle="modal" data-target="#map_modal"><i
                                                        class="fa fa-map-marker"
                                                        title="Nhập vị trí"></i>
                                            </button>
                                            <button id="clear_location" class="btn btn-danger waves-effect waves-light"
                                                    type="button"><i class="fa fa-trash" title="Xóa"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @include('layouts.backend.elements._map')
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('status', $entity->tA('status')) !!}
                                    {!! MyForm::dropDown('status', $entity->status, $status_list, false, [ 'class' => ' minimal']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('type', $entity->tA('type')) !!}
                                    {!! MyForm::dropDown('type', $entity->type, $type_list, false, [ 'class' => ' minimal']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('active', $entity->tA('active')) !!}
                                    {!! MyForm::dropDown('active', $entity->active, $active_list, false, [ 'class' => ' minimal']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('length', trans('models.vehicle.attributes.length'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('length', numberFormat($entity->length),
                                        ['placeholder'=>$entity->tA('length'),'class' => 'number-input capacity']) !!}
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                       m
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('width', trans('models.vehicle.attributes.width'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('width', numberFormat($entity->width),
                                        ['placeholder'=>$entity->tA('width'),'class' => 'number-input capacity']) !!}
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                       m
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('height', trans('models.vehicle.attributes.height'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('height', numberFormat($entity->height),
                                        ['placeholder'=>$entity->tA('height'),'class' => 'number-input capacity']) !!}
                                        <div class="input-group-prepend">
                                            <span class="input-group-text form-group-right">m</span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('volume', trans('models.vehicle.attributes.volume'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('volume', numberFormat($entity->volume),
                                        ['placeholder'=>$entity->tA('volume'),'class' => 'number-input']) !!}
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                       m³
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('weight', trans('models.vehicle.attributes.weight'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('weight', numberFormat($entity->weight),
                                        ['placeholder'=>$entity->tA('weight'),'class' => 'number-input']) !!}
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                      kg
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('repair_distance', trans('models.vehicle.attributes.repair_distance'), [], false) !!}
                                    <div class="input-group">
                                        {!! MyForm::text('repair_distance', numberFormat($entity->repair_distance),
                                        ['placeholder'=>$entity->tA('length'),'class' => 'number-input']) !!}
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                       km
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('repair_date', trans('models.vehicle.attributes.repair_date'), [], false) !!}
                                    {!! MyForm::text('repair_date', $entity->repair_date, ['placeholder'=>$entity->tA('repair_date'), 'class' => 'datepicker date-input']) !!}
                                </div>
                            </div>
                            @include('backend.vehicle._general')
                        </div>
                        @include('backend.vehicle._file')
                        @include('backend.vehicle._specification')

                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
@include('layouts.backend.elements.search._driver_search')
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
<script>
    let currentLat = '{{ empty($entity->latitude) ? 0 : $entity->latitude }}',
        currentLng = '{{ empty($entity->longitude) ? 0 : $entity->longitude }}';

    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [];

    @foreach($vehicle_config_file_list as $vehicle_config)
    @foreach($vehicle_file_list[$vehicle_config->id] as $vehicle_file)
    @if(!empty($vehicle_file['file_id']))
    existingFiles.push({
        name: '{{ $vehicle_file['file_name']}}',
        size: '{{ $vehicle_file['size'] }}',
        type: 'image/jpeg',
        url: '{{ route('file.getImage',  $vehicle_file['file_id']) }}',
        urlDownload: '{{ route('file.downloadFile',$vehicle_file['file_id']) }}',
        full_url: '{{ route('file.getImage', ['id' =>  $vehicle_file['file_id'], 'full' => true]) }}',
        id: '{{  $vehicle_file['file_id'] }}',
        vehicle_config_file_id: '{{$vehicle_config->id}}'
    });
    @endif
    @endforeach
    @endforeach

</script>
<?php
$searchJsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}

