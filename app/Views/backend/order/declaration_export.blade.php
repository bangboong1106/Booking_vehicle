<div class="modal fade" id="declaration_export" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xuất dữ liệu bảng kê</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body declaration-export-body">

                <div class="title">Thời gian báo cáo</div>
                <div class="row parameter">
                    <div class="col-md-12">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="month" value="1"
                                   name="displayType" checked>
                            <label for="month">Tháng này (<span></span>)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="last_month" value="2"
                                   name="displayType">
                            <label for="last_month">Tháng trước (<span></span>)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="week" value="3" name="displayType">
                            <label for="week">Tuần này (<span></span>)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="last_week" value="4" name="displayType">
                            <label for="last_week">Tuần trước (<span></span>)</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="custom" value="5" name="displayType">
                            <label for="custom">Tùy chỉnh<span></span></label>
                        </div>
                    </div>
                </div>
                <div class="custom-parameter hide">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                Trạng thái<br/>
                                <select name="declaration-status" id="declaration-status" class="form-control select2">
                                    <option value="-1" {{$dayCondition !=4 ? 'selected' : ''}}>Tất cả</option>
                                    <option value=1>Khởi tạo</option>
                                    <option value=2>Sẵn sàng</option>
                                    <option value=7>Chờ tài xê xác nhận</option>
                                    <option value=3>Chờ nhận hàng</option>
                                    <option value=6>Đang vận chuyển</option>
                                    <option value=5 {{$dayCondition ==4 ? 'selected' : ''}}>Hoàn thành</option>
                                    <option value=6>Đã hủy</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                Khách hàng<br/>
                                <select name="declaration-customer" id="declaration-customer"
                                        class="form-control select2 select-customer"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                Xe<br/>
                                <select name="declaration-vehicle" id="declaration-vehicle"
                                        class="form-control select2 select-vehicle" multiple></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="row">
                                Đội tài xế<br/>
                                <select name="declaration-vehicle-team" id="declaration-vehicle-team"
                                        class="form-control select2 select-vehicle-team"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                Mã đơn hàng<br/>
                                <input name="declaration-order-code" id="declaration-order-code"
                                       class="form-control" placeholder="Nhập mã đơn hàng"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            Loại thời gian<br/>
                            <div class="input-group">
                                <select class="select2" id="dayCondition">
                                    <option value="1" {{$dayCondition == 1 ? "selected":""}}>Thời
                                        gian nhận hàng dự kiến
                                    </option>
                                    <option value="2" {{$dayCondition == 2 ? "selected":""}}>Thời
                                        gian nhận hàng thực tế
                                    </option>
                                    <option value="3" {{$dayCondition == 3 ? "selected":""}}>Thời
                                        gian trả hàng dự kiến
                                    </option>
                                    <option value="4" {{$dayCondition == 4 ? "selected":""}}>Thời
                                        gian trả hàng thực tế
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                Thời gian</br>
                                <div id="declaration-range-real-destination-date"
                                     class="pull-right form-control">
                                    <span></span>
                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--                    <div class="row form-group">--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày nhận hàng</br>--}}
                    {{--                                <div id="declaration-range-destination-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày nhận hàng thực tế</br>--}}
                    {{--                                <div id="declaration-range-real-destination-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="row form-group">--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày trả hàng</br>--}}
                    {{--                                <div id="declaration-range-arrival-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày trả hàng thực tế</br>--}}
                    {{--                                <div id="declaration-range-real-arrival-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="row form-group">--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày tạo</br>--}}
                    {{--                                <div id="declaration-range-created-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <div class="row">--}}
                    {{--                                Ngày sửa</br>--}}
                    {{--                                <div id="declaration-range-modified-date"--}}
                    {{--                                     class="pull-right form-control">--}}
                    {{--                                    <span></span>--}}
                    {{--                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success btn-declaration-export" id="declartion-export-btn">Xuất
                </button>
            </div>
        </div>
    </div>
</div>