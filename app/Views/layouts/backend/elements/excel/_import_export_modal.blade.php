@php
    $exampleFileName = isset($exampleName) ? $exampleName : $routePrefix.'_example.xlsx';
@endphp
<div class="modal fade" id="import_excel" tabindex="-1">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{trans('messages.import_form_excel')}}</h4>
            </div>
            {!! MyForm::import() !!}
            <div class="modal-body">
                <div class="wizard-steps row">
                    <div class="wizard-progress">
                        <div class="wizard-progress-line" data-number-of-steps="3" style="width: 33.33%;"></div>
                    </div>
                    <div class="wizard-step import-wizard active col-4">
                        <div class="wizard-step-icon"><i class="fa fa-cloud-upload"></i></div>
                        <p>{{ trans('common.upload_file') }}</p>
                    </div>
                    <div class="wizard-step check-data col-4">
                        <div class="wizard-step-icon"><i class="fa fa-pencil"></i></div>
                        <p>{{ trans('common.check_data') }}</p>
                    </div>
                    <div class="wizard-step import-done col-4">
                        <div class="wizard-step-icon"><i class="fa fa-check"></i></div>
                        <p>{{ trans('common.done') }}</p>
                    </div>
                </div>
                <fieldset class="wizard-content wizard-content-1 active">
                    @if($excelUpdate)
                        <div>
                            <h5>{{ trans('common.choose_upload_type') }}</h5>
                            <div class="row import-type-excel">
                                <div class="col-md-3">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="create" value="check_data" name="import-excel-type"
                                               checked>
                                        <label for="create">{{ trans('common.upload_type_new') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="update" value="check_update" name="import-excel-type">
                                        <label for="update">{{ trans('common.upload_type_update') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="box has-advanced-upload">
                        <div class="box__input">
                            <div><i class="fa fa-cloud-upload fa-3x"></i></div>
                            <input type="file" name="file_excel" id="import-excel" class="box__file">
                            <label for="import-excel" class="text-success"
                                   data-error="{{ trans('messages.error_file_type') }}">
                                @include('layouts.backend.elements.excel._import_label')
                            </label>
                        </div>
                    </div>
                    @if(isset($urlTemplate))
                        <p class="mt-2">Tải xuống file mẫu kèm dữ liệu <a href="{{$urlTemplate}}">tại đây</a>.</p>
                    @endif
                    <div class="checkbox checkbox-purple text-left"
                         style="{{$routePrefix == 'quota' ? '': 'display:none' }}">
                        <input id="update_route" type="checkbox">
                        <label for="update_route">
                            &nbsp;Lưu và cập nhật vào chuyến xe
                        </label>
                    </div>
                </fieldset>
                <fieldset class="wizard-content wizard-content-2">
                    <div class="result-import mt-2"></div>
                </fieldset>
                <fieldset class="wizard-content wizard-content-3">
                </fieldset>
            </div>
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <a target="_blank" href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName.'/import')}}"
                               data-toggle="tooltip" data-placement="top" title=""
                               data-original-title="{{trans('actions.help')}}">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-md-10 text-right">
                            <a href="#" class="btn close-import-modal" data-dismiss="modal">{{trans('actions.close')}}</a>
                            <button type="button" id="import-button-back" class="btn btn-info d-none"><i
                                        class="fa fa-arrow-left"
                                        aria-hidden="true"></i> {{trans('actions.back')}}
                            </button>
                            <button type="button" id="import-button"
                                    class="btn btn-success d-none">{{trans('actions.import')}}</button>
                            <button type="button" id="import-button-next" class="btn btn-success" data-model="{{$routePrefix}}">
                                {{trans('actions.next')}} <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
            {!! MyForm::close() !!}
        </div>
    </div>
</div>
<div class="modal fade" id="export_excel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{trans('messages.export_to_excel')}}</h4>
            </div>
            <div class="modal-body">
                <p id="export_message"></p>
                <div class="result-export mt-2"></div>
            </div>
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <a class="float-left" target="_blank"
                            href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName.'/export')}}"
                            data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="{{trans('actions.help')}}">
                                {{--        <img src="{{public_url('css/backend/img/help.png')}}" alt=""/>--}}
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-md-10 text-right">
                            <a href="#" class="btn close-parent-modal" data-parent-modal="mass_destroy_confirm"
                            data-dismiss="modal">{{trans('actions.cancel')}}</a>
                            <button type="button" id="export-button" class="btn btn-primary"
                                    data-url="{{isset($routePrefix) ? route($routePrefix.'.export') : ''}}">
                                {{trans('actions.export')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
