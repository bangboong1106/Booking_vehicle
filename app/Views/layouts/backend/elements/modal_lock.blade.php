<div class="modal fade" id="modal_lock" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row form-group description">
                    <div class="col-md-12">
                        <i><span>Sau khi khoá, bạn không thể thực hiện bất cứ thao tác đến đơn hàng.</span><br />
                            <span>Muốn thực hiện sửa đổi bạn phải thực hiện chức năng <b>"Mở khoá số"</b></span></i>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <span>Loại thời gian</span><br />
                        <select class="select2" id="day-condition-lock">
                            <option value="1" {{ $dayCondition == 1 ? 'selected' : '' }}>Thời
                                gian nhận hàng dự kiến
                            </option>
                            <option value="2" {{ $dayCondition == 2 ? 'selected' : '' }}>Thời
                                gian nhận hàng thực tế
                            </option>
                            <option value="3" {{ $dayCondition == 3 ? 'selected' : '' }}>Thời
                                gian trả hàng dự kiến
                            </option>
                            <option value="4" {{ $dayCondition == 4 ? 'selected' : '' }}>Thời
                                gian trả hàng thực tế
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <span>Khoảng thời gian</span></br>
                        <div id="range-date-lock" class="pull-right form-control">
                            <span></span>
                            <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn-lock-items">
                    <i id="i-lock" class="fa" style="margin-right: 8px"></i><span id="span-lock"></span>
                </button>
            </div>
        </div>
    </div>
</div>
