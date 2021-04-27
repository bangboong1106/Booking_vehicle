<div class="modal fade order-modal" id="merge_order_modal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Gộp đơn</h4>
            </div>
            <div class="modal-body" style="overflow-y: visible">
                @if(empty($errorMessage))
                    <div class="form-group">
                        Bạn có muốn gộp các đơn hàng <b><span id="span_orders">{{$orderCodes}}</span></b>
                        không?
                    </div>
                @else
                    <div class="form-group"> {{$errorMessage}}</div>
                @endif
            </div>
            <div class="modal-footer">
                <div class="col-md-10 text-right">
                    <a href="#" class="btn " data-dismiss="modal">{{trans('actions.close')}}</a>
                    @if(empty($errorMessage))
                        <button type="button" id="btn_confirm_merge_order_save"
                                data-url="{{ route('order.mergeOrderSave') }}"
                                class="btn btn-blue" style="width: 120px">
                            <i class="fa fa-save" style="margin-right: 8px"></i>Cập nhật
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>