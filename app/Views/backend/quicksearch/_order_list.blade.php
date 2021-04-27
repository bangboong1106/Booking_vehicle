<?php
$modal = isset($modal) ? $modal : 'order_modal';
$table = isset($table) ? $table : 'table_order';
$button = isset($button) ? $button : 'btn-order';

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
                <h4 class="modal-title">Chọn đơn hàng</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th style="width: 80px;"></th>
                        <th class="text-left"
                            style="width: 150px;">{{ trans('models.order.attributes.order_code') }}</th>
                        <th class="text-left"
                            style="width: 250px;">{{ trans('models.order.attributes.order_no') }}</th>
                        <th class="text-left"
                            style="width: 250px;">{{ trans('models.order.attributes.status') }}</th>
                        <th class="text-left"
                            style="width: 250px;">{{ trans('models.order.attributes.precedence') }}</th>
                        <th class="text-left"
                            style="width: 350px;">{{ trans('models.order.attributes.customer_name') }}</th>
                        <th class="text-left"
                            style="width: 150px;">{{ trans('models.customer.attributes.mobile_no') }}</th>
                    </tr>
                    </thead>
                </table>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span> <i class="fa fa-backward"
                                                                                              style="margin-left: 8px"></i> {{trans('actions.close')}}
                        </button>
                    </span>
                    <span>
                        <button class="btn btn-blue" id="{{$button}}">
                            <span class="ls-icon ls-icon-check" aria-hidden="true"></span> {{trans('actions.choice')}}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>