<script>
    var editableFormConfig = {};
    allowEditableControlOnForm(editableFormConfig);
</script>
<ul class="list-group" id="location_model">
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
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'title'])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'name_of_customer_id', 'value'=>isset($entity->customer) ? $entity->customer->full_name : '-','isEditable' => false])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'province', 'value'=>isset($entity->province) ? $entity->province->title : '-', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'district', 'value'=>isset($entity->district) ? $entity->district->title : '-', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ward', 'value'=>isset($entity->ward) ? $entity->ward->title : '-', 'isEditable' => false])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'address', 'widthWrap'=>'col-md-4'])
                            <div class="col-md-4">
                                {!! MyForm::label('location_group_id', $entity->tA('location_group_id'), [], false) !!}
                                <br/>
                                <span class="view-control" style="height: 35px">{{ null == $entity->group ?
                                    '-' : $entity->group->title }}</span>
                            </div>
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'limited_day', 'value'=> $entity->limited_day ? numberFormat($entity->limited_day ) : ''])
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {!! MyForm::label('location_type_id', $entity->tA('location_type_id'), [], false) !!}
                                <br/>
                                <span class="view-control" style="height: 35px">{{ null == $entity->type()->first() ?
                                    '' : $entity->type()->first()->title }}</span>
                            </div>
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'full_address', 'widthWrap'=>'col-md-8'])
                        </div>
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
                <div class="card-header" role="tab"
                     id="headingInformation">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseMap" aria-expanded="true"
                           aria-controls="collapseMap" class="">
                            Thông tin địa chỉ
                            <i class="fa"></i>
                        </a>
                    </h5>
                </div>
                <div id="collapseMap" class="collapse show" role="tabpanel"
                     aria-labelledby="headingOne"
                     style="">
                    <div class="card-body">
                        <div>
                            <div id="map_show"></div>
                            <input type="hidden" id="latShow" value="{{ $entity->latitude ? $entity->latitude : 0 }}">
                            <input type="hidden" id="lngShow" value="{{ $entity->longitude ? $entity->longitude : 0 }}">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
@if(empty($formAdvance))
    @push('scripts')
        {!! loadFiles(['vendor/lib/locationObject'], $area, 'js') !!}
    @endpush
@endif