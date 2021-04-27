<div class="modal modal_show fade" id="modal_show">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" style="padding: 0 !important;"></div>
            <div class="modal-footer">
                <a href="#" class="btn btn-blue" data-dismiss="modal" style="width: 100px">
                    <i class="fa fa-times" style="margin-right: 8px"></i>Đóng
                </a>
            </div>

            <input type="hidden" class="url" value="">
            <input type="hidden" id="back_url_key" value="{{ isset($backUrlKey) ? $backUrlKey : '' }}">
        </div>
    </div>
</div>