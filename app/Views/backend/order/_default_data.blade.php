
<div class="modal fade" id="default_data_modal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Lựa chọn dữ liệu mặc định của khách hàng</h4>
            </div>
            <div class="modal-body" style="overflow-y: scroll;
            max-height: 480px;
            height: 100%;">

            </div>
            <div class="modal-footer">
                <div class="col-md-10 text-right">
                    <a href="#" class="btn " data-dismiss="modal">{{trans('actions.close')}}</a>
                    <button type="button" id="btn-default-data"
                            class="btn btn-blue" style="width: 120px">
                        <i class="fa fa-save" style="margin-right: 8px"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>