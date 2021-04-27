<div id="table-scroll" class="table-scroll deleted-table">
    <div id="deleted-table" class="main-table">
        <table class="table table-bordered table-hover">
            <thead id="head_content">
            <tr class="active">
                <th class="text-center  header-sticky" style="width:150px">{!! Sorting::aLink('customer_code') !!}</th>
                <th style="width:200px">{!! Sorting::aLink('full_name') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('mobile_no') !!}</th>
                <th style="width:200px">{!! Sorting::aLink('type') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('admin_user_username', trans('models.admin.attributes.username')) !!}</th>
                <th style="width:200px">{!! Sorting::aLink('email',trans('models.admin.attributes.email')) !!}</th>
                <th style="width:200px">{!! Sorting::aLink('delegate') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('tax_code') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('birth_date') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('sex') !!}</th>
                <th style="width:250px">{!! Sorting::aLink('current_address',trans('models.customer.attributes.address')) !!}</th>
                <th style="width:200px">{!! Sorting::aLink('note') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('ins_id') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('upd_id') !!}</th>
            </tr>
            </thead>
            <tbody id="body_content">
            @include('backend.customer._list_deleted')
            </tbody>
        </table>
    </div>

</div>
<div class="row" id="paginate_content">
    <div class="col-md-5 col-sm-12 m-t-15">
        @include('layouts.backend.elements.pagination_info')

    </div>
    <div class="col-md-7 col-sm-12">
        @include('layouts.backend.elements.pagination', ['isAjax'=> true, 'hidePerPage' => true])
    </div>
</div>