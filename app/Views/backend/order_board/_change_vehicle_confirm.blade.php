<div class="modal" id="change_vehicle_confirm">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{ trans('messages.confirm_change_vehicle_trip') }}</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="form_vehicle_trip">
                    <p class="title">
                        Bạn chắc chắn muốn thay đổi đơn hàng từ xe <b><span class="source_vehicle"></span></b> sang
                        xe
                        <b><span class="destination_vehicle"></span></b>?
                        <br />Nếu đồng ý, vui lòng nhập thông tin thời gian
                    </p>
                    <hr />
                    {!! MyForm::hidden('change_trip_id', '', ['id' => 'change_trip_id']) !!}
                    {!! MyForm::hidden('change_old_vehicle_id', '', ['id' => 'change_old_vehicle_id']) !!}
                    {!! MyForm::hidden('change_vehicle_id', '', ['id' => 'change_vehicle_id']) !!}

                    <div class="form-group">
                        <label for="p-in" class="label-heading">{{ trans('models.order.attributes.ETD') }}</label>
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" class="form-control datepicker" name="vehicle_start_date"
                                    id="vehicle_start_date">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control timepicker" name="vehicle_start_time"
                                    id="vehicle_start_time">
                            </div>
                        </div>
                        <span id="startdate_id_error" class="help-block error-help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="p-in" class="label-heading">{{ trans('models.order.attributes.ETA') }}</label>
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" class="form-control datepicker" name="vehicle_end_date"
                                    id="vehicle_end_date">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control timepicker" name="vehicle_end_time"
                                    id="vehicle_end_time">
                            </div>
                        </div>
                        <span id="enddate_id_error" class="help-block error-help-block"></span>
                    </div>
                    <div class="advanced form-group row">
                        <div class="col-md-12">
                            <label for="vehicle">Chọn tài xế</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select2 select-driver" id="change_driver_id" name="change_driver_id">
                                </select>
                                <span class="input-group-addon" id="change-vehicle-driver-search">
                                    <div class="input-group-text bg-transparent">
                                        <i class="fa fa-search driver-search"></i>
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
                                href="{{ env('HELP_DOMAIN', '') . trans('helps.dashboard.change_vehicle') }}"
                                data-toggle="tooltip" data-placement="top" title=""
                                data-original-title="{{ trans('actions.help') }}">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-md-10 text-right">
                            <button type="button" class="btn btn-default" id="close-vehicle-trip"
                                data-dismiss="modal">{{ trans('actions.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.backend.elements.search._driver_search',
['modal' => 'change_vehicle_driver_modal',
'table'=>'table_change_vehicle_drivers',
'button'=> 'btn-change-vehicle-driver'])
