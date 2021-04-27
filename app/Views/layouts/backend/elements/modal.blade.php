<div class="modal fade" id="del-confirm" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{trans('messages.confirm_delete')}}</h4>
            </div>
            <div class="modal-body">Bạn chắc chắn muốn xóa <b><span></span></b> không?</div>
            <div class="modal-footer">
                {!! MyForm::delete() !!}
                <a href="#" class="btn btn-default" data-dismiss="modal">{{trans('actions.cancel')}}</a>
                <button type="submit" class="btn btn-primary">{{trans('actions.destroy')}}</button>
                {!! MyForm::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mass_destroy_confirm" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{trans('messages.confirm_bulk_delete')}}</h4>
            </div>
            <div class="modal-body">{{trans('messages.confirm_bulk_delete_message')}}
                <br>{{trans('messages.are_you_sure')}}
            </div>
            <div class="modal-footer">
                {!! MyForm::massDelete() !!}
                <a href="#" class="btn close-parent-modal" data-parent-modal="mass_destroy_confirm"
                   data-dismiss="modal">{{trans('actions.cancel')}}</a>
                <button type="submit" class="btn btn-danger">{{trans('actions.destroy')}}</button>
                {!! MyForm::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="preview-image">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" id="preview-img" width="100%" alt="">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="used_modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thông báo<span></span></h4>
            </div>
            <div class="modal-body">
                Đối tượng đã được sử dụng. Bạn không thể xóa.
            </div>
            <div class="modal-footer">
                <button id="close-confirm-costs" type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="has_delete_modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thông báo<span></span></h4>
            </div>
            <div class="modal-body">
                Đối tượng không phải bạn tạo hoặc không còn quyền thao tác. Bạn không thể xóa.
            </div>
            <div class="modal-footer">
                <button id="close-confirm-costs" type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="order_prevent" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thông báo<span></span></h4>
            </div>
            <div class="modal-body">
                Bạn không thể thao tác với đơn hàng vận tải đã được xác nhận.
            </div>
            <div class="modal-footer">
                <button id="close-confirm-costs" type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>


@if(!empty($excel))
    @include('layouts.backend.elements.excel._import_export_modal')
@endif

@include('layouts.backend.elements._add_modal')

@if(Session()->has('used_message'))
    <script>
        $(document).ready(function () {
            $('#used_modal').modal('show');
        });
    </script>
@endif

@if(Session()->has('has_delete_modal'))
    <script>
        $(document).ready(function () {
            $('#has_delete_modal').modal('show');
        });
    </script>
@endif

