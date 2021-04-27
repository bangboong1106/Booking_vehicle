<script>
    let comboLocationGroupUri = '{{route('location-group.combo-location-group')}}';
    let comboVehicleTeamUri = '{{route('vehicle-team.combo-vehicle-team')}}';
    let backendUri = '{{getBackendDomain()}}';
    var token = '{!! csrf_token() !!}';
</script>
<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{$entity->id}}">
        {!! MyForm::model($entity, ['route' => ['price-quote.valid', $entity->id]])!!}
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
                                            <div class="col-md-6">
                                                {!! MyForm::label('code', $entity->tA('code')  . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('code', $entity->code != null ? $entity->code : $code, ['placeholder'=>$entity->tA('code')]) !!}

                                            </div>
                                            <div class="col-md-6">
                                                {!! MyForm::label('name', $entity->tA('name') . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name'), 'id'=>'name' ]) !!}
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                {!! MyForm::label('type', $entity->tA('type')) !!}
                                                {!! MyForm::dropDown('type', $entity->type, config('system.price_quote_type'), false, ['class'=>'select2'])  !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! MyForm::label('isDefault', $entity->tA('isDefault'), [], false) !!}
                                                <input hidden="hidden" name="isDefault"
                                                       id="isDefault"
                                                       value="{{ $entity->isDefault }}"/>
                                                <div>
                                                    {!! MyForm::checkbox('switchery_price_quote_default', $entity->isDefault, $entity->isDefault  == 1 ? true : false
                                                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_price_quote_default']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                {!! MyForm::label('description', $entity->tA('description'), [], false) !!}
                                                {!! MyForm::textarea('description', $entity->description , ['rows'=> 2, 'placeholder'=>$entity->tA('description')]) !!}
                                            </div>
                                        </div>
                                        {{--Ngày áp dụng--}}
                                        <div class="advanced form-group row">
                                            <div class="col-md-3">
                                                {!! MyForm::label('date_from', $entity->tA('date_from'), [], false) !!}
                                                {!! MyForm::text('date_from', \App\Common\AppConstant::convertDate($entity->date_from,'d-m-Y'),['placeholder'=>$entity->tA('date_from'), 'class' => 'datepicker date-input']) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! MyForm::label('date_to', $entity->tA('date_to'), [], false) !!}
                                                {!! MyForm::text('date_to', \App\Common\AppConstant::convertDate($entity->date_to,'d-m-Y'),['placeholder'=>$entity->tA('date_to'), 'class' => 'datepicker date-input']) !!}
                                            </div>

                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                {!! MyForm::label('isApplyAll', $entity->tA('isApplyAll'), [], false) !!}
                                                <input hidden="hidden" name="isApplyAll" id="isApplyAll"
                                                       value="{{ $entity->isApplyAll }}"/>
                                                <div>
                                                    {!! MyForm::checkbox('switchery_customer_apply_all', $entity->isApplyAll, $entity->isApplyAll  == 1 ? true : false
                                                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_customer_apply_all']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row listCustomerGroup"
                                             style="{!!$entity->isApplyAll == 0 ? '' : 'display: none'!!}">
                                            <div class="col-md-6">
                                                {!! MyForm::label('customer_groups', $entity->tA('customer_groups'), [], false) !!}
                                                <div class="input-group">
                                                    {!! MyForm::dropDown('customerGroups[]', isset($currentListCustomerGroup) ? $currentListCustomerGroup : [], $customerGroupList, false,
                                                    ['multiple' => 'multiple', 'class' => 'select2 minimal', 'style'=>'visibility: hidden']) !!}
                                                </div>
                                                @if(isset($currentListCustomerGroups))
                                                    @foreach($currentListCustomerGroups as $item)
                                                        <input type="hidden" name="current_customer_groups[]"
                                                               value="{{ $item }}">
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Danh sách công thức--}}
                                <div class="card-header" role="tab" id="headingFormula">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseFormula" aria-expanded="true"
                                           aria-controls="collapseFormula" class="collapse-expand">
                                            Danh sách công thức <i class="fa"></i>

                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseFormula" class="collapse show" role="tabpanel"
                                     aria-labelledby="headingOne"
                                     style="">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                {!! MyForm::label('isDistance', $entity->tA('isDistance'), [], false) !!}
                                                <input hidden="hidden" name="isDistance" id="isDistance"
                                                       value="{{ $entity->isDistance }}"/>
                                                <div>
                                                    {!! MyForm::checkbox('switchery_distance', $entity->isDistance, $entity->isDistance  == 1 ? true : false
                                                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_distance']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-hover table-formula">
                                            <thead id="head_content">
                                            <tr class="active">
                                                <th class="is-distance"
                                                    style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}"></th>
                                                <th class="is-distance"
                                                    style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}"></th>
                                                <th class="group_type"
                                                    colspan="2">
                                                {{ $entity->type == 4 ? 'Loại hàng hóa' : ($entity->type == 3 ? 'Tổng thể tích đơn hàng':
                                                    ($entity->type == 2 ? 'Tổng khối lượng đơn hàng': 'Chủng loại xe')) }}
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            <tr class="active">
                                                <th style="font-size: 14px; font-weight: bold; {!!$entity->isDistance == 0 ? '' : 'display: none'!!}"
                                                    class="is-distance">
                                                    Nhóm địa điểm nhận
                                                </th>
                                                <th style="font-size: 14px; font-weight: bold; {!!$entity->isDistance == 0 ? '' : 'display: none'!!}"
                                                    class="is-distance">
                                                    Nhóm địa điểm trả
                                                </th>
                                                <th style="width: 150px;font-size: 14px; font-weight: bold;">
                                                    Điều kiện
                                                </th>
                                                <th style="width: 350px;font-size: 14px; font-weight: bold;">
                                                    Giá trị
                                                </th>
                                                <th style="width: 150px; font-size: 14px; font-weight: bold;"
                                                    class="text-right">Số tiền (VND)
                                                </th>
                                                <th style="width: 80px" class="text-center"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="body_content">
                                            @if(isset($entity->formulas) && count($entity->formulas) > 0)
                                                @foreach($entity->formulas as  $index=>$formula)
                                                    <tr>
                                                        <td class="is-distance"
                                                            style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}">
                                                            <div class="input-group" style="width: 250px">
                                                                <select class="formula select-location-group destination form-control"
                                                                        name="formulas[{{$index}}][location_group_destination_id]"
                                                                        id="formulas[{{$index}}][location_group_destination_id]">
                                                                    @if(isset($formula['location_group_destination_id']))
                                                                        <option value="{{$formula['location_group_destination_id']}}"
                                                                                selected="selected"
                                                                                title="{{isset($locationGroupList[$formula['location_group_destination_id']]) ? $locationGroupList[$formula['location_group_destination_id']] :''}}">
                                                                            {{isset($locationGroupList[$formula['location_group_destination_id']]) ? $locationGroupList[$formula['location_group_destination_id']] :''}}</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                        </td>
                                                        <td class="is-distance"
                                                            style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}">
                                                            <div class="input-group" style="width: 250px">
                                                                <select class="formula select-location-group arrival form-control"
                                                                        name="formulas[{{$index}}][location_group_arrival_id]"
                                                                        id="formulas[{{$index}}][location_group_arrival_id]">
                                                                    @if(isset($formula['location_group_arrival_id']))
                                                                        <option value="{{$formula['location_group_arrival_id']}}"
                                                                                selected="selected"
                                                                                title="{{isset($locationGroupList[$formula['location_group_arrival_id']]) ? $locationGroupList[$formula['location_group_arrival_id']] :''}}">
                                                                            {{isset($locationGroupList[$formula['location_group_arrival_id']]) ? $locationGroupList[$formula['location_group_arrival_id']] :''}}</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <div class="input-group" style="width: 150px">
                                                                <select class="formula select2 select-operator"
                                                                        name="formulas[{{$index}}][operator]"
                                                                        id="formulas[{{$index}}][operator]"
                                                                        {{$entity->type == 1 || $entity->type == 4 ? 'disabled' : ''}} >
                                                                    <option {{$formula['operator'] == 'equal' ? 'selected="selected"' : ''}} value="equal">
                                                                        (=) Bằng
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'not_equal' ? 'selected="selected"' : ''}} value="not_equal">
                                                                        (!=) Khác
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'greater' ? 'selected="selected"' : ''}} value="greater">
                                                                        (>) Lớn hơn
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'less' ? 'selected="selected"' : ''}} value="less">
                                                                        (<) Nhỏ hơn
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'greater_equal' ? 'selected="selected"' : ''}} value="greater_equal">
                                                                        (>=) Lớn hơn hoặc bằng
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'less_equal' ? 'selected="selected"' : ''}} value="less_equal">
                                                                        (<=) Nhỏ hơn hoặc bằng
                                                                    </option>
                                                                    <option {{$formula['operator'] == 'in' ? 'selected="selected"' : ''}} value="in">
                                                                        (IN) Trong khoảng
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="condition-group">
                                                            <div class="input-group" style="width: 100%">
                                                                <div class="condition-vehicle-group"
                                                                     style="width: 100%; {!!$entity->type == 1 ? '' : 'display: none'!!}">
                                                                    <select class="formula select2 condition formula_1 select-vehicle-group"
                                                                            name="formulas[{{$index}}][vehicle_group_id]"
                                                                            id="formulas[{{$index}}][vehicle_group_id]"
                                                                            {{$entity->type != 1 ? 'disabled' : ''}}>
                                                                        <option>Vui lòng chọn chủng loại xe</option>
                                                                        @if($vehicleGroupList)
                                                                            @foreach($vehicleGroupList as $vehicleGroup)
                                                                                <option value="{{explode("_", $vehicleGroup)[0]}}"
                                                                                        {{isset($formula['vehicle_group_id']) && $formula['vehicle_group_id'] == explode("_", $vehicleGroup)[0] ? 'selected="selected"' : ''}}>
                                                                                    {{explode("_", $vehicleGroup)[1]}}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="condition-goods-type"
                                                                     style="width: 100%; {!!$entity->type == 4 ? '' : 'display: none'!!}">
                                                                    <select class="formula select2 select-goods-type"
                                                                            name="formulas[{{$index}}][goods_type_id]"
                                                                            id="formulas[{{$index}}][goods_type_id]"
                                                                            {{$entity->type != 4 ? 'disabled' : ''}}>
                                                                        <option>Vui lòng chọn loại hàng hoá</option>
                                                                        @if($goodsTypeList)
                                                                            @foreach($goodsTypeList as $id=>$title)
                                                                                <option value="{{$id}}"
                                                                                        {{isset($formula['goods_type_id']) && $formula['goods_type_id'] == $id ? 'selected="selected"' : ''}}>
                                                                                    {{ $title }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>

                                                                <input placeholder="Khối lượng"
                                                                       class="number-input condition formula formula_2 form-control"
                                                                       name="formulas[{{$index}}][weight_from]"
                                                                       type="text"
                                                                       id="formulas[{{$index}}][weight_from]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 2 ? '' : 'display: none'!!}"
                                                                       value="{{numberFormat(isset($formula['weight_from']) ? $formula['weight_from'] : 0) }}"/>
                                                                <input placeholder="Khối lượng"
                                                                       class="number-input condition formula formula_2 form-control"
                                                                       name="formulas[{{$index}}][weight_to]"
                                                                       type="text"
                                                                       id="formulas[{{$index}}][weight_to]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 2 && $formula['operator'] == 'in'  ? '' : 'display: none'!!}"
                                                                       value="{{ numberFormat(isset($formula['weight_to']) ? $formula['weight_to'] : 0) }}"/>
                                                                <input placeholder="Thể tích"
                                                                       class="number-input condition formula formula_3 form-control"
                                                                       name="formulas[{{$index}}][volume_from]"
                                                                       type="text"
                                                                       id="formulas[{{$index}}][volume_from]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 3 ? '' : 'display: none'!!}"
                                                                       value="{{numberFormat(isset($formula['volume_from']) ? $formula['volume_from'] : 0) }}"/>
                                                                <input placeholder="Thể tích"
                                                                       class="number-input condition formula formula_3 form-control"
                                                                       name="formulas[{{$index}}][volume_to]"
                                                                       type="text"
                                                                       id="formulas[{{$index}}][volume_to]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 3 && $formula['operator'] == 'in'  ? '' : 'display: none'!!}"
                                                                       value="{{numberFormat(isset($formula['volume_to']) ? $formula['volume_to'] : 0) }}"/>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="input-group" style="width: 150%">
                                                                <input placeholder="Số tiền"
                                                                       class="number-input formula form-control"
                                                                       name="formulas[{{$index}}][price]" type="text"
                                                                       id="formulas[{{$index}}][price]"
                                                                       aria-invalid="false"
                                                                       value="{{numberFormat(isset($formula['price']) ? $formula['price'] : 0) }}"/>
                                                            </div>
                                                        </td>
                                                        <td class="text-center text-middle">
                                                            <a class="delete-formula" href="#"
                                                               style="display:inline-block"
                                                               title="Xóa">
                                                                <i class="fa fa-trash" aria-hidden="true"
                                                                   title="Xóa"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="is-distance"
                                                        style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}">
                                                        <div class="input-group" style="width: 250px">
                                                            <select class="formula select-location-group destination form-control"
                                                                    name="formulas[0][location_group_destination_id]"
                                                                    id="formulas[0][location_group_destination_id]">
                                                            </select>
                                                        </div>

                                                    </td>
                                                    <td class="is-distance"
                                                        style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}">
                                                        <div class="input-group" style="width: 250px">
                                                            <select class="formula select-location-group arrival form-control"
                                                                    name="formulas[0][location_group_arrival_id]"
                                                                    id="formulas[0][location_group_arrival_id]">
                                                            </select>
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="input-group" style="width: 150px">
                                                            <select class="formula select2 select-operator"
                                                                    name="formulas[0][operator]"
                                                                    id="formulas[0][operator]"
                                                                    disabled>

                                                                <option value="equal">(=) Bằng</option>
                                                                <option value="not_equal">(!=) Khác</option>
                                                                <option value="greater">(>) Lớn hơn</option>
                                                                <option value="less">(<) Nhỏ hơn</option>
                                                                <option value="greater_equal">(>=) Lớn hơn hoặc bằng
                                                                </option>
                                                                <option value="less_equal">(<=) Nhỏ hơn hoặc bằng
                                                                </option>
                                                                <option value="in">(IN) Trong khoảng</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="condition-group">
                                                        <div class="input-group" style="width: 100%">
                                                            <div class="condition-vehicle-group"
                                                                 style="width: 100%; {!! !isset($entity->id) || $entity->type == 1 ? '' : 'display: none' !!}">
                                                                <select class="formula select2 condition formula_1 select-vehicle-group"
                                                                        name="formulas[0][vehicle_group_id]"
                                                                        id="formulas[0][vehicle_group_id]">
                                                                    <option></option>
                                                                    @if($vehicleGroupList)
                                                                        @foreach($vehicleGroupList as $vehicleGroup)
                                                                            <option value="{{explode("_", $vehicleGroup)[0]}}">
                                                                                {{explode("_", $vehicleGroup)[1]}}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="condition-goods-type"
                                                                 style="width: 100%; {!! $entity->type == 4 ? '' : 'display: none' !!}">
                                                                <select class="formula select2 select-goods-type"
                                                                        name="formulas[0][goods_type_id]"
                                                                        id="formulas[0][goods_type_id]">
                                                                    <option></option>
                                                                    @if($goodsTypeList)
                                                                        @foreach($goodsTypeList as $id=>$title)
                                                                            <option value="{{$id}}">
                                                                                {{ $title }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <input placeholder="Khối lượng"
                                                                   class="number-input condition formula formula_2 form-control"
                                                                   name="formulas[0][weight_from]" type="text"
                                                                   id="formulas[0][weight_from]"
                                                                   aria-invalid="false"
                                                                   style="{!!$entity->type == 2 ? '' : 'display: none'!!}"
                                                                   value="0"/>
                                                            <input placeholder="Khối lượng"
                                                                   class="number-input condition formula formula_2 form-control"
                                                                   name="formulas[0][weight_to]" type="text"
                                                                   id="formulas[0][weight_to]"
                                                                   aria-invalid="false"
                                                                   style="display: none"
                                                                   value="0"/>
                                                            <input placeholder="Thể tích"
                                                                   class="number-input condition formula formula_3 form-control"
                                                                   name="formulas[0][volume_from]" type="text"
                                                                   id="formulas[0][volume_from]"
                                                                   aria-invalid="false"
                                                                   style="{!!$entity->type == 3 ? '' : 'display: none' !!}"
                                                                   value="0"/>
                                                            <input placeholder="Thể tích"
                                                                   class="number-input condition formula formula_3 form-control"
                                                                   name="formulas[0][volume_to]" type="text"
                                                                   id="formulas[0][volume_to]"
                                                                   aria-invalid="false"
                                                                   style="display: none"
                                                                   value="0"/>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="input-group" style="width: 150%">
                                                            <input placeholder="Số tiền"
                                                                   class="number-input formula form-control"
                                                                   name="formulas[0][price]" type="text"
                                                                   id="formulas[0][price]"
                                                                   aria-invalid="false"
                                                                   value="0"/>
                                                        </div>
                                                    </td>
                                                    <td class="text-center text-middle">
                                                        <a class="delete-formula" href="#" style="display:inline-block"
                                                           title="Xóa">
                                                            <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="2">
                                                    <div id="wrap-add-formula">
                                                        <button id="btn-add-formula" class="btn btn-default btn-plus">
                                                            <span><i class="fa fa-plus" style="margin-right: 8px"></i>Thêm công thức</span>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td colspan="2" class="is-distance"
                                                    style="{!!$entity->isDistance == 0 ? '' : 'display: none'!!}"></td>
                                                <td colspan="2" class="text-right">Tổng số công thức <span
                                                            class="row-number">{{isset($entity->formulas) && count($entity->formulas) > 0 ? count($entity->formulas) : 1}}</span>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>

                                    </div>

                                </div>

                                {{--Danh sách phí rớt điểm--}}
                                <div class="card-header" role="tab" id="headingPointCharge">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapsePointCharge" aria-expanded="true"
                                           aria-controls="collapsePointCharge"
                                           class="collapse-expand}">
                                            Danh sách phí rớt điểm
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapsePointCharge" class="collapse show"
                                     role="tabpanel" aria-labelledby="pointCharge">
                                    <div class="card-body m-l-24">
                                        <table class="table table-bordered table-hover table-point-charge"
                                               style="width: 800px">
                                            <thead id="head_content">
                                            <tr class="active">
                                                <th style="width: 150px;font-size: 14px; font-weight: bold;">
                                                    Điều kiện
                                                </th>
                                                <th style="width: 250px;font-size: 14px; font-weight: bold;"
                                                    class="group_type">
                                                    {{ $entity->type == 4 ? 'Loại hàng hóa' : ($entity->type == 3 ? 'Tổng thể tích đơn hàng':
                                                    ($entity->type == 2 ? 'Tổng khối lượng đơn hàng': 'Chủng loại xe')) }}
                                                </th>
                                                <th style="width: 250px; font-size: 14px; font-weight: bold;"
                                                    class="text-center">Số tiền (VND)
                                                </th>
                                                <th style="width: 50px" class="text-center"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="body_content">
                                            @if(isset($entity->pointCharges) && count($entity->pointCharges) > 0)
                                                @foreach($entity->pointCharges as  $index=>$pointCharge)
                                                    <tr>
                                                        <td>
                                                            <div class="input-group" style="width: 150px">
                                                                <select class="formula select2 select-operator"
                                                                        name="pointCharges[{{$index}}][operator]"
                                                                        id="pointCharges[{{$index}}][operator]"
                                                                        {{$entity->type == 1 || $entity->type == 4 ? 'disabled' : ''}}>
                                                                    <option {{$pointCharge['operator'] == 'equal' ? 'selected="selected"' : ''}} value="equal">
                                                                        (=) Bằng
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'not_equal' ? 'selected="selected"' : ''}} value="not_equal">
                                                                        (!=) Khác
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'greater' ? 'selected="selected"' : ''}} value="greater">
                                                                        (>) Lớn hơn
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'less' ? 'selected="selected"' : ''}} value="less">
                                                                        (<) Nhỏ hơn
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'greater_equal' ? 'selected="selected"' : ''}} value="greater_equal">
                                                                        (>=) Lớn hơn hoặc bằng
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'less_equal' ? 'selected="selected"' : ''}} value="less_equal">
                                                                        (<=) Nhỏ hơn hoặc bằng
                                                                    </option>
                                                                    <option {{$pointCharge['operator'] == 'in' ? 'selected="selected"' : ''}} value="in">
                                                                        (IN) Trong khoảng
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="condition-group" style="width: 250px">
                                                            <div class="input-group">
                                                                <div class="condition-vehicle-group"
                                                                     style="width: 100%; {!!$entity->type == 1 ? '' : 'display: none'!!}">
                                                                    <select class="formula select2 condition formula_1 select-vehicle-group"
                                                                            name="pointCharges[{{$index}}][vehicle_group_id]"
                                                                            id="pointCharges[{{$index}}][vehicle_group_id]">
                                                                        <option></option>
                                                                        @if($vehicleGroupList)
                                                                            @foreach($vehicleGroupList as $vehicleGroup)
                                                                                <option value="{{explode("_", $vehicleGroup)[0]}}"
                                                                                        {{isset($pointCharge['vehicle_group_id']) && $pointCharge['vehicle_group_id'] == explode("_", $vehicleGroup)[0] ? 'selected="selected"' : ''}}>
                                                                                    {{explode("_", $vehicleGroup)[1]}}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="condition-goods-type"
                                                                     style="width: 100%; {!!$entity->type == 4 ? '' : 'display: none'!!}">
                                                                    <select class="formula select2 select-goods-type"
                                                                            name="pointCharges[{{$index}}][goods_type_id]"
                                                                            id="pointCharges[{{$index}}][goods_type_id]">
                                                                        <option></option>
                                                                        @if($goodsTypeList)
                                                                            @foreach($goodsTypeList as $id=>$title)
                                                                                <option value="{{$id}}"
                                                                                        {{isset($pointCharge['goods_type_id']) && $pointCharge['goods_type_id'] == $id ? 'selected="selected"' : ''}}>
                                                                                    {{ $title }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <input placeholder="Khối lượng"
                                                                       class="number-input condition formula formula_2 form-control"
                                                                       name="pointCharges[{{$index}}][weight_from]"
                                                                       type="text"
                                                                       id="pointCharges[{{$index}}][weight_from]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 2 ? '' :  'display: none'!!}"
                                                                       value="{{numberFormat(isset($pointCharge['weight_from']) ? $pointCharge['weight_from'] : 0 ) }}"/>
                                                                <input placeholder="Khối lượng"
                                                                       class="number-input condition formula formula_2 form-control"
                                                                       name="pointCharges[{{$index}}][weight_to]"
                                                                       type="text"
                                                                       id="pointCharges[{{$index}}][weight_to]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 2 && $pointCharge['operator'] == 'in' ? '' :  'display: none'!!}"
                                                                       value="{{numberFormat(isset($pointCharge['weight_to']) ? $pointCharge['weight_to'] : 0) }}"/>
                                                                <input placeholder="Thể tích"
                                                                       class="number-input condition formula formula_3 form-control"
                                                                       name="pointCharges[{{$index}}][volume_from]"
                                                                       type="text"
                                                                       id="pointCharges[{{$index}}][volume_from]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 3 ? '' :  'display: none'!!}"
                                                                       value="{{numberFormat(isset($pointCharge['volume_from']) ? $pointCharge['volume_from'] : 0) }}"/>
                                                                <input placeholder="Thể tích"
                                                                       class="number-input condition formula formula_3 form-control"
                                                                       name="pointCharges[{{$index}}][volume_to]"
                                                                       type="text"
                                                                       id="pointCharges[{{$index}}][volume_to]"
                                                                       aria-invalid="false"
                                                                       style="{!!$entity->type == 3 && $pointCharge['operator'] == 'in' ? '' :  'display: none'!!}"
                                                                       value="{{numberFormat(isset($pointCharge['volume_to']) ? $pointCharge['volume_to'] : 0) }}"/>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="input-group" style="width: 250px">
                                                                <input placeholder="Số tiền"
                                                                       class="number-input formula form-control"
                                                                       name="pointCharges[{{$index}}][price]"
                                                                       type="text"
                                                                       id="pointCharges[{{$index}}][price]"
                                                                       aria-invalid="false"
                                                                       value="{{numberFormat(isset($pointCharge['price']) ? $pointCharge['price'] : 0) }}"/>
                                                            </div>
                                                        </td>
                                                        <td class="text-center text-middle">
                                                            <a class="delete-point-charge" href="#"
                                                               style="display:inline-block"
                                                               title="Xóa">
                                                                <i class="fa fa-trash" aria-hidden="true"
                                                                   title="Xóa"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <div class="input-group" style="width: 150px">
                                                            <select class="formula select2 select-operator"
                                                                    name="pointCharges[0][operator]"
                                                                    id="pointCharges[0][operator]"
                                                                    disabled>
                                                                <option value="equal">(=) Bằng</option>
                                                                <option value="not_equal">(!=) Khác</option>
                                                                <option value="greater">(>) Lớn hơn</option>
                                                                <option value="less">(<) Nhỏ hơn</option>
                                                                <option value="greater_equal">(>=) Lớn hơn hoặc bằng
                                                                </option>
                                                                <option value="less_equal">(<=) Nhỏ hơn hoặc bằng
                                                                </option>
                                                                <option value="in">(IN) Trong khoảng</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="condition-group" style="width: 250px">
                                                        <div class="input-group">
                                                            <div class="condition-vehicle-group"
                                                                 style="width: 100%; {!! !isset($entity->id) || $entity->type == 1 ? '' : 'display: none'!!}">
                                                                <select class="formula select2 condition formula_1 select-vehicle-group"
                                                                        name="pointCharges[0][vehicle_group_id]"
                                                                        id="pointCharges[0][vehicle_group_id]">
                                                                    <option></option>
                                                                    @if($vehicleGroupList)
                                                                        @foreach($vehicleGroupList as $vehicleGroup)
                                                                            <option value="{{explode("_", $vehicleGroup)[0]}}">
                                                                                {{explode("_", $vehicleGroup)[1]}}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="condition-goods-type"
                                                                 style="width: 100%; {!! $entity->type == 4 ? '' : 'display: none'!!}">
                                                                <select class="formula select2 select-goods-type"
                                                                        name="pointCharges[0][goods_type_id]"
                                                                        id="pointCharges[0][goods_type_id]">
                                                                    <option></option>
                                                                    @if($goodsTypeList)
                                                                        @foreach($goodsTypeList as $id=>$title)
                                                                            <option value="{{$id}}">
                                                                                {{ $title }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <input placeholder="Khối lượng"
                                                                   class="number-input condition formula formula_2 form-control"
                                                                   name="pointCharges[0][weight_from]" type="text"
                                                                   id="pointCharges[0][weight_from]"
                                                                   aria-invalid="false"
                                                                   style="{!!$entity->type == 2 ? '' :  'display: none'!!}"
                                                                   value="0"/>
                                                            <input placeholder="Khối lượng"
                                                                   class="number-input condition formula formula_2 form-control"
                                                                   name="pointCharges[0][weight_to]" type="text"
                                                                   id="pointCharges[0][weight_to]"
                                                                   aria-invalid="false"
                                                                   style="display: none"
                                                                   value="0"/>
                                                            <input placeholder="Thể tích"
                                                                   class="number-input condition formula formula_3 form-control"
                                                                   name="pointCharges[0][volume_from]" type="text"
                                                                   id="pointCharges[0][volume_from]"
                                                                   aria-invalid="false"
                                                                   style="{!!$entity->type == 3 ? '' :  'display: none'!!}"
                                                                   value="0"/>
                                                            <input placeholder="Thể tích"
                                                                   class="number-input condition formula formula_3 form-control"
                                                                   name="pointCharges[0][volume_to]" type="text"
                                                                   id="pointCharges[0][volume_to]"
                                                                   aria-invalid="false"
                                                                   style="display: none"
                                                                   value="0"/>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="input-group" style="width: 250px">
                                                            <input placeholder="Số tiền"
                                                                   class="number-input formula form-control"
                                                                   name="pointCharges[0][price]" type="text"
                                                                   id="pointCharges[0][price]"
                                                                   aria-invalid="false"
                                                                   value="0"/>
                                                        </div>
                                                    </td>
                                                    <td class="text-center text-middle">
                                                        <a class="delete-point-charge" href="#"
                                                           style="display:inline-block"
                                                           title="Xóa">
                                                            <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="2">
                                                    <div id="wrap-add-point-charge">
                                                        <button id="btn-add-point-charge"
                                                                class="btn btn-default btn-plus">
                                                            <span><i class="fa fa-plus" style="margin-right: 8px"></i>Thêm phí rớt điểm</span>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td colspan="2" class="text-right">Tổng số dòng <span
                                                            class="row-number">{{isset($entity->pointCharges) && count($entity->pointCharges) > 0 ? count($entity->pointCharges) : 1}}</span>
                                                </td>

                                            </tr>
                                            </tfoot>
                                        </table>
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

<?php
$searchJsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}