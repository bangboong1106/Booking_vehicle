<div id="table-scroll" class="table-scroll">
    <div id="deleted-table" class="main-table">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr class="active">
                <th>{!! Sorting::aLink('username') !!}</th>
                <th>{!! Sorting::aLink('email') !!}</th>
                <th>{!! Sorting::aLink('role') !!}</th>
                <th>{!! Sorting::aLink('ins_date') !!}</th>
                <th>{!! Sorting::aLink('upd_date') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('ins_id') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('upd_id') !!}</th>
            </tr>
            </thead>
            <tbody id="body_content">
                @include('backend.admin._list_deleted')
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