<div class="modal fade" id="driver-history" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="driver-history-label">Lịch sử tài xế</h4>
            </div>
            <div class="modal-body content-history">
                <div class="card-box">
                    <div class="form-group row">
                        {!! MyForm::hidden('driver_id','' ,['id'=>'driver_id']) !!}
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Từ ngày</label>
                            <input type="text" class="form-control datepicker" name="driver_start_date"
                                   id="driver_start_date"
                                   value="{{date('d-m-Y', strtotime(' -1 day'))}}">
                        </div>
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Đến ngày</label>
                            <input type="text" class="form-control datepicker" name="driver_end_date"
                                   id="driver_end_date" value="{{ date("d-m-Y")}}">
                        </div>
                    </div>
                    <div class="advanced form-group row">
                        <div class="col-md-6">
                            <label for="driver"> Xe </label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select-vehicle" id="vehicle_id"
                                        name="vehicle_id">
                                    <option value="">Chọn</option>
                                </select>
                                <span class="input-group-addon">
                                            <div class="input-group-text bg-transparent">
                                                <i class="fa fa-remove driver-remove " id="vehicle-remove"></i>
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
                        <button type="submit" class="btn btn-primary" id="driver_history_submit">Xem lịch sử</button>
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
                                <div class="    form-inline m-b-20 justify-content-between">
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
                            <a data-toggle="collapse" href="#collapseVehicleList" aria-expanded="false"
                               aria-controls="collapseVehicleList" class="collapsed">
                                Danh sách xe : <span id="vehicle_size"></span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseVehicleList" class="collapse" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="card-box list-ajax">
                                <div class="form-inline m-b-20 justify-content-between">
                                    <div class="row">
                                        <div class="col-md-12 text-xs-center">
                                            <div class="form-inline">
                                                <label for="per_page">Số bản ghi</label>
                                                <select id="per_page_vehicle"
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
                                                <a class="sorting-vehicle" data-name="reg_no"
                                                   href="#">{{trans('models.vehicle.attributes.reg_no')}}</a>
                                            </th>
                                            <th width="150">
                                                <a class="sorting-vehicle" data-name="weight"
                                                   href="#">{{trans('models.vehicle.attributes.weight')}} (kg)</a>
                                            </th>
                                            <th width="150">
                                                <a class="sorting-vehicle" data-name="volume"
                                                   href="#">{{trans('models.vehicle.attributes.volume')}} (m³)</a>
                                            </th>
                                            <th width="300">
                                                {{ trans('models.vehicle.attributes.length_width_height') }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="body_content_vehicle">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center" id="paginate_content_vehicle">

                                </div>
                                <input type="hidden" class="sort_field" id="sort_field_vehicle" value="">
                                <input type="hidden" class="sort_type" id="sort_type_vehicle" value="">
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
    let driverHistoryUrl = '{{route('driver.driver-history')}}',
        urlVehicle = '{{route('vehicle.combo-vehicle')}}',
        customerDropdownUri = '{{route('customer.combo-customer')}}',
        orderTableActionUrl = '{{route('driver.order-table-action')}}',
        vehicleTableActionUrl = '{{route('driver.vehicle-table-action')}}';
</script>