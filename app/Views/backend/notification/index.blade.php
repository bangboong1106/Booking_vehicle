@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                <div class="form-inline m-b-10 justify-content-between">
                    <div class="row">
                        <div class="col-md-12 text-xs-center">
                            <h4 class="page-title">{{$title}}</h4>
                        </div>
                    </div>
                </div>
                <hr>
                <div>
                    <div id="notify-list"></div>
                    <div id="notify-load-more" style="display: none">Xem thêm</div>
                    <input type="hidden" id="notify-page" value="1">

                </div>
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._show_modal')
    <script>
        let urlLoadMoreNotify = '{{route('notify.loadNotifyPage')}}';
        let urlUpdateStatusNotify = '{{route('notification-log.updateReadNotify')}}';
        let urlVehicleNotifyDetail = '{{route('notify.vehicleNotifyDetail')}}';
        let urlUpdateDocument = '{{route('order.updateDocuments')}}';

    </script>

    <!--- Modal canh bao xe -->
    <div class="modal" id="modal_vehicle_notify_detail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="modal-label">Cảnh báo xe</h4>
                </div>
                <div class="modal-body" id="modal-vehicle-notify-content">
                </div>
            </div>
        </div>
    </div>
@endsection
