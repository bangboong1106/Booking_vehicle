@include('layouts.backend.elements._context_menu')
@include('layouts.backend.elements._show_modal')
@include('layouts.backend.elements.modal_lock')
@include('backend.order.declaration_export')
@include('backend.order.history')
@include('backend.order.import_export_modal')
@include('backend.order.update_documents_modal')
<div class="modal fade" id="modal_template">
    <div class="modal-dialog modal-md">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade modal_add" id="modal_update_revenue">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade" id="modal_update_vin_no">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade" id="modal_order_editor" data-url="{{ route('order-editor.index') }}">
    <div class="modal-dialog modal-xlg modal-fullscreen">
        <div class="modal-content">
            <div>
                <div class="modal-header">
                    <button type="button" class="maximize" style="display: none"><i class="fa fa-window-maximize"></i></button>
                    <button type="button" class="minimize" ><i class="fa fa-window-minimize"></i>
                    </button>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="modal-label">Cập nhật đơn hàng với {{config('constant.APP_NAME')}} Sheet</h4>
                </div>
                <div class="modal-body" style="max-height: 95vh !important">
                    <iframe id="frame-order-editor" title="Nhập đơn hàng" scrolling="no" frameBorder="0" width="100%" style="display:none; height: 90vh"></iframe>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</div>