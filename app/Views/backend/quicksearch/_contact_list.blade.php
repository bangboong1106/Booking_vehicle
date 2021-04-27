<?php
$modal = isset($modal) ? $modal : 'contact_modal';
$table = isset($table) ? $table : 'table_contacts';
$button = isset($button) ? $button :  'btn-contact';  

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
                <h4 class="modal-title">Chọn liên hệ</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="width:100%" id="{{$table}}">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="width: 200px">{{ trans('models.contact.attributes.contact_name') }}</th>
                        <th style="width: 180px">{{ trans('models.contact.attributes.phone_number') }}</th>
                        <th style="width: 180px">{{ trans('models.contact.attributes.email') }}</th>
                        <th style="width: 300px">{{ trans('models.contact.attributes.location_id') }}</th>
                        {{--<th>{{ trans('models.contact.attributes.active') }}</th>--}}
                    </tr>
                    </thead>
                </table>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span>
                            <i class="fa fa-backward" style="margin-left: 8px"></i> {{trans('actions.close')}}
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