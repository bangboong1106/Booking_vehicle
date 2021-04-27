<div class="modal fade" id="vehicle-history" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="vehicle-history-label">Lịch sử xe</h4>
            </div>
            <div class="modal-body content-history">
                <div class="card-box">
                    <div class="form-group row">
                        {!! MyForm::hidden('vehicle_id','' ,['id'=>'vehicle_id']) !!}
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Từ ngày</label>
                            <input type="text" class="form-control datepicker" name="vehicle_start_date"
                                   id="vehicle_start_date"
                                   value="{{date('d-m-Y', strtotime(' -1 day'))}}">
                        </div>
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Đến ngày</label>
                            <input type="text" class="form-control datepicker" name="vehicle_end_date"
                                   id="vehicle_end_date" value="{{ date("d-m-Y")}}">
                        </div>
                    </div>
                    <div class="advanced form-group row">
                        <div class="col-md-6">
                            <label for="driver"> Tài xế </label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select-driver" id="driver_id"
                                        name="driver_id">
                                    <option value="">Chọn</option>
                                </select>
                                <span class="input-group-addon">
                                            <div class="input-group-text bg-transparent">
                                                <i class="fa fa-remove driver-remove " id="driver-remove"></i>
                                            </div>
                                        </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="customer"> Khách hàng </label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select_customer" id="customer_id"
                                        name="customer_id">
                                    <option value="">Chọn</option>
                                </select>
                                <span class="input-group-addon">
                                            <div class="input-group-text bg-transparent">
                                                <i class="fa fa-remove customer-search " id="customer-remove"></i>
                                            </div>
                                        </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="vehicle_history_submit">Xem lịch sử</button>
                    </div>
                </div>
                <div class="card-box">
                    <div class="card-header" role="tab" id="headingDestination">
                        <h5 class="mb-0 mt-0 font-16">
                            <a data-toggle="collapse" href="#collapseOrderList" aria-expanded="false"
                               aria-controls="collapseOrderList" class="collapsed">
                                Tổng đơn hàng : <span id="order_size"></span>
                                <br><br>
                                Doanh thu : <span id="order_money"></span> vnd
                            </a>
                        </h5>
                    </div>
                    <div id="collapseOrderList" class="collapse" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="card-box list-ajax">
                                <div class="form-inline m-b-20 justify-content-between">
                                    <div class="row">
                                        <div class="col-md-12 text-xs-center">
                                            <div class="form-inline">
                                                <label for="per_page">Số bản ghi</label>
                                                <select id="per_page_order"
                                                        class="form-control input-sm m-l-10 range-per-page">
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr class="active">
                                            <th width="100">
                                                <a class="sorting-order" data-name="order_code"
                                                   href="#">{{trans('models.order.attributes.order_code')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="order_no"
                                                   href="#">{{trans('models.order.attributes.order_no')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="ETD"
                                                   href="#">{{trans('models.order.attributes.ETD')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="ETA"
                                                   href="#">{{trans('models.order.attributes.ETA')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="location_destination"
                                                   href="#">{{trans('models.order.attributes.location_destination')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="location_arrival"
                                                   href="#">{{trans('models.order.attributes.location_arrival')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="customer_name"
                                                   href="#">{{trans('models.order.attributes.customer_name')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-order" data-name="status"
                                                   href="#">{{trans('models.order.attributes.status')}}</a>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="body_content_order">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" id="paginate_content_order">
                                </div>
                                <input type="hidden" class="sort_field" id="sort_field_order" value="">
                                <input type="hidden" class="sort_type" id="sort_type_order" value="">
                            </div>
                        </div>
                    </div>
                    <div class="card-header" role="tab" id="headingDestination">
                        <h5 class="mb-0 mt-0 font-16">
                            <a data-toggle="collapse" href="#collapseDriverList" aria-expanded="false"
                               aria-controls="collapseDriverList" class="collapsed">
                                Danh sách tài xế : <span id="driver_size"></span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseDriverList" class="collapse" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="card-box list-ajax">
                                <div class="form-inline m-b-20 justify-content-between">
                                    <div class="row">
                                        <div class="col-md-12 text-xs-center">
                                            <div class="form-inline">
                                                <label for="per_page">Số bản ghi</label>
                                                <select id="per_page_driver"
                                                        class="form-control input-sm m-l-10 range-per-page">
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr class="active">
                                            <th width="150">
                                                <a class="sorting-driver" data-name="full_name"
                                                   href="#">{{trans('models.driver.attributes.full_name')}}</a>
                                            </th>
                                            <th width="150">
                                                <a class="sorting-driver" data-name="mobile_no"
                                                   href="#">{{trans('models.driver.attributes.mobile_no')}}</a>
                                            </th>
                                            <th width="100">
                                                <a class="sorting-driver" data-name="id_no"
                                                   href="#">{{trans('models.driver.attributes.id_no')}}</a>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="body_content_driver">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" id="paginate_content_driver">

                                </div>
                                <input type="hidden" class="sort_field" id="sort_field_driver" value="">
                                <input type="hidden" class="sort_type" id="sort_type_driver" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="add_trip_close" type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    let vehicleHistoryUrl = '{{route('vehicle.vehicle-history')}}',
        driverDropdownUri = '{{route('driver.combo-driver')}}',
        customerDropdownUri = '{{route('customer.combo-customer')}}',
        orderTableActionUrl = '{{route('vehicle.order-table-action')}}',
        driverTableActionUrl = '{{route('vehicle.driver-table-action')}}';
</script>