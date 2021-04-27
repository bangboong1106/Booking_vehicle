<script>
    var editableFormConfig = {};
    if(typeof allowEditableControlOnForm != 'undefined'){
        allowEditableControlOnForm(editableFormConfig);
    }
</script>
<div class="form-info-wrap" data-id="{{$entity->id}}" id="template_model"
     data-entity='template'>
    <div>
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row form-info" data-id="{{$entity->id}}">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action')
                        </div>
                    @endif
                    <div class="col-md-12 content-body">
                        <div class="content-detail">
                            <div class="{{isset($showAdvance) ? 'first' : ''}} card-header" role="tab"
                                 id="headingInformation">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                       aria-controls="collapseInformation" class="">
                                        {{trans('models.driver.attributes.information')}}
                                        <i class="fa"></i>

                                    </a>
                                </h5>
                            </div>
                            <div id="collapseInformation" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne"
                                 style="">
                                <div class="card-body">
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',[
                                             'property' => 'title',
                                            'controlType' => 'string',
                                            'widthWrap' => 'col-md-12'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',[
                                          'property' => 'type',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'value'=> $entity->getType(),
                                            'widthWrap' => 'col-md-12'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'export_type',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'value'=> $entity->getExportType(),
                                            'widthWrap' => 'col-md-12'])
                                    </div>
                                    @if($entity->type == 3 || $entity->type == 6 || $entity->type == 7)
                                        <div class="form-group row">
                                            {!! MyForm::label('is_print_empty_cost', $entity->tA('is_print_empty_cost'), [], false) !!}
                                            <br/>
                                            <span class="view-control" id="is_print_empty_cost">
                                                @if($entity->is_print_empty_cost == "1")
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa"></i>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    @if($entity->type == 1)
                                        <div class="form-group row">
                                            {!! MyForm::label('is_print_empty_goods', $entity->tA('is_print_empty_goods'), [], false) !!}
                                            <br/>
                                            <span class="view-control" id="is_print_empty_goods">
                                                @if($entity->is_print_empty_goods == "1")
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @else
                                                    <i class="fa"></i>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    @if($entity->type == 1 || $entity->type == 3 || $entity->type == 7)
                                        <div class="form-group row">
                                            {!! MyForm::label('list_item', $entity->tA('list_item'), [], false) !!}
                                            <br/>
                                            <span class="view-control" id="list_item">
                                                @if(isset($selectedList))
                                                    @foreach ($selectedList as $item)
                                                        <a class="tag-order" value="{{ $item->id }}">
                                                            {{ $item->name }}</a>
                                                    @endforeach
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            {!! MyForm::label('file', $entity->tA('file'), [], false) !!}
                                            <div class="preview-file">
                                                @if ($entity->file_id)
                                                    <div>
                                                        <img src="{{ route('file.getImage', ['id' => $entity->file_id, 'full' => true]) }}"
                                                             class=" img-fluid preview-image">
                                                    </div>

                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'description', 'controlType' =>'textarea', 'widthWrap' => 'col-md-12'])
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
                </div>
            </li>
            @include('layouts.backend.elements.auditing._show_auditing')
        </ul>
    </div>
</div>