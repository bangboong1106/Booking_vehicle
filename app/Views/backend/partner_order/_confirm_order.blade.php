<div class="modal fade" id="dialog_accept_order" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xác nhận đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        <p style="margin: 0">
                            Bạn có muốn nhận đơn hàng ?
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn_accept_order"
                        data-url="{{ route('partner-order.acceptOrderSave') }}">Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dialog_request_edit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Yêu cầu sửa đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <label>Lý do</label>
                        {!! MyForm::textarea('reason', '',['id'=>'reason', 'rows'=>2, 'placeholder'=>'Lý do']) !!}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn_request_edit"
                        data-url="{{ route('partner-order.requestEditOrderSave') }}">Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dialog_order_cancel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hủy đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        <p style="margin: 0">
                            Bạn có muốn hủy đơn hàng ?
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn_order_cancel"
                        data-url="{{ route('partner-order.cancelOrderSave') }}">Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$today = (new DateTime());
$tomorrow = new DateTime('tomorrow');

?>
<div class="modal fade" id="dialog_order_complete" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hoàn thành đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" style="overflow-y: visible">
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        {!! MyForm::label('ETD_date_reality', trans('models.order.attributes.ETD_date_reality'). ' <span class="text-danger">*</span>', [], false) !!}
                        <div class="row">
                            <div class="col-md-5">
                                {!! MyForm::text('ETD_time_reality',  $today->format('H-i'),
                                ['placeholder'=> trans('models.partner_orderETD_time_reality'), 'class'=>'timepicker time-input', 'data-field' => 'time' ,'id' =>'complete_etd_time_reality']) !!}
                            </div>
                            <div class="col-md-7">
                                {!! MyForm::text('ETD_date_reality', $today->format('d-m-Y'),
                                ['placeholder'=> trans('models.partner_orderETD_date_reality'), 'class'=>'datepicker date-input', 'data-field' => 'date','id' =>'complete_etd_date_reality']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        {!! MyForm::label('ETA_date_reality', trans('models.order.attributes.ETA_date_reality'). ' <span class="text-danger">*</span>', [], false) !!}
                        <div class="row">
                            <div class="col-md-5">
                                {!! MyForm::text('ETA_time_reality',  $today->modify('+1 hours')->format('H-i'),
                                ['placeholder'=> trans('models.partner_orderETA_time_reality'), 'class'=>'timepicker time-input', 'data-field' => 'time' ,'id' =>'complete_eta_time_reality']) !!}
                            </div>
                            <div class="col-md-7">
                                {!! MyForm::text('ETA_date_reality', $today->format('d-m-Y'),
                                ['placeholder'=> trans('models.partner_orderETA_date_reality'), 'class'=>'datepicker date-input', 'data-field' => 'date','id' =>'complete_eta_date_reality']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn_order_complete"
                        data-url="{{ route('partner-order.completeOrderSave') }}">Lưu
                </button>
            </div>
        </div>
    </div>
</div>