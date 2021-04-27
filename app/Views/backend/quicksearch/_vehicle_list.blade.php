<style>
    div.toolbar {
        float: left;
    }

    div.toolbar select {
        width: 150px;
        border: 1px solid #e5e4e5;
        height: 38px;
        background: white;
        border-radius: 4px;
        margin: 4px;
    }
</style>
<?php
$modal = isset($modal) ? $modal : 'vehicle_modal';
$table = isset($table) ? $table : 'table_vehicles';
$button = isset($button) ? $button : 'btn-vehicle';

?>
<div id="{{$modal}}" class="modal fade" role="dialog" aria-labelledby="myModalLabel" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Chọn xe</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th style="width: 80px">STT</th>
                        <th style="width: 120px">{{ trans('models.vehicle.attributes.reg_no') }}</th>
                        <th class="text-right" style="width: 130px;">{{ trans('models.vehicle.attributes.volume') }}
                            (m³)
                        </th>
                        <th class="text-right" style="width: 130px">{{ trans('models.vehicle.attributes.weight') }}
                            (kg)
                        </th>
                        <th class="text-right"
                            style="width: 300px">{{trans('models.vehicle.attributes.bag_size')  .'('. trans('models.vehicle.attributes.length_width_height').')' }}
                            (m)
                        </th>
                        @if(isset($location) && $location == true)
                            <th class="text-right" style="width: 160px">Khoảng cách (km)</th>
                        @else
                            <th class="text-right" style="width: 160px; display: none">Khoảng cách (km)</th>
                        @endif
                    </tr>
                    </thead>
                </table>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span> {{trans('actions.close')}}
                        </button>
                    </span>
                    <span>
                        <button class="btn btn-blue" id="{{$button}}" style="width: 100px">
                            <span class="ls-icon ls-icon-check" aria-hidden="true"></span> <i class="fa fa-check"
                                                                                              style="margin-right: 8px"></i>{{trans('actions.choice')}}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>