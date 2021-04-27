<script>
    let urlLocation = '{{route('location.combo-location')}}',
        locationDestinationId = '{{empty($entity->location_destination_id) ? 0 : $entity->location_destination_id}}',
        locationArrivalId = '{{empty($entity->location_arrival_id) ? 0 : $entity->location_arrival_id}}';

    let urlVehicle = '{{route('vehicle.combo-vehicle')}}';
    let orderDropdownUri = '{{route('order-customer.combo-order')}}';
    let comboCustomerUri = '{{route('customer.combo-owner')}}';
    let checkOrderNoUri = '{{route('order-customer.check-order-no')}}';
    let calcETAUri = '{{route('order-customer.calc-eta')}}';
    let backendUri = '{{getBackendDomain()}}';
    var token = '{!! csrf_token() !!}';
</script>
@php
    $today = (new DateTime());
    $tomorrow = new DateTime('tomorrow');

@endphp
<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{$entity->id}}">
        {!! MyForm::model($entity, ['route' => ['order-customer.valid', $entity->id]])!!}

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
                        {{--  <li class="nav-item">
                              <a href="#route_file" data-toggle="tab" aria-expanded="false" class="nav-link">
                                  {{trans('models.order.attributes.files_info')}}
                              </a>
                          </li>--}}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="route_info">
                            <div class="content-body">

                                {{--Thông tin chung--}}
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
                                            {{--                                            <div class="col-md-4">--}}
                                            {{--                                                {!! MyForm::label('code', $entity->tA('code')  . ' <span class="text-danger">*</span>', [], false) !!}--}}
                                            {!! MyForm::hidden('code', $entity->code != null ? $entity->code : $code, ['placeholder'=>$entity->tA('code')]) !!}

                                            {{--                                            </div>--}}
                                            <div class="col-md-4">
                                                {!! MyForm::label('order_no', $entity->tA('order_no') . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('order_no', $entity->order_no != null ? $entity->order_no :  $code, ['placeholder'=>$entity->tA('order_no'), 'id'=>'order_no']) !!}
                                            </div>
                                            {{--                                            <div class="col-md-4">--}}
                                            {{--                                                {!! MyForm::label('name', $entity->tA('name'), [], false) !!}--}}
                                            {{--                                                {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name'), 'id'=>'name' ]) !!}--}}
                                            {{--                                            </div>--}}
                                        </div>

                                        {{--Khách hàng--}}
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="customer_id">{{trans('models.customer.name') }}<span
                                                            class="text-danger">*</span></label>
                                                <div class="input-group {{empty($formAdvance) && auth()->user()->can('add customer') ? 'with-button-add' : ''}}">
                                                    <select class="select2 select-customer form-control"
                                                            id="customer_id_order_customer"
                                                            name="customer_id"
                                                            {{Request::is('*/edit') ? 'disabled' : ''}}>
                                                        @foreach($customers as $customer)
                                                            @if ($customer->id== $entity->customer_id)
                                                                <option value="{{$customer->id}}" selected="selected"
                                                                        title="{{$customer->full_name}}">
                                                                    {{$customer->full_name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @if (Request::is('*/edit'))
                                                        <input type="hidden" value="{{$entity->customer_id}}"
                                                               name="customer_id" id="customer_id_hidden">
                                                    @endif
                                                    @if(empty($formAdvance) && auth()->user()->can('add customer'))
                                                        <div class="input-group-append">
                                                            <button class="btn btn-third quick-add" type="button"
                                                                    data-model="customer"
                                                                    data-url="{{route('customer.advance')}}"
                                                                    data-callback="addCustomerComplete">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4 {{empty($entity->customer_id) && empty($entity->customer_name) ? 'hide' : ''}}">
                                                {!! MyForm::label('customer_name', $entity->tA('customer_name'), [], false) !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-user"
                                                                                          aria-hidden="true"></i></span>
                                                    </div>
                                                    {!! MyForm::text('customer_name', $entity->customer_name, ['placeholder'=>$entity->tA('customer_name')]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4 {{empty($entity->customer_id) && empty($entity->customer_name) ? 'hide' : ''}}">
                                                {!! MyForm::label('customer_mobile_no', trans('models.customer.attributes.mobile_no'), [], false) !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-phone"
                                                                                          aria-hidden="true"></i></span>
                                                    </div>
                                                    {!! MyForm::text('customer_mobile_no', $entity->customer_mobile_no, ['placeholder'=>trans('models.customer.attributes.mobile_no')]) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        {!! MyForm::label('order_date', $entity->tA('order_date'), [], false) !!}
                                                        {!! MyForm::text('order_date', (empty($entity->id) && empty($entity->order_date)) ? $today->format('d-m-Y') : format($entity->order_date,'d-m-Y'),
                                                        ['placeholder'=>$entity->tA('order_date'), 'class' => 'datepicker date-input']) !!}
                                                    </div>
                                                </div>
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
                                        <div class="location-order location-order-destination">
                                            <div class="form-group row label-info">
                                                <div class="delete-location"></div>
                                                <div class="col-4">
                                                    {!! MyForm::label('location_destination', $entity->tA('location_destination'). ' <span class="text-danger">*</span>', [], false) !!}
                                                </div>
                                                <div class="col-4">
                                                    {!! MyForm::label('ETD_date', $entity->tA('ETD_date'), [], false) !!}
                                                </div>
                                            </div>

                                            <div class="form-group row location-item">
                                                <div class="col-md-4 lc-item">
                                                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : 'hide-button-add' }}">
                                                        <select class="select-location form-control"
                                                                name="location_destination_id"
                                                                id="location_destination_id"
                                                                data-field="location_id" disabled>
                                                            @if($entity->location_destination_id)
                                                                <option value="{{$entity->location_destination_id}}"
                                                                        selected="selected"
                                                                        title="{{$entity->locationDestination->title}}">
                                                                    {{$entity->locationDestination->title}}</option>
                                                            @endif
                                                        </select>
                                                        <div class="input-group-append">
                                                            @if(empty($formAdvance) && auth()->user()->can('add location'))
                                                                <span class="input-group-addon location-search">
                                                                    <div class="input-group-text bg-transparent">
                                                                        <i class="fa fa-search"></i>
                                                                    </div>
                                                                </span>
                                                                <button class="btn btn-third quick-add" type="button"
                                                                        data-model="location"
                                                                        data-url="{{route('location.advance')}}">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-third location-search bg-disable-combo-box"
                                                                        type="button">
                                                                    <i class="fa fa-search"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            {!! MyForm::text('ETD_time', !isset($entity->ETD_time) ? $today->format('H-i') : $entity->ETD_time,
                                                            ['placeholder'=>$entity->tA('ETD_time'), 'class'=>'timepicker time-input', 'data-field' => 'time', 'id'=> 'ETD_time']) !!}
                                                        </div>
                                                        <div class="col-md-7">
                                                            {!! MyForm::text('ETD_date', !isset($entity->ETD_date) ? $today->format('d-m-Y') : format($entity->ETD_date,'d-m-Y') ,
                                                            ['placeholder'=>$entity->tA('ETD_date'), 'class'=>'datepicker date-input', 'data-field' => 'date', 'id'=> 'ETD_date']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--        Địa điểm trả hàng--}}
                                        <div class="location-order location-order-arrival">
                                            <div class="form-group row label-info">
                                                <div class="col-4">
                                                    {!! MyForm::label('location_arrival', $entity->tA('location_arrival'). ' <span class="text-danger">*</span>', [], false) !!}
                                                </div>
                                                <div class="col-4">
                                                    {!! MyForm::label('ETA_date', $entity->tA('ETA_date'), [], false) !!}
                                                </div>
                                            </div>
                                            <div class="form-group row location-item">
                                                <div class="col-md-4 lc-item">
                                                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : 'hide-button-add' }}">
                                                        <select class="select-location form-control"
                                                                name="location_arrival_id"
                                                                id="location_arrival_id"
                                                                data-field="location_id" disabled>
                                                            @if($entity->location_arrival_id)
                                                                <option value="{{$entity->location_arrival_id}}"
                                                                        selected="selected"
                                                                        title="{{$entity->locationArrival->title}}">
                                                                    {{$entity->locationArrival->title}}</option>
                                                            @endif
                                                        </select>

                                                        <div class="input-group-append">
                                                            @if(empty($formAdvance) && auth()->user()->can('add location'))
                                                                <span class="input-group-addon location-search">
                                                                    <div class="input-group-text bg-transparent">
                                                                        <i class="fa fa-search"></i>
                                                                    </div>
                                                                </span>
                                                                <button class="btn btn-third quick-add"
                                                                        type="button" data-model="location"
                                                                        data-url="{{route('location.advance')}}">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-third location-search bg-disable-combo-box"
                                                                        type="button">
                                                                    <i class="fa fa-search"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            {!! MyForm::text('ETA_time', !isset($entity->ETA_time) ? $today->modify('+1 hours')->format('H-i') : $entity->ETA_time,
                                                            ['placeholder'=>$entity->tA('ETA_time'), 'class'=>'timepicker time-input', 'data-field' => 'time', 'id'=> 'ETA_time']) !!}
                                                        </div>
                                                        <div class="col-md-7">
                                                            {!! MyForm::text('ETA_date', !isset($entity->ETA_date) ? $today->format('d-m-Y') : format($entity->ETA_date,'d-m-Y'),
                                                            ['placeholder'=>$entity->tA('ETA_date'), 'class'=>'datepicker date-input', 'data-field' => 'date', 'id'=> 'ETA_date']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('distance', $entity->tA('distance'), [], false) !!}
                                                <div class="input-group">
                                                    {!! MyForm::text('distance', numberFormat($entity->distance),
                                                    ['placeholder'=>$entity->tA('distance'),'class' => 'number-input' , 'id' => 'distance']) !!}
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text form-group-right">
                                                      km
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{--Thông tin hàng hóa--}}
                                <div class="card-header" role="tab" id="headingGoods">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseGoods" aria-expanded="true"
                                           aria-controls="collapseGoods"
                                           class="collapse-expand}">
                                            {{trans('models.order.attributes.goods_info')}}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseGoods" class="collapse show"
                                     role="tabpanel" aria-labelledby="Goods">
                                    <div class="card-body m-l-24">

                                        <div class="form-group">
                                            <div class="wrap-add-field {{ count($entity->goods) > 0 ? 'hide' : '' }}">
                                                <div class="wrap-add-btn crm-flex goods-search" data-type="multiple">
                                                    <span class="ic-add-blue-16">
                                                        <i class="fa fa-plus"></i><span
                                                                class="text">Chọn hàng hóa </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="goods-table">
                                                <div class="row ">
                                                    <div class="col-12 table-responsive">
                                                        <table class="table table-bordered table-goods {{ count($entity->goods) > 0 ? '' : 'hide' }}">
                                                            <thead id="head_content">
                                                            <tr class="active">
                                                                <td style="width: 250px">
                                                                    <b>{{ trans('models.goods_type.attributes.title') }}</b>
                                                                </td>
                                                                <td style="width: 100px">
                                                                    <b>{{ trans('models.order.attributes.goods_quantity') }}</b>
                                                                </td>
                                                                <td style="width: 250px">
                                                                    <b>{{ trans('models.order.attributes.goods_unit') }}</b>
                                                                </td>
                                                                <td style="width: 140px">
                                                                    <b>{{ trans('models.order.attributes.goods_insured') }}</b>
                                                                </td>
                                                                <td style="width: 100px"><b>Dung tích</b></td>
                                                                <td style="width: 100px"><b>Tải trọng</b></td>
                                                                <td style="width: 100px"><b>Thể tích(m3)</b></td>
                                                                <td style="width: 100px"><b>Trọng lượng(kg)</b></td>
                                                                <td style="width: 250px"><b>Diễn giải</b></td>
                                                                <td style="width: 50px" class="text-center"></td>
                                                            </tr>
                                                            <tbody>
                                                            <tr class="hide">
                                                                <td width="200">
                                                                    <div class="input-group">
                                                                        <input class="form-control" type="hidden"
                                                                               data-field="goods_type_id"/>
                                                                        <input class="form-control" type="text"
                                                                               data-field="goods_type"
                                                                               readonly="readonly"/>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input class="number-input form-control"
                                                                               type="text" value="1"
                                                                               data-field="quantity"/>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input class="form-control" type="text"
                                                                               data-field="goods_unit"
                                                                               readonly="readonly"/>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group text-center goods-insure">
                                                                        <label class="container">
                                                                            {!! MyForm::checkbox('', 1, true, ['class' => 'form-control',
                                                                            'data-field' => 'insured_goods']) !!}
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td width="150">
                                                                    <div class="input-group">
                                                                        {!! MyForm::text('', numberFormat($entity->volume), ['placeholder' =>
                                                                        $entity->tA('volume'), 'class' => 'number-input', 'readonly' => 'true', 'data-field' => 'volume'])
                                                                        !!}

                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        {!! MyForm::text('', numberFormat($entity->weight), ['placeholder' =>
                                                                        $entity->tA('weight'), 'class' => 'number-input', 'readonly' => 'true', 'data-field' => 'weight'])
                                                                        !!}

                                                                    </div>
                                                                </td>
                                                                <td width="150">
                                                                    <div class="input-group">
                                                                        {!! MyForm::text('', numberFormat($entity->total_volume), ['placeholder' =>
                                                                        $entity->tA('total_volume'), 'class' => 'number-input', 'data-field' =>
                                                                        'total_volume', 'readonly' => 'true']) !!}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        {!! MyForm::text('', numberFormat($entity->total_weight), ['placeholder' =>
                                                                        $entity->tA('total_weight'), 'class' => 'number-input', 'data-field' =>
                                                                        'total_weight', 'readonly' => 'readonly']) !!}
                                                                    </div>
                                                                </td>
                                                                <td width="150">
                                                                    <div class="input-group">
                                                                        <input type="text" id="" class="form-control"
                                                                               data-field="note">
                                                                    </div>
                                                                </td>
                                                                <td class="text-center text-middle">
                                                                    <a class="delete-goods d-inline-block" href="#"
                                                                       title="Xóa">
                                                                        <i class="fa fa-trash" aria-hidden="true"
                                                                           title="Xóa"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @foreach ($entity->goods as $key => $goods)
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input class="form-control"
                                                                                   name="goods[{{ $key }}][goods_type_id]"
                                                                                   type="hidden"
                                                                                   value="{{ $goods['goods_type_id'] }}"
                                                                                   data-field="goods_type_id"/>
                                                                            <input class="form-control"
                                                                                   readonly="readonly" type="text"
                                                                                   name="goods[{{ $key }}][goods_type]"
                                                                                   value="{{ isset($goodsTypes[$goods['goods_type_id']]) ? $goodsTypes[$goods['goods_type_id']] : '' }}"
                                                                                   data-field="goods_type"/>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input class="number-input form-control"
                                                                                   name="goods[{{ $key }}][quantity]"
                                                                                   type="text"
                                                                                   value="{{ $goods['quantity'] }}"
                                                                                   data-field="quantity"/>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input class="form-control" type="text"
                                                                                   data-field="goods_unit"
                                                                                   readonly="readonly"
                                                                                   name="goods[{{$key}}][goods_unit]"
                                                                                   value="{{ !empty($goods['goods_unit_id']) && isset($goodsUnits[$goods['goods_unit_id']]) ? $goodsUnits[$goods['goods_unit_id']] : 0}}"/>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group text-center goods-insure">
                                                                            <label class="container">
                                                                                {!! MyForm::checkbox('goods[' . $key . '][insured_goods]', 1,
                                                                                !empty($goods['insured_goods']), ['class' => 'form-control',
                                                                                'data-field' => 'insured_goods']) !!}
                                                                                <span class="checkmark"></span>

                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td width="150">
                                                                        <div class="input-group">
                                                                            {!! MyForm::text('goods[' . $key . '][volume]',
                                                                            numberFormat($goods['volume']), ['placeholder' => $entity->tA('volume'),
                                                                            'class' => 'number-input', 'readonly' => 'true', 'data-field' => 'volume']) !!}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            {!! MyForm::text('goods[' . $key . '][weight]',
                                                                            numberFormat($goods['weight']), ['placeholder' => $entity->tA('weight'),
                                                                            'class' => 'number-input', 'readonly' =>'true', 'data-field' => 'weight']) !!}

                                                                        </div>
                                                                    </td>
                                                                    <td width="150">
                                                                        <div class="input-group">
                                                                            {!! MyForm::text('goods[' . $key . '][total_volume]',
                                                                            numberFormat($goods['total_volume']), ['placeholder' =>
                                                                            $entity->tA('total_volume'), 'class' => 'number-input', 'readonly' =>
                                                                            'true', 'data-field' => 'total_volume']) !!}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            {!! MyForm::text('goods[' . $key . '][total_weight]',
                                                                            numberFormat($goods['total_weight']), ['placeholder' =>
                                                                            $entity->tA('total_weight'), 'class' => 'number-input', 'readonly' =>
                                                                            'true', 'data-field' => 'total_weight']) !!}
                                                                        </div>
                                                                    </td>
                                                                    <td width="150">
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control"
                                                                                   name="goods[{{ $key }}][note]"
                                                                                   value="{!!  $goods['note'] !!}">
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center text-middle">
                                                                        <a class="delete-goods d-inline-block" href="#"
                                                                           title="Xóa">
                                                                            <i class="fa fa-trash" aria-hidden="true"
                                                                               title="Xóa"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row wrap-total" style="width: 100%">
                                                    <div class="col-8">
                                                        <span id="btn-goods-search"
                                                              class="btn btn-secondary2 goods-search {{ count($entity->goods) > 0 ? '' : 'hide' }}"
                                                              tabindex="0" data-type="multiple">
                                                            <div class="crm-flex crm-align-items-center">
                                                                <i class="fa fa-plus" style="margin-right: 8px"></i>Thêm hàng hóa
                                                            </div>
                                                        </span>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="row" style="padding: 4px ; display: none">
                                                            <div class="col-6">
                                                                {!! MyForm::label('quantity', $entity->tA('quantity'), [], false) !!}
                                                            </div>
                                                            <div class="col-6">
                                                                {!! MyForm::text('quantity', numberFormat($entity->quantity), ['placeholder' =>
                                                                $entity->tA('quantity'), 'class' => 'number-input']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="row" style="padding: 4px">
                                                            <div class="col-6">
                                                                {!! MyForm::label('weight', $entity->tA('weight'), [], false) !!}
                                                                (kg)
                                                            </div>
                                                            <div class="col-6">
                                                                {!! MyForm::text('weight', numberFormat($entity->weight), ['placeholder' =>
                                                                $entity->tA('weight'), 'class' => 'number-input']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="row" style="padding: 4px">
                                                            <div class="col-6">
                                                                {!! MyForm::label('volume', $entity->tA('volume'), [], false) !!}
                                                                (m3)
                                                            </div>
                                                            <div class="col-6">
                                                                {!! MyForm::text('volume', numberFormat($entity->volume), ['placeholder' =>
                                                                $entity->tA('volume'), 'class' => 'number-input']) !!}
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if (session()->has('goods_error'))
                                            <div id="order_code-error"
                                                 class="help-block error-help-block">{{ session()->get('goods_error') }}</div>
                                        @endif
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                {!! MyForm::label('goods_detail', $entity->tA('goods_detail'), [], false) !!}
                                                {!! MyForm::textarea('goods_detail', $entity->goods_detail, ['placeholder' =>
                                                $entity->tA('goods_detail'), 'rows' => 2]) !!}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('route_number', $entity->tA('route_number')) !!}
                                                {!! MyForm::text('route_number', numberFormat($entity->route_number),
                                                  ['placeholder'=>$entity->tA('route_number'),'class' => 'number-input','id'=>'route_number']) !!}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <table class="table table-bordered table-hover"
                                                   id="table-vehicle-group-info">
                                                <thead id="head_content">
                                                <tr class="active">
                                                    <td style="font-size: 14px; font-weight: bold;">
                                                        {!! MyForm::label('vehicle_group_id', $entity->tA('vehicle_group_id')) !!}
                                                    </td>
                                                    <td style="width: 200px; font-size: 14px; font-weight: bold;">
                                                        {!! MyForm::label('vehicle_number', $entity->tA('vehicle_number')) !!}
                                                    </td>
                                                    <td style="width: 50px;"></td>
                                                </tr>
                                                <tbody id="body_content">
                                                @if( isset($entity->listVehicleGroups))
                                                    @foreach($entity->listVehicleGroups as $key => $vehicleGroup)
                                                        <tr class="vehicle-group-item">
                                                            <td>
                                                                <select class="select2 combo"
                                                                        name="listVehicleGroup[{{$key}}][vehicle_group_id]">
                                                                    @foreach($vehicle_group_list as $vehicleGroupId => $vehicleGroupName)

                                                                        <option value="{{$vehicleGroupId}}"
                                                                                {{$vehicleGroup['vehicle_group_id'] == $vehicleGroupId ? 'selected':'' }}
                                                                                title="{{$vehicleGroupName}}">
                                                                            {{$vehicleGroupName}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="vgi-item number-input form-control "
                                                                       data-field="vehicle_number"
                                                                       name="listVehicleGroup[{{$key}}][vehicle_number]"
                                                                       value="{{$vehicleGroup['vehicle_number']}}">
                                                            </td>
                                                            <td>
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif

                                                <tr class="d-none vehicle-group-info-default">
                                                    <td>
                                                        {!! MyForm::dropDown('vehicle_group_id', null, $vehicle_group_list, true, [ 'class' => 'combo vgi-item', 'data-field'=>'vehicle_group_id']) !!}
                                                    </td>
                                                    <td>
                                                        <input class="vgi-item number-input form-control " type="text"
                                                               data-field="vehicle_number">
                                                    </td>
                                                    <td>
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div id="wrap-add-vehicle-group">
                                            <button id="btn-add-vehicle-group" class="btn btn-default">
                                                <span><i class="fa fa-plus"></i>Thêm yêu cầu xe</span>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                {{--Thông tin thanh toán--}}
                                <div class="card-header" role="tab" id="headingPayment">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapsePayment" aria-expanded="false"
                                           aria-controls="collapsePayment" class="collapse-expand">
                                            {{trans('models.order.attributes.payment_info')}}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapsePayment" class="collapse"
                                     role="tabpanel" aria-labelledby="payment_info">
                                    <div class="card-body m-l-24">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('amount', $entity->tA('amount'), [], false) !!}
                                                {!! MyForm::text('amount', numberFormat($entity->amount),
                                                   ['placeholder'=>$entity->tA('amount'),'class' => 'number-input','id'=>'amount']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('payment_type', $entity->tA('payment_type'), [], false) !!}
                                                {!! MyForm::dropDown('payment_type', $entity->payment_type, config('system.order_payment_type'), false, [ 'class' => 'select2 minimal']) !!}
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('payment_user_id', $entity->tA('payment_user_id'), [], false) !!}
                                                {!! MyForm::dropDown('payment_user_id', $entity->payment_user_id, $userAdminList, true, [ 'class' => 'select2 minimal']) !!}
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('vat', $entity->tA('vat'), [], false) !!}
                                                <input hidden="hidden" name="vat"
                                                       id="vat"
                                                       value="{{ $entity->vat }}"/>
                                                <div>
                                                    {!! MyForm::checkbox('switchery_vat_default', $entity->vat, $entity->vat  == 1 ? true : false
                                                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_vat_default']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('goods_amount', $entity->tA('goods_amount'), [], false) !!}
                                                {!! MyForm::text('goods_amount', numberFormat($entity->goods_amount),
                                                   ['placeholder'=>$entity->tA('goods_amount'),'class' => 'number-input','id'=>'goods_amount']) !!}
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('anonymous_amount', $entity->tA('anonymous_amount'), [], false) !!}
                                                {!! MyForm::text('anonymous_amount', numberFormat($entity->anonymous_amount),
                                                   ['placeholder'=>$entity->tA('anonymous_amount'),'class' => 'number-input','id'=>'anonymous_amount']) !!}
                                            </div>
                                        </div>
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
@include('layouts.backend.elements.search._order_search')
@include('layouts.backend.elements.search._location_search')
@include('layouts.backend.elements.search._goods_search')

<?php
$searchJsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}
<script>
    let is_create = @json(Request::is('*/create') ? true : false);
</script>