<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{ $entity->id }}">
        {!! MyForm::model($entity, ['route' => ['template-excel-converter.valid', $entity->id]]) !!}
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
                                            {{ trans('models.order.attributes.information') }}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseInformation" class="collapse show" role="tabpanel"
                                    aria-labelledby="headingOne" style="">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                {!! MyForm::label('title', $entity->tA('title') . ' <span
                                                    class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('title', $entity->title, ['placeholder' =>
                                                $entity->tA('title')]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                {!! MyForm::label('description', $entity->tA('description')) !!}
                                                {!! MyForm::textarea('description', $entity->description, ['rows' => 4,
                                                'placeholder' => $entity->tA('description')]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! MyForm::label('header_row_index',
                                                    $entity->tA('header_row_index')) . ' <span
                                                        class="text-danger">*</span>' !!}
                                                    {!! MyForm::text('header_row_index', $entity->header_row_index,
                                                    ['placeholder' => $entity->tA('header_row_index'), 'class' =>
                                                    'number-input']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! MyForm::label('max_row', $entity->tA('max_row')) .
                                                    ' <span class="text-danger">*</span>' !!}
                                                    {!! MyForm::text('max_row', $entity->max_row, ['placeholder' =>
                                                    $entity->tA('max_row'), 'class' => 'number-input']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    <div class="form-group">
                                        {!! MyForm::label('is_use_convert_sheet', $entity->tA('is_use_convert_sheet'), [], false) !!}
                                        <input hidden="hidden" name="is_use_convert_sheet" id="is_use_convert_sheet"
                                            value="{{ $entity->is_use_convert_sheet }}"/>
                                        <div>
                                            {!! MyForm::checkbox('switchery_is_use_convert_sheet', $entity->is_use_convert_sheet, $entity->is_use_convert_sheet  == 1 ? true : false
                                            , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_use_convert_sheet']) !!}
                                            <span></span>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <div class="card-header" role="tab" id="headingMapping">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseMapping" aria-expanded="true"
                                            aria-controls="collapseMapping" class="collapse-expand">
                                            Bảng ánh xạ trường với tệp mẫu <i class="fa"></i>

                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseMapping" class="collapse show" role="tabpanel"
                                    aria-labelledby="headingMapping" style="">
                                    <div class="card-body">

                                        <table class="table table-bordered table-hover table-mapping">
                                            <thead id="head_content">
                                                <tr class="active">
                                                    <th style="font-size: 14px; font-weight: bold;">
                                                        {{ trans('models.template_excel_converter.attributes.field') }}
                                                    </th>   
                                                    <th style="font-size: 14px; font-weight: bold;">
                                                        {{ trans('models.template_excel_converter.attributes.formula') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_content">
                                                @if (isset($excelColumnConfigMappings))
                                                    @foreach ($excelColumnConfigMappings as $index => $excelColumnConfigMapping)
                                                        <?php
                                                        $column_index;
                                                        $formula;
                                                        if(isset($currentTemplateExcelConverterMappings)){
                                                            $current = collect($currentTemplateExcelConverterMappings)
                                                            ->filter(function ($value){
                                                                return empty($value->column_index);
                                                            })
                                                            ->filter(function ($value) use ($excelColumnConfigMapping) {
                                                            return $value->field == $excelColumnConfigMapping->field;
                                                            })
                                                            ->first();
                                                            if(!empty($current)){
                                                                $column_index = $current->column_index;
                                                                $formula = $current->formula;
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input placeholder="Trường"
                                                                        class="form-control input-uppercase mapping"
                                                                        type="hidden"
                                                                        name="templateExcelConverterMappings[{{ $index }}][field]"
                                                                        id="templateExcelConverterMappings[{{ $index }}][field]"
                                                                        value="{{ $excelColumnConfigMapping->field }}"
                                                                        aria-invalid="false" />
                                                                    <input placeholder="Trường" type="text" disabled
                                                                        class="form-control input-uppercase mapping"
                                                                        name="templateExcelConverterMappings[{{ $index }}][column_name]"
                                                                        id="templateExcelConverterMappings[{{ $index }}][column_name]"
                                                                        value="{{ $excelColumnConfigMapping->column_name }}"
                                                                        aria-invalid="false" />

                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input placeholder="Công thức"
                                                                        class="form-control input-uppercase mapping"
                                                                        type="text"
                                                                        name="templateExcelConverterMappings[{{ $index }}][formula]"
                                                                        id="templateExcelConverterMappings[{{ $index }}][formula]"
                                                                        value="{{ isset($formula) ? $formula : '' }} "
                                                                        aria-invalid="false" />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! MyForm::label('type', $entity->tA('file')) !!}
                                    <div class="dropzone-outer previewsContainer"></div>
                                    <div class="dropzone text-center" data-file_type="3"></div>
                                    {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
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
</script>