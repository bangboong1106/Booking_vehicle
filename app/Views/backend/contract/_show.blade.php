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
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'contract_no', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'customer_id', 'isEditable'=>false, 'value'=>empty($entity->customer) ? '' : $entity->customer->full_name])

                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'type', 'isEditable' => false, 'value'=>$entity->tryGet('contractType')->name])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'status', 'isEditable' => false, 'value'=>$entity->getStatus()])

                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'issue_date', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'expired_date', 'isEditable' => false])

                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'widthWrap' => 'col-md-12', 'controlType'=>'textarea', 'isEditable' => false])
                        </div>
                        <div class="card-header" role="tab" id="headingFiles">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseFiles" aria-expanded="true"
                                   aria-controls="collapseFile">
                                    {{trans('models.driver.attributes.files_info')}}
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseFiles"
                             role="tabpanel"
                             aria-labelledby="headingFiles">
                            <div class="card-body">
                                <div class="form-group row">
                                    @if (count($file_list) === 0)
                                        <p>Không có thông tin đính kèm</p>
                                    @else
                                        @foreach($file_list as $file)
                                            <div class="col-md-3">
                                                <img src="{{ route('file.getImage', ['id' => $file->file_id, 'full' => true]) }}"
                                                     class="img-fluid" style="width: 150px; height: 150px"></div>
                                            <div class="text-center">
                                                <a id="download_file" class="fa fa-download"
                                                   target="_blank"
                                                   href="{{ route('file.downloadFile',$file->file_id) }}"></a>
                                            </div>
                                        @endforeach
                                    @endif


                                </div>
                            </div>
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