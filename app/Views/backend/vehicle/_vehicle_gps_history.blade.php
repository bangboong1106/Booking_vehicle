<div class="modal fade" id="vehicle-gps-history" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="vehicle-gps-history-label">Lịch sử GPS của xe</h4>
            </div>
            <div class="modal-body content-history">
                {!! MyForm::hidden('vehicle_id','' ,['id'=>'vehicle_id']) !!}
                {!! MyForm::hidden('vehicle_plate','' ,['id'=>'vehicle_plate']) !!}
                <div class="card-box">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Từ ngày</label>
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control datepicker" name="gps_from_date"
                                           id="gps_from_date"
                                           value="{{date('d-m-Y')}}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control timepicker" name="gps_from_time"
                                           id="gps_from_time" value="{{ date("H:i", strtotime(' -5 min'))}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="p-in"
                                   class="label-heading">Đến ngày</label>
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control datepicker" name="gps_to_date"
                                           id="gps_to_date" value="{{ date("d-m-Y")}}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control timepicker" name="gps_to_time"
                                           id="gps_to_time" value="{{ date("H:i")}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="vehicle_gps_history_submit">Xem lịch sử
                        </button>
                    </div>
                </div>
                <div class="map" id="map" style="height: 400px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    let vehicleGpsHistoryUrl = '{{route('vehicle.vehicle-gps-history')}}';
</script>