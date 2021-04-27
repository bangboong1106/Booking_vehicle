<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, [
        'route' => [empty($formAdvance) ? 'customer-default-data.valid' : 'customer-default-data.advance', $entity->id],
        'validation' => empty($validation) ? null : $validation,
        'autocomplete' => 'off',
        'class' => 'no-convert',
        ]) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="order_info">
                            <div class="card-header" role="tab" id="headingInformation">
                                <h5 class="mb-0 mt-0 font-20">
                                    <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                       aria-controls="collapseInformation" class="collapse-expand">
                                        {{ trans('models.order.attributes.information') }}
                                        <i class="fa"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseInformation" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne" style="">
                                <div class="card-body m-l-24">
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('code', $entity->tA('code'). '<span class="text-danger">*</span>', [], false) !!}
                                            {!! MyForm::text('code', $entity->id != null ? $entity->code : $code, ['placeholder'=>$entity->tA('code')]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('customer_id', $entity->tA('customer_id'). '<span class="text-danger">*</span>', [], false) !!}
                                            @if(Request::is('*/edit'))
                                                <input type="hidden" value="{{$entity->customer_id}}" name="customer_id"
                                                       id="customer_id_hidden">
                                            @endif
                                            <div class="input-group with-button-add">
                                                <select class="select2 select-customer form-control" id="customer_id"
                                                        name="customer_id">
                                                    @if (isset($entity->customer))
                                                        <option value="{{ $entity->customer->id }}" selected="selected"
                                                                title="{{ $entity->customer->full_name }}">
                                                            {{ $entity->customer->full_name }}</option>
                                                    @endif
                                                </select>

                                                @if(empty($formAdvance) && auth()->user()->can('add customer'))
                                                    <div class="input-group-append">
                                                        <button class="btn btn-third quick-add {{$entity->id !== null ? 'disable-btn' : ''}}"
                                                                type="button"
                                                                data-model="customer"
                                                                data-url="{{ route('customer.advance') }}"
                                                                data-callback="addCustomerComplete" {{$entity->id !== null ? 'disabled' : ''}}>
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('client_id', $entity->tA('client_id'). '<span class="text-danger">*</span>', [], false) !!}
                                            <select class="select2 select-client form-control" id="client_id"
                                                    name="client_id" {{$entity->id == null ? 'disabled' : ''}}>

                                                @if (isset($entity->client))
                                                    <option value="{{ $entity->client->id }}" selected="selected"
                                                            title="{{ $entity->client->full_name }}">
                                                        {{ $entity->client->full_name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('location_destination_ids', $entity->tA('location_destination_ids'). '<span class="text-danger">*</span>', [], false) !!}
                                            {!! MyForm::hidden('location_destination_ids', $entity->location_destination_ids, ['id' => 'location_destination_ids']) !!}
                                            {!! MyForm::hidden('location_destination_id', $entity->location_destination_id, ['id' => 'location_destination_id']) !!}

                                            <div class="input-group">
                                                <select class="select2 select-location form-control"
                                                        id="combo_location_destination_ids"
                                                        name="combo_location_destination_ids"
                                                        multiple {{$entity->id == null ? 'disabled' : ''}}>
                                                    @foreach ($entity->locationDestinationAttributes() as $location)
                                                        <option value="{{ $location->id }}" selected="selected"
                                                                title="{{ $location->title }}">
                                                            {{ $location->title }}</option>
                                                    @endforeach
                                                </select>

                                                {{-- <div class="input-group-append">
                                                    <button class="btn btn-third quick-add" type="button"
                                                        data-model="location" data-url="{{ route('location.advance') }}"
                                                        data-callback="addLocationComplete">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('location_arrival_ids' ,$entity->tA('location_arrival_ids'). '<span class="text-danger">*</span>', [], false) !!}
                                            {!! MyForm::hidden('location_arrival_ids', $entity->location_arrival_ids, ['id' => 'location_arrival_ids']) !!}
                                            {!! MyForm::hidden('location_arrival_id', $entity->location_arrival_id, ['id' => 'location_arrival_id']) !!}

                                            <div class="input-group">
                                                <select class="select2 select-location form-control"
                                                        id="combo_location_arrival_ids"
                                                        name="combo_location_arrival_ids"
                                                        multiple {{$entity->id == null ? 'disabled' : ''}}>
                                                    @foreach ($entity->locationArrivalAttributes() as $location)
                                                        <option value="{{ $location->id }}" selected="selected"
                                                                title="{{ $location->title }}">
                                                            {{ $location->title }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <div class="input-group-append">
                                                    <button class="btn btn-third quick-add" type="button"
                                                        data-model="location" data-url="{{ route('location.advance') }}"
                                                        data-callback="addLocationComplete">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('system_code_config_id',
                                            $entity->tA('system_code_config_id'), [], false) !!}
                                            <div class="input-group  with-button-add">
                                                <div class="code-config input-group select2-bootstrap-prepend">
                                                    <select class="select2 select-code-config" id="system_code_config_id"
                                                        name="system_code_config_id">
                                                        @if (isset($entity->systemCodeConfig))
                                                        <option value="{{ $entity->systemCodeConfig->id }}" selected="selected"
                                                            title="{{ $entity->systemCodeConfig->prefix }}">
                                                            {{ $entity->systemCodeConfig->prefix }}</option>
                                                    @endif
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-third quick-add" type="button"
                                                            data-model="system-code-config" data-url="{{ route('system-code-config.advance') }}"
                                                            data-callback="addSystemCodeConfigComplete">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>--}}
                                </div>
                            </div>

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
@include('layouts.backend.elements.search._location_search')
@if(empty($formAdvance))
    @include('layouts.backend.elements._map')
@endif
<?php $jsFiles = ['autoload/object-select2', 'autoload/customer', 'vendor/lib/locationObject']; ?>
{!!loadFiles($jsFiles, $area, 'js') !!}
<script>
    var urlLocation = '{{route('location.combo-location')}}',
        backendUri = '{{getBackendDomain()}}',
        urlCodeConfig = '{{route('system-code-config.getCodeConfig')}}',
        comboCustomerUri = '{{route('customer.combo-owner')}}',
        comboClientUri = '{{route('client.combo-client')}}',
        is_create = @json(Request::is('*/create') ? true : false);
</script>
