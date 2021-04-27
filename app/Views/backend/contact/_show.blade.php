<script>
    var editableFormConfig = {};
    allowEditableControlOnForm(editableFormConfig);
</script>
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
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'contact_name', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'phone_number', 'isEditable'=>false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'email', 'isEditable'=>false])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'location_id',
                             'widthWrap' => 'col-md-12',
                             'controlType'=>'textarea',
                             'value'=>empty($entity->location_id) || empty($entity->locationRel) ? '' : $entity->locationRel->getFullLocation(),
                             'isEditable' => false])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'active', 'isEditable'=>false, 'value'=>$entity->getActive()])
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
                @include('layouts.backend.elements.auditing._show_auditing')
            </div>
        </div>
    </li>
</ul>