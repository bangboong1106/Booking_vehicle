<div id="table-scroll" class="table-scroll deleted-table">
    <div id="deleted-table" class="main-table">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr class="active">
                <th class="text-center  header-sticky" style="width:200px">{!! Sorting::aLink('order_code') !!}</th>
                <th style="width: 170px">{!! Sorting::aLink('status') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('order_no') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('bill_no') !!}</th>
                <th style="width: 320px">{!! Sorting::aLink('customer_name') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('customer_mobile_no') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('order_date') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('amount') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('insured_goods') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('quantity') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('volume',trans('models.order.attributes.volume'). ' (m³)') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('weight',trans('models.order.attributes.weight'). ' (kg)') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('ETD_date',trans('models.order.attributes.ETD')) !!}</th>
                <th style="width: 220px">{!! Sorting::aLink('ETD_date_reality',trans('models.order.attributes.ETD_reality')) !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('location_destination') !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('contact_mobile_no_destination') !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('contact_name_destination') !!}</th>
                <th style="width: 300px">{!! Sorting::aLink('loading_destination_fee',trans('models.order.attributes.loading_destination_fee').' (VND)') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('ETA_date',trans('models.order.attributes.ETA')) !!}</th>
                <th style="width: 220px">{!! Sorting::aLink('ETA_date_reality',trans('models.order.attributes.ETA_reality')) !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('location_arrival') !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('contact_mobile_no_arrival') !!}</th>
                <th style="width: 250px">{!! Sorting::aLink('contact_name_arrival') !!}</th>
                <th style="width: 300px">{!! Sorting::aLink('loading_arrival_fee',trans('models.order.attributes.loading_arrival_fee').' (VND)') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('precedence') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('ins_id') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('upd_id', trans('models.common.delete_id')) !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('ins_date') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('upd_date', trans('models.common.delete_date')) !!}</th>
                <th style="width: 300px">{!! Sorting::aLink('note') !!}</th>
                <th style="width: 200px">{!! Sorting::aLink('extend_cost', trans('models.order.attributes.extend_cost'). ' (VND)') !!}</th>
            </tr>
            </thead>
            <tbody id="body_content">
                @include('backend.order._list_deleted')
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