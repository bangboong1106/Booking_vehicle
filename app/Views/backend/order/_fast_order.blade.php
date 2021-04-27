@php
    $today = (new DateTime());
@endphp
<div class="fast-order-form hidden-dialog">
    <div class="row">
        <div class="col-12">
            {!! MyForm::model($fastOrder, [
                'route' => ['order.advance', $fastOrder->id],
                'validation' => empty($validation) ? null : $validation
            ])!!}
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box container-card-box">
                        <div class="card-header" role="tab">
                            <h5 class="mb-0 mt-0 font-20">
                                {{trans('models.order.attributes.information')}}
                            </h5>
                        </div>
                        {{--Thông tin chung--}}
                        <div id="collapseInformation"
                             aria-labelledby="headingOne" style="">
                            <div class="form-group row">
                                <div class="col-md-8">
                                    {!! MyForm::label('order_code', $fastOrder->tA('order_code') . ' <span class="text-danger">*</span>', [], false) !!}
                                    <br/>
                                    <div>
                                        <div class="code-config input-group select2-bootstrap-prepend">
                                            <select class="select2 select-code-config fast-order-code-config"
                                                    id="fast_order_code_config"
                                                    name="code_config">
                                                <option></option>
                                            </select>
                                        </div>
                                        {!! MyForm::text('order_code', $order_code, ['placeholder'=>$fastOrder->tA('order_code'),
                                            'id'=>'fast_order_order_code','class'=>'order-code']) !!}
                                    </div>
                                </div>
                                {{--Ngày đặt hàng--}}
                                <div class="col-md-4">
                                    {!! MyForm::label('order_date', $fastOrder->tA('order_date'), [], false) !!}
                                    {!! MyForm::text('order_date', (empty($fastOrder->id) && empty($fastOrder->order_date)) ? $today->format('d-m-Y') :$fastOrder->order_date,
                                     ['placeholder'=>$fastOrder->tA('order_date'), 'class' => 'datepicker date-input fast-order-date']) !!}
                                </div>
                            </div>
                            {{--Khách hàng--}}
                            <div class="form-group row">
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('customer_id', $fastOrder->tA('customer_id'), [], false) !!}
                                    <div class="input-group {{empty($formAdvance) && auth()->user()->can('add customer') ? 'with-button-add' : ''}}">
                                        <select class="select2 fast-order-customer form-control"
                                                id="fast_order_customer_id"
                                                name="customer_id"
                                                data-url="{{route("customer-default-data.default")}}">
                                            @foreach($customer as $key => $title)
                                                <option value="{{$key}}"
                                                        {{ $key == $fastOrder->customer_id ? 'selected="selected"' : '' }}
                                                        title="{{$title}}">{{$title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {!! MyForm::error('customer_id') !!}
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('customer_name', $fastOrder->tA('customer_name'), [], false) !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-user" aria-hidden="true"></i></span>
                                        </div>
                                        {!! MyForm::text('customer_name', $fastOrder->customer_name,
                                            ['placeholder'=>$fastOrder->tA('customer_name'), 'id'=> 'fast_order_customer_name']) !!}
                                    </div>

                                </div>
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('customer_mobile_no', trans('models.customer.attributes.mobile_no'), [], false) !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-phone" aria-hidden="true"></i></span>
                                        </div>
                                        {!! MyForm::text('customer_mobile_no', $fastOrder->customer_mobile_no,
                                        ['placeholder'=>trans('models.customer.attributes.mobile_no'), 'id' => 'fast_order_customer_mobile_no']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Thông tin trạng thái đơn hàng --}}
                        <div>
                            {{--Độ ưu tiên--}}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        {!! MyForm::hidden('precedence',  config('constant.ORDER_PRECEDENCE_NORMAL')) !!}
                                    </div>
                                </div>
                            </div>
                            {{--Trạng thái đơn hàng--}}
                            {!! MyForm::label('status', $fastOrder->tA('status')) !!}
                            <div class="row">
                                <div class="col-sm-6 col-lg-4">
                                    <label class="card-box border-primary text-primary order-status-select">
                                        {{ trans('common.KHOI_TAO') }}
                                        <input type="radio" name="status"
                                               value="{{config("constant.KHOI_TAO")}}"
                                                {!! empty($fastOrder->status) || $fastOrder->status == config('constant.KHOI_TAO') ? 'checked' : '' !!}>
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <label class="card-box border-secondary text-secondary order-status-select">
                                        {{ trans('common.SAN_SANG') }}
                                        <input type="radio" name="status"
                                               value="{{config("constant.SAN_SANG")}}"
                                                {!! $fastOrder->status == config('constant.SAN_SANG') ? 'checked' : '' !!}>
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <label class="card-box border-stpink text-stpink order-status-select">
                                        {{ trans('common.TAI_XE_XAC_NHAN') }}
                                        <input type="radio" name="status"
                                               value="{{config("constant.TAI_XE_XAC_NHAN")}}"
                                                {!! $fastOrder->status == config('constant.TAI_XE_XAC_NHAN') ? 'checked' : '' !!}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            {!! MyForm::error('status') !!}
                        </div>
                        <div class="card-header" role="tab" id="headingInformation">
                            <h5 class="mb-0 mt-0 font-20">
                                Thông tin nhận trả hàng
                            </h5>
                        </div>
                        {{--Thông tin giao hàng--}}
                        <div id="collapseDestination" aria-labelledby="Destination">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('locationDestinations.0.location_id', $fastOrder->tA('etd'), [], false) !!}
                                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                                        <select class="select2 select-location fast-order-location"
                                                id="fast_order_location_destination_id"
                                                name="locationDestinations[0][location_id]">
                                            <option></option>
                                            @foreach($locationList as $key => $title)
                                                <option value="{{$key}}" title="{{$title}}"
                                                        {{ isset($fastOrder->locationDestinations[0]['location_id']) && $key == $fastOrder->locationDestinations[0]['location_id'] ? 'selected="selected"' : '' }}
                                                >{{$title}}</option>
                                            @endforeach
                                            @if(isset($fastOrder->locationDestinations[0]['location_id']) &&
                                                !is_numeric($fastOrder->locationDestinations[0]['location_id']))
                                                <option value="{{$fastOrder->locationDestinations[0]['location_id']}}"
                                                    title="{{$fastOrder->locationDestinations[0]['location_id']}}"
                                                    selected>
                                                    {{$fastOrder->locationDestinations[0]['location_id']}}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                    {!! MyForm::error('locationDestinations.0.location_id') !!}
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            {!! MyForm::label('ETD', $fastOrder->tA('ETD'), [], false) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            {!! MyForm::text('locationDestinations[0][time]', empty($fastOrder['locationDestinations'][0]['time'])
                                                ? $today->format('H:i') : $fastOrder['locationDestinations'][0]['time'],
                                            ['placeholder'=>$fastOrder->tA('ETD_time'), 'class'=>'timepicker time-input']) !!}
                                        </div>
                                        <div class="col-md-7">
                                            {!! MyForm::text('locationDestinations[0][date]', empty($fastOrder['locationDestinations'][0]['date'])
                                                ? $today->format('d-m-Y') : $fastOrder['locationDestinations'][0]['date'],
                                            ['placeholder'=>$fastOrder->tA('ETD'), 'class'=>'datepicker date-input fast-order-date', 'id' => 'fast_order_ETD_date']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Thông tin trả hàng--}}
                        <div id="collapseArrival" aria-labelledby="Arrival">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('locationArrivals.0.location_id', $fastOrder->tA('eta'), [], false) !!}
                                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                                        <select class="select2 select-location fast-order-location"
                                                id="fast_order_location_arrival_id"
                                                name="locationArrivals[0][location_id]">
                                            <option></option>
                                            @foreach($locationList as $key => $title)
                                                <option value="{{$key}}"
                                                        {{ isset($fastOrder->locationArrivals[0]['location_id']) && $key == $fastOrder->locationArrivals[0]['location_id'] ? 'selected="selected"' : '' }}
                                                        title="{{$title}}">{{$title}}</option>
                                            @endforeach
                                            @if(isset($fastOrder->locationArrivals[0]['location_id']) &&
                                                !is_numeric($fastOrder->locationArrivals[0]['location_id']))
                                                <option value="{{$fastOrder->locationArrivals[0]['location_id']}}"
                                                        title="{{$fastOrder->locationArrivals[0]['location_id']}}"
                                                        selected>
                                                    {{$fastOrder->locationArrivals[0]['location_id']}}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                    {!! MyForm::error('locationArrivals.0.location_id') !!}
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            {!! MyForm::label('ETD', $fastOrder->tA('ETA'), [], false) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            {!! MyForm::text('locationArrivals[0][time]', empty($fastOrder['locationArrivals'][0]['time'])
                                                ? $today->modify('+1 hours')->format('H:i') : $fastOrder['locationArrivals'][0]['time'],
                                            ['placeholder'=>$fastOrder->tA('ETA_time'), 'class'=>'timepicker time-input']) !!}
                                        </div>
                                        <div class="col-md-7">
                                            {!! MyForm::text('locationArrivals[0][date]', empty($fastOrder['locationArrivals'][0]['date'])
                                                ? $today->format('d-m-Y') : $fastOrder['locationArrivals'][0]['date'],
                                            ['placeholder'=>$fastOrder->tA('ETA'), 'class'=>'datepicker date-input fast-order-date', 'id' => 'fast_order_ETA_date']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingInformation">
                            <h5 class="mb-0 mt-0 font-20">
                                {{trans('models.order.attributes.choose_vehicle_driver')}}
                            </h5>
                        </div>
                        {{--Thông tin xe và tài xế--}}
                        <div id="collapseVehicle" aria-labelledby="headingVehicle">
                            <div class="row trip-create">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            {!! MyForm::hidden('route_create', 0, ['id'=>'route_create']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="advanced form-group row">
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('vehicle_id', $fastOrder->tA('vehicle'), [], false) !!}
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-vehicle fast-order-vehicle"
                                                id="vehicle_id"
                                                name="vehicle_id">
                                            <option></option>
                                            @if(!empty($vehicle) && !(strpos($routeName, 'duplicate')))
                                                <option value="{{$vehicle->id}}" selected="selected"
                                                        title="{{$vehicle->reg_no}}">{{$vehicle->reg_no}}</option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="current_vehicle_id">
                                    </div>
                                    {!! MyForm::error('vehicle_id') !!}
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('primary_driver_id', $fastOrder->tA('primary_driver'), [], false) !!}
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-driver fast-order-driver"
                                                id="primary_driver_id"
                                                name="primary_driver_id">
                                            <option></option>
                                            @if(!empty($primaryDriver) && !(strpos($routeName, 'duplicate')))
                                                <option value="{{$primaryDriver->id}}"
                                                        selected="selected"
                                                        title="{{$primaryDriver->full_name}}"
                                                >{{$primaryDriver->full_name}}</option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="current_primary_driver_id">
                                    </div>
                                    {!! MyForm::error('primary_driver_id') !!}
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    {!! MyForm::label('driver', $fastOrder->tA('secondary_driver'), [], false) !!}
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-driver fast-order-driver"
                                                id="secondary_driver_id"
                                                name="secondary_driver_id">
                                            <option></option>
                                            @if(!empty($secondaryDriver) && !(strpos($routeName, 'duplicate')))
                                                <option value="{{$secondaryDriver->id}}"
                                                        selected="selected"
                                                        title="{{$secondaryDriver->full_name}}">{{$secondaryDriver->full_name}}</option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="current_secondary_driver_id">
                                    </div>
                                    {!! MyForm::error('driver') !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                                    {!! MyForm::textarea('note', $entity->note,['placeholder'=>$entity->tA('note'),'rows'=>2]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="m-t-20"></div>

                        <div class="submit-button text-right row">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title">
                                    {{trans('actions.create').' '.transb('order.name')}}
                                </h4>
                            </div>
                            <div class="col-md-6">
                                <span class="padr20">
                                    <a class="btn back-button" href="{!! getBackUrl() !!}">
                                        <span class="ls-icon ls-icon-reply" aria-hidden="true"></span>
                                        <i class="fa fa-backward" style="margin-right: 8px"></i>{{trans('actions.close')}}
                                    </a>
                                </span>

                                <span>
                                    <button type="submit" class="btn btn-blue" style="width: 120px">
                                        <span class="ls-icon ls-icon-check" aria-hidden="true"></span>
                                        <i class="fa fa-save" style="margin-right: 8px"></i>{{trans('actions.submit')}}
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {!! MyForm::close() !!}
            </div>
        </div>
    </div>
</div>

@if($formAdvance)
    <script>
        if (typeof fastLocationDropdownUri === 'undefined' || urlLocation === null) {
            fastLocationDropdownUri = '{{route('location.combo-location')}}';
        }
        if (typeof fastDriverDropdownUri === 'undefined') {
            fastDriverDropdownUri = '{{route('driver.combo-driver')}}';
        }
        if (typeof backendUri === 'undefined') {
            backendUri = '{{getBackendDomain()}}';
        }
        if (typeof fastVehicleDropdownUri === 'undefined') {
            fastVehicleDropdownUri = '{{route('vehicle.combo-vehicle')}}';
        }
        if (typeof urlVehicleDriver === 'undefined') {
            urlVehicleDriver = '{{route('driver.getVehicleDriver')}}';
        }
        if (typeof fastCodeConfigDropDownUri === 'undefined') {
            fastCodeConfigDropDownUri = '{{route('system-code-config.getCodeConfig')}}';
        }
        if (typeof urlCode === 'undefined') {
            urlCode = '{{route('system-code.getCode')}}';
        }
        if (typeof routeDropdownUri === 'undefined') {
            routeDropdownUri = '{{route('route.combo-route')}}';
        }
        if (typeof quotaDropdownUri === 'undefined') {
            quotaDropdownUri = '{{route('quota.combo-quota')}}';
        }
        if (typeof fastCustomerDropdownUri === 'undefined') {
            fastCustomerDropdownUri = '{{route('customer.combo-customer')}}';
        }
    </script>
@endif

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
<div class="fast-order-form">
    @include('backend.order._default_data')
</div>