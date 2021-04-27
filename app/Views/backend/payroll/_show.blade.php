<div class="form-info-wrap" data-id="{{$entity->id}}">
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
                               href="#">{{trans('models.payroll.attributes.general_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingFormula"
                               href="#">{{trans('models.payroll.attributes.formula_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> {{trans('models.payroll.attributes.system_info')}}</a></li>
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
                            @include('layouts.backend.elements.detail_to_action')
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'description'])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'isDefault',
                                        'isEditable' => false,
                                        'controlType'=>'label',
                                        'value'=>  '<span id="isDefault"></span>'.(($entity->isDefault == 1) ?'<i class="fa fa-check" aria-hidden="true"></i>': '-')
                                        ])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'date_from', 'controlType' =>'date'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'date_to', 'controlType' =>'date'])

                                </div>

                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'isApplyAll',
                                        'isEditable' => false,
                                        'controlType'=>'label',
                                        'value'=>  '<span id="isApplyAll"></span>'.(($entity->isApplyAll == 1) ?'<i class="fa fa-check" aria-hidden="true"></i>': '-')
                                        ])
                                    @if($entity->isApplyAll != 1)
                                        <?php
                                        $customerGroupValue = '';
                                        foreach ($entity->customerGroups as $customerGroupId) {
                                            $customerGroupValue .= '<a class="order-detail" href="#"
                                                                    data-show-url="' . (isset($showAdvance) ? route('customer-group.show', $customerGroupId) : '') . '"
                                                        data-id="' . $customerGroupId . '"><span class="tag-order">' . (isset($customerGroupList[$customerGroupId]) ? $customerGroupList[$customerGroupId] : '') . '</span></a>';
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'customer_groups',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'widthWrap'=>'col-md-8',
                                            'value'=> $customerGroupValue
                                            ])
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-header" role="tab" id="headingFormula">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseFormula" aria-expanded="true"
                           aria-controls="collapseInformation" class="collapse-expand">
                            {{trans('models.price_quote.attributes.formula_info')}}

                        </a>
                    </h5>
                </div>
                <div id="collapseFormula" class="collapse show" role="tabpanel"
                     aria-labelledby="headingOne"
                     style="">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover" style="max-width: 1200px">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">
                                            {{trans('models.payroll.attributes.location_group_destination')}}
                                        </th>
                                        <th scope="col" class="text-left">
                                            {{trans('models.payroll.attributes.location_group_arrival')}}
                                        </th>
                                        <th scope="col" class="text-left">
                                            {{trans('models.payroll.attributes.vehicle_group')}}
                                        </th>
                                        <th scope="col" class="text-right">
                                            {{trans('models.payroll.attributes.price')}}
                                        </th>

                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->formulas) && count($entity->formulas) > 0)

                                        @foreach($entity->formulas as  $index=>$formula)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    {{ isset($locationGroupList[$formula['location_group_destination_id']]) ? $locationGroupList[$formula['location_group_destination_id']] :'' }}
                                                </td>
                                                <td class="text-left">
                                                    {{ isset($locationGroupList[$formula['location_group_arrival_id']]) ? $locationGroupList[$formula['location_group_arrival_id']] : '' }}
                                                </td>
                                                <td class="text-left">
                                                    {{isset($vehicleGroupList[$formula['vehicle_group_id']]) ? $vehicleGroupList[$formula['vehicle_group_id']] : ''}}
                                                </td>
                                                <td class="text-right">
                                                    {{numberFormat($formula['price'])}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                {{trans('models.payroll.attributes.empty_formula')}}
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                {{--Thông tin hệ thống--}}
                <div class="card-header" role="tab" id="headingSystem">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseSystem"
                           aria-expanded="true" aria-controls="collapseNote" class="collapse-expand">
                            {{trans('models.price_quote.attributes.system_info')}}
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
</script>