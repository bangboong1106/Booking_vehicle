<div class="form-info-wrap" data-id="{{$entity->id}}" id="order_customer_model"
     data-quicksave='{{route('order-customer.quickSave')}}' data-entity='order_customer'>
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
                               href="#">Thông tin chung</a></li>
                        <li><a class="list-info" data-dest="headingMapping"
                               href="#">Bảng ánh xạ chi phí với tệp mẫu</a></li>
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'title'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'description'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'matching_column_index'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'header_row_index'])
                                </div>
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
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-header" role="tab" id="headingMapping">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseMapping" aria-expanded="true"
                           aria-controls="collapseMapping" class="collapse-expand">
                            Bảng ánh xạ chi phí với tệp mẫu

                        </a>
                    </h5>
                </div>
                <div id="collapseMapping" class="collapse show" role="tabpanel"
                     aria-labelledby="headingMapping"
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
                                            {{trans('models.template_payment.attributes.receipt_payment')}}
                                        </th>
                                        <th scope="col" class="text-left">
                                            {{trans('models.template_payment.attributes.column_index')}}
                                        </th>

                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->templatePaymentMappings) && count($entity->templatePaymentMappings) > 0)

                                        @foreach($entity->templatePaymentMappings as  $index=>$templatePaymentMapping)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    {{ isset($receiptPayments[$templatePaymentMapping['receipt_payment_id']]) ? explode("_", $receiptPayments[$templatePaymentMapping['receipt_payment_id']])[1] :'' }}
                                                </td>
                                                <td class="text-left">
                                                    {{ isset($templatePaymentMapping['column_index']) ? $templatePaymentMapping['column_index'] :'' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                {{trans('models.template_payment.attributes.empty_mapping')}}
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