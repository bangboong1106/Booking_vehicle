@include('layouts.backend.elements.column_config._head',[
    'entity'=>'payroll',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("payroll"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- <tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    <th class="text-center">{{trans('actions.action')}}</th>
    <th class="text-center  header-sticky"
        style="width: 150px">{!! Sorting::aLink('code') !!}</th>
    <th style="width: 250px">{!! Sorting::aLink('name') !!}</th>
    <th style="width: 200px">{!! Sorting::aLink('description') !!}</th>
    <th style="width:180px">{!! Sorting::aLink('date_from') !!}</th>
    <th style="width: 180px">{!! Sorting::aLink('date_to') !!}</th>
    <th style="width: 200px">{!! Sorting::aLink('isDefault') !!}</th>
    <th style="width: 200px">{!! Sorting::aLink('isApplyAll') !!}</th>
    <th style="width: 300px">{!! Sorting::aLink('customer_groups') !!}</th>

</tr>
<tr class="filter-row">
    <th class="text-center  header-sticky"></th>
    <th></th>
    <th class="text-center  header-sticky">
        @include('layouts.backend.elements._filter_string', ['field' => 'code'])
    </th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'description'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'date_from', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'date_to', 'class' => 'datepicker'])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'isDefault', 'element' => 'dropDown', 'options' => config('system.option') ])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'isApplyAll', 'element' => 'dropDown', 'options' => config('system.option') ])</th>
    <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer_groups'])</th>


</tr> --}}