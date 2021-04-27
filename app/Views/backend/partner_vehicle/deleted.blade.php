<div id="table-scroll" class="table-scroll deleted-table">
    <div id="deleted-table" class="main-table">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr class="active">
                <th class="text-center  header-sticky" style="width: 150px">{!! Sorting::aLink('reg_no') !!}</th>
                <th style="width:200px">{!! Sorting::aLink('group_id') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('weight',trans('models.vehicle.attributes.weight'). ' (kg)') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('volume',trans('models.vehicle.attributes.volume'). ' (m³)') !!}</th>
                <th style="width:250px">{{ trans('models.vehicle.attributes.bag_size').'/'.trans('models.vehicle.attributes.length_width_height') }}(m)</th>
                <th style="width:180px">{!! Sorting::aLink('status') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('type') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('active') !!}</th>
                <th style="width:300px">{!! Sorting::aLink('current_location') !!}</th>
                <th style="width:180px">{!! Sorting::aLink('category_of_barrel',trans('models.vehicle_general_info.attributes.category_of_barrel')) !!}</th>
                <th style="width:180px">{!! Sorting::aLink('weight_lifting_system',trans('models.vehicle_general_info.attributes.weight_lifting_system')) !!}</th>
                <th style="width:250px">{!! Sorting::aLink('max_fuel',trans('models.vehicle_general_info.attributes.max_fuel').' (lít)') !!}</th>
                <th style="width:150px">{!! Sorting::aLink('register_year',trans('models.vehicle_general_info.attributes.register_year')) !!}</th>
                <th style="width:150px">{!! Sorting::aLink('brand',trans('models.vehicle_general_info.attributes.brand')) !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('ins_id') !!}</th>
                <th style="width: 150px">{!! Sorting::aLink('upd_id') !!}</th>
            </tr>
            </thead>
            <tbody id="body_content">
                @include('backend.vehicle._list_deleted')
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