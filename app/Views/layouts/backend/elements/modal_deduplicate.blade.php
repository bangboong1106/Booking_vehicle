<div class="modal fade" id="modal_deduplicate" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gộp trùng dữ liệu</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-12">
                        <i><span>Bạn vui lòng chọn lại bản ghi được giữ lại.<br /> Các bản ghi khác sẽ được xoá bỏ khỏi
                                hệ thống và các danh sách liên quan đến bản ghi đó</span></i>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12 body">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="btn-process-deduplicate"
                    data-url="{{ route($routePrefix . '.process-deduplicate') }}">
                    <i class="fa fa-compress" style="margin-right: 8px"></i>Gộp
                </button>
            </div>
        </div>
    </div>
</div>
