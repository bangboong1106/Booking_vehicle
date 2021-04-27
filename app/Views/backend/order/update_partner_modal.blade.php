<div class="modal fade" id="update_partner_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Giao đơn hàng cho đối tác vận tải</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body declaration-export-body">
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        <p style="margin: 0">Hệ thống sẽ tự động tạo chuyến xe cho <b><span class="total-order"></span>
                                đơn hàng</b> mà bạn đã chọn.<br/>
                            Nếu bạn chọn tài xế và xe ở dưới.
                        </p>
                    </div>
                </div>
                <hr/>
                <div class="advanced form-group row">
                    <div class="col-md-6">
                        TG trả hàng dự kiến muộn nhất
                    </div>
                    <div class="col-md-6 text-right">
                        <b><span id="time"></span></b>
                    </div>
                </div>
                <div class="advanced form-group row">
                    <div class="col-md-6">
                        Tổng thể tích các đơn hàng
                    </div>
                    <div class="col-md-6 text-right">
                        <b><span id="total_volume" class="text-right"></span> (m3)</b>
                    </div>
                </div>
                <div class="advanced form-group row">
                    <div class="col-md-6">
                        Tổng khối lượng các đơn hàng
                    </div>
                    <div class="col-md-6 text-right">
                        <b><span id="total_weight"></span> (kg)</b>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <div class="col-md-12">
                        <label for="vehicle_ids">Đối tác vận tải <span class="text-danger"> *</span></label>
                        {!! MyForm::dropDown('partner_id', null, $partnerList, true, [ 'class' => 'select2 minimal' ,'id'=>'partner_id']) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12 checkbox checkbox-purple text-left">
                        <input id="merge_route" type="checkbox">
                        <label for="merge_route">
                            &nbsp;Ghép tất cả đơn đã chọn vào cùng 1 chuyến xe
                        </label>
                    </div>
                </div>
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        <label for="vehicle_ids">Xe</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select2 select-vehicle" id="vehicle_ids" name="vehicle_ids">
                                <option>Vui lòng chọn xe</option>
                            </select>
                            <span class="input-group-addon vehicle-search" id="vehicle-search" data-all="1">
                                <div class="input-group-text bg-transparent">
                                    <i class="fa fa-truck"></i>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="advanced form-group row">
                    <div class="col-md-12">
                        <label for="driver_ids">Tài xế</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select2 select-driver" id="driver_ids" name="driver_ids">
                                <option>Vui lòng chọn tài xế</option>
                            </select>
                            <span class="input-group-addon driver-search" id="driver-search" data-all="1">
                                <div class="input-group-text bg-transparent">
                                    <i class="fa fa-id-card"></i>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success btn-update-partner" id="btn_update_partner"
                        data-url={{ route('order.updatePartner') }}>Lưu
                </button>
            </div>
        </div>
    </div>
</div>
