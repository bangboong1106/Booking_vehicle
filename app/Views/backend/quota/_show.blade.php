<div class="form-info-wrap" data-id="{{$entity->id}}" id="quota_model" data-quicksave='{{route('quota.quickSave')}}'
     data-entity='quota'>
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
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingHistory"
                               href="#">{{trans('models.auditing.name')}}</a></li>
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
                            @include('layouts.backend.elements.detail_to_action', ['exportEntityType' => config('constant.QUOTA')])
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'quota_code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'vehicle_group_id', 'isEditable' => false, 'value' => $entity->vehicleGroup ? $entity->vehicleGroup->name :'' ])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'distance', 'controlType'=>'number'])
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingRoute">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseRoute" aria-expanded="true"
                                   aria-controls="collapseInformation" class="collapse-expand">
                                    Thông tin lộ trình
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseRoute" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
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
                                                                    <div class="col-md-6">
                                                                        {{$location['location_title']? $location['location_title']:'--'}}
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
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingCost">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseCost" aria-expanded="true"
                                   aria-controls="collapseCost" class="collapse-expand">
                                    Thông tin chi phí
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseCost" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body cost">
                                <table class="table table-bordered table-hover table-cost view">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" class="text-left">
                                            Diễn giải
                                        </th>
                                        <th scope="col" style="width: 200px" class="text-right">Số tiền (VND)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if($entity->costs)
                                        @foreach($entity->costs as $cost)
                                            <tr>
                                                <td class="text-left">
                                                    {{$cost['receipt_payment_name']}}
                                                </td>
                                                <td class="text-right">
                                                    {{numberFormat($cost['amount'])}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    </tbody>
                                    <tfoot>
                                    <td>Số dòng: <span
                                                class="row-number">{{isset($entity->costs) ? count($entity->costs) : '0'}}</span>
                                    </td>
                                    <td class="result-cost text-right">{{numberFormat($entity->total_cost)}}</td>
                                    </tfoot>
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
                    </div>
                </div>
                <div id="headingAuditing">
                    @include('layouts.backend.elements.auditing._show_auditing')
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

