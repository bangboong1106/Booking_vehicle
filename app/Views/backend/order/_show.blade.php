<div id='order_show' class="form-info-wrap" data-id="{{$entity->id}}" data-code="{{$entity->order_code}}"
     data-quicksave='{{route('order.quickSave')}}' data-entity='order'>
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
                        <li><a class="list-info" data-dest="headingStatus"
                               href="#">{{trans('models.order.attributes.customer_and_status')}}</a></li>
                        <li><a class="list-info" data-dest="headingArrivalDestination"
                               href="#">{{trans('models.order.attributes.destination_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingArrival"
                               href="#">{{trans('models.order.attributes.arrival_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingVehicle"
                               href="#">{{trans('models.order.attributes.choose_vehicle_driver')}}</a></li>
                        <li><a class="list-info" data-dest="headingGoods"
                               href="#"> {{trans('models.order.attributes.goods_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingPayment"
                               href="#"> {{trans('models.order.attributes.payment_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> Thông tin hệ thống</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingFiles"
                               href="#">{{trans('models.order.attributes.files_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingHistory"
                               href="#">{{trans('models.order.attributes.history_info')}}</a></li>
                        <li><a class="list-info" data-dest="orderReview"
                               href="#">{{trans('models.order.attributes.order_review')}}</a></li>
                        <li><a class="list-info" data-dest="headingAuditing"
                               href="#">{{trans('models.auditing.name')}}</a></li>
                    </ul>
                </li>
                <hr/>
                <li>
                    <div class="qr-wrap" style="font-style: italic;">
                        <div class="text-center">
                            <img src="data:image/png;base64, {!! 
                            ($qrcode) !!} ">
                        </div>
                        <div class="text-center">
                            <button style="border: none; background: none; color: #007bfe;" id="download-qr-code"
                                    onclick="downloadQRCode()">Tải mã QR
                            </button>
                        </div>
                        <hr/>
                        <div>Bạn có thể sử dụng CAMERA trên ứng dụng {{config('constant.APP_NAME')}} Quản trị, điều hành
                            và khách hàng để xem chi
                            tiết đơn hàng thông qua mã QR
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? "width-related-list" : "" }}">
        <ul class="list-group">
            <li class="list-group-item detail-info">
                <div class="row">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            {{--                            @include('backend.order.detail_to_action')--}}
                        </div>
                    @endif
                    <div class="col-md-12 content-detail">
                        <div class="{{isset($showAdvance) ? 'first' : ''}} card-header" role="tab"
                             id="headingInformation">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                   aria-controls="collapseInformation" class="">
                                    {{trans('models.order.attributes.information')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="padding: 12px">
                            <div class="card-body">
                                @if(!empty($entity->extend_cost) && $entity->extend_cost != 0 )
                                    <div class="stamp">
                                        <div class="box cost">
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'order_code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'order_no','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'bill_no','isEditable' => false])
                                </div>
                                @if(env('SUPPORT_CAR_TRANSPORTATION', false))
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'model_no','isEditable' => false])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'vin_no','isEditable' => false])
                                    </div>
                                @endif
                                {{--Khách hàng--}}
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_id', 'isEditable' => false, 'value' => isset($entity->customer) ? $entity->customer->full_name : "-"])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_name','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_mobile_no','isEditable' => false])
                                </div>
                                {{--Ngày đặt hàng--}}
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'order_date', 'controlType' =>'date','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'is_merge_item',
                                        'controlType' => 'bool',
                                        'isEditable' => false
                                        ])
                                </div>

                            </div>
                        </div>

                        {{--Thông tin trạng thái đơn hàng --}}
                        <div class="card-header" role="tab" id="headingStatus">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseStatus"
                                   aria-expanded="true" aria-controls="collapseStatus">
                                    {{trans('models.order.attributes.customer_and_status')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseStatus" class="collapse show" role="tabpanel" style="padding: 12px">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        {!! MyForm::label('precedence', $entity->tA('precedence'), [], false) !!}
                                        <br/>
                                        <span class="view-control">
                                            <label>{!! $entity->getPrecedence() !!}(</label>
                                            @if($entity->precedence == config('constant.ORDER_PRECEDENCE_SPECIAL'))
                                                <span class="fa fa-star text-warning"></span>
                                                <span class="fa fa-star text-warning"></span>
                                                <span class="fa fa-star text-warning"></span>
                                            @endif
                                            @if($entity->precedence == config('constant.ORDER_PRECEDENCE_NORMAL'))
                                                <span class="fa fa-star text-warning"></span>
                                                <span class="fa fa-star text-warning"></span>
                                            @endif
                                            @if($entity->precedence == config('constant.ORDER_PRECEDENCE_LOW'))
                                                <span class="fa fa-star text-warning"></span>
                                            @endif
                                            <label>)</label>
                                        </span>
                                    </div>
                                    <div class="col-md-4" style="padding: 6px">
                                        {!! MyForm::label('status', $entity->tA('status'), [], false) !!}
                                        <br/>
                                        <span class="view-control">
                                            @if($entity->status == config("constant.TAI_XE_XAC_NHAN"))
                                                <span class="status bg-stpink text-white"> {!! $entity->getStatus() !!}</span>
                                            @elseif($entity->status == config("constant.CHO_NHAN_HANG"))
                                                <span class="status bg-brown text-white"> {!! $entity->getStatus() !!}</span>
                                            @elseif($entity->status == config("constant.DANG_VAN_CHUYEN"))
                                                <span class="status bg-blue text-white"> {!! $entity->getStatus() !!}</span>
                                            @elseif($entity->status == config("constant.HOAN_THANH"))
                                                <span class="status bg-success text-white"> {!! $entity->getStatus() !!}</span>
                                            @elseif($entity->status == config("constant.HUY"))
                                                <span class="status bg-dark text-white"> {!! $entity->getStatus() !!}</span>
                                            @else
                                                <span class="status bg-secondary text-white"> {!! $entity->getStatus() !!}</span>
                                            @endif
                                        </span>
                                    </div>
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'is_collected_documents',
                                        'controlType' => 'bool',
                                        'isEditable' => false
                                        ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                                    'property' => 'status_collected_documents',
                                                    'isEditable' => false,
                                                    'controlType'=>'label',
                                                    'value'=>  '<span id="status_collected_documents">'.$entity->getStatusDocuments().'</span>'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                      'property' => 'datetime_collected_documents',
                                      'isEditable' => false,
                                      'controlType'=>'label',
                                      'value'=>  isset($entity->date_collected_documents) ? format($entity->date_collected_documents,'d-m-Y').' '.format($entity->time_collected_documents,'H:i') : '-'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                      'property' => 'datetime_collected_documents_reality',
                                      'isEditable' => false,
                                      'controlType'=>'label',
                                      'value'=>  isset($entity->date_collected_documents_reality) ? format($entity->date_collected_documents_reality,'d-m-Y').' '.format($entity->time_collected_documents_reality,'H:i') : '-'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                      'property' => 'num_of_document_page',
                                      'isEditable' => false,
                                      'controlType'=>'number',
                                      'value'=>  isset($entity->num_of_document_page) ? '' . $entity->num_of_document_page : '-'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                      'property' => 'document_type',
                                      'isEditable' => false,
                                      'controlType'=>'label',
                                      'value'=>  isset($entity->document_type) ? '' . $entity->document_type : '-'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                      'property' => 'document_note',
                                      'isEditable' => false,
                                      'controlType'=>'label',
                                      'value'=>  isset($entity->document_note) ? '' . $entity->document_note : '-'
                                    ])
                                    @if($entity->status_partner == config('constant.PARTNER_YEU_CAU_SUA'))
                                        @include('layouts.backend.elements.detail_to_edit',[
                                         'property' => 'reason',
                                         'isEditable' => false,
                                         'controlType'=>'label',
                                         'value'=>  isset($entity->reason) ? '' . $entity->reason : '-'
                                       ])
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{--Thông tin giao hàng--}}
                        <div class="card-header" role="tab" id="headingArrivalDestination">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseArrivalDestination"
                                   aria-expanded="true" aria-controls="collapseArrival">
                                    {{trans('models.order.attributes.destination_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseArrivalDestination" class="collapse show" role="tabpanel"
                             aria-labelledby="Destination"
                             style="padding: 12px">
                            <div class="card-body">
                                @if(isset($entity->locationDestinations))
                                    @if(count($entity->locationDestinations) > 1)
                                        <div class="form-group row location-wrap">
                                            <div class="location-item location-header col-md-10 row">
                                                <div class="col-md-4 edit-group-control">
                                                    <label>{{trans('models.order.attributes.etd')}}</label>
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETD', $entity->tA('ETD'), [], false) !!}
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETD_reality', $entity->tA('ETD_reality'), [], false) !!}
                                                </div>
                                            </div>
                                            @foreach($entity->locationDestinations as $index => $locationDestination)
                                                @if(empty($locationDestination['location_id'])) @continue @endif
                                                <div class="location-item col-md-10 row">
                                                    <div class="col-md-4">
                                                         <span class=" disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! isset($locations[$locationDestination['location_id']]) ? urlencode($locations[$locationDestination['location_id']]) : (isset($locationDestination['location_id']) ? urlencode($locationDestination['location_id']) : '' ) !!}">
                                                            {!! isset($locations[$locationDestination['location_id']]) ? $locations[$locationDestination['location_id']] : (isset($locationDestination['location_id']) ? $locationDestination['location_id'] : '') !!}
                                                        </a>
                                                         </span>
                                                    </div>
                                                    @if($index==0)
                                                        <div class="col-md-4">
                                                            <span class=" disabled">
                                                                {{format($locationDestination['date'],'d-m-Y').' '.format($locationDestination['time'],'H:i')}}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4 ">
                                                             <span class=" disabled">
                                                            {{format($entity->ETD_date_reality,'d-m-Y').' '.format($entity->ETD_time_reality,'H:i')}}</span>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 ">
                                                             <span class=" disabled">
                                                            {{format($locationDestination['date'],'d-m-Y').' '.format($locationDestination['time'],'H:i')}}</span>
                                                        </div>
                                                        {{--<div class="col-md-4">
                                                            {{format($locationDestination['date_reality'],'d-m-Y').' '.format($locationDestination['time_reality'],'H:i')}}
                                                        </div>--}}
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        @foreach($entity->locationDestinations as $index => $locationDestination)
                                            <div class="form-group row">
                                                <div class="col-md-4 edit-group-control">
                                                    <label for="location_destination_id">{{trans('models.order.attributes.etd')}}</label>
                                                    <br/>
                                                    @if(empty($locationDestination['location_id']))
                                                        <span>-</span>
                                                    @else
                                                        <span class="view-control disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! isset($locations[$locationDestination['location_id']]) ? urlencode($locations[$locationDestination['location_id']]) : (isset($locationDestination['location_id']) ? urlencode($locationDestination['location_id']) : '') !!}">
                                                            {!! isset($locations[$locationDestination['location_id']]) ? $locations[$locationDestination['location_id']] : (isset($locationDestination['location_id']) ? $locationDestination['location_id'] : '') !!}
                                                        </a>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETD', $entity->tA('ETD'), [], false) !!}
                                                    <br/>
                                                    <span class="view-control disabled">
                                                    {{format($locationDestination['date'],'d-m-Y').' '.format($locationDestination['time'],'H:i')}}
                                                    </span>
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETD_reality', $entity->tA('ETD_reality'), [], false) !!}
                                                    <br/>
                                                    @if($index==0)
                                                        <span class="view-control disabled">
                                                        {{isset($entity->ETD_date_reality) ? format($entity->ETD_date_reality,'d-m-Y').' '.format($entity->ETD_time_reality,'H:i') :'-'}}
                                                        </span>
                                                    @else
                                                        {{--{{format($locationDestination['date_reality'],'d-m-Y').' '.format($locationDestination['time_reality'],'H:i')}}--}}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'number_of_delivery_points', 'controlType'=>'number','isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_mobile_no_destination','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_name_destination','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_email_destination','isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'loading_destination_fee',
                                        'controlType'=>'number',
                                        'append' => 'VND',
                                        'isEditable' => false
                                    ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'informative_destination', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea','isEditable' => false])
                                </div>
                            </div>
                        </div>

                        {{--Thông tin nhận hàng--}}
                        <div class="card-header" role="tab" id="headingArrival">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseArrival"
                                   aria-expanded="true" aria-controls="collapseArrival">
                                    {{trans('models.order.attributes.arrival_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseArrival" class="collapse show" role="tabpanel" aria-labelledby="Destination"
                             style="padding: 12px">
                            <div class="card-body">
                                @if(isset($entity->locationArrivals))
                                    @if(count($entity->locationArrivals) > 1)
                                        <div class="form-group row location-wrap">
                                            <div class="location-item location-header col-md-10 row">
                                                <div class="col-md-4">
                                                    <label for="location_arrival_id">{{trans('models.order.attributes.eta')}}</label>
                                                </div>
                                                <div class="col-md-4">
                                                    {!! MyForm::label('ETA', $entity->tA('ETA'), [], false) !!}
                                                </div>
                                                <div class="col-md-4">
                                                    {!! MyForm::label('ETA_reality', $entity->tA('ETA_reality'), [], false) !!}
                                                </div>
                                            </div>
                                            @foreach($entity->locationArrivals as $index => $locationArrival)
                                                @if(empty($locationArrival['location_id'])) @continue @endif
                                                <div class="location-item col-md-10 row">
                                                    <div class="col-md-4 edit-group-control">
                                                         <span class=" disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! isset($locations[$locationArrival['location_id']]) ? urlencode($locations[$locationArrival['location_id']]) : (isset($locationArrival['location_id']) ? urlencode($locationArrival['location_id']) : '') !!}">
                                                            {!! isset($locations[$locationArrival['location_id']]) ? $locations[$locationArrival['location_id']] : (isset($locationArrival['location_id']) ? $locationArrival['location_id'] : '') !!}
                                                        </a>
                                                         </span>
                                                    </div>
                                                    @if($index==0)
                                                        <div class="col-md-4 edit-group-control">
                                                             <span class=" disabled">
                                                                 {{format($locationArrival['date'],'d-m-Y').' '.format($locationArrival['time'],'H:i')}}</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                             <span class=" disabled">
                                                                 {{format($entity->ETA_date_reality,'d-m-Y').' '.format($entity->ETA_time_reality,'H:i')}}</span>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 edit-group-control">
                                                             <span class="disabled">
                                                                 {{format($locationArrival['date'],'d-m-Y').' '.format($locationArrival['time'],'H:i')}}</span>
                                                        </div>
                                                        {{-- <div class="col-md-4">
                                                             {{format($locationArrival['date_reality'],'d-m-Y').' '.format($locationArrival['time_reality'],'H:i')}}
                                                         </div>--}}
                                                    @endif

                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        @foreach($entity->locationArrivals as $index => $locationArrival)
                                            <div class="form-group row">
                                                <div class="col-md-4 edit-group-control">
                                                    <label for="location_destination_id">{{trans('models.order.attributes.eta')}}</label>
                                                    <br/>
                                                    @if(empty($locationArrival['location_id']))
                                                        <span>-</span>
                                                    @else
                                                        <span class="view-control disabled">
                                                        <a target="_blank"
                                                           href="https://www.google.com/maps/search/?api=1&query={!! isset($locations[$locationArrival['location_id']]) ? urlencode($locations[$locationArrival['location_id']]) : (isset($locationArrival['location_id']) ? urlencode($locationArrival['location_id']) : '') !!}">
                                                            {!! isset($locations[$locationArrival['location_id']]) ? $locations[$locationArrival['location_id']] : (isset($locationArrival['location_id']) ? $locationArrival['location_id'] : '') !!}
                                                        </a>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETA', $entity->tA('ETA'), [], false) !!}
                                                    <br/>
                                                    <span class="view-control disabled">
                                                    {{format($locationArrival['date'],'d-m-Y').' '.format($locationArrival['time'],'H:i')}}
                                                    </span>
                                                </div>
                                                <div class="col-md-4 edit-group-control">
                                                    {!! MyForm::label('ETA_reality', $entity->tA('ETA_reality'), [], false) !!}
                                                    <br/>
                                                    @if($index==0)
                                                        <span class="view-control disabled">
                                                        {{isset($entity->ETA_date_reality) ? format($entity->ETA_date_reality,'d-m-Y').' '.format($entity->ETA_time_reality,'H:i') : '-'}}
                                                        </span>
                                                    @else
                                                        {{--{{format($locationArrival['date_reality'],'d-m-Y').' '.format($locationArrival['time_reality'],'H:i')}}--}}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'number_of_arrival_points', 'controlType'=>'number','isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_mobile_no_arrival','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_name_arrival','isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_email_arrival','isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'loading_arrival_fee',
                                        'controlType'=>'number',
                                        'append' => 'VND',
                                        'isEditable' => false
                                    ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'informative_arrival', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea','isEditable' => false])
                                </div>
                            </div>
                        </div>

                        {{--Thông tin xe và tài xế--}}
                        <div class="card-header" role="tab" id="headingVehicle">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseVehicle"
                                   aria-expanded="true" aria-controls="collapseVehicle">
                                    {{trans('models.order.attributes.choose_vehicle_driver')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseVehicle" class="collapse show" role="tabpanel"
                             aria-labelledby="headingVehicle"
                             style="padding: 12px">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'partner_id', 'isEditable' => false, 'value' => $entity->partner ? $entity->partner->full_name : '-'])
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        {!! MyForm::label('vehicle', 'Xe', [], false) !!}
                                        <br/>
                                        @if(isset($vehicle))
                                            @can('view vehicle')
                                                <a class="vehicle-detail view-control" href="#"
                                                data-show-url="{{isset($showAdvance) ? route('vehicle.show', $vehicle->id ): ''}}"
                                                data-id="{{ $vehicle->id}}">
                                                    {{$vehicle->reg_no}}
                                                </a>
                                            @else
                                                <span class="view-control" href="#">{{$vehicle->reg_no}}</span>
                                            @endcan
                                        @else
                                            <span class="view-control" href="#">-</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        {!! MyForm::label('primary_driver', $entity->tA('primary_driver'), [], false) !!}
                                        <br/>
                                        @if(isset($primary_driver))
                                            @can('view driver')
                                                <a class="driver-detail view-control" href="#"
                                                data-show-url="{{isset($showAdvance) ? route('driver.show', $primary_driver->id ): ''}}"
                                                data-id="{{ $primary_driver->id}}">
                                                    {{$primary_driver->full_name}}
                                                </a>
                                            @else
                                                <span class="view-control" href="#">{{$primary_driver->full_name}}</span>
                                            @endcan
                                        @else
                                            <span class="view-control" href="#">-</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        {!! MyForm::label('secondary_driver', $entity->tA('secondary_driver'), [], false) !!}
                                        <br/>
                                        @if(isset($secondary_driver))
                                            @can('view driver')
                                                <a class="driver-detail view-control" href="#"
                                                data-show-url="{{isset($showAdvance) ? route('driver.show', $secondary_driver->id ): ''}}"
                                                data-id="{{ $secondary_driver->id}}">
                                                    {{$secondary_driver->full_name}}
                                                </a>
                                            @else
                                                <span class="view-control" href="#">{{$secondary_driver->full_name}}</span>
                                            @endcan
                                        @else
                                            <span class="view-control" href="#">-</span>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        {!! MyForm::label('order_customer', $entity->tA('order_customer'), [], false) !!}
                                        <br/>
                                        @if( isset($order_customer))
                                            @can('view order_customer')
                                                <a class="route-detail view-control" href="#"
                                                data-show-url="{{isset($showAdvance) ? route('order-customer.show', $order_customer->id ).'?is_modal=t' : ''}}"
                                                data-id="{{ $order_customer->id}}">
                                                    {{$order_customer->code.' | '.$order_customer->order_no}}
                                                </a>
                                            @else
                                                <span class="view-control" href="#">{{$order_customer->code.' | '.$order_customer->order_no}}</span>
                                            @endcan
                                        @else
                                            <span class="view-control" href="#">-</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        {!! MyForm::label('route', $entity->tA('route'), [], false) !!}
                                        <br/>
                                        @if(isset($route))
                                            @can('view route')
                                                <a class="route-detail view-control" href="#"
                                                data-show-url="{{isset($showAdvance) ? route('route.show', $route->id ).'?is_modal=t' : ''}}"
                                                data-id="{{ $route->id}}">
                                                    {{$route->route_code .' | '. $route->name}}
                                                </a>
                                            @else
                                                <span class="view-control" href="#">{{$route->route_code .' | '. $route->name}}</span>
                                            @endcan
                                        @else
                                            <span class="view-control" href="#">-</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 warning-message">
                                        @if (!empty($messages))
                                            @foreach($messages as $message)
                                                <p class="text-warning"><i class="fa fa-exclamation-triangle"
                                                                           aria-hidden="true"></i> {{ $message }}</p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Thông tin hàng hóa--}}
                        <div class="card-header" role="tab" id="headingGoods">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseGoods"
                                   aria-expanded="true" aria-controls="collapseVehicle">
                                    {{trans('models.order.attributes.goods_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseGoods" class="collapse show" role="tabpanel" aria-labelledby="headingGoods"
                             style="padding: 12px">
                            <div class="card-body">
                                <div class="form-group row">
                                    @can('view revenue')
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'amount',
                                            'controlType' => 'number',
                                            'append' => 'VND'
                                        ])
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'commission_amount',
                                            'controlType' => 'number',
                                            'append' => 'VND'
                                        ])
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'final_amount',
                                            'controlType' => 'number',
                                            'value' => numberFormat($entity->amount - $entity->commission_amount - $entity->tryGet('orderPayment')->anonymous_amount) ,
                                            'isEditable' => false,
                                            'append' => 'VND'
                                        ])
                                    @endcan
                                </div>
                                <div class="form-group row">
                                    @can('view revenue')
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'cod_amount',
                                            'controlType' => 'number',
                                            'append' => 'VND'
                                        ])
                                    @endcan
                                    @include('layouts.backend.elements.detail_to_edit',[
                                    'property' => 'is_insured_goods',
                                    'controlType' => 'bool',
                                    ])
                                </div>

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
                                                            <td>{{ $goods['quantity'] }}</td>
                                                            <td>{{ !isset($goods['goods_unit_id']) ? '' : isset($goodsUnits[$goods['goods_unit_id']]) ?
                                                                $goodsUnits[$goods['goods_unit_id']] : '' }}</td>
                                                            <td class="text-center">
                                                                {{ empty($goods['insured_goods']) ? trans('messages.no') : trans('messages.yes') }}
                                                            </td>
                                                            <td class="text-right">{{ numberFormat($goods['weight']) }}</td>
                                                            <td class="text-right">{{ numberFormat($goods['volume']) }}</td>
                                                            <td class="text-right">{{ numberFormat($goods['total_weight']) }}</td>
                                                            <td class="text-right">{{ numberFormat($goods['total_volume']) }}</td>
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
                                    {{-- @include('layouts.backend.elements.detail_to_edit',[
                                         'property' => 'quantity',
                                         'controlType' => 'number',
                                     ])--}}
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'weight',
                                        'controlType' => 'number',
                                        'append' => 'kg',
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'volume',
                                        'controlType' => 'number',
                                        'append' => 'm3',
                                    ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'good_details', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                            </div>
                        </div>
                        {{--Thông tin diễn giải--}}
                        <div class="card-header" role="tab" id="headingGoods">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseNote"
                                   aria-expanded="true" aria-controls="collapseNote" class="collapse-expand">
                                    {{trans('models.order.attributes.note_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseNote" class="collapse show"
                             role="tabpanel" aria-labelledby="note_info"
                             style="padding: 12px">
                            <div class="card-body">
                                {{--Số km--}}
                                @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'gps_distance',
                                        'controlType' => 'number',
                                        'value'=> isset($entity->gps_distance) ? $entity->gps_distance/1000 : '-',
                                        'append' => 'km',
                                    ])
                                {{--Ghi chú--}}
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                            </div>
                        </div>

                        {{--Thông tin thanh toán--}}
                        <div class="card-header" role="tab" id="headingPayment">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapsePayment"
                                   aria-expanded="true" aria-controls="collapsePayment" class="collapse-expand">
                                    {{trans('models.order.attributes.payment_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapsePayment" class="collapse show"
                             role="tabpanel" aria-labelledby="payment_info"
                             style="padding: 12px">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                                  'property' => 'payment_type',
                                                  'isEditable' => false,
                                                  'controlType'=>'label',
                                                  'value'=>  '<span id="payment_type">'.$entity->tryGet('orderPayment')->getPaymentType() .'</span>'
                                  ])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'payment_user_id', 'value'=> $entity->tryGet('orderPayment')->tryGet('paymentUser')->username, 'isEditable' => false])
                                    <div class="col-md-4" style="padding: 6px">
                                        {!! MyForm::label('vat', $entity->tA('vat'), [], false) !!}
                                        <br/>
                                        <span class="view-control" id="vat">
                                            @if($entity->tryGet('orderPayment')->vat == 1)
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            @else
                                                <i class="fa"></i>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'goods_amount',
                                        'controlType' => 'number',
                                        'value' => numberFormat($entity->tryGet('orderPayment')->goods_amount),
                                        'append' => 'VND'
                                    ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'anonymous_amount',
                                        'controlType' => 'number',
                                        'value' => numberFormat($entity->tryGet('orderPayment')->anonymous_amount),
                                        'append' => 'VND'
                                    ])
                                </div>

                            </div>
                        </div>

                        {{--Thông tin file--}}
                        <div class="card-header" role="tab" id="headingFiles">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseFiles"
                                   aria-expanded="true" aria-controls="collapseFile">
                                    {{trans('models.order.attributes.files_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseFiles" class="collapse show" role="tabpanel" aria-labelledby="headingFiles"
                             style="padding: 12px">
                            <div class="card-body">
                                @foreach($order_status_file_list as $order_status)
                                    {{-- Bỏ file đánh giá từ khách hàng --}}
                                    @if($order_status['id'] != config("constant.FILE_REVIEW_ORDER_TYPE"))
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                {!! MyForm::label($order_status['name'], $order_status['name'] , [], false) !!}
                                                @if(isset($order_file_list[$order_status['id']]['file_id']))
                                                    @php $file_id_list = explode(';' ,$order_file_list[$order_status['id']]['file_id'])@endphp
                                                    <div class="preview-file">
                                                        @foreach($file_id_list as $file_id)
                                                            @if(isset($file_id) && !empty($file_id))
                                                                <div>
                                                                    <img src="{{ route('file.getImage', ['id' => $file_id, 'full' => true]) }}"
                                                                         class="img-fluid preview-image">
                                                                    <div class="text-center">
                                                                        <a id="download_file" class="fa fa-download"
                                                                           target="_blank"
                                                                           href="{{ route('file.downloadFile',$file_id) }}"></a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                @if($order_status['id'] != config("constant.KHOI_TAO"))
                                                    {!! trans('models.order.attributes.reason') !!} :
                                                    {!! isset($order_file_list[$order_status['id']]['reason'])? $order_file_list[$order_status['id']]['reason'] :"" !!}
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                @endforeach
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
                             role="tabpanel" aria-labelledby="note_info"
                             style="padding: 12px">
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
                        @if($show_history)
                            {{-- Lịch sử--}}
                            <div class="card-header" role="tab" id="headingHistory">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseHistory"
                                       aria-expanded="true" aria-controls="collapseHistory">
                                        {{trans('models.order.attributes.history_info')}}
                                        <i class="fa"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseHistory"
                                 class="collapse show"
                                 role="tabpanel"
                                 aria-labelledby="headingHistory"
                                 style="padding: 12px">
                                <div class="card-body content-history">
                                    @include('backend.order._order_history_list')
                                </div>
                            </div>
                        @endif
                        <div class="card-header" role="tab" id="orderReview">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseOrderReview"
                                   aria-expanded="true" aria-controls="collapseOrderReview" class="collapse-expand">
                                    {{trans('models.order.attributes.order_review')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseOrderReview" class="collapse show"
                             role="tabpanel" aria-labelledby="review_info"
                             style="padding: 12px">
                            @if (isset($orderReviewCustomer))
                                <div class="col-md-4">
                                    <span class="view-control">
                                                <label>Độ hài lòng (</label>
                                        @if($orderReviewCustomer->point == config('constant.review_point_1'))
                                            <span class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                        @endif
                                        @if($orderReviewCustomer->point == config('constant.review_point_2'))
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                        @endif
                                        @if($orderReviewCustomer->point == config('constant.review_point_3'))
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                        @endif
                                        @if($orderReviewCustomer->point == config('constant.review_point_4'))
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span style="color: grey !important" class="fa fa-star text-warning"></span>
                                        @endif
                                        @if($orderReviewCustomer->point == config('constant.review_point_5'))
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                            <span class="fa fa-star text-warning"></span>
                                        @endif
                                        <label>)</label>
                                    </span>
                                </div>
                                <br/>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            {!! MyForm::label($orderReviewCustomer->description, 'Nhận xét', [], false) !!}
                                            {!! MyForm::textarea($orderReviewCustomer->description, empty($orderReviewCustomer->description) ? '-' : $orderReviewCustomer->description, ['class'=>'view-control disabled', 'rows' => '1']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            {!! MyForm::label($orderReviewCustomer->ins_date, 'Ngày đánh giá', [], false) !!}
                                            {!! MyForm::text($orderReviewCustomer->ins_date, \Carbon\Carbon::parse($orderReviewCustomer->ins_date)->format('d-m-Y H:i'), ['class'=>'datepicker date-input view-control disabled', 'autocomplete'=>'off']) !!}
                                        </div>
                                    </div>
                                </div>
                                @foreach($order_status_file_list as $order_status)
                                    @if($order_status['id'] == config("constant.FILE_REVIEW_ORDER_TYPE"))
                                        @if(isset($order_file_list[$order_status['id']]['file_id']))
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        @php $file_id_list = explode(';' ,$order_file_list[$order_status['id']]['file_id'])@endphp
                                                        <div class="preview-file">
                                                            @foreach($file_id_list as $file_id)
                                                                @if(isset($file_id) && !empty($file_id))
                                                                    <div>
                                                                        <img src="{{ route('file.getImage', ['id' => $file_id, 'full' => true]) }}"
                                                                             class="img-fluid preview-image">
                                                                        <div class="text-center">
                                                                            <a id="download_file" class="fa fa-download"
                                                                               target="_blank"
                                                                               href="{{ route('file.downloadFile',$file_id) }}"></a>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div></div>
                            @endif
                        </div>
                        <div id="headingAuditing">
                            @if(isset($showAdvance) && isset($showAuditing) && auth()->user()->can('view auditing'))
                                @php
                                    $auditing_route = isset($auditing_route) ? $auditing_route : $routePrefix.'.auditing';
                                @endphp
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-header" role="tab" id="headingHistory">
                                            <h5 class="mb-0 mt-0 font-16">
                                                <a data-toggle="collapse" href="#collapseAuditing" aria-expanded="false"
                                                   aria-controls="collapseAuditing" id="showAuditing"
                                                   data-url="{{ route($auditing_route, $entity->id) }}">
                                                    {{ trans('models.auditing.name') }}
                                                    <i class="fa"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div class="collapse" id="collapseAuditing">
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script>
    var downloadUrl = '{{ route('file.downloadFile',-1) }}';

    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        editableFormConfig.customAfterSave = function (id, field, form) {
            if (field !== 'amount' && field !== 'commission_amount') return;
            var $amount = $("#amount");
            var $finalAmount = $('#final_amount');
            var $commissionAmount = $("#commission_amount");

            var finalAmount = 0;

            let inputAmount = $amount.val().replace(/\./g, "").replace(/,/g, '.'),
                amount = Number.isNaN(parseFloat(inputAmount)) ? 0 : parseFloat(inputAmount);
            let inputCommissionAmount = $commissionAmount.val().replace(/\./g, "").replace(/,/g, '.'),
                commissionAmount = Number.isNaN(parseFloat(inputCommissionAmount)) ? 0 : parseFloat(inputCommissionAmount);

            if (field === 'amount') {
                let commissionValue = Number('{{$entity->commission_value}}');
                let commissionType = Number('{{$entity->commission_type}}');
                switch (commissionType) {
                    case 1:
                        commissionAmount = amount * (commissionValue / 100);
                        $commissionAmount.val(commissionAmount);
                        break;
                }
                finalAmount = amount - commissionAmount;
            }
            if (field === 'commission_amount') {

                finalAmount = amount - commissionAmount;
            }

            $finalAmount.val(formatNumber(finalAmount));

        };

        allowEditableControlOnForm(editableFormConfig);


    }

    function downloadQRCode() {
        var img = $('#download-qr-code').parent().parent().find('img').attr('src');
        let a = document.createElement('a')
        a.href = img;
        a.download = $('#order_code').val() + '.png';
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a);
    }
</script>
