@extends('layouts.backend.layouts.main')
@section('content')
    <ul class="list-group">
        <li class="list-group-item detail-info">
            <div class="row form-info-wrap">
                <div class="col-md-12">
                    <div class="card-header" role="tab" id="headingInformation">
                        <h6 class="mb-0 mt-0 font-18">
                            BẢNG ĐIỀU KHIỂN
                        </h6>
                    </div>
                    <div id="collapseInformation" class="collapse show" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Kiểu hiển thị của bảng điều khiển</div>
                                        <div class="col-md-4 edit-group-control">
                                            <select class="select2 setting-view-control disabled"
                                                    name="Dashboard.ViewType">
                                                <option value="timelineTwoWeek"
                                                        {{$dashboardViewType == 'timelineTwoWeek' ? 'selected' : ''}}>
                                                    14 ngày
                                                </option>
                                                <option value="timelineDay" {{$dashboardViewType == 'timelineDay' ? 'selected' : ''}}>
                                                    Ngày
                                                </option>
                                                <option value="customTimelineWeek" {{$dashboardViewType == 'customTimelineWeek' ? 'selected' : ''}}>
                                                    Tuần
                                                </option>
                                                <option value="customTimelineMonth" {{$dashboardViewType == 'customTimelineMonth' ? 'selected' : ''}}>
                                                    Tháng
                                                </option>
                                            </select>
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Số lượng xe muốn hiển thị trên bảng điều khiển</div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('Dashboard.VehiclePageSize', empty($dashboardVehiclePageSize) ? '0' : numberFormat($dashboardVehiclePageSize), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Thời gian cảnh báo khi ĐH sắp đến thời gian lấy hàng<span>(phút)</span>
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('Dashboard.NotifyVehicle', empty($dashboardNotifyVehicle) ? '0' : numberFormat($dashboardNotifyVehicle), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Thời gian tự động tải lại BĐK <span>(phút)</span>
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('Dashboard.Reload', empty($dashboardReload) ? '0' : numberFormat($dashboardReload), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header" role="tab" id="headingRoute">
                        <h6 class="mb-0 mt-0 font-18">
                            THÔNG BÁO
                        </h6>
                    </div>
                    <div id="collapseRoute" class="collapse show" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Khoảng cách xe tới điểm nhận trả hàng (km)
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('Notification.DistanceUnit', empty($notificationDistanceUnit) ? '0' : numberFormat($notificationDistanceUnit), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header" role="tab" id="headingCost">
                        <h6 class="mb-0 mt-0 font-18">
                            CHI PHÍ
                        </h6>
                    </div>
                    <div id="collapseRoute" class="collapse show" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Giá xăng/dầu (VND)
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('Cost.Fuel', empty($fuelPrice) ? '0' : numberFormat($fuelPrice), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header" role="tab" id="headingCost">
                        <h6 class="mb-0 mt-0 font-18">
                            ỨNG DỤNG DÀNH CHO TÀI XẾ
                        </h6>
                    </div>
                    <div id="collapseRoute" class="collapse show" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Thời gian hạn chế thao tác trả hàng (phút)
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            {!! MyForm::text('DriverMobile.CompletedLimitTime', empty($driverMobileLimitedTime) ? '0' : numberFormat($driverMobileLimitedTime), ['class'=>'number-input setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-8">Tài xế có cần xác nhận khi nhận/trả hàng không?
                                            </div>
                                            <div class="col-md-4 edit-group-control">
                                                <select class="select2 setting-view-control disabled"
                                                        name="DriverMobile.AllowConfirmOrder">
                                                    <option value="1"
                                                            {{$driverMobileAllowConfirmOrder == 1 ? 'selected' : ''}}>
                                                        Có
                                                    </option>
                                                    <option value="0" {{$driverMobileAllowConfirmOrder == 0 ? 'selected' : ''}}>
                                                        Không
                                                    </option>
                                                </select>
                                                <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                                <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                                <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                            </div>
                                        </div>
                                        </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-8">Có cho phép tài xế upload giấy tờ <b>Chuyến xe</b>?
                                            </div>
                                            <div class="col-md-4 edit-group-control">
                                                <select class="select2 setting-view-control disabled"
                                                        name="DriverMobile.AllowUploadRoute">
                                                    <option value="1"
                                                            {{$driverMobileAllowUploadRoute == 1 ? 'selected' : ''}}>
                                                        Có
                                                    </option>
                                                    <option value="0" {{$driverMobileAllowUploadRoute == 0 ? 'selected' : ''}}>
                                                        Không
                                                    </option>
                                                </select>
                                                <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                                <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                                <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                            </div>
                                        </div>
                                        </div>
                                </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Có cho phép tài xế upload giấy tờ <b>Đơn hàng</b>?
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            <select class="select2 setting-view-control disabled"
                                                    name="DriverMobile.AllowUploadOrder">
                                                <option value="1"
                                                        {{$driverMobileAllowUploadOrder == 1 ? 'selected' : ''}}>
                                                    Có
                                                </option>
                                                <option value="0" {{$driverMobileAllowUploadOrder == 0 ? 'selected' : ''}}>
                                                    Không
                                                </option>
                                            </select>
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-8">Có cho phép tài xế <b>Huỷ chuyến xe</b> hay không?
                                            </div>
                                            <div class="col-md-4 edit-group-control">
                                                <select class="select2 setting-view-control disabled"
                                                        name="DriverMobile.AllowCancelRoute">
                                                    <option value="1"
                                                            {{$driverMobileAllowCancelRoute == 1 ? 'selected' : ''}}>
                                                        Có
                                                    </option>
                                                    <option value="0" {{$driverMobileAllowCancelRoute == 0 ? 'selected' : ''}}>
                                                        Không
                                                    </option>
                                                </select>
                                                <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                                <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                                <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                            </div>
                                        </div>
                                        </div>
                                </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Có cho phép tài xế <b>Huỷ đơn hàng</b> hay không?
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            <select class="select2 setting-view-control disabled"
                                                    name="DriverMobile.AllowCancelOrder">
                                                <option value="1"
                                                        {{$driverMobileAllowCancelOrder == 1 ? 'selected' : ''}}>
                                                    Có
                                                </option>
                                                <option value="0" {{$driverMobileAllowCancelOrder == 0 ? 'selected' : ''}}>
                                                    Không
                                                </option>
                                            </select>
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-8">Có bắt buộc đính kèm ảnh trước khi hoàn thành đơn hàng ko?
                                        </div>
                                        <div class="col-md-4 edit-group-control">
                                            <select class="select2 setting-view-control disabled"
                                                    name="DriverMobile.ForceUploadBeforeArrival">
                                                <option value="1"
                                                        {{$driverMobileForceUploadBeforeArrival == 1 ? 'selected' : ''}}>
                                                    Có
                                                </option>
                                                <option value="0" {{$driverMobileForceUploadBeforeArrival == 0 ? 'selected' : ''}}>
                                                    Không
                                                </option>
                                            </select>
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <script>
        let url = '{{route('system-config.updateSystemConfig')}}';
    </script>
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'autoload/system-config'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush