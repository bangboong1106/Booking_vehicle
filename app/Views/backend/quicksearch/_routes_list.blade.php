<?php
$modal = isset($modal) ? $modal : 'route_modal';
$table = isset($table) ? $table : 'table_route';
$button = isset($button) ? $button : 'btn-route';

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
                <h4 class="modal-title">Chọn chuyến xe</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="width: 100px;">{{ trans('models.route.attributes.route_code') }}</th>
                        <th style="width: 300px;">{{ trans('models.route.attributes.name') }}</th>
                        <th style="width: 150px;">Xe và tài xế</th>
                        <th style="width: 150px;">{{ trans('models.route.attributes.ETA') }}</th>
                        <th style="width: 150px;">{{ trans('models.route.attributes.ETD') }}</th>
                        <th style="width: 150px;">Tỉ lệ loading</th>
                    
                    </tr>
                    </thead>
                </table>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span> {{trans('actions.back')}}
                        </button>
                    </span>
                    <span>
                        <button class="btn btn-success" id="{{$button}}">
                            <span class="ls-icon ls-icon-check" aria-hidden="true"></span> {{trans('actions.submit')}}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>