<div class="modal fade" id="split-order" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Tách đơn hàng vận tải</h4>
            </div>
            <div class="modal-body">
                <label for="quantities-order">Số lượng muốn tách :</label>
                <input class="number-input form-control" type="text" name="quantities-order" id="quantities-order" value="2">
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">{{trans('actions.cancel')}}</a>
                <button class="btn btn-primary" id="submit-quantity-order" data-url>Tách</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade show" id="modal-split-order">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Tách đơn hàng vận tải</h4>
            </div>
            <div class="modal-body" id="content-split-order" style="padding-left:0px!important; padding-right:0px!important;">

            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">{{trans('actions.cancel')}}</a>
                <button type="submit" class="btn btn-primary" id="submit-split-order">Tách</button>
            </div>
        </div>
    </div>
</div>