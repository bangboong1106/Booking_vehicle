@include('layouts.backend.elements.column_config._head',[
    'entity'=>'driver',
    'is_action' => true,
    'attributes' => getColumnConfig("driver"), 
    'configList'=> isset($configList) ? $configList : []])

{{-- <tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    <th class="text-center">{{trans('actions.action')}}</th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('driver_code')
                    <th class="text-center  header-sticky" name="driver_code"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('code') !!}</th>
                    @break
                    @case('username')
                    <th name="username"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('admin_user_username', trans('models.admin.attributes.username')) !!}</th>
                    @break
                    @case('full_name')
                    <th name="full_name"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('full_name') !!}</th>
                    @break
                    @case('email')
                    <th name="email"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('email',trans('models.admin.attributes.email')) !!}</th>
                    @break
                    @case('mobile_no')
                    <th name="mobile_no"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('mobile_no') !!}</th>
                    @break
                    @case('id_no')
                    <th name="id_no"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('id_no') !!}</th>
                    @break
                    @case('driver_license')
                    <th name="driver_license"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('driver_license') !!}</th>
                    @break
                    @case('sex')
                    <th name="sex"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('sex') !!}</th>
                    @break
                    @case('birth_date')
                    <th name="birth_date"
                        style="width: {{$config['width']?$config['width']:180}}px">{!! Sorting::aLink('birth_date') !!}</th>
                    @break
                    @case('vehicle_team_id')
                    <th name="vehicle_team_id"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('vehicle_team_id',trans('models.driver.attributes.vehicle_team_id'))!!}</th>
                    @break
                    @case('vehicles_reg_no')
                    <th name="vehicles_reg_no"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('vehicles_reg_no','Danh sách xe')!!}</th>
                    @break
                    @case('work_date')
                    <th name="work_date"
                        style="width: {{$config['width']?$config['width']:180}}px">{!! Sorting::aLink('work_date') !!}</th>
                    @break
                    @case('experience_drive')
                    <th name="experience_drive"
                        style="width: {{$config['width']?$config['width']:350}}px">{!! Sorting::aLink('experience_drive',trans('models.driver.attributes.experience_drive').' (năm)') !!}</th>
                    @break
                    @case('experience_work')
                    <th name="experience_work"
                        style="width: {{$config['width']?$config['width']:300}}px">{!! Sorting::aLink('experience_work',trans('models.driver.attributes.experience_work').' (năm)') !!}</th>
                    @break
                    @case('address')
                    <th name="address"
                        style="width: {{$config['width']?$config['width']:300}}px">{!! Sorting::aLink('address') !!}</th>
                    @break
                    @case('hometown')
                    <th name="hometown"
                        style="width: {{$config['width']?$config['width']:150}}px">{!! Sorting::aLink('hometown') !!}</th>
                    @break
                    @case('evaluate')
                    <th name="evaluate"
                        style="width: {{$config['width']?$config['width']:350}}px">{!! Sorting::aLink('evaluate') !!}</th>
                    @break
                    @case('rank')
                    <th name="rank"
                        style="width: {{$config['width']?$config['width']:200}}px">{!! Sorting::aLink('rank') !!}</th>
                    @break
                    @case('work_description')
                    <th name="work_description"
                        style="width: {{$config['width']?$config['width']:250}}px">{!! Sorting::aLink('work_description') !!}</th>
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
            style="width: 180px">{!! Sorting::aLink('code') !!}</th>
        <th style="width: 150px">{!! Sorting::aLink('admin_user_username', trans('models.admin.attributes.username')) !!}</th>
        <th>{!! Sorting::aLink('full_name') !!}</th>
        <th style="width: 150px">{!! Sorting::aLink('mobile_no') !!}</th>
        <th style="width: 150px">{!! Sorting::aLink('id_no') !!}</th>
    @endif
</tr>
<tr class="filter-row">
    <th class="text-center  header-sticky"></th>
    <th></th>
    <th class="text-center  header-sticky">
        @include('layouts.backend.elements._filter_string', ['field' => 'drivers|code'])
    </th>
    @if(isset($configList))
        @foreach($configList as $config)
            @if($config['shown'])
                @switch($config['name'])
                    @case('username')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'username'])</th>
                    @break
                    @case('full_name')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'full_name'])</th>
                    @break
                    @case('email')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'email'])</th>
                    @break
                    @case('mobile_no')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'mobile_no'])</th>
                    @break
                    @case('id_no')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'id_no'])</th>
                    @break
                    @case('driver_license')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'driver_license', 'element' => 'dropDown', 'options' => config('system.driver_license')])</th>
                    @break
                    @case('sex')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'sex', 'element' => 'dropDown', 'options' => config('system.sex')])</th>
                    @break
                    @case('birth_date')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'birth_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('vehicle_team_id')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle_team_id'])</th>
                    @break
                    @case('vehicles_reg_no')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicles_reg_no']) </th>
                    @break
                    @case('work_date')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'work_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('experience_drive')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'experience_drive'])</th>
                    @break
                    @case('experience_work')
                    <th>@include('layouts.backend.elements._filter_number', ['field' => 'experience_drive'])</th>
                    @break
                    @case('address')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'address'])</th>
                    @break
                    @case('hometown')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'hometown'])</th>
                    @break
                    @case('evaluate')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'evaluate'])</th>
                    @break
                    @case('rank')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'rank'])</th>
                    @break
                    @case('work_description')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'work_description'])</th>
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
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'ins_date', 'class' => 'datepicker'])</th>
                    @break
                    @case('upd_date')
                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'upd_date', 'class' => 'datepicker'])</th>
                    @break
                @endswitch
            @endif
        @endforeach
    @else
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'username'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'full_name'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'mobile_no'])</th>
        <th>@include('layouts.backend.elements._filter_string', ['field' => 'id_no'])</th>
    @endif
</tr> --}}