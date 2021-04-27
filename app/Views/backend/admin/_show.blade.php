<script>
    var editableFormConfig = {};
    allowEditableControlOnForm(editableFormConfig);
</script>
<div class="form-info-wrap" data-id="{{$entity->id}}" id="admin_model" data-quicksave=''
     data-entity='admin-team'>
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
                               href="#">{{trans('models.role.attributes.information')}}</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingPermission"
                               href="#"> {{trans('models.role.permissions.title')}}</a></li>
                        <li><a class="list-info" data-dest="headingVehicleTeam"
                               href="#"> {{trans('models.role.attributes.vehicleTeam')}}</a></li>
                        <li><a class="list-info" data-dest="headingCustomerGroup"
                               href="#"> {{trans('models.role.attributes.customerGroup')}}</a></li>
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
                                   aria-controls="collapseInformation">
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
                                    <div class="col-md-4 edit-group-control">
                                        <label>{{$entity->tA('avatar')}}</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    @if ($entity->avatar_id)
                                        <img src="{{ route('file.getImage', ['id' => $entity->avatar_id, 'full' => true]) }}"
                                             class="img-fluid" width="200px" alt="" style="border-radius: 50%; border: 1px solid #cccccc">
                                    @endif
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'username', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'email', 'isEditable'=>false])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'full_name', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'password', 'isEditable'=>false, 'value'=>$entity->passwordText()])
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-4 edit-group-control">
                                        <label for="active">{{ trans('models.admin.attributes.active') }}</label><br>
                                        <p>{{ $entity->active == 1 ? trans('models.admin.attributes.activate') :
                                                trans('models.admin.attributes.disable')}}</p>
                                    </div>
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'partner_id', 'isEditable'=>false, 'value'=> $entity->partner ? $entity->partner->full_name : '-'])
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingPermission">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapsePermission" aria-expanded="true"
                                   aria-controls="collapsePermission" class="">
                                    {{trans('models.role.permissions.title')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapsePermission" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 50% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Vai trò</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->listRole))
                                        @foreach($entity->listRole as  $index=>$role)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    {{$roles->firstWhere('name', $role) ? $roles->firstWhere('name', $role)->title : $rolePartners->firstWhere('name', $role)->title}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="card-header" role="tab"
                             id="headingVehicleTeam">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseVehicleTeam" aria-expanded="true"
                                   aria-controls="collapseVehicleTeam" class="">
                                   {{trans('models.role.attributes.vehicleTeam')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseVehicleTeam" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 50% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Đội tài xế</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->listVehicleTeam))
                                        @foreach($entity->listVehicleTeam as  $index=>$vehicleTeam)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    <a class="vehicle-team-detail" href="#"
                                                       data-show-url="{{isset($showAdvance) ? route('vehicle-team.show', $vehicleTeam) : ''}}"
                                                       data-id="{{ isset($showAdvance) ? $vehicleTeam : ''}} ">{{isset($vehicleTeamList[$vehicleTeam]) ? $vehicleTeamList[$vehicleTeam] : ''}}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="card-header" role="tab"
                             id="headingCustomerGroup">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseCustomer" aria-expanded="true"
                                   aria-controls="collapseCustomer" class="">
                                   {{trans('models.role.attributes.customerGroup')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseCustomer" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 50% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Nhóm khách hàng</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->listCustomerGroup))
                                        @foreach($entity->listCustomerGroup as  $index=>$customerGroup)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    <a class="customer-group-detail" href="#"
                                                       data-show-url="{{isset($showAdvance) ? route('customer-group.show', $customerGroup) : ''}}"
                                                       data-id="{{ isset($showAdvance) ? $customerGroup : ''}} ">{{isset($customerGroupList[$customerGroup]) ? $customerGroupList[$customerGroup] : ''}}</a>
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '-', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '-', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>