{{--Thông tin xe và tài xế--}}
<div class="card-header" role="tab" id="headingVehicle">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseVehicle" aria-expanded="true" aria-controls="collapseVehicle"
           class="collapse-expand">
            {{ trans('models.order.attributes.choose_vehicle_driver') }}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseVehicle" class="collapse show" role="tabpanel" aria-labelledby="headingVehicle">
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('partner_id', $entity->tA('partner_id'). ' <span class="text-danger">*</span>', [], false) !!}
                {!! MyForm::dropDown('partner_id', $entity->partner_id, $partnerList, true, [ 'class' => 'select2 minimal']) !!}
            </div>
        </div>

        <div class="advanced form-group row">
            <div class="col-md-4">
                <label for="vehicle_id">{{ $entity->tA('vehicle') }}</label>
                <div class="input-group select2-bootstrap-prepend">
                    <select class="select2 select-vehicle" id="vehicle_id" name="vehicle_id">
                        <option></option>
                        @if (!empty($vehicle) && !strpos($routeName, 'duplicate'))
                            <option value="{{ $vehicle->id }}" selected="selected" title="{{ $vehicle->reg_no }}">
                                {{ $vehicle->reg_no }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="current_vehicle_id" value="{{ $entity->vehicle_id }}">
                    <span class="input-group-addon vehicle-search" id="vehicle-search" data-distance="1">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-truck"></i>
                        </div>
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <label for="driver">{{ $entity->tA('primary_driver') }}</label>
                <div class="input-group select2-bootstrap-prepend">
                    <select class="select2 select-driver" id="primary_driver_id" name="primary_driver_id">
                        <option></option>
                        @if (!empty($primaryDriver) && !strpos($routeName, 'duplicate'))
                            <option value="{{ $primaryDriver->id }}" selected="selected"
                                    title="{{ $primaryDriver->full_name }}">{{ $primaryDriver->full_name }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="current_primary_driver_id" value="{{ $entity->primary_driver_id }}">
                    <span class="input-group-addon driver-search" id="primary-driver-search">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-id-card"></i>
                        </div>
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <label for="driver">{{ $entity->tA('secondary_driver') }}</label>
                <div class="input-group select2-bootstrap-prepend">
                    <select class="select2 select-driver" id="secondary_driver_id" name="secondary_driver_id">
                        <option></option>
                        @if (!empty($secondaryDriver) && !strpos($routeName, 'duplicate'))
                            <option value="{{ $secondaryDriver->id }}" selected="selected"
                                    title="{{ $secondaryDriver->full_name }}">{{ $secondaryDriver->full_name }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="current_secondary_driver_id" value="{{ $entity->secondary_driver_id }}">
                    <span class="input-group-addon driver-search" id="secondary-driver-search">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-id-card"></i>
                        </div>
                    </span>
                </div>
            </div>
        </div>

        <div class="advanced form-group row">
            <div class="col-md-4">
                <label for="route_id">{{ $entity->tA('choose-route') }}</label>
                <div class="input-group select2-bootstrap-prepend">
                    <select class="select2 select-route" id="route_id" name="route_id"
                            {{ empty($entity->id) ? 'disabled' : '' }}>
                        <option></option>
                        @if (!empty($route) && !strpos($routeName, 'duplicate'))
                            <option value="{{ $route->id }}" selected="selected" title="{{ $route->name }}">
                                {{ $route->name }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="current_route_id"
                           value="{{ empty($currentRoute) ? '' : $currentRoute->id }}">
                    <span class="input-group-addon route-search" id="route-search">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-barcode"></i>
                        </div>
                    </span>
                </div>
            </div>
            <div class="col-md-4 warning-message">
            </div>
        </div>

        @if (session()->has('route_error'))
            <div id="order_code-error" class="help-block error-help-block">{{ session()->get('route_error') }}</div>
        @endif

    </div>
</div>

{{--Thông tin hàng hóa--}}
<div class="card-header" role="tab" id="headingGoods">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseGoods" aria-expanded="true" aria-controls="collapseGoods"
           class="collapse-expand}">
            {{ trans('models.order.attributes.goods_info') }}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseGoods" class="collapse show" role="tabpanel" aria-labelledby="Goods">
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('amount', trans('models.order.attributes.amount'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('amount', numberFormat($entity->amount), ['placeholder' => $entity->tA('amount'),
                    'class' => 'number-input', 'id' => 'amount']) !!}
                    <div class="input-group-prepend">
                        {!! MyForm::dropDown('currency_id', $entity->currency_id, $currencyList, false, ['class' =>
                        'form-group-right minimal']) !!}

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('commission_value', trans('models.order.attributes.commission_value'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('commission_value', numberFormat($entity->commission_value), ['placeholder' =>
                    $entity->tA('commission_value'), 'class' => 'number-input', 'id' => 'commission_value']) !!}
                    <div class="input-group-prepend">
                        {!! MyForm::dropDown('commission_type', $entity->commission_type, $commissionType, false,
                        ['class' => 'form-group-right minimal', 'id' => 'commission_type']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('commission_amount', $entity->tA('commission_amount'), [], false) !!}
                {!! MyForm::text('commission_amount', numberFormat($entity->commission_amount), ['class' =>
                'number-input', 'id' => 'commission_amount', 'disabled' => true]) !!}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('cod_amount', trans('models.order.attributes.cod_amount'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('cod_amount', numberFormat($entity->cod_amount), ['placeholder' =>
                    $entity->tA('cod_amount'), 'class' => 'number-input', 'id' => 'cod_amount']) !!}
                    <div class="input-group-prepend">
                        {!! MyForm::dropDown('cod_currency_id', $entity->cod_currency_id, $currencyList, false, ['class'
                        => 'form-group-right minimal']) !!}

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('is_insured_goods', $entity->tA('is_insured_goods'), [], false) !!}
                <input hidden="hidden" name="is_insured_goods" id="form_is_insured_goods"
                       value="{{ $entity->is_insured_goods }}"/>
                <div>
                    {!! MyForm::checkbox('switchery_is_insured_goods', $entity->is_insured_goods,
                    $entity->is_insured_goods == 1 ? true : false, ['data-plugin' => 'switchery', 'data-color' =>
                    '#11509b', 'class' => 'switchery', 'id' => 'switchery_is_insured_goods']) !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="wrap-add-field {{ count($entity->goods) > 0 ? 'hide' : '' }}">
                <div class="wrap-add-btn crm-flex goods-search" data-type="multiple">
                    <span class="ic-add-blue-16">
                        <i class="fa fa-plus"></i><span class="text">Chọn hàng hóa </span>
                    </span>
                </div>
            </div>
            <div class="goods-table">
                <div class="row ">
                    <div class="col-12">
                        <table class="table table-bordered table-goods {{ count($entity->goods) > 0 ? '' : 'hide' }}">
                            <thead id="head_content">
                            <tr class="active">
                                <td style="width: 250px"><b>{{ trans('models.goods_type.attributes.title') }}</b>
                                </td>
                                <td style="width: 100px">
                                    <b>{{ trans('models.order.attributes.goods_quantity') }}</b></td>
                                <td style="width: 250px"><b>{{ trans('models.order.attributes.goods_unit') }}</b>
                                </td>
                                <td style="width: 140px"><b>{{ trans('models.order.attributes.goods_insured') }}</b>
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
                                        <input class="form-control" type="hidden" data-field="goods_type_id"/>
                                        <input class="form-control" type="text" data-field="goods_type"
                                               readonly="readonly"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input class="number-input form-control" type="text" value="1"
                                               data-field="quantity"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        {!! MyForm::dropDown('', null, $goodsUnits, false, [
                                        'class' => 'form-control
                                        form-group-right minimal',
                                        'data-field' => 'goods_unit_id',
                                        ]) !!}
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
                                        <input type="text" id="" class="form-control" data-field="note">
                                    </div>
                                </td>
                                <td class="text-center text-middle">
                                    <a class="delete-goods d-inline-block" href="#" title="Xóa">
                                        <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
                                    </a>
                                </td>
                            </tr>
                            @foreach ($entity->goods as $key => $goods)
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <input class="form-control" name="goods[{{ $key }}][goods_type_id]"
                                                   type="hidden" value="{{ $goods['goods_type_id'] }}"
                                                   data-field="goods_type_id"/>
                                            <input class="form-control" readonly="readonly" type="text"
                                                   name="goods[{{ $key }}][goods_type]"
                                                   value="{{ isset($goodsTypes[$goods['goods_type_id']]) ? $goodsTypes[$goods['goods_type_id']] : '' }}"
                                                   data-field="goods_type"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input class="number-input form-control"
                                                   name="goods[{{ $key }}][quantity]" type="text"
                                                   value="{{ $goods['quantity'] }}" data-field="quantity"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            {!! MyForm::dropDown('goods[' . $key . '][goods_unit_id]',
                                            empty($goods['goods_unit_id']) ? 0 : $goods['goods_unit_id'],
                                            $goodsUnits, false, ['class' => 'form-control form-group-right minimal',
                                            'data-field' => 'goods_unit_id']) !!}
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
                                            'class' => 'number-input', 'readonly' => 'true', 'data-field' => 'weight']) !!}

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
                                            <input type="text" class="form-control" name="goods[{{ $key }}][note]"
                                                   value="{!!  $goods['note'] !!}">
                                        </div>
                                    </td>
                                    <td class="text-center text-middle">
                                        <a class="delete-goods d-inline-block" href="#" title="Xóa">
                                            <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
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
                                {!! MyForm::label('weight', $entity->tA('weight'), [], false) !!} (kg)
                            </div>
                            <div class="col-6">
                                {!! MyForm::text('weight', numberFormat($entity->weight), ['placeholder' =>
                                $entity->tA('weight'), 'class' => 'number-input']) !!}
                            </div>
                        </div>
                        <div class="row" style="padding: 4px">
                            <div class="col-6">
                                {!! MyForm::label('volume', $entity->tA('volume'), [], false) !!} (m3)
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
        <div class="form-group row">
            <div class="col-md-12">
                {!! MyForm::label('good_details', $entity->tA('good_details'), [], false) !!}
                {!! MyForm::textarea('good_details', $entity->good_details, ['placeholder' =>
                $entity->tA('good_details'), 'rows' => 2]) !!}
            </div>
        </div>

    </div>
</div>

{{--Thông tin diễn giải--}}
<div class="card-header" role="tab" id="headingGoods">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseNote" aria-expanded="true" aria-controls="collapseNote"
           class="collapse-expand">
            {{ trans('models.order.attributes.note_info') }}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseNote" class="collapse show" role="tabpanel" aria-labelledby="note_info">
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-12">
                {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                {!! MyForm::textarea('note', $entity->note, ['placeholder' => $entity->tA('note'), 'rows' => 3]) !!}
            </div>
        </div>
    </div>
</div>
{{--Thông tin sản lượng ĐHKH--}}
{{--<div class="card-header" role="tab" id="headingOrderCustomer">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseOrderCustomer" aria-expanded="false"
           aria-controls="collapseOrderCustomer" class="collapse-expand">
            {{ trans('models.order.attributes.goods_order_customer') }}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseOrderCustomer" class="collapse" role="tabpanel" aria-labelledby="goods_order_customer">
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('quantity_order_customer', $entity->tA('quantity_order_customer'), [], false) !!}
                {!! MyForm::text('quantity_order_customer', numberFormat($entity->quantity_order_customer),
                ['placeholder' => $entity->tA('quantity_order_customer'), 'class' => 'number-input']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('weight_order_customer', $entity->tA('weight_order_customer'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('weight_order_customer', numberFormat($entity->weight_order_customer),
                    ['placeholder' => $entity->tA('weight_order_customer'), 'class' => 'number-input']) !!}
                    <div class="input-group-prepend">
                        <span class="input-group-text form-group-right">
                            kg
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('volume_order_customer', $entity->tA('volume_order_customer'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('volume_order_customer', numberFormat($entity->volume_order_customer),
                    ['placeholder' => $entity->tA('volume_order_customer'), 'class' => 'number-input']) !!}
                    <div class="input-group-prepend">
                        <span class="input-group-text form-group-right">
                            m3
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>--}}
{{--Thông tin thanh toán--}}
<div class="card-header" role="tab" id="headingPayment">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapsePayment" aria-expanded="false" aria-controls="collapsePayment"
           class="collapse-expand">
            {{ trans('models.order.attributes.payment_info') }}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapsePayment" class="collapse" role="tabpanel" aria-labelledby="payment_info">
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('orderPayment[payment_type]', $entity->tA('payment_type'), [], false) !!}
                {!! MyForm::dropDown('orderPayment[payment_type]', $entity->tryGet('orderPayment')->payment_type,
                config('system.order_payment_type'), false, ['class' => 'select2 minimal']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('orderPayment[payment_user_id]', $entity->tA('payment_user_id'), [], false) !!}
                {!! MyForm::dropDown('orderPayment[payment_user_id]', $entity->tryGet('orderPayment')->payment_user_id,
                $userAdminList, true, ['class' => 'select2 minimal']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('orderPayment[vat]', $entity->tA('vat'), [], false) !!}
                <input hidden="hidden" name="vat" id="vat" value="{{ $entity->tryGet('orderPayment')->vat }}"/>
                <div>
                    {!! MyForm::checkbox('switchery_vat_default', $entity->tryGet('orderPayment')->vat,
                    $entity->tryGet('orderPayment')->vat == 1 ? true : false, ['data-plugin' => 'switchery',
                    'data-color' => '#11509b', 'class' => 'switchery', 'id' => 'switchery_vat_default']) !!}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('orderPayment[goods_amount]', $entity->tA('goods_amount'), [], false) !!}
                {!! MyForm::text('orderPayment[goods_amount]',
                numberFormat($entity->tryGet('orderPayment')->goods_amount), ['placeholder' =>
                $entity->tA('goods_amount'), 'class' => 'number-input', 'id' => 'goods_amount']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('orderPayment[anonymous_amount]', $entity->tA('anonymous_amount'), [], false) !!}
                {!! MyForm::text('orderPayment[anonymous_amount]',
                numberFormat($entity->tryGet('orderPayment')->anonymous_amount), ['placeholder' =>
                $entity->tA('anonymous_amount'), 'class' => 'number-input', 'id' => 'anonymous_amount']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('final_amount', 'Doanh thu', [], false) !!}
                {!! MyForm::text('final_amount', numberFormat($entity->amount - $entity->commission_amount -
                $entity->tryGet('orderPayment')->anonymous_amount), ['class' => 'number-input', 'id' => 'final_amount',
                'disabled' => true]) !!}
            </div>
        </div>
    </div>
</div>
