<div id="table-scroll" class="table-scroll deleted-table">
    <div id="deleted-table" class="main-table">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr class="active">
                    <th class="text-center header-sticky" style="width:200px">{!! Sorting::aLink('code') !!}</th>
                    <th style="width: 250px">{!! Sorting::aLink('name') !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('order_no') !!}</th>
                    <th style="width:180px">{!! Sorting::aLink('status') !!}</th>
                    <th style="width: 180px">{!! Sorting::aLink('order_date') !!}</th>
                    <th style="width: 250px">{!! Sorting::aLink('customer_id') !!}</th>
                    <th style="width: 270px">{!! Sorting::aLink('customer_name') !!}</th>
                    <th style="width:230px">{!! Sorting::aLink('customer_mobile_no') !!}</th>
                    <th style="width:250px">{!! Sorting::aLink('ETA_date_reality',
                        trans('models.order_customer.attributes.ETA_date_reality')) !!}</th>
                    <th style="width:250px">{!! Sorting::aLink('location_destination',
                        trans('models.order_customer.attributes.location_destination')) !!}</th>
                    <th style="width: 250px">{!! Sorting::aLink('location_arrival',
                        trans('models.order_customer.attributes.location_arrival')) !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('amount') !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('commission_amount') !!}</th>
                    <th style="width: 220px">{!! Sorting::aLink('ETD_date',
                        trans('models.order_customer.attributes.ETD_date')) !!}</th>
                    <th style="width: 220px">{!! Sorting::aLink('ETA_date',
                        trans('models.order_customer.attributes.ETA_date')) !!}</th>
                    <th style="width: 150px">{!! Sorting::aLink('route_number') !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('weight') !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('volume') !!}</th>
                    <th style="width: 200px">{!! Sorting::aLink('distance') !!}</th>
                </tr>
            </thead>
            <tbody id="body_content">
                @include('backend.order_customer._list_deleted')
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
