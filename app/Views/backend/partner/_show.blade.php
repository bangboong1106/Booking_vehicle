<div class="form-info-wrap" data-id="{{$entity->id}}" id="customer_model"
     data-quicksave='{{route('customer.quickSave')}}'
     data-entity='customer'>
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
                        <li><a class="list-info" data-dest="headingDriver"
                               href="#"> Thông tin tài xế</a></li>
                        <li><a class="list-info" data-dest="headingVehicle"
                                href="#"> Thông tin xe</a></li>
                        <li><a class="list-info" data-dest="headingVehicleTeam"
                                href="#"> Thông tin đội xe</a></li>
                        <li><a class="list-info" data-dest="headingSystem"
                            href="#"> Thông tin hệ thống</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li>
                            <a class="list-info" data-dest="headingHistory" data-trigger="showAuditing"
                               href="#">{{trans('models.auditing.name')}}</a></li>
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
                            @include('layouts.backend.elements.detail_to_action', ['exportEntityType' => config('constant.CUSTOMER')])
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'full_name'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'email'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'mobile_no'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'delegate'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'tax_code'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'current_address', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                                {{-- <div class="">
                                    <div id="map_show"></div>
                                    <input type="hidden" id="latShow"
                                           value="{{ $entity->latitude ? $entity->latitude : 0 }}">
                                    <input type="hidden" id="lngShow"
                                           value="{{ $entity->longitude ? $entity->longitude : 0 }}">
                                </div> --}}
                            </div>
                        </div>


                        {{-- Thông tin tài xế --}}
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
                                <table class="table table-bordered table-hover" style="width: 75% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Mã tài xế</th>
                                        <th scope="col" class="text-left">Tên tài xế</th>
                                        <th scope="col" class="text-left">Số điện thoại</th>
                                        <th scope="col" class="text-left">Bằng lái xe</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($drivers))
                                        @php $driverIndex = 1 @endphp
                                        @foreach($drivers as $index => $driver)
                                            <tr>
                                                <td class="text-center">
                                                    {{$driverIndex++}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->code}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->full_name}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->mobile_no}}
                                                </td>
                                                <td class="text-left">
                                                    {{$driver->driver_license}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Thông tin xe --}}
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
                                <table class="table table-bordered table-hover" style="width: 75% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Biển số</th>
                                        <th scope="col" class="text-left">Chủng loại xe</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($vehicles))
                                        @php $vehicleIndex = 1 @endphp
                                        @foreach($vehicles as $index => $vehicle)
                                            <tr>
                                                <td class="text-center">
                                                    {{$vehicleIndex++}}
                                                </td>
                                                <td class="text-left">
                                                    {{$vehicle->reg_no}}
                                                </td>
                                                <td class="text-left">
                                                    {{$vehicle->vehicleGroup->name}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Thông tin đội xe --}}
                        <div class="card-header" role="tab"
                             id="headingVehicleTeam">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseVehicleTeam" aria-expanded="true"
                                   aria-controls="collapseVehicleTeam" class="">
                                    Thông tin đội xe
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseVehicleTeam" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 75% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Mã đội tài xế</th>
                                        <th scope="col" class="text-left">Tên đội</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($vehicleTeam))
                                        @php $vehicleTeamIndex = 1 @endphp
                                        @foreach($vehicleTeam as $index => $team)
                                            <tr>
                                                <td class="text-center">
                                                    {{$vehicleTeamIndex++}}
                                                </td>
                                                <td class="text-left">
                                                    {{$team->code}}
                                                </td>
                                                <td class="text-left">
                                                    {{$team->name}}
                                                </td>
                                            </tr>
                                        @endforeach
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                            </div>
                        </div>

                        @include('layouts.backend.elements.auditing._show_auditing')
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
</script>