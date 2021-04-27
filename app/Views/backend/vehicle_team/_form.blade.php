<script>
    var driverDropdownUri = '{{route('driver.combo-driver')}}',
        backendUri = '{{getBackendDomain()}}';

    var searchDriverExceptIds = JSON.parse('{{ json_encode($entity->drivers->map(function($da) {
        return $da->id;
    })) }}');
    var primaryDriverExceptIds = JSON.parse('[{{ $entity->capital_driver_id }}]');
</script>
<div class="row">
    <div class="col-12 offset-3">
        {!! MyForm::model($entity, ['route' => ['vehicle-team.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-6">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="form-group">
                        {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('name', $entity->tA('name') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('partner_id', $entity->tA('partner_id'). ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::dropDown('partner_id', $entity->partner_id, $partnerList, true, [ 'class' => 'select2 minimal', 'id'=>'partner_id']) !!}
                    </div>
                    <div class="form-group">
                        <div class="advanced form-group row">
                            <div class="col-md-12">
                                {!! MyForm::label('capital_driver_id', $entity->tA('capital_driver_id')) !!}
                                <div class="input-group select2-bootstrap-prepend">
                                    <select class="select2 select-driver capital-driver" id="capital_driver_id"
                                            name="capital_driver_id">
                                        @if($entity->capital_driver_id !== 0)
                                            <option value="{{$entity->capital_driver_id}}" selected="selected"
                                                    title="{{$entity->tryGet('capital_driver')->full_name}}">{{$entity->tryGet('capital_driver')->full_name}}</option>
                                        @endif
                                    </select>
                                    <span class="input-group-addon" id="primary-driver-search-wrap" data-all="1">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search" id="primary-driver-search"></i>
                                        </div>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="advanced form-group row">
                            <div class="col-md-12">
                                {!! MyForm::label('driver_ids', $entity->tA('driver_ids')) !!}
                                <div class="input-group select2-bootstrap-prepend">
                                    <select class="select2 select-driver" id="driver_ids[]"
                                            name="driver_ids[]" multiple='multiple'>
                                        @foreach($entity->drivers as $driver)
                                            <option value="{{$driver->id}}" selected="selected"
                                                    title="{{$driver->full_name}}">{{$driver->full_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-addon driver-search"
                                          id="team-driver-search" data-type="multiple" data-all="1">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-id-card"></i>
                                                    </div>
                                                </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
<?php
$jsFiles = [
    'autoload/object-select2'
];
?>
{!! loadFiles($jsFiles, $area, 'js') !!}
@include('layouts.backend.elements.search._driver_search',
 ['modal' => 'primary_driver_modal',
 'table'=>'table_primary_drivers',
 'button'=> 'btn-primary-driver'])
@include('layouts.backend.elements.search._driver_search')

