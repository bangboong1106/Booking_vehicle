<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{$entity->id}}">
        {!! MyForm::model($entity, ['route' => ['template-payment.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#route_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{ trans('models.order.attributes.communication') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active">
                            <div class="content-body">

                                {{--Thông tin chung--}}
                                <div class="card-header" role="tab" id="headingInformation">
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
                                            <div class="col-md-8">
                                                {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                {!! MyForm::label('description', $entity->tA('description')) !!}
                                                {!! MyForm::textarea('description', $entity->description, ['rows'=>4, 'placeholder'=>$entity->tA('description')]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! MyForm::label('matching_column_index', $entity->tA('matching_column_index')). ' <span class="text-danger">*</span>' !!}
                                                    {!! MyForm::text('matching_column_index', $entity->matching_column_index,
                                                    ['placeholder'=>$entity->tA('matching_column_index'),'class'=>'input-uppercase']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! MyForm::label('header_row_index', $entity->tA('header_row_index')). ' <span class="text-danger">*</span>' !!}
                                                    {!! MyForm::text('header_row_index', $entity->header_row_index,
                                                    ['placeholder'=>$entity->tA('header_row_index'), 'class' => 'number-input']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-8">
                                                {!! MyForm::label('file', $entity->tA('file')) !!}
                                                <div class="dropzone-outer previewsContainer"></div>
                                                <div class="dropzone text-center" data-file_type="3"></div>
                                                {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-header" role="tab" id="headingMapping">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseMapping" aria-expanded="true"
                                           aria-controls="collapseMapping" class="collapse-expand">
                                            Bảng ánh xạ chi phí với tệp mẫu <i class="fa"></i>

                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseMapping" class="collapse show" role="tabpanel"
                                     aria-labelledby="headingMapping"
                                     style="">
                                    <div class="card-body">

                                        <table class="table table-bordered table-hover table-mapping">
                                            <thead id="head_content">
                                            <tr class="active">
                                                <th style="font-size: 14px; font-weight: bold;">
                                                    {{trans('models.template_payment.attributes.receipt_payment')}}
                                                </th>
                                                <th style="font-size: 14px; font-weight: bold;">
                                                    {{trans('models.template_payment.attributes.column_index')}}
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="body_content">

                                            @if($receiptPayments)
                                                @foreach($receiptPayments as $index=>$receiptPaymentMapping)
                                                    @php
                                                        $receiptPaymentMappingId = explode("_", $receiptPaymentMapping)[0];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="input-group">
                                                                <select class="select2 select-receipt-payment mapping"
                                                                        name="templatePaymentMappings[{{$index}}][receipt_payment_id]"
                                                                        id="templatePaymentMappings[{{$index}}][receipt_payment_id]">
                                                                    <option></option>
                                                                    @foreach($receiptPayments as $receiptPayment)
                                                                        <option value="{{explode("_", $receiptPayment)[0]}}"
                                                                                {{ $receiptPaymentMappingId == explode("_", $receiptPayment)[0] ? 'selected':'' }}>
                                                                            {{explode("_", $receiptPayment)[1]}}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input placeholder="Cột"
                                                                       class="form-control input-uppercase mapping"
                                                                       type="text"
                                                                       name="templatePaymentMappings[{{$index}}][column_index]"
                                                                       id="templatePaymentMappings[{{$index}}][column_index]"
                                                                       aria-invalid="false"
                                                                       value="{{isset($currentTemplatePaymentMappings[$receiptPaymentMappingId]) ? $currentTemplatePaymentMappings[$receiptPaymentMappingId] : ''}} "/>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="m-t-20"></div>
                        @include('layouts.backend.elements._submit_form_button')
                    </div>
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
<script>
    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [];
    @if(!empty($entity->file_id))
    existingFiles.push({
        name: '{{ $entity->tryGet('getFile')->file_name }}',
        size: '{{ $entity->tryGet('getFile')->size }}',
        url: '{{ route('file.getImage', $entity->tryGet('getFile')->file_id) }}',
        type: '{{ $entity->tryGet('getFile')->file_type }}',
        urlDownload: '{{ route('file.downloadFile',$entity->tryGet('getFile')->file_id) }}',
        id: '{{ $entity->tryGet('getFile')->file_id }}',
    });
    @endif

</script>

<?php
$searchJsFiles = [
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}

