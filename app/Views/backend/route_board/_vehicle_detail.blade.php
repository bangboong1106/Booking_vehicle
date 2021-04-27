<div class="modal" id="vehicle_detail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label"></h4>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item vehicle-detail-group-item">
                            <span>
                                 <p class="text-primary text-item">Dung tích: </p>
                                 <strong id="vehicle_volume"></strong>
                            </span>
                        <span>
                                <p class="text-primary text-item">Trọng lượng: </p>
                                <strong id="vehicle_weight"></strong>
                        </span>

                    </li>
                    <li class="list-group-item">
                        <p class="text-primary text-item">Kích thước bao (Dài x Rộng x Cao): </p>
                        <strong id="vehicle_bag"></strong>
                    </li>
                    <li class="list-group-item vehicle-detail-group-item">
                            <span>
                                 <p class="text-primary">Tài xế: </p>
                                 <strong id="vehicle_primary"></strong>
                            </span>
                        {{--<span>
                                <p class="text-primary">Phụ xe: </p>
                                <strong id="vehicle_secondary"></strong>
                            </span>--}}
                    </li>
                </ul>
                <div class="map" id="map" style="height: 300px;"></div>
            </div>
            {{--<div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">{{trans('actions.done')}}</button>
            </div>--}}
        </div>
    </div>
</div>
