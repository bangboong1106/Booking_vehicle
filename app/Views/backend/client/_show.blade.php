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
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> Thông tin hệ thống</a></li>
                        <li><a class="list-info" data-dest="headingGroup"
                               href="#"> Thông tin nhóm khách hàng</a></li>
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'parent_id','value' => $entity->parent ? $entity->parent->full_name : '-', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'type', 'isEditable'=>false, 'value'=>$entity->getCustomerType()])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'full_name'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'username',
                                        'value' => isset($entity->adminUser) ? $entity->adminUser->username : null, 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'email',
                                        'value' => isset($entity->adminUser) ? $entity->adminUser->email : null, 'isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'mobile_no'])
                                    @if($entity->type == config('constant.INDIVIDUAL_CUSTOMERS'))
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'sex', 'isEditable'=>false, 'value'=>$entity->getSexText()])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'birth_date', 'controlType'=>'date'])
                                    @else
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'delegate'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'tax_code'])
                                    @endif
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'current_address', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea'])
                                </div>
                                <div class="">
                                    <div id="map_show"></div>
                                    <input type="hidden" id="latShow"
                                           value="{{ $entity->latitude ? $entity->latitude : 0 }}">
                                    <input type="hidden" id="lngShow"
                                           value="{{ $entity->longitude ? $entity->longitude : 0 }}">
                                </div>
                            </div>
                        </div>

                        <div class="card-header" role="tab"
                             id="headingCustomer">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseCustomer" aria-expanded="true"
                                   aria-controls="collapseCustomer" class="">
                                    Thông tin nhóm khách hàng
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
                                        <th scope="col" class="text-left">Người quản lý</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($listCustomerGroup))
                                        @foreach($listCustomerGroup as  $index=>$customerGroup)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    <a class="customer-group-detail" href="#"
                                                       data-show-url="{{isset($showAdvance) ? route('customer-group.show', $customerGroup->id) : ''}}"
                                                       data-id="{{ isset($showAdvance) ? $customerGroup->id : ''}} ">{{$customerGroup->name}}</a>
                                                </td>
                                                <td class="text-left">
                                                    {{$customerGroup->username}}
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