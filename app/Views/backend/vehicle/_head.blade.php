@include('layouts.backend.elements.column_config._head',[
    'entity'=>'vehicle',
    'is_action' => true,
    'attributes' => getColumnConfig("vehicle"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- <tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    <th class="text-center">{{trans('actions.action')}}</th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('reg_no')
                    <th class="text-center  header-sticky" name="reg_no"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('reg_no') !!}</th>
                    @break
                    @case('group_id')
                    <th name="group_id"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('group_id') !!}</th>
                    @break
                    @case('drivers_name')
                    <th name="drivers_name"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('drivers_name','Danh sách tài xế') !!}</th>
                    @break
                    @case('weight')
                    <th name="weight"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('weight',trans('models.vehicle.attributes.weight'). ' (kg)') !!}</th>
                    @break
                    @case('volume')
                    <th name="volume"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('volume',trans('models.vehicle.attributes.volume'). ' (m³)') !!}</th>
                    @break
                    @case('length_width_height')
                    <th name="length_width_height"
                        style="width: {{$config['width']?$config['width']:250}}px">{{ trans('models.vehicle.attributes.length_width_height') }}
                        (m)
                    </th>
                    @break
                    @case('status')
                    <th name="status"
                        style="width: {{$config['width']?$config['width']:180}}px">{!! Sorting::aLink('status') !!}</th>
                    @break
                    @case('type')
                    <th name="type"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('type') !!}</th>
                    @break
                    @case('active')
                    <th name="active"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('active') !!}</th>
                    @break
                    @case('current_location')
                    <th name="current_location"
                        style="width: {{$config['width']?$config['width']:300}}px">{!! Sorting::aLink('current_location') !!}</th>
                    @break
                    @case('driver')
                    <th name="driver"
                        style="width: {{$config['width']?$config['width']:150}}px"><a class="table-sorting" href="#"
                                                                                      data-sort="driver">{{ trans('models.vehicle.attributes.driver') }}</a>
                    </th>
                    @break
                    @case('category_of_barrel')
                    <th name="category_of_barrel"
                        style="width: {{$config['width']?$config['width']:180}}px">{!! Sorting::aLink('category_of_barrel',trans('models.vehicle_general_info.attributes.category_of_barrel')) !!}</th>
                    @break
                    @case('weight_lifting_system')
                    <th name="weight_lifting_system"
                        style="width: {{$config['width']?$config['width']:180}}px">{!! Sorting::aLink('weight_lifting_system',trans('models.vehicle_general_info.attributes.weight_lifting_system')) !!}</th>
                    @break
                    @case('max_fuel')
                    <th name="max_fuel"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('max_fuel',trans('models.vehicle_general_info.attributes.max_fuel_header').' (lít)') !!}</th>
                    @break
                    @case('max_fuel_with_goods')
                    <th name="max_fuel_with_goods"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('max_fuel_with_goods',trans('models.vehicle_general_info.attributes.max_fuel_with_goods_header').' (lít)') !!}</th>
                    @break
                    @case('register_year')
                    <th name="register_year"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('register_year',trans('models.vehicle_general_info.attributes.register_year')) !!}</th>
                    @break
                    @case('brand')
                    <th name="brand"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('brand',trans('models.vehicle_general_info.attributes.brand')) !!}</th>
                    @break
                    @case('gps_company_id')
                    <th name="gps_company_id"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('gps_company_id') !!}</th>
                    @break
                    @case('ins_id')
                    <th style="width: {{$config['width']?$config['width']:150}}px"
                        name="ins_id">{!! Sorting::aLink('ins_id') !!}</th>
                    @break
                    @case('upd_id')
                    <th style="width: {{$config['width']?$config['width']:150}}px"
                        name="upd_id">{!! Sorting::aLink('upd_id') !!}</th>
                    @break
                    @case('ins_date')
                    <th style="width: {{$config['width']?$config['width']:200}}px"
                        name="ins_date">{!! Sorting::aLink('ins_date') !!}</th>
                    @break
                    @case('upd_date')
                    <th style="width: {{$config['width']?$config['width']:200}}px"
                        name="upd_date">{!! Sorting::aLink('upd_date') !!}</th>
                    @break
                    @case('repair_distance')
                    <th style="width: {{$config['width']?$config['width']:200}}px"
                        name="repair_distance">{!! Sorting::aLink('repair_distance') !!}</th>
                    @break
                    @case('repair_date')
                    <th style="width: {{$config['width']?$config['width']:200}}px"
                        name="repair_date">{!! Sorting::aLink('repair_date') !!}</th>
                    @break
                @endswitch
            @endif
        @endforeach
    @else
        <th class="text-center  header-sticky"
            style="width: 150px">{!! Sorting::aLink('reg_no') !!}</th>
        <th style="width: 200px">{!! Sorting::aLink('group_id') !!}</th>
        <th style="width: 200px">{!! Sorting::aLink('drivers_name','Danh sách tài xế') !!}</th>
        <th style="width: 150px">{!! Sorting::aLink('weight',trans('models.vehicle.attributes.weight'). ' (kg)') !!}</th>
        <th style="width: 150px">{!! Sorting::aLink('volume',trans('models.vehicle.attributes.volume'). ' (m³)') !!}</th>
        <th style="width: 250px">{{ trans('models.vehicle.attributes.bag_size').'/'.trans('models.vehicle.attributes.length_width_height') }}
            (m)
        </th>
    @endif
