<div class="modal" id="add_order_vehicle_confirm">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                    id="add_order_vehicle_close_header">×
                </button>
                <h4 class="modal-title" id="modal-label">{{ trans('messages.confirm_add_order_to_trip') }}</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="form_add_order_to_vehicle">
                    <span>{{ trans('messages.are_you_sure_add_order_to_vehicle') }}</span>
                    {!! MyForm::hidden('order_id', '', ['id' => 'order_id']) !!}
                    {!! MyForm::hidden('vehicle_id', '', ['id' => 'vehicle_id']) !!}
                    <div class="advanced form-group row">
                        <div class="col-md-12">
                            <label for="vehicle">Chọn tài xế</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select2 select-driver" id="order_driver_id" name="order_driver_id">
                                </select>
                                <span class="input-group-addon" id="add-order-driver-search">
                                    <div class="input-group-text bg-transparent">
                                        <i class="fa fa-id-card"></i>
                                    </div>
                                </span>
                            </div>
                            <span id="driver_id_error" class="help-block error-help-block"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <a class="float-left" target="_blank"
                                href="{{ env('HELP_DOMAIN', '') . trans('helps.dashboard.add_order_to_vehicle') }}"
                                data-toggle="tooltip" data-placement="top" title=""
                                data-original-title="{{ trans('actions.help') }}">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-md-10 text-right">
                            <button type="button" class="btn btn-default" id="add_order_vehicle_close"
                                data-dismiss="modal">{{ trans('actions.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include('layouts.backend.elements.search._driver_search',
['modal' => 'add_order_driver_modal',
'table'=>'table_add_order_drivers',
'button'=> 'btn-add-order-driver'])
