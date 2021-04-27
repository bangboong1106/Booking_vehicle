@include('layouts.backend.elements.column_config._head',[
    'entity'=>'route',
    'is_action' => true,
    'attributes' => getColumnConfig("route"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- <tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    <th class="text-center">{{trans('actions.action')}}</th>
    <th class="text-center  header-sticky"
        style="width: 150px">{!! Sorting::aLink('route_code') !!}</th>
    <th style="width: 250px">{!! Sorting::aLink('name') !!}</th>
    <th style="width:180px">{!! Sorting::aLink('route_status') !!}</th>
    <th style="width: 150px">{!! Sorting::aLink('vehicle') !!}</th>
    <th style="width:150px">{!! Sorting::aLink('primary_driver') !!}</th>
    <th style="width:250px">{!! Sorting::aLink('order',trans('models.route.attributes.order')) !!}</th>
    <th style="width:250px">{!! Sorting::aLink('location_destination_id',trans('models.route.attributes.location_destination_id')) !!}</th>
    <th style="width:250px">{!! Sorting::aLink('location_arrival_id',trans('models.route.attributes.location_destination_id')) !!}</th>
    <th style="width: 200px">{!! Sorting::aLink('is_approved') !!}</th>
    <th style="width:150px">{!! Sorting::aLink('final_cost') !!}</th>
    <th style="width:220px">{!! Sorting::aLink('ETD_date_reality',trans('models.route.attributes.ETD_reality')) !!}</th>
    <th style="width:220px">{!! Sorting::aLink('ETA_date_reality',trans('models.route.attributes.ETA_reality')) !!}</th>
    <th style="width:220px">{!! Sorting::aLink('ETD_date',trans('models.route.attributes.ETD')) !!}</th>
    <th style="width:220px">{!! Sorting::aLink('ETA_date',trans('models.route.attributes.ETA')) !!}</th>
    <th style="width:150px">{!! Sorting::aLink('gps_distance') !!}</th>
    <th style="width:150px">{!! Sorting::aLink('capacity_weight_ratio') !!}</th>
    <th style="width:150px">{!! Sorting::aLink('capacity_volume_ratio') !!}</th>
    <th style="width: 250px">{!! Sorting::aLink('route_note') !!}</th>
</tr>
<tr class="filter-row">
    <th class="text-center  header-sticky"></th>
    <th></th>
    <th class="text-center  header-sticky">
        @include('layouts.backend.elements._filter_string', ['field' => 'route_code'])
    </th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'route_status', 'element' => 'dropDown', 'options' => config('system.route_status') ])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'primary_driver'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'order'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'location_destination_id'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'location_arrival_id'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'is_approved', 'element' => 'dropDown', 'options' => config('system.route_is_approved') ])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'final_cost'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'ETD_date_reality', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'ETA_date_reality', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'ETD_date', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'ETA_date', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'gps_distance'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'capacity_weight_ratio'])</th>
    <th>@include('layouts.backend.elements._filter_number', ['field' => 'capacity_volume_ratio'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'route_note'])</th>
</tr> --}}