<div class="form-info-wrap" data-id="{{$entity->id}}" id="order_customer_model"
     data-quicksave='{{route('order-customer.quickSave')}}' data-entity='order_customer'>
    @if($show_history)
        <div class="related-list">
            <span class="collapse-view dIB detailViewCollapse dvLeftPanel_show"
                  onclick="showHideDetailViewLeftPanel(this);" id="dv_leftPanel_showHide" style="">
                <span class="svgIcons dIB fCollapseIn"></span>
            </span>
            <ul class="list-related-list">
                <li>
                    <span class="title">Thông tin</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingInformation"
                               href="#">{{trans('models.order.attributes.information')}}</a></li>
                        <li><a class="list-info" data-dest="headingRoute"
                               href="#">Thông tin lộ trình</a></li>
                        <li><a class="list-info" data-dest="headingCost"
                               href="#">Thông tin hàng hóa</a></li>
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> Thông tin hệ thống</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? "width-related-list" : "" }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row content-body">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            {{--                            @include('layouts.backend.elements.detail_to_action')--}}
                        </div>
                    @endif
                    <div class="col-md-12 content-detail">
                        <div class="{{isset($showAdvance) ? 'first' : ''}} card-header" role="tab"
                             id="headingInformation">
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
                                    {{--@include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])--}}
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'order_no', 'isEditable' => false])
                                    {{-- @include('layouts.backend.elements.detail_to_edit',['property' => 'name'])--}}
                                </div>
                                {{--Khách hàng--}}
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_id', 'isEditable' => false, 'value' => isset($entity->customer) ? $entity->customer->full_name : "-"])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_name','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_mobile_no','isEditable' => false])
                                </div>
                                {{--Ngày đặt hàng--}}
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'order_date', 'controlType' =>'date','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'status',
                                       'isEditable' => false,
                                       'controlType' => 'label',
                                      'value'=> $entity->getStatusOnList()])
                                </div>

                                @if(isset($entity->orders))
                                    <div class="form-group row">
                                        <?php
                                        $orderList = '';
                                        foreach ($entity->orders as $order) {
                                            $orderList .= '<a class="order-detail" href="#"
                                                              data-show-url="' . (isset($showAdvance) ? route('order.show', $order->id) : '') . '"
                                                   data-id="' . $order->id . '">' . $order->generateStatus(isset($order->status) ? $order->status : null, $order->order_code) . '</a>';
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'order',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'widthWrap'=>'col-md-12',
                                            'value'=> $orderList
                                            ])
                                    </div>
                                @endif

                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                               'property' => 'amount',
                                               'controlType' => 'number',
                                               'append' => 'VND',
                                               'isEditable' => false
                                           ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                               'property' => 'amount_estimate',
                                               'controlType' => 'number',
                                               'append' => 'VND',
                                               'isEditable' => false
                                           ])
                                </div>

                                <div class="form-group row">
                                    @if(isset($entity->ETD_date_reality))
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ETD_date_reality',
                                   'value' =>$entity->getDateTime('ETD_date_reality', 'd-m-Y').' '. $entity->getDateTime('ETD_time_reality', 'H:i'),'isEditable' => false,'controlType' => 'date'])
                                    @endif
                                    @if($entity->status == 1)
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ETA_date_reality',
                                   'value' =>$entity->getDateTime('ETA_date_reality', 'd-m-Y').' '. $entity->getDateTime('ETA_time_reality', 'H:i'),'isEditable' => false,'controlType' => 'date'])
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-header" role="tab" id="headingRoute">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseRoute" aria-expanded="true"
                           aria-controls="collapseInformation" class="collapse-expand">
                            {{trans('models.route.attributes.route_info')}}
                        </a>
                    </h5>
                </div>
                <div id="collapseRoute" class="collapse show" role="tabpanel"
                     aria-labelledby="headingOne"
                     style="">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-4 edit-group-control">
                                {!! MyForm::label('location_destination', $entity->tA('location_destination'), [], false) !!}
                                <br/>
                                @if(empty($entity->locationDestination))
                                    <span>-</span>
                                @else
                                    <span class="view-control disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! urlencode($entity->locationDestination->id) !!}">
                                                            {!! $entity->locationDestination->title!!}
                                                        </a>
                                                        </span>
                                @endif
                            </div>
                            <div class="col-md-4 edit-group-control">
                                {!! MyForm::label('ETD_date', $entity->tA('ETD_date'), [], false) !!}
                                <br/>
                                <span class="view-control disabled">
                                 {{format($entity->ETD_date,'d-m-Y').' '.format($entity->ETD_time,'H:i')}}
                                 </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4 edit-group-control">
                                {!! MyForm::label('location_arrival', $entity->tA('location_arrival'), [], false) !!}
                                <br/>
                                @if(empty($entity->locationArrival))
                                    <span>-</span>
                                @else
                                    <span class="view-control disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! urlencode($entity->locationArrival->id) !!}">
                                                            {!! $entity->locationArrival->title!!}
                                                        </a>
                                                        </span>
                                @endif
                            </div>
                            <div class="col-md-4 edit-group-control">
                                {!! MyForm::label('ETA_date', $entity->tA('ETA_date'), [], false) !!}
                                <br/>
                                <span class="view-control disabled">
                                                    {{format($entity->ETA_date,'d-m-Y').' '.format($entity->ETA_time,'H:i')}}
                                                    </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'distance', 'controlType' =>'number','isEditable' => false])
                        </div>
                    </div>
                </div>
                <div class="card-header" role="tab" id="headingCost">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseCost" aria-expanded="true"
                           aria-controls="collapseCost" class="collapse-expand">
                            {{trans('models.order.attributes.goods_info')}}
                            <i class="fa"></i>

                        </a>
                    </h5>
                </div>
                <div id="collapseCost" class="collapse show" role="tabpanel"
                     aria-labelledby="headingOne"
                     style="">
                    <div class="card-body cost">
                        <div class="form-group row"
                             style="position: relative; min-height: 180px; overflow: auto;">
                            <div class="col-12">
                                {!! MyForm::label('amount', trans('models.order.attributes.list_goods'), [], false) !!}
                                <div class="">
                                    <table class="table table-borderless mb-0 list-goods">
                                        <thead class="thead-light">
                                        <tr>
                                            <th style="width: 250px">{{ trans('models.order.attributes.goods_type') }}</th>
                                            <th style="width: 120px">{{ trans('models.order.attributes.quantity') }}</th>
                                            <th style="width: 120px">{{ trans('models.order.attributes.quantity_out') }}</th>
                                            <th style="width: 130px">{{ trans('models.order.attributes.goods_unit') }}</th>
                                            <th style="width: 170px">{{ trans('models.order.attributes.goods_insured') }}</th>
                                            <th style="width: 120px" class="text-right">Tải trọng</th>
                                            <th style="width: 120px" class="text-right">Dung tích</th>
                                            <th style="width: 120px"
                                                class="text-right">{{ trans('models.order.attributes.acronym_total_weight') }}</th>
                                            <th style="width: 120px"
                                                class="text-right">{{ trans('models.order.attributes.acronym_total_volume') }}</th>
                                            <th style="width: 200px">{{ trans('models.order.attributes.goods_description') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($entity->goods) == 0)
                                            <tr>
                                                <td colspan="8" style="text-align: center">Không có dữ liệu hàng
                                                    hóa
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($entity->goods as $goods)
                                                <tr>
                                                    <th scope="row">{{ $goods['goods_type'] }}</th>
                                                    <td>{{ isset($goods['quantity']) ? numberFormat($goods['quantity']) : '' }}</td>
                                                    <td>{{ isset($goods['quantity_out']) ? numberFormat($goods['quantity_out']) : '' }}</td>
                                                    <td>{{ isset($goods['goods_unit']) ? $goods['goods_unit'] : '' }}</td>
                                                    <td class="text-center">
                                                        {{ empty($goods['insured_goods']) ? trans('messages.no') : trans('messages.yes') }}
                                                    </td>
                                                    <td class="text-right">{{ isset($goods['weight']) ? numberFormat($goods['weight']) : ''}}</td>
                                                    <td class="text-right">{{ isset($goods['volume']) ? numberFormat($goods['volume']) : '' }}</td>
                                                    <td class="text-right">{{ isset($goods['total_weight']) ? numberFormat($goods['total_weight']) : ''}}</td>
                                                    <td class="text-right">{{ isset($goods['total_volume']) ? numberFormat($goods['total_volume']) : ''}}</td>
                                                    <td>{!! $goods['note'] !!}</td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            {{--  @include('layouts.backend.elements.detail_to_edit',[
                                  'property' => 'quantity',
                                  'controlType' => 'number',
                                  'isEditable' => false
                              ])--}}
                            @include('layouts.backend.elements.detail_to_edit',[
                                'property' => 'weight',
                                'controlType' => 'number',
                                'append' => 'kg',
                                'isEditable' => false
                            ])
                            @include('layouts.backend.elements.detail_to_edit',[
                                'property' => 'volume',
                                'controlType' => 'number',
                                'append' => 'm3',
                                'isEditable' => false
                            ])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'goods_detail', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea','isEditable' => false])
                        </div>

                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'route_number', 'controlType' => 'number','isEditable' => false])
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! MyForm::label('vehicle', 'Danh sách yêu cầu xe', [], false) !!}
                                <br>
                                <table class="table table-bordered table-hover">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-center">Chủng loại xe</th>
                                        <th scope="col" class="text-center">Số lượng xe</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->listVehicleGroups) && count($entity->listVehicleGroups) > 0)

                                        @foreach($entity->listVehicleGroups as  $index=>$vehicleGroup)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-center">
                                                    {{$vehicleGroup['vehicle_group_name']}}
                                                </td>
                                                <td class="text-center">
                                                    {{numberFormat($vehicleGroup['vehicle_number'])}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                Không có yêu cầu xe
                                            </td>

                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',[
                                       'property' => 'goods_amount',
                                       'controlType' => 'number',
                                       'append' => 'VND',
                                       'isEditable' => false
                                   ])
                            @include('layouts.backend.elements.detail_to_edit',[
                                'property' => 'anonymous_amount',
                                'controlType' => 'number',
                                'append' => 'VND',
                                'isEditable' => false
                            ])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',[
                                          'property' => 'payment_type',
                                          'isEditable' => false,
                                          'controlType'=>'label',
                                          'value'=>  '<span id="payment_type">'.$entity->getPaymentType() .'</span>'
                          ])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'payment_user_id', 'value'=> $entity->tryGet('paymentUser')->username, 'isEditable' => false])
                            <div class="col-md-4" style="padding: 6px">
                                {!! MyForm::label('vat', $entity->tA('vat'), [], false) !!}
                                <br/>
                                <span class="view-control" id="vat">
                                            @if($entity->vat == 1)
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    @else
                                        <i class="fa"></i>
                                    @endif
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Thông tin hệ thống--}}
                <div class="card-header" role="tab" id="headingSystem">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseSystem"
                           aria-expanded="true" aria-controls="collapseNote" class="collapse-expand">
                            Thông tin hệ thống
                            <i class="fa"></i>
                        </a>
                    </h5>
                </div>
                <div id="collapseSystem" class="collapse show"
                     role="tabpanel" aria-labelledby="note_info">
                    <div class="card-body">
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script>
    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        allowEditableControlOnForm(editableFormConfig);
    }
    $('.badge').tooltip();
</script>