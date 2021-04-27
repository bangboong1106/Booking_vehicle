<div class="form-info-wrap" data-id="{{$entity->id}}" id="route_model" data-quicksave='{{route('route.quickSave')}}'
     data-entity='route'>
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
                        @can('view cost')
                            <li><a class="list-info" data-dest="headingCost"
                                   href="#">Thông tin chi phí</a></li>
                        @endcan
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> Thông tin hệ thống</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingFiles"
                               href="#">{{trans('models.order.attributes.files_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingApproved"
                               href="#">{{trans('models.route.attributes.approved_history')}}</a></li>
                    </ul>
                </li>
                <hr/>
                <li>
                    <div class="qr-wrap" style="font-style: italic;">
                        <div class="text-center">
                            <button style="border: none; background: none; color: #007bfe;"
                                    data-url={{route('route.shipping-order', $entity->id)}}
                                    onclick="downloadShippingOrder(event)">
                                    <i class="fa fa-download" style="margin-right: 8px" />Tải lệnh vận chuyển
                            </button>
                        </div>
                        <hr/>
                        <div>Bạn có thể tải và gửi lệnh vận chuyển đến các bộ phận liên quan
                        </div>
                    </div>
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
                            @include('backend.route.detail_to_action', ['exportEntityType' => config('constant.ROUTE')])
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
                             style="padding: 12px">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'route_code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                                     'property' => 'vehicle',
                                                     'isEditable' => false,
                                                     'controlType' => 'label',
                                                     'value'=> empty($entity->vehicle) ? null : (auth()->user()->can('view vehicle') ? '<a class="vehicle-detail" href="#"
                                                                data-show-url="'.(isset($showAdvance) ? route('vehicle.show', $entity->vehicle->id ): '').'"
                                                                data-id="'.$entity->vehicle->id.'">'.$entity->vehicle->reg_no.'</a>' : $entity->vehicle->reg_no )
                                                     ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'primary_driver',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'value'=>empty($entity->driver) ? null : (auth()->user()->can('view driver') ? '<a class="driver-detail" href="#"
                                                   data-show-url="'.(isset($showAdvance) ? route('driver.show', $entity->driver->id ): '').'"
                                                   data-id="'.$entity->driver->id.'">'.$entity->driver->full_name.'</a>' : $entity->driver->full_name )
                                        ])
                                </div>
                                <div class="form-group row">
                                    <?php
                                    $orderList = '';
                                    foreach ($entity->orders as $item) {
                                        if (auth()->user()->can('view order')) {
                                            $orderList .= '<a class="order-detail" href="#" style="margin: 0 1px;"
                                                              data-show-url="' . (isset($showAdvance) ? route('order.show', $item->id) : '') . '"
                                                                data-id="' . $item->id . '">' . $item->generateStatus(isset($item->status) ? $item->status : null, $item->order_code) . '</a>';
                                        } else {
                                            $orderList .= $item->generateStatus(isset($item->status) ? $item->status : null, $item->order_code);
                                        }              
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
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'route_status',
                                     'isEditable' => false,
                                     'controlType' => 'label',
                                    'value'=> '<span class="badge badge-'.($entity->route_status == 1 ? 'success': 'secondary').'">'.($entity->getStatus()).'</span>'])
                                    <div class="col-md-4">
                                        <label for="capacity_weight_ratio">{{ trans('models.route.attributes.capacity_weight_ratio') }}</label><br>
                                        <label for="capacity_weight_ratio" id="capacity_weight_ratio"
                                               class="view-control">
                                            {{ isset($entity->capacity_weight_ratio) ? numberFormat($entity->capacity_weight_ratio) . '%' : '' }}
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="capacity_volume_ratio">{{ trans('models.route.attributes.capacity_volume_ratio') }}</label><br>
                                        <label for="capacity_volume_ratio" id="capacity_volume_ratio"
                                               class="view-control">
                                            {{ isset($entity->capacity_volume_ratio) ? numberFormat($entity->capacity_volume_ratio) . '%' : '' }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    @if(isset($entity->ETD_date_reality))
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ETD_reality',
                                   'value' =>$entity->getDateTime('ETD_date_reality', 'd-m-Y').' '. $entity->getDateTime('ETD_time_reality', 'H:i'),'isEditable' => false,'controlType' => 'date'])
                                    @endif
                                    @if($entity->route_status == 1)
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ETA_reality',
                                   'value' =>$entity->getDateTime('ETA_date_reality', 'd-m-Y').' '. $entity->getDateTime('ETA_time_reality', 'H:i'),'isEditable' => false,'controlType' => 'date'])
                                    @endif
                                </div>
                                @if($show_history)
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',[
                                         'property' => 'is_approved',
                                         'isEditable' => false,
                                         'controlType'=>'label',
                                         'value'=>  '<span id="is_approved"></span>'.(($entity->is_approved == 1) ?'<i class="fa fa-check" aria-hidden="true"></i>': '-')
                                         ])
                                        @include('layouts.backend.elements.detail_to_edit',[
                                         'property' => 'approved_id',
                                         'isEditable' => false,
                                         'value'=> isset($entity->approvedUser) ? $entity->approvedUser->username : '-'
                                         ])
                                        @include('layouts.backend.elements.detail_to_edit',[
                                       'property' => 'approved_date',
                                       'isEditable' => false,
                                       ])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'approved_note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea','isEditable'=>false])
                                    </div>
                                @endif
                            </div>

                        </div>

                        <div class="card-header" role="tab" id="headingRoute">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseRoute" aria-expanded="true"
                                   aria-controls="collapseInformation" class="collapse-expand">
                                    {{trans('models.route.attributes.route_info')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseRoute" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="padding: 12px">
                            <div class="card-body">
                                @if($entity->locations)
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Lộ trình chuyến xe</label>
                                        </div>
                                    </div>
                                    <div class=" form-group row">
                                        <div class="timeline location">
                                            @foreach($entity->locations as $location)
                                                <article class="timeline-item">
                                                    <div class="timeline-desk">
                                                        <div class="panel">
                                                            <div class="panel-body">
                                                                <span class="arrow"></span>
                                                                <span class="timeline-icon"></span>
                                                                <div class="row">
                                                                    <label class="col-md-2">Nhận hàng: </label>
                                                                    <div class="col-md-7">
                                                                        {{$location['destination_location_title']? $location['destination_location_title']:'--'}}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        {{$location['destination_location_date'] ? \Carbon\Carbon::parse($location['destination_location_date'])->format('d-m-Y').'  '.\Carbon\Carbon::parse($location['destination_location_time'])->format('H:i'):'--'}}
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <label class="col-md-2">Trả hàng: </label>
                                                                    <div class="col-md-7">
                                                                        {{$location['arrival_location_title']? $location['arrival_location_title']:'--'}}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        {{$location['arrival_location_date'] ? \Carbon\Carbon::parse($location['arrival_location_date'])->format('d-m-Y') .'  '. \Carbon\Carbon::parse($location['arrival_location_time'])->format('H:i'):'--'}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Lộ trình chuyến xe</label>
                                            <br/>--
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'location_destination_id',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'value'=> isset($entity->location_destination_title) ? $entity->location_destination_title :
                                        (isset($entity->locationDestination) ? $entity->locationDestination->title : '-')
                                        ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'ETD',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'value'=> isset($entity->ETD_date) ? $entity->ETD_date  .'  '. $entity->getDateTime('ETD_time', 'H:i'):'--'
                                        ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'location_arrival_id',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'value'=> isset($entity->location_arrival_title) ? $entity->location_arrival_title :
                                        (isset($entity->locationArrival) ? $entity->locationArrival->title : '-')
                                        ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'ETA',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'value'=> isset($entity->ETA_date) ? $entity->ETA_date .'  '. $entity->getDateTime('ETA_time', 'H:i') :'--'
                                        ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                         'property' => 'gps_distance',
                                         'controlType' => 'label',
                                         'isEditable' => false,
                                         'value'=> isset($entity->gps_distance) ? numberFormat($entity->gps_distance / 1000) : '-'
                                         ])
                                </div>
                            </div>
                        </div>
                        @can('view cost')
                            <div class="card-header" role="tab" id="headingCost">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseCost" aria-expanded="true"
                                       aria-controls="collapseCost" class="collapse-expand">
                                        {{trans('models.route.attributes.cost_info')}}
                                        <i class="fa"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseCost" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne"
                                 style="">
                                <div class="card-body cost">
                                    @if($entity->quota)
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <label>{{trans('models.route.attributes.quota')}}</label>
                                                <a class="modal-detail quota-show" href="#"
                                                   data-show-url="{{isset($showAdvance) ? route('quota.show', $entity->quota->id) : ''}}"
                                                   data-id="{{$entity->quota->id}}">
                                                    <span class="quota-name">{{$entity->quota->name}}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="list-cost">
                                        @if(isset($showAdvance))
                                            <table class="table table-bordered table-hover table-cost view">
                                                <thead id="head_content">
                                                <tr class="active">
                                                    <th scope="col" class="text-left">
                                                        {{ trans('models.route.attributes.description') }}
                                                    </th>
                                                    <th scope="col"
                                                        class="text-right">{{ trans('models.route.attributes.amount_admin') }}</th>
                                                    <th scope="col"
                                                        class="text-right">{{ trans('models.route.attributes.amount_driver') }}</th>
                                                    <th scope="col"
                                                        class="text-right">{{ trans('models.route.attributes.amount_approval') }}</th>
                                                    <th scope="col"
                                                        class="text-right">{{ trans('models.route.attributes.route_file') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="body_content">
                                                @if($entity->costs)
                                                    @foreach($entity->costs as $cost)
                                                        <tr>
                                                            <td class="text-left">
                                                                {{$cost['receipt_payment_name']}}
                                                            </td>
                                                            <td class="text-right">{{ numberFormat($cost['amount_admin']) }}</td>
                                                            <td class="text-right">{{ numberFormat($cost['amount_driver']) }}</td>
                                                            <td class="text-right">{{ numberFormat($cost['amount']) }}</td>
                                                            <td class="text-right">
                                                                @if(isset($fileCostList) && isset($fileCostList[$cost['receipt_payment_id']]))
                                                                    @foreach($fileCostList[$cost['receipt_payment_id']] as $fileId)
                                                                        <img style="width: 25px; height: 25px;"
                                                                             src="{{ route('file.getImage', ['id' => $fileId, 'full' => true]) }}"
                                                                             class="img-fluid preview-image">
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td class="text-left">
                                                        <b>{{ trans('models.route.attributes.total_quota') }}</b>
                                                    </td>
                                                    <td class="text-right">
                                                        <b>{{numberFormat($entity->total_cost_admin)}}</b></td>
                                                    <td class="text-right">
                                                        <b>{{numberFormat($entity->total_cost_driver)}}</b></td>
                                                    <td class="text-right"><b>{{numberFormat($entity->final_cost)}}</b>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table table-bordered table-hover table-cost view">
                                                <thead id="head_content">
                                                <tr class="active">
                                                    <th scope="col" class="text-left">
                                                        {{ trans('models.route.attributes.description') }}
                                                    </th>
                                                    <th scope="col"
                                                        class="text-right">{{ trans('models.route.attributes.amount_admin') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="body_content">
                                                @if($entity->listCost)
                                                    @if(count($entity->listCost) === 0)
                                                        <tr>
                                                            <td class="text-cennter" colspan="4">
                                                                <p>{{ trans('messages.no_result_found') }}</p>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @foreach($entity->listCost as $cost)
                                                            <tr>
                                                                <td class="text-left">
                                                                    {{$cost['receipt_payment_name']}}
                                                                </td>
                                                                <td class="text-right">{{numberFormat($cost['amount_admin'])}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endif
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endcan

                        @if(isset($showAdvance) && isset($auditing) && auth()->user()->can('view auditing'))
                            <div class="card-header" role="tab" id="headingAuditing">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseAuditing" aria-expanded="true"
                                       aria-controls="collapseInformation" class="collapse-expand">
                                        {{trans('models.auditing.name')}}
                                        <i class="fa"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseAuditing" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne">
                                <div class="card-body">
                                    @if($auditing)
                                        @if($auditing->count() === 0)
                                            <p>{{ trans('messages.no_result_found') }}</p>
                                        @else
                                            <table id="mainTable" class="table table-striped m-b-0">
                                                <thead>
                                                <tr>
                                                    <td>{{ trans('models.auditing.attributes.username') }}</td>
                                                    <td>{{ trans('actions.action') }}</td>
                                                    <td>{{ trans('models.auditing.attributes.old_values') }}</td>
                                                    <td>{{ trans('models.auditing.attributes.new_values') }}</td>
                                                    <td>{{ trans('models.auditing.attributes.time') }}</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($auditing as $item)
                                                    <tr>
                                                        <td>{{ $item->user->username }}</td>
                                                        <td>{{ trans('models.auditing.actions.' . $item->event) }}</td>
                                                        <td>
                                                            @foreach($item->old_values as $attr => $value)
                                                                <p class="m-0">{{ $entity->tA($attr) }}
                                                                    : {{ $value }}</p>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @foreach($item->new_values as $attr => $value)
                                                                <p class="m-0">{{ $entity->tA($attr) }}
                                                                    : {{ $value }}</p>
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $item->created_at }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{--Thông tin hệ thống--}}
                        <div class="card-header" role="tab" id="headingSystem">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseSystem" aria-expanded="true"
                                   aria-controls="collapseNote" class="collapse-expand">
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

                        {{--Thông tin đính kèm--}}
                        <div class="card-header" role="tab" id="headingFiles">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseFileInfo" aria-expanded="true"
                                   aria-controls="collapseFileInfo" class="collapse-expand">
                                    {{trans('models.order.attributes.files_info')}}
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseFileInfo" class="collapse show" role="tabpanel"
                             aria-labelledby="headingFiles"
                             style="">
                            <div class="card-body">
                                <div class="form-group row">
                                    @foreach($file_list as $file)
                                        <div class="col-md-3"><img
                                                    src="{{ route('file.getImage', ['id' => $file->file_id, 'full' => true]) }}"
                                                    class="img-fluid preview-image"></div>
                                    @endforeach
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'route_note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                            </div>
                        </div>


                        {{--Lịch sử phê duyệt--}}
                        <div class="card-header" role="tab" id="headingApproved">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseApproved" aria-expanded="true"
                                   aria-controls="collapseApproved" class="collapse-expand">
                                    {{trans('models.route.attributes.approved_history')}}
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseApproved" class="collapse show" role="tabpanel"
                             aria-labelledby="headingApproved"
                             style="">
                            <div class="card-body">
                                <table id="approvedTable" class="table table-bordered table-hover view">
                                    <thead>
                                    <tr class="active">
                                        <th>{{ trans('models.route.attributes.approved_note') }}</th>
                                        <th>{{ trans('models.route.attributes.approved_id') }}</th>
                                        <th>{{ trans('models.route.attributes.approved_date') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($approved_histories))
                                        @foreach($approved_histories as $approved_history)
                                            <tr>
                                                <td>{{$approved_history->approved_note}}</td>
                                                <td>{{$approved_history->approvedUser->username}}</td>
                                                <td>{{\Carbon\Carbon::parse($approved_history->approved_date)->format('d-m-Y H:i')}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
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

    function downloadShippingOrder(e) {
        e.preventDefault();
        var url = $(e.currentTarget).data('url');
        let a = document.createElement('a')
        a.href = url;
        $(a).attr('target', "_blank");
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a);
    }
</script>
