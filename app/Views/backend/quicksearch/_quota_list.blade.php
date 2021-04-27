<?php
$modal = isset($modal) ? $modal : 'quota_modal';
$table = isset($table) ? $table : 'table_quota';
$button = isset($button) ? $button : 'btn-quota';

?>
<div id="{{$modal}}" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     style="display: none; z-index: 99999;"
     aria-hidden="true">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Chọn bảng định mức chi phí</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="width: 150px;">{{ trans('models.quota.attributes.quota_code') }}</th>
                        <th style="width: 250px;">{{ trans('models.quota.attributes.name') }}</th>
                        <th style="width: 300px;">{{ trans('models.quota.attributes.locations') }}</th>
                        <th style="width: 200px; text-align: right;">{{ trans('models.quota.attributes.total_cost') }}</th>
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