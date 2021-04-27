@include('layouts.backend.elements.column_config._head',[
    'entity'=>'customer',
    'is_action' => true,
    'attributes' => getColumnConfig("customer"), 
    'configList'=> isset($configList) ? $configList : []])

{{-- <tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    <th class="text-center">{{trans('actions.action')}}</th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('customer_code')
                    <th class="text-center  header-sticky" name="customer_code"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('customer_code') !!}</th>
                    @break
                    @case('full_name')
                    <th name="full_name"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('full_name') !!}</th>
                    @break
                    @case('customer_group')
                    <th name="customer_group"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('customer_group') !!}</th>
                    @break
                    @case('mobile_no')
                    <th name="mobile_no"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('mobile_no') !!}</th>
                    @break
                    @case('type')
                    <th name="type"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('type') !!}</th>
                    @break
                    @case('username')
                    <th name="username"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('admin_user_username', trans('models.admin.attributes.username')) !!}</th>
                    @break
                    @case('email')
                    <th name="email"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('email',trans('models.admin.attributes.email')) !!}</th>
                    @break
                    @case('delegate')
                    <th name="delegate"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('delegate') !!}</th>
                    @break
                    @case('tax_code')
                    <th name="tax_code"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('tax_code') !!}</th>
                    @break
                    @case('birth_date')
                    <th name="birth_date"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('birth_date') !!}</th>
                    @break
                    @case('sex')
                    <th name="sex"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('sex') !!}</th>
                    @break
                    @case('current_address')
                    <th name="current_address"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('current_address',trans('models.customer.attributes.address')) !!}</th>
                    @break
                    @case('note')
                    <th name="note"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('note') !!}</th>
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
                @endswitch
            @endif
        @endforeach
    @else
        <th class="text-center  header-sticky"
            style="width: 180px">{!! Sorting::aLink('customer_code') !!}</th>
        <th style="width: 250px">{!! Sorting::aLink('full_name') !!}</th>
        <th style="width: 250px">{!! Sorting::aLink('customer_group') !!}</th>
        <th style="width: 200px">{!! Sorting::aLink('mobile_no') !!}</th>
        <th style="width: 220px">{!! Sorting::aLink('type') !!}</th>
        <th style="width: 200px">{!! Sorting::aLink('ins_date') !!}</th>
        <th style="width: 200px">{!! Sorting::aLink('upd_date') !!}</th>
    @endif
</tr>
<tr class="filter-row">
    <th class="text-center  header-sticky"></th>
    <th></th>
    <th class="text-center  header-sticky">
        @include('layouts.backend.elements._filter_string', ['field' => 'customer_code'])
    </th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('full_name')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'full_name'])</th>
                    @break
                    @case('customer_group')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer_group'])</th>
                    @break
                    @case('mobile_no')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'mobile_no'])</th>
                    @break
                    @case('type')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'type', 'element' => 'dropDown', 'options' => config('system.customer_type')])</th>
                    @break
                    @case('username')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'username'])</th>
                    @break
                    @case('email')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'email'])</th>
                    @break
                    @case('delegate')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'delegate'])</th>
                    @break
                    @case('tax_code')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'tax_code'])</th>
                    @break
                    @case('birth_date')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'birth_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('sex')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'sex', 'element' => 'dropDown', 'options' => config('system.sex')])</th>
                    @break
                    @case('current_address')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'current_address'])</th>
                    @break
                    @case('note')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'note'])</th>
                    @break
                    @case('ins_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'ins_id'])</th>
                    @break
                    @case('upd_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'upd_id'])</th>
                    @break
                    @case('ins_date')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'ins_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('upd_date')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'upd_date', 'class' => 'datepicker'])</th>
                    @break
                @endswitch
            @endif
        @endforeach
    @else
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'full_name'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer_group'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'mobile_no'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'type', 'element' => 'dropDown', 'options' => config('system.customer_type')])</th>
        <th>@include('layouts.backend.elements._filter_number',['field' => 'ins_date', 'class' => 'datepicker'])</th>
        <th>@include('layouts.backend.elements._filter_number',['field' => 'upd_date', 'class' => 'datepicker'])</th>
    @endif
</tr> --}}