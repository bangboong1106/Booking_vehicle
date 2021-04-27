<div class="form-info-wrap" data-id="{{$entity->id}}" id="vehicle_team_model" data-quicksave=''
     data-entity='vehicle-team'>
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
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingDriver"
                               href="#">Thông tin tài xế</a></li>
                        <li><a class="list-info" data-dest="headingVehicle"
                               href="#">Thông tin xe</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? "width-related-list" : "" }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action')
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
                             style="">
                            <div class="card-body">

                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name', 'isEditable'=>false])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'capital_driver_id', 'isEditable'=>false, 'value'=>$entity->tryGet('capital_driver')->full_name])
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab"
                             id="headingDriver">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseDriver" aria-expanded="true"
                                   aria-controls="collapseDriver" class="">
                                    Thông tin tài xế
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseDriver" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 100% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 50px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Tài xế</th>
                                        <th scope="col" class="text-left">Số điện thoại</th>
                                        <th scope="col" class="text-left">CMND</th>
                                        <th scope="col" class="text-left">Bằng lái</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->drivers))
                                        @foreach($entity->drivers as $index=>$driver)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="">
                                                    @can('view driver')
                                                        <a class="driver-left" href="#"
                                                            data-show-url="{{isset($showAdvance) ? route('driver.show', $driver->id) : ''}}"
                                                            data-id="{{ isset($showAdvance) ? $driver->id : ''}} ">{{$driver->full_name}}</a>
                                                    @else
                                                        <span>{{$driver->full_name}}</span>
                                                    @endcan
                                                </td>
                                                <td class="text-left">
                                                    <span><i class="fa fa-phone"></i></span> {{$driver->mobile_no}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->identity_no}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->driver_license}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">Không có dữ liệu</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        @if($show_history)
                            <div class="card-header" role="tab"
                                 id="headingVehicle">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseVehicle" aria-expanded="true"
                                       aria-controls="collapseVehicle" class="">
                                        Thông tin xe
                                        <i class="fa"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseVehicle" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne"
                                 style="">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover" style="width: 560px !important;">
                                        <thead id="head_content">
                                        <tr class="active">
                                            <th scope="col" style="width: 50px" class="text-center">
                                                STT
                                            </th>
                                            <th scope="col" class="text-center">Biển số</th>
                                            <th scope="col" class="text-center">Chủng loại xe</th>
                                        </tr>
                                        </thead>
                                        <tbody id="body_content">
                                        @if(isset($vehicles))
                                            @foreach($vehicles as $index=>$vehicle)
                                                <tr>
                                                    <td class="text-center">
                                                        {{$index +1}}
                                                    </td>
                                                    <td class="">
                                                        @can('view vehicle')
                                                            <a class="driver-detail" href="#"
                                                            data-show-url="{{isset($showAdvance) ? route('vehicle.show', $vehicle->id) : ''}}"
                                                            data-id="{{ isset($showAdvance) ? $vehicle->id : ''}} ">{{$vehicle->reg_no}}</a>
                                                        @else
                                                            <span>{{$vehicle->reg_no}}</span>
                                                        @endcan
                                                    </td>
                                                    <td class="">
                                                        {{$vehicle->name}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3">Không có dữ liệu</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>

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
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '-', 'isEditable' => false])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '-', 'isEditable' => false])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>