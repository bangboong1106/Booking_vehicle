@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box form-display">
                        @include('layouts.backend.elements._form_label')
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <div class="content-body">
                                    <div id="collapseInformation" class="collapse show" role="tabpanel"
                                        aria-labelledby="headingOne" style="">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <label>Chọn mẫu</label>
                                                    {!! MyForm::dropDown('template_id', null, $templates,
                                                    false, ['class' => 'select2' ,'id'=>'template_id']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <div class="dropzone-outer previewsContainer"></div>
                                                    <div class="dropzone text-center" data-file_type="3"></div>
                                                    {!! MyForm::hidden('path', null, ['id' => 'path']) !!}
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="m-t-20"></div>
                            <div class="submit-button text-right row">
                                <div class="col-md-6 submit-button-title">
                                    <h4 class="m-t-0 header-title">
                                        Chuyển đổi dữ liệu Excel
                                    </h4>
                                </div>
                                <div class="col-md-6 wrap-submit-button">
                                    <span class="padr20">
                                        <a class="btn btn-default back-button"
                                            href="{!!  getBackUrl(false, route($routePrefix . '.index')) !!}">
                                            <i class="fa fa-backward"></i>{{ trans('actions.back') }}
                                        </a>
                                    </span>
                                    <span>
                                        <button class="btn btn-blue" id="btn-convert" style="width: 150px"
                                            data-url="{{ route('excel-converter.convert') }}">
                                            <i class="fa fa-scissors"></i>Chuyển đổi
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var token = "{!!  csrf_token() !!}",
            uploadUrl = "{{ route('file.uploadFile') }}",
            downloadUrl = "{{ route('file.downloadFile', -1) }}",
            removeUrl = "{{ route('file.destroy', -1) }}",
            existingFiles = [];

    </script>
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/jszip.min',
        'vendor/FileSaver.min',
        'autoload/object-select2',
        'autoload/report_utility'
    ]
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush