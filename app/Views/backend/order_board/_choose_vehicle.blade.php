<div class="modal fade" id="choose-vehicle-modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="choose-vehicle-label">Chọn nhanh xe cho đơn hàng</h4>
            </div>
            <div class="modal-body">
                <div class="advanced form-group row">
                    <div class="col-md-12 title">
                        Có phải bạn muốn thêm nhanh <b><span id="total-order"></span></b> đơn hàng: <b><span
                                    id="order-list"></span></b>?<br/>
                        Nếu đồng ý, vui lòng chọn xe và tài xế ở bước dưới.
                    </div>
                    <hr/>
                    <div class="col-md-12">
                        <label for="vehicle">Chọn xe</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select2 select-vehicle" id="choose_vehicle_id"
                                    name="choose_vehicle_id">
                            </select>
                            <span class="input-group-addon" id="choose-vehicle-vehicle-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                </span>
                        </div>
                        <span id="driver_id_error"
                              class="help-block error-help-block"></span>
                    </div>
                    <div class="col-md-12">
                        <label for="vehicle">Chọn tài xế</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select2 select-driver" id="choose_driver_id"
                                    name="choose_driver_id">
                            </select>
                            <span class="input-group-addon" id="choose-vehicle-driver-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                </span>
                        </div>
                        <span id="driver_id_error"
                              class="help-block error-help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="choose_vehicle_close" type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
                <button id="choose_vehicle_submit" type="button" class="btn btn-success">Lưu</button>
            </div>
        </div>
    </div>
</div>
@include('layouts.backend.elements.search._vehicle_search',
 ['modal' => 'choose_vehicle_vehicle_modal',
 'table'=>'table_choose_vehicle_vehicles',
 'button'=> 'btn-choose-vehicle-vehicle'])

@include('layouts.backend.elements.search._driver_search',
 ['modal' => 'choose_vehicle_driver_modal',
 'table'=>'table_choose_vehicle_drivers',
 'button'=> 'btn-choose-vehicle-driver'])