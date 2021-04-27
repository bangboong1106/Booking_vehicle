<script>
    let comboLocationGroupUri = '{{route('location-group.combo-location-group')}}';
    let comboVehicleTeamUri = '{{route('vehicle-team.combo-vehicle-team')}}';
    let backendUri = '{{getBackendDomain()}}';
    var token = '{!! csrf_token() !!}';
</script>
<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{$entity->id}}">
        {!! MyForm::model($entity, ['route' => ['payroll.valid', $entity->id]])!!}
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
                                                {!! MyForm::label('description', $entity->tA('description'), [], false) !!}
                                                {!! MyForm::textarea('description', $entity->description , ['rows'=> 2, 'placeholder'=>$entity->tA('description')]) !!}
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
                                        {{--Ngày áp dụng--}}
                                        <div class="advanced form-group row">
                                            <div class="col-md-3">
                                                {!! MyForm::label('date_from', $entity->tA('date_from'), [], false) !!}
                                                {!! MyForm::text('date_from', $entity->date_from,['placeholder'=>$entity->tA('date_from'), 'class' => 'datepicker date-input']) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! MyForm::label('date_to', $entity->tA('date_to'), [], false) !!}
                                                {!! MyForm::text('date_to', $entity->date_to,['placeholder'=>$entity->tA('date_to'), 'class' => 'datepicker date-input']) !!}
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

                                        <table class="table table-bordered table-hover table-formula">
                                            <thead id="head_content">
                                            <tr class="active">
                                                <th style="font-size: 14px; font-weight: bold;">
                                                    Nhóm địa điểm nhận
                                                </th>
                                                <th style="font-size: 14px; font-weight: bold;">
                                                    Nhóm địa điểm trả
                                                </th>
                                                <th style="font-size: 14px; font-weight: bold;">
                                                    Chủng loại xe
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
                                                        <td>
                                                            <div class="input-group" style="width: 300px">
                                                                <select class="formula select-location-group destination form-control"
                                                                        name="formulas[{{$index}}][location_group_destination_id]"
                                                                        id="formulas[{{$index}}][location_group_destination_id]">
                                                                    @if($formula['location_group_destination_id'])
                                                                        <option value="{{$formula['location_group_destination_id']}}"
                                                                                selected="selected"
                                                                                title="{{isset($locationGroupList[$formula['location_group_destination_id']]) ? $locationGroupList[$formula['location_group_destination_id']] :''}}">
                                                                            {{isset($locationGroupList[$formula['location_group_destination_id']]) ? $locationGroupList[$formula['location_group_destination_id']] :''}}</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <div class="input-group" style="width: 300px">
                                                                <select class="formula select-location-group arrival form-control"
                                                                        name="formulas[{{$index}}][location_group_arrival_id]"
                                                                        id="formulas[{{$index}}][location_group_arrival_id]">
                                                                    @if($formula['location_group_arrival_id'])
                                                                        <option value="{{$formula['location_group_arrival_id']}}"
                                                                                selected="selected"
                                                                                title="{{isset($locationGroupList[$formula['location_group_arrival_id']]) ? $locationGroupList[$formula['location_group_arrival_id']] :''}}">
                                                                            {{isset($locationGroupList[$formula['location_group_arrival_id']]) ? $locationGroupList[$formula['location_group_arrival_id']] :''}}</option>
                                                                    @endif
                                                                </select>
                                                            </div>

                                                        </td>
                                                        <td class="condition-group">
                                                            <div class="input-group" style="width: 200px">
                                                                <div style="width: 100%;">
                                                                    <select class="formula select2 condition formula_1 select-vehicle-group"
                                                                            name="formulas[{{$index}}][vehicle_group_id]"
                                                                            id="formulas[{{$index}}][vehicle_group_id]">
                                                                        <option></option>
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
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group" style="width: 150px">
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
                                                    <td>
                                                        <div class="input-group" style="width: 300px">
                                                            <select class="formula select-location-group destination form-control"
                                                                    name="formulas[0][location_group_destination_id]"
                                                                    id="formulas[0][location_group_destination_id]">
                                                            </select>
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="input-group" style="width: 300px">
                                                            <select class="formula select-location-group arrival form-control"
                                                                    name="formulas[0][location_group_arrival_id]"
                                                                    id="formulas[0][location_group_arrival_id]">
                                                            </select>
                                                        </div>

                                                    </td>
                                                    <td class="condition-group">
                                                        <div class="input-group" style="width: 200px">
                                                            <div style="width: 100%;">
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
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="input-group" style="width: 150px">
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
                                                <td colspan="1"></td>
                                                <td colspan="2" class="text-right">Tổng số công thức <span
                                                            class="row-number">{{isset($entity->formulas) && count($entity->formulas) > 0 ? count($entity->formulas) : 1}}</span>
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