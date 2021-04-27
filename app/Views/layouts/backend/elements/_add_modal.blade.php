<div class="modal modal_add fade" id="modal_add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
            <input type="hidden" class="url" value="">
        </div>
    </div>
</div>

<div class="modal" id="add_complete" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">{{trans('messages.add_complete')}}
            </div>
            <div class="modal-footer">
                <a href="#" class="btn renew-btn" data-dismiss="modal" data-model="order"
                   data-url="{{route('order.advance')}}">{{trans('actions.renew')}}</a>
                <a href="#" class="btn" data-dismiss="modal">OK</a>
            </div>
        </div>
    </div>
</div>