</tr>
<tr class="filter-row">
    <th class="text-center  header-sticky"></th>
    <th></th>
    <th class="text-center  header-sticky">
        @include('layouts.backend.elements._filter_string', ['field' => 'reg_no'])
    </th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('group_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'group_id', 'element' => 'dropDown', 'options' => $vehicle_groups])</th>
                    @break
                    @case('drivers_name')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'driver_id', 'element' => 'dropDown', 'options' => $drivers])</th>
                    @break
                    @case('weight')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'weight'])</th>
                    @break
                    @case('volume')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'volume'])</th>
                    @break
                    @case('length_width_height')
                    <th></th>
                    @break
                    @case('status')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'status', 'element' => 'dropDown', 'options' => config('system.vehicle_status') ])</th>
                    @break
                    @case('type')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'type', 'element' => 'dropDown', 'options' => config('system.vehicle_type')])</th>
                    @break
                    @case('active')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle|active', 'element' => 'dropDown', 'options' => config('system.vehicle_active')])</th>
                    @break
                    @case('current_location')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'current_location'])</th>
                    @break
                    @case('category_of_barrel')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'category_of_barrel'])</th>
                    @break
                    @case('weight_lifting_system')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'weight_lifting_system'])</th>
                    @break
                    @case('max_fuel')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'max_fuel'])</th>
                    @break
                    @case('max_fuel_with_goods')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'max_fuel_with_goods'])</th>
                    @break
                    @case('register_year')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'register_year'])</th>
                    @break
                    @case('brand')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'brand'])</th>
                    @break
                    @case('gps_company_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'gps_company_id', 'element' => 'dropDown', 'options' => $gps_company_list])</th>
                    @break
                    @case('ins_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle|ins_id'])</th>
                    @break
                    @case('upd_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle|upd_id'])</th>
                    @break
                    @case('ins_date')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle|ins_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('upd_date')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle|upd_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('repair_distance')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'repair_distance'])</th>
                    @break
                    @case('repair_date')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'repair_date', 'class' => 'datepicker'])</th>
                    @break
                @endswitch
            @endif
        @endforeach
    @else
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'group_id', 'element' => 'dropDown', 'options' => $vehicle_groups])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'drivers_name'])</th>
        <th>@include('layouts.backend.elements._filter_number', ['field' => 'weight'])</th>
        <th>@include('layouts.backend.elements._filter_number', ['field' => 'volume'])</th>
        <th></th>
    @endif
</tr> --}}