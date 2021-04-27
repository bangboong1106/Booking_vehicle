<?php
$modal = isset($modal) ? $modal : 'customer_modal';
$table = isset($table) ? $table : 'table_customer';
$button = isset($button) ? $button : 'btn-customer';

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
                <h4 class="modal-title">Chọn khách hàng</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>{{ trans('models.customer.attributes.customer_code') }}</th>
                        <th>{{ trans('models.customer.attributes.full_name') }}</th>
                        <th>{{ trans('models.customer.attributes.mobile_no') }}</th>

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