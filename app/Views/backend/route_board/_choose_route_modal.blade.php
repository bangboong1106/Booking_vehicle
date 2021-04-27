<div class="modal" id="choose_route_modal">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Thao tác đơn hàng trên chuyến cho xe <span class="vehicle-title"></span></h4>
            </div>
            <div class="modal-body" style="min-height: 400px; overflow-y: scroll; max-height: 600px">
            </div>
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <a class="float-left" target="_blank"
                               href="{{env('HELP_DOMAIN','').trans('helps.route_board.choose_route')}}"
                               data-toggle="tooltip" data-placement="top" title=""
                               data-original-title="{{trans('actions.help')}}">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-md-10 text-right">
                            <button type="button" class="btn btn-default" id="button_close_choose_route"
                                    data-dismiss="modal">
                                    <i class="fa fa-close" aria-hidden="true" style="margin-right: 8px"></i>
                                    {{trans('actions.cancel')}}</button>
                            <button type="button" class="btn btn-primary" id="button_save_choose_route" data-url={{route('route-board.merge-route')}}>
                                <i class="fa fa-compress" aria-hidden="true" style="margin-right: 8px"></i>Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>