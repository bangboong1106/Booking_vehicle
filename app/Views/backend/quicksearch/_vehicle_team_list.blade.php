<?php
$modal = isset($modal) ? $modal : 'vehicle_team_modal';
$table = isset($table) ? $table : 'table_vehicle_team';
$button = isset($button) ? $button : 'btn-vehicle-team';

?>
<div id="{{$modal}}" class="modal fade" role="dialog" aria-labelledby="myModalLabel" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Chọn đội tài xế</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>{{ trans('models.vehicle_team.attributes.code') }}</th>
                        <th>{{ trans('models.vehicle_team.attributes.name') }}</th>

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
                        <button class="btn btn-success" id="{{$button}}">
                            <span class="ls-icon ls-icon-check" aria-hidden="true"></span> {{trans('actions.choice')}}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